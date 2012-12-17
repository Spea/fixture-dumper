<?php

namespace Sp\FixtureDumper;

use Doctrine\Common\Persistence\Mapping\AbstractClassMetadataFactory;
use Doctrine\Common\Persistence\ObjectManager;
use Sp\FixtureDumper\Generator\AbstractGenerator;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Symfony\Component\Filesystem\Filesystem;
use PhpCollection\MapInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sp\FixtureDumper\Converter\Handler\HandlerRegistryInterface;
use Sp\FixtureDumper\Converter\DefaultNavigator;

/**
 * General class for dumping fixtures.
 *
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
abstract class Dumper
{
    /**
     * @var Generator\AbstractGenerator
     */
    protected $generator;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $objectManager;

    /**
     * @var Converter\Handler\HandlerRegistryInterface
     */
    protected $handlerRegistry;

    /**
     * @var bool
     */
    protected $dumpMultipleFiles;

    /**
     * Construct.
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
     * @param Converter\Handler\HandlerRegistryInterface $handlerRegistry
     * @param \PhpCollection\MapInterface                $generators
     *
     * @internal param \Sp\FixtureDumper\Generator\AbstractGenerator $generator
     */
    public function __construct(ObjectManager $objectManager, HandlerRegistryInterface $handlerRegistry, MapInterface $generators)
    {
        $this->objectManager = $objectManager;
        $this->handlerRegistry = $handlerRegistry;
        $this->generators = $generators;
        $this->dumpMultipleFiles = true;
    }

    public function dump($path, $format, array $models = null, array $options = array())
    {
        $metadata = $this->getDumpOrder($this->getAllMetadata());
        $generator = $this->generators->get($format)->get();
        $generator->setNavigator(new DefaultNavigator($this->handlerRegistry, $format));

        $fixtures = array();
        foreach ($metadata as $data) {
            $fixture = $generator->generate($data, $models, $options);
            if ($this->dumpMultipleFiles) {
                $fileName = $generator->createFileName($data, true);
                $this->writeFixture($generator, $fixture, $path, $fileName);
            } else {
                $fileName = $generator->createFileName($data, false);
            }

            $fixtures[] = $fixture;
        }

        if (!$this->dumpMultipleFiles && count($fixtures) != 0) {
            $fixture = implode("\n\n", $fixtures);

            $this->writeFixture($generator, $fixture, $path, $fileName);
        }
    }

    /**
     * @param bool $dumpMultipleFiles
     */
    public function setDumpMultipleFiles($dumpMultipleFiles)
    {
        $this->dumpMultipleFiles = $dumpMultipleFiles;
    }

    /**
     * @return bool
     */
    public function getDumpMultipleFiles()
    {
        return $this->dumpMultipleFiles;
    }

    /**
     * @param Generator\AbstractGenerator $generator
     * @param string                      $fixture
     * @param string                      $path
     * @param string                      $fileName
     */
    protected function writeFixture(AbstractGenerator $generator, $fixture, $path, $fileName)
    {
        $filesystem = new Filesystem();
        if (!$filesystem->exists($path)) {
            $filesystem->mkdir($path);
        }

        $fixture = $generator->prepareForWrite($fixture);

        file_put_contents($path .DIRECTORY_SEPARATOR. $fileName, $fixture);
    }

    protected function getAllMetadata()
    {
        return $this->objectManager->getMetadataFactory()->getAllMetadata();
    }

    abstract protected function getDumpOrder(array $classes);

}
