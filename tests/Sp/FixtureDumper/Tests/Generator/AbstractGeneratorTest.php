<?php

/*
 * This file is part of the FixtureDumper library.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\FixtureDumper\Tests\Generator;

use Sp\FixtureDumper\Generator\AbstractGenerator;
use Sp\FixtureDumper\Tests\Generator\Fixture\Author;
use Sp\FixtureDumper\Tests\Generator\Fixture\Post;
use Sp\FixtureDumper\Converter\DefaultNavigator;
use Sp\FixtureDumper\Converter\Handler\HandlerRegistry;
use Sp\FixtureDumper\Converter\Handler\DateHandler;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
abstract class AbstractGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Sp\FixtureDumper\Generator\AbstractGenerator
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

    /**
     * @var \Sp\FixtureDumper\Converter\Handler\HandlerRegistry
     */
    protected $handlerRegistry;

    /**
     * @return \Sp\FixtureDumper\Generator\AbstractGenerator
     */
    abstract protected function loadGenerator();

    /**
     * @return string
     */
    abstract protected function getFormat();

    protected function getOptions()
    {
        return array();
    }

    protected function setUp()
    {
        $this->namingStrategy = $this->getMock('Sp\FixtureDumper\Generator\NamingStrategyInterface');
        $this->manager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->metadata = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $this->repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->generator = $this->loadGenerator();
        $this->handlerRegistry = new HandlerRegistry();
        $this->handlerRegistry->addSubscribingHandler(new DateHandler());
        $this->generator->setNavigator(new DefaultNavigator($this->handlerRegistry, $this->getFormat()));
    }

    public function testBasicGenerate()
    {
        $this->generator = $this->getMockForAbstractClass('Sp\FixtureDumper\Generator\AbstractGenerator', array($this->manager, $this->namingStrategy), '', true, true, true, array('getModels'));

        $this->generator->expects($this->once())->method('getModels')->will($this->returnValue(array()));
        $this->generator->expects($this->once())->method('doGenerate')->with($this->equalTo($this->metadata), $this->equalTo(array()), $this->equalTo(array()));

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
        $this->generator->setVisitor($visitor);

        $result = $this->generator->getVisitor();
        $this->assertEquals($visitor, $result);
    }

    public function testGenerate()
    {
        $author = new Author();
        $author->setId(2);
        $author->setUsername('Username');

        $post1 = new Post();
        $post1->setId(10);
        $post1->setDescription('Description');
        $post1->setTitle('Title');
        $post1->setCreated(new \DateTime("2012-12-12 12:12:12"));
        $post1->setAuthor($author);

        $post2 = new Post();
        $post2->setId(11);
        $post2->setDescription('Description2');
        $post2->setTitle('Title2');
        $post2->setCreated(new \DateTime("2012-12-12 12:12:12"));
        $post2->setAuthor($author);

        $models = array($post1, $post2);

        $fieldNames = array('description', 'title', 'created');
        $associationNames = array('author');

        $classMetadata = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $classMetadata->expects($this->any())->method('getName')->will($this->returnValue('Sp\FixtureDumper\Tests\Fixture\Post'));
        $classMetadata->expects($this->any())->method('getFieldNames')->will($this->returnValue($fieldNames));
        $classMetadata->expects($this->any())->method('isSingleValuedAssociation')->will($this->returnValue(true));
        $classMetadata->expects($this->any())->method('getAssociationNames')->will($this->returnValue($associationNames));
        $classMetadata->expects($this->any())->method('getAssociationTargetClass')->with($this->equalTo('author'))->will($this->returnValue('Sp\FixtureDumper\Tests\Generator\Fixture\Author'));
        $classMetadata->expects($this->any())->method('getIdentifierValues')->will($this->returnCallback(function($model) {
            return array('id' => $model->getId());
        }));

        $authorClassMetadata = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $authorClassMetadata->expects($this->any())->method('getName')->will($this->returnValue('Sp\FixtureDumper\Tests\Generator\Fixture\Author'));
        $authorClassMetadata->expects($this->any())->method('getIdentifierValues')->will($this->returnCallback(function($model) {
            return array('id' => $model->getId());
        }));

        $this->repository->expects($this->once())->method('findAll')->will($this->returnValue($models));
        $this->manager->expects($this->once())->method('getRepository')->will($this->returnValue($this->repository));
        $this->manager->expects($this->any())->method('getClassMetadata')->with($this->equalTo('Sp\FixtureDumper\Tests\Generator\Fixture\Author'))->will($this->returnValue($authorClassMetadata));

        $output = $this->generator->generate($classMetadata, null, $this->getOptions());

        $this->assertEquals($this->getContent('post_data'), $output);
    }

    public function testGenerateWithoutDependencies()
    {
        $author = new Author();
        $author->setId(2);
        $author->setUsername('Username');

        $models = array($author);

        $fieldNames = array('username');

        $classMetadata = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $classMetadata->expects($this->any())->method('getName')->will($this->returnValue('Sp\FixtureDumper\Tests\Fixture\Author'));
        $classMetadata->expects($this->any())->method('getFieldNames')->will($this->returnValue($fieldNames));
        $classMetadata->expects($this->any())->method('isSingleValuedAssociation')->will($this->returnValue(true));
        $classMetadata->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $classMetadata->expects($this->any())->method('getIdentifierValues')->will($this->returnCallback(function($model) {
            return array('id' => $model->getId());
        }));

        $this->repository->expects($this->once())->method('findAll')->will($this->returnValue($models));
        $this->manager->expects($this->once())->method('getRepository')->will($this->returnValue($this->repository));

        $output = $this->generator->generate($classMetadata, null, $this->getOptions());

        $this->assertEquals($this->getContent('author_data'), $output);
    }

    private function getContent($file)
    {
        return file_get_contents(__DIR__.'/Fixture/generated/'. $this->getFormat() .'/'.$file);
    }
}
