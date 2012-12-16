<?php

namespace Sp\FixtureDumper\Tests\Generator;

use Sp\FixtureDumper\Generator\ClassFixtureGenerator;
use Sp\FixtureDumper\Generator\ClassNamingStrategy;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class ClassFixtureGeneratorTest extends AbstractGeneratorTest
{
    /**
     * @return \Sp\FixtureDumper\Generator\AbstractGenerator
     */
    protected function loadGenerator()
    {
        $generator = new ClassFixtureGenerator($this->manager, new ClassNamingStrategy());

        return $generator;
    }

    protected function getOptions()
    {
        return array('namespace' => 'Sp\FixtureDumper\Tests\Generator\Fixture');
    }

    /**
     * @return string
     */
    protected function getFormat()
    {
        return 'php';
    }
}
