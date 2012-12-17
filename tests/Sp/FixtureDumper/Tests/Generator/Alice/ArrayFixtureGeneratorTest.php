<?php

namespace Sp\FixtureDumper\Tests\Generator\Alice;

use Sp\FixtureDumper\Generator\DefaultNamingStrategy;
use Sp\FixtureDumper\Tests\Generator\AbstractGeneratorTest;
use Sp\FixtureDumper\Generator\Alice\ArrayFixtureGenerator;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class ArrayFixtureGeneratorTest extends AbstractGeneratorTest
{
    /**
     * @return \Sp\FixtureDumper\Generator\AbstractGenerator
     */
    protected function loadGenerator()
    {
        $generator = new ArrayFixtureGenerator($this->manager, new DefaultNamingStrategy());

        return $generator;
    }

    /**
     * @return string
     */
    protected function getFormat()
    {
        return 'array';
    }
}
