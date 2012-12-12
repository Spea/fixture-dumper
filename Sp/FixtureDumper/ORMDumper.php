<?php

namespace Sp\FixtureDumper;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
use Doctrine\ORM\Internal\CommitOrderCalculator;

class ORMDumper extends Dumper
{
    protected function getDumpOrder(array $classes)
    {
        $calc = new CommitOrderCalculator();

        foreach ($classes as $class) {
            $calc->addClass($class);
        }

        return $calc->getCommitOrder();
    }
}
