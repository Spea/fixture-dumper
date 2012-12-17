<?php

/*
 * This file is part of the FixtureDumper library.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\FixtureDumper\Tests;

use PhpCollection\Map;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class DumperTest extends \PHPUnit_Framework_TestCase
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
        $this->metadata = array($this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata'), $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata'));
        $this->manager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->generator = $this->getMockBuilder('Sp\FixtureDumper\Generator\AbstractGenerator')->disableOriginalConstructor()->setMethods(array('generate', 'setNavigator'))->getMockForAbstractClass();
        $this->handlerRegistry = $this->getMock('Sp\FixtureDumper\Converter\Handler\HandlerRegistryInterface');
        $this->dumper = $this->getMockForAbstractClass('Sp\FixtureDumper\Dumper', array($this->manager, $this->handlerRegistry, new Map(array('php' => $this->generator))), '', true, true, true, array('writeFixture', 'getAllMetadata'));

        $this->dumper->expects($this->once())->method('getAllMetadata')->will($this->returnValue($this->metadata));
        $this->dumper->expects($this->once())->method('getDumpOrder')->will($this->returnValue($this->metadata));
    }

    public function testGenericDump()
    {
        $fixture1 = '$fixture1';
        $fixture2 = '$fixture2';
        $this->generator->expects($this->at(0))->method('setNavigator');
        $this->generator->expects($this->exactly(2))->method('createFileName');
        $this->generator->expects($this->at(1))->method('generate')->with($this->equalTo($this->metadata[0]), $this->equalTo(null), $this->equalTo(array()))->will($this->returnValue($fixture1));
        $this->generator->expects($this->at(3))->method('generate')->with($this->equalTo($this->metadata[1]), $this->equalTo(null), $this->equalTo(array()))->will($this->returnValue($fixture2));
        $this->dumper->expects($this->at(2))->method('writeFixture')->with($this->equalTo($this->generator), $this->equalTo($fixture1), $this->equalTo('/foo'));
        $this->dumper->expects($this->at(3))->method('writeFixture')->with($this->equalTo($this->generator), $this->equalTo($fixture2), $this->equalTo('/foo'));

        $this->dumper->dump('/foo', 'php');
    }

    public function testGenericDumpSingleFile()
    {
        $fixture1 = '$fixture1';
        $fixture2 = '$fixture2';
        $this->dumper->setDumpMultipleFiles(false);
        $this->generator->expects($this->at(0))->method('setNavigator');
        $this->generator->expects($this->at(1))->method('generate')->with($this->equalTo($this->metadata[0]), $this->equalTo(null), $this->equalTo(array()))->will($this->returnValue($fixture1));
        $this->generator->expects($this->at(3))->method('generate')->with($this->equalTo($this->metadata[1]), $this->equalTo(null), $this->equalTo(array()))->will($this->returnValue($fixture2));
        $this->dumper->expects($this->once())->method('writeFixture')->with($this->equalTo($this->generator), $this->equalTo($fixture1 ."\n\n". $fixture2), $this->equalTo('/foo'));

        $this->dumper->dump('/foo', 'php');
    }
}
