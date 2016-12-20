<?php

/*
 * This file is part of the FixtureDumper library.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\FixtureDumper;

use Doctrine\Common\Persistence\ObjectManager;
use Sp\FixtureDumper\ExclusionStrategy\ExclusionStrategyInterface;
use Sp\FixtureDumper\Generator\AbstractGenerator;
use Symfony\Component\Filesystem\Filesystem;
use PhpCollection\MapInterface;
use Sp\FixtureDumper\Converter\Handler\HandlerRegistryInterface;
use Sp\FixtureDumper\Converter\DefaultNavigator;

/**
 * General class for dumping fixtures.
 *
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
abstract class AbstractDumper
{

    /**
     * @var \PhpCollection\MapInterface
     */
    protected $generators;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $objectManager;

    /**
     * @var Converter\Handler\HandlerRegistryInterface
     */
    protected $handlerRegistry;

    /**
     * @var \Sp\FixtureDumper\ExclusionStrategy\ExclusionStrategyInterface
     */
    protected $exclusionStrategy;

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

    /**
     * @param       $path
     * @param       $format
     * @param array $options
     */
    public function dump($path, $format, array $options = array())
    {
        $exclusionStrategy = $this->getExclusionStrategy();
        $metadata = $this->getAllMetadata();

        if (null !== $exclusionStrategy) {
            $metadata = array_filter($metadata, function($class) use ($exclusionStrategy) {
                return ! $exclusionStrategy->shouldSkipClass($class);
            });
        }

        $metadata = $this->getDumpOrder($metadata);
        $generator = $this->generators->get($format)->get();
        $generator->setNavigator(new DefaultNavigator($this->handlerRegistry, $format));
        $generator->setManager($this->objectManager);

        $fixtures = array();

        foreach ($metadata as $data) {
            $fixture = $generator->generate($data, null, $options);
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
    public function shouldDumpMultipleFiles()
    {
        return $this->dumpMultipleFiles;
    }

    /**
     * @param \Sp\FixtureDumper\ExclusionStrategy\ExclusionStrategyInterface $exclusionStrategy
     */
    public function setExclusionStrategy(ExclusionStrategyInterface $exclusionStrategy)
    {
        $this->exclusionStrategy = $exclusionStrategy;
    }

    /**
     * @return \Sp\FixtureDumper\ExclusionStrategy\ExclusionStrategyInterface
     */
    public function getExclusionStrategy()
    {
        return $this->exclusionStrategy;
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
