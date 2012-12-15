<?php

namespace Sp\FixtureDumper\Tests\Generator;

use Sp\FixtureDumper\Generator\ClassFixtureGenerator;
use Sp\FixtureDumper\Tests\Generator\Fixture\Author;
use Sp\FixtureDumper\Tests\Generator\Fixture\Post;
use Sp\FixtureDumper\Generator\ClassNamingStrategy;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class ClassFixtureGeneratorTest extends \PHPUnit_Framework_TestCase
{
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

        $objectManager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $repository->expects($this->once())->method('findAll')->will($this->returnValue($models));
        $objectManager->expects($this->once())->method('getRepository')->will($this->returnValue($repository));
        $objectManager->expects($this->any())->method('getClassMetadata')->with($this->equalTo('Sp\FixtureDumper\Tests\Generator\Fixture\Author'))->will($this->returnValue($authorClassMetadata));
        $classFixtureGenerator = new ClassFixtureGenerator($objectManager, new ClassNamingStrategy());

        $output = $classFixtureGenerator->generate($classMetadata, null, array('namespace' => 'Sp\FixtureDumper\Tests\Generator\Fixture'));

        $this->assertEquals($this->getContent('LoadPostData.php'), $output);
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

        $objectManager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $repository->expects($this->once())->method('findAll')->will($this->returnValue($models));
        $objectManager->expects($this->once())->method('getRepository')->will($this->returnValue($repository));
        $classFixtureGenerator = new ClassFixtureGenerator($objectManager, new ClassNamingStrategy());

        $output = $classFixtureGenerator->generate($classMetadata, null, array('namespace' => 'Sp\FixtureDumper\Tests\Generator\Fixture'));

        $this->assertEquals($this->getContent('LoadAuthorData.php'), $output);
    }

    private function getContent($file)
    {
        return file_get_contents(__DIR__.'/Fixture/generated/'.$file);
    }
}
