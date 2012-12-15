<?php

namespace Sp\FixtureDumper;

use Doctrine\Common\Persistence\Mapping\AbstractClassMetadataFactory;
use Doctrine\Common\Persistence\ObjectManager;
use Sp\FixtureDumper\Generator\AbstractGenerator;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Symfony\Component\Filesystem\Filesystem;

/**
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
     * @var bool
     */
    protected $dumpMultipleFiles;

    /**
     * Construct.
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
     * @param Generator\AbstractGenerator                $generator
     */
    public function __construct(ObjectManager $objectManager, AbstractGenerator $generator = null)
    {
        $this->generator = $generator;
        $this->objectManager = $objectManager;
        $this->dumpMultipleFiles = true;
    }

    public function dump($path, array $models = null, array $options = array())
    {
        $metadata = $this->getDumpOrder($this->getAllMetadata());
        $fixtures = array();
        foreach ($metadata as $data) {
            $fixture = $this->generator->generate($data, $models, $options);
            $fileName = $this->generator->createFileName($data, false);
            if ($this->getDumpMultipleFiles()) {
                $fileName = $this->generator->createFileName($data, true);
                $this->writeFixture($fixture, $path, $fileName);
            }

            $fixtures[] = $fixture;
        }

        if (!$this->getDumpMultipleFiles() && count($fixtures) != 0) {
            $fixture = implode("\n\n", $fixtures);

            $this->writeFixture($fixture, $path, $fileName);
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
     * @param $fixture
     * @param $path
     * @param $fileName
     */
    protected function writeFixture($fixture, $path, $fileName)
    {
        $filesystem = new Filesystem();
        if (!$filesystem->exists($path)) {
            $filesystem->mkdir($path);
        }

        $fixture = $this->generator->prepareForWrite($fixture);

        file_put_contents($path .DIRECTORY_SEPARATOR. $fileName, $fixture);
    }

    protected function getAllMetadata()
    {
        return $this->objectManager->getMetadataFactory()->getAllMetadata();
    }

    abstract protected function getDumpOrder(array $classes);

}
