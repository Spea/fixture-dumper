<?php

namespace Sp\FixtureDumper\Tests\Generator;

use Sp\FixtureDumper\Generator\ClassNamingStrategy;
use Sp\FixtureDumper\Tests\Generator\Fixture\Post;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class ClassNamingStrategyTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Sp\FixtureDumper\Generator\DefaultNamingStrategy
     */
    protected $namingStrategy;

    public function setUp()
    {
        $this->namingStrategy = new ClassNamingStrategy();
    }

    public function testFixtureName()
    {
        $model = new Post();
        $model->setId(22);

        $metadata = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $metadata->expects($this->once())->method('getName')->will($this->returnValue('Acme\Demo\Entity\Post'));

        $result = $this->namingStrategy->fixtureName($metadata);

        $this->assertEquals('LoadPostData', $result);
    }
}
