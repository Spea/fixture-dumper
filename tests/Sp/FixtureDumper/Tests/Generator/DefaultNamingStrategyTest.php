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

use Sp\FixtureDumper\Generator\DefaultNamingStrategy;
use Sp\FixtureDumper\Tests\Generator\Fixture\Post;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class DefaultNamingStrategyTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Sp\FixtureDumper\Generator\DefaultNamingStrategy
     */
    protected $namingStrategy;

    public function setUp()
    {
        $this->namingStrategy = new DefaultNamingStrategy();
    }

    public function testModelName()
    {
        $model = new Post();
        $model->setId(22);

        $metadata = $this->createMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $metadata->expects($this->once())->method('getName')->will($this->returnValue('Acme\Demo\Entity\Post'));
        $metadata->expects($this->once())->method('getIdentifierValues')->with($this->equalTo($model))->will($this->returnValue(array('id' => 22)));

        $result = $this->namingStrategy->modelName($model, $metadata);

        $this->assertEquals('post22', $result);
    }

    public function testFixtureName()
    {
        $model = new Post();
        $model->setId(22);

        $metadata = $this->createMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $metadata->expects($this->once())->method('getName')->will($this->returnValue('Acme\Demo\Entity\Post'));

        $result = $this->namingStrategy->fixtureName($metadata);

        $this->assertEquals('Post', $result);
    }
}
