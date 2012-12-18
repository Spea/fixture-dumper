<?php

/*
 * This file is part of the FixtureDumper library.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\FixtureDumper;

use Doctrine\ODM\MongoDB\Internal\CommitOrderCalculator;
use Sp\FixtureDumper\Mapping\MongoDB\ClassMetadataProxy;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class MongoDBDumper extends Dumper
{
    /**
     * {@inheritdoc}
     */
    public function getDumpOrder(array $classes)
    {
        $calc = new CommitOrderCalculator();

        // See if there are any new classes in the changeset, that are not in the
        // commit order graph yet (don't have a node).
        // We have to inspect changeSet to be able to correctly build dependencies.
        // It is not possible to use IdentityMap here because post inserted ids
        // are not yet available.
        $newNodes = array();
        foreach ($classes as $class) {
            if ($calc->hasClass($class->getName())) {
                continue;
            }

            $calc->addClass($class);

            $newNodes[] = $class;
        }

        // Calculate dependencies for new nodes
        while ($class = array_pop($newNodes)) {
            $this->addDependencies($class, $calc);
        }

        return $calc->getCommitOrder();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllMetadata()
    {
        $metadata = parent::getAllMetadata();

        $newMetadata = array();
        foreach ($metadata as $data) {
            $newMetadata[] = new ClassMetadataProxy($data);
        }

        return $newMetadata;
    }

    private function addDependencies(ClassMetadataProxy $class, CommitOrderCalculator $calc)
    {
        foreach ($class->origFieldMappings as $mapping) {
            $isOwningReference = isset($mapping['reference']) && $mapping['isOwningSide'];
            $isAssociation = isset($mapping['embedded']) || $isOwningReference;
            if (!$isAssociation || !isset($mapping['targetDocument'])) {
                continue;
            }

            $targetClass = $this->objectManager->getClassMetadata($mapping['targetDocument']);

            if ( ! $calc->hasClass($targetClass->name)) {
                $calc->addClass($targetClass);
            }

            $calc->addDependency($targetClass, $class);

            // If the target class has mapped subclasses, these share the same dependency.
            if ( ! $targetClass->subClasses) {
                continue;
            }

            foreach ($targetClass->subClasses as $subClassName) {
                $targetSubClass = $this->dm->getClassMetadata($subClassName);

                if ( ! $calc->hasClass($subClassName)) {
                    $calc->addClass($targetSubClass);

                    $newNodes[] = $targetSubClass;
                }

                $calc->addDependency($targetSubClass, $class);
            }

            // avoid infinite recursion
            if ($class !== $targetClass) {
                $this->addDependencies($targetClass, $calc);
            }
        }
    }
}
