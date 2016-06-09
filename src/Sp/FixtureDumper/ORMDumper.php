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

use Doctrine\ORM\Internal\CommitOrderCalculator;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class ORMDumper extends AbstractDumper
{
    protected function getDumpOrder(array $classes)
    {
        $calc = new CommitOrderCalculator();
        foreach ($classes as $class) {
            if (!$class->getReflectionClass()->isInstantiable() || $class->isMappedSuperclass) {
                continue;
            }

            $calc->addClass($class);

            // $class before its parents
            foreach ($class->parentClasses as $parentClass) {
                $parentClass = $this->objectManager->getClassMetadata($parentClass);
                
                if (!$parentClass->getReflectionClass()->isInstantiable() || $parentClass->isMappedSuperclass) {
                    continue;
                }

                if (!$calc->hasClass($parentClass->name)) {
                    $calc->addClass($parentClass);
                }

                $calc->addDependency($class, $parentClass);
            }

            foreach ($class->associationMappings as $assoc) {
                if ($assoc['isOwningSide']) {
                    $targetClass = $this->objectManager->getClassMetadata($assoc['targetEntity']);
                    
                    if (!$targetClass->getReflectionClass()->isInstantiable() || $targetClass->isMappedSuperclass) {
                        continue;
                    }

                    if (!$calc->hasClass($targetClass->name)) {
                        $calc->addClass($targetClass);
                    }

                    // add dependency ($targetClass before $class)
                    $calc->addDependency($targetClass, $class);

                    // parents of $targetClass before $class, too
                    foreach ($targetClass->parentClasses as $parentClass) {
                        $parentClass = $this->objectManager->getClassMetadata($parentClass);

                        if (!$parentClass->getReflectionClass()->isInstantiable() || $parentClass->isMappedSuperclass) {
                            continue;
                        }

                        if ( ! $calc->hasClass($parentClass->name)) {
                            $calc->addClass($parentClass);
                        }

                        $calc->addDependency($parentClass, $class);
                    }
                }
            }
        }

        return $calc->getCommitOrder();
    }
}
