<?php

namespace Sp\FixtureDumper;

use Doctrine\ORM\Internal\CommitOrderCalculator;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class ORMDumper extends Dumper
{
    protected function getDumpOrder(array $classes)
    {
        $calc = new CommitOrderCalculator();
        foreach ($classes as $class) {
            $calc->addClass($class);

            // $class before its parents
            foreach ($class->parentClasses as $parentClass) {
                $parentClass = $this->objectManager->getClassMetadata($parentClass);

                if (!$calc->hasClass($parentClass->name)) {
                    $calc->addClass($parentClass);
                }

                $calc->addDependency($class, $parentClass);
            }

            foreach ($class->associationMappings as $assoc) {
                if ($assoc['isOwningSide']) {
                    $targetClass = $this->objectManager->getClassMetadata($assoc['targetEntity']);

                    if (!$calc->hasClass($targetClass->name)) {
                        $calc->addClass($targetClass);
                    }

                    // add dependency ($targetClass before $class)
                    $calc->addDependency($targetClass, $class);

                    // parents of $targetClass before $class, too
                    foreach ($targetClass->parentClasses as $parentClass) {
                        $parentClass = $this->objectManager->getClassMetadata($parentClass);

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
