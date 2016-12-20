<?php

namespace Sp\FixtureDumper\Tests\ExclusionStrategy;

use Sp\FixtureDumper\ExclusionStrategy\ArrayExclusionStrategy;

/**
 * @author Miguel González <infinit89@gmail.com>
 */
class ArrayExclusionStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataProviderArrayExclusionStrategy
     */
    public function testShouldSkipClassName($excludedClasses, $classMetadata, $shouldSkip)
    {
        $exclusionStrategy = new ArrayExclusionStrategy($excludedClasses);

        $this->assertEquals($exclusionStrategy->shouldSkipClass($classMetadata), $shouldSkip);
    }

    public function dataProviderArrayExclusionStrategy()
    {
        $postClass = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $postClass->expects($this->any())->method('getName')->will($this->returnValue('Acme\Demo\Entity\Post'));

        return array(
            array(array('Post'), $postClass, true),
            array(array('Acme\Demo\Entity\Post'), $postClass, true),
            array(array('Acme\Demo\Entity\PostBlog'), $postClass, false),
            array(array('', 'aPost', 'Posti'), $postClass, false),
        );
    }
}
