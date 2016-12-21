<?php

namespace Sp\FixtureDumper\Tests\ExclusionStrategy;

use PhpCollection\Map;

/**
 * @author Miguel González <infinit89@gmail.com>
 */
class ExclusionStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $handlerRegistry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $manager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $generator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $metadata;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dumper;

    protected function setUp()
    {
        $this->metadata = array(
            $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata'),
            $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata'),
        );
        $this->manager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->generator = $this
            ->getMockBuilder('Sp\FixtureDumper\Generator\AbstractGenerator')
            ->disableOriginalConstructor()
            ->setMethods(array('generate', 'setNavigator'))
            ->getMockForAbstractClass();
        $this->handlerRegistry = $this->getMock('Sp\FixtureDumper\Converter\Handler\HandlerRegistryInterface');
        $this->dumper = $this
            ->getMockForAbstractClass(
                'Sp\FixtureDumper\AbstractDumper',
                array(
                    $this->manager,
                    $this->handlerRegistry,
                    new Map(array('php' => $this->generator)),
                ),
                '',
                true,
                true,
                true,
                array('writeFixture', 'getAllMetadata', 'getDumpOrder', 'getExclusionStrategy')
            );

        $this->dumper->expects($this->once())->method('getAllMetadata')->will($this->returnValue($this->metadata));
        $this->dumper->expects($this->once())->method('getDumpOrder')->will($this->returnValue($this->metadata));
    }

    public function testCollaborationExclusionStrategy()
    {
        $this->exclusionStrategy = $this
            ->getMock('Sp\FixtureDumper\ExclusionStrategy\ExclusionStrategyInterface');

        $this->dumper->setExclusionStrategy($this->exclusionStrategy);

        $this->dumper
            ->expects($this->once())
            ->method('getExclusionStrategy')
            ->will($this->returnValue($this->exclusionStrategy));

        $this->exclusionStrategy
            ->expects($this->exactly(2))
            ->method('shouldSkipClass');

        $this->dumper->dump('/foo', 'php');
    }
}
