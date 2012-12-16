<?php

namespace Sp\FixtureDumper\Tests\Generator;

use Sp\FixtureDumper\Generator\AbstractGenerator;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class AbstractGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $generator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $namingStrategy;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $manager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $metadata;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $repository;

    protected function setUp()
    {
        $this->namingStrategy = $this->getMock('Sp\FixtureDumper\Generator\NamingStrategy');
        $this->manager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->generator = $this->getMockForAbstractClass('Sp\FixtureDumper\Generator\AbstractGenerator', array($this->manager, $this->namingStrategy));
        $this->metadata = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $this->repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
    }


    public function testGenerate()
    {
        $this->generator = $this->getMockForAbstractClass('Sp\FixtureDumper\Generator\AbstractGenerator', array($this->manager, $this->namingStrategy));

        $this->metadata->expects($this->once())->method('getName')->will($this->returnValue('Acme\Demo\Entity\Post'));
        $this->manager->expects($this->once())->method('getRepository')->with($this->equalTo('Acme\Demo\Entity\Post'))->will($this->returnValue($this->repository));
        $this->generator->expects($this->once())->method('doGenerate')->with($this->equalTo($this->metadata), $this->equalTo(null), $this->equalTo(array()));

        $this->generator->generate($this->metadata);
    }

    public function testGetVisitorReturnsDefault()
    {
        $result = $this->generator->getVisitor();

        $this->assertInstanceOf('Sp\FixtureDumper\Converter\DefaultVisitor', $result);
    }

    public function testGetVisitorReturnsSet()
    {
        $visitor = $this->getMock('Sp\FixtureDumper\Converter\VisitorInterface');
        $generator = $this->getMockForAbstractClass('Sp\FixtureDumper\Generator\AbstractGenerator', array($this->manager, $this->namingStrategy, $visitor));

        $result = $generator->getVisitor();
        $this->assertEquals($visitor, $result);
    }

}
