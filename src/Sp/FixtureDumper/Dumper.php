<?php

namespace Sp\FixtureDumper;

use Doctrine\Common\Persistence\Mapping\AbstractClassMetadataFactory;
use Doctrine\Common\Persistence\ObjectManager;
use Sp\FixtureDumper\Generator\AbstractGenerator;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
abstract class Dumper
{
    protected $generator;

    protected $objectManager;

    public function __construct(ObjectManager $objectManager, AbstractGenerator $generator = null)
    {
        $this->generator = $generator;
        $this->objectManager = $objectManager;
    }

    public function dump($format, $path)
    {
        $meta = $this->getAllMetadata();
        $this->getDumpOrder($meta);

    }

    protected function getAllMetadata()
    {
        return $this->objectManager->getMetadataFactory()->getAllMetadata();
    }

    abstract protected function getDumpOrder(array $classes);

}
