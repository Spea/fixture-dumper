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
        }

        return $calc->getCommitOrder();
    }
}
