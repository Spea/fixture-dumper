<?php

namespace Sp\FixtureDumper\Generator;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\Common\Persistence\ObjectManager;
use Sp\FixtureDumper\Converter\DefaultNavigator;
use Sp\FixtureDumper\Converter\VisitorInterface;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
abstract class AbstractGenerator
{
    /**
     * @var NamingStrategy
     */
    protected $namingStrategy;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $manager;

    /**
     * @var \Sp\FixtureDumper\Converter\DefaultNavigator
     */
    protected $navigator;

    /**
     * @var \Sp\FixtureDumper\Converter\VisitorInterface
     */
    protected $visitor;

    /**
     * @var array
     */
    protected $models;

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager   $manager
     * @param NamingStrategy                               $namingStrategy
     * @param \Sp\FixtureDumper\Converter\VisitorInterface $visitor
     * @param \Sp\FixtureDumper\Converter\DefaultNavigator $navigator
     */
    public function __construct(ObjectManager $manager, NamingStrategy $namingStrategy, VisitorInterface $visitor = null)
    {
        $this->manager = $manager;
        $this->namingStrategy = $namingStrategy;
        $this->visitor = $visitor;
    }

    public function generate(ClassMetadata $metadata, array $models = null, array $options = array())
    {
        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);
        $options = $resolver->resolve($options);
        if (null === $models) {
            $models = $this->getModels($metadata);
        }

        return $this->doGenerate($metadata, $models, $options);
    }

    /**
     * @param \Sp\FixtureDumper\Converter\VisitorInterface $visitor
     */
    public function setVisitor(VisitorInterface $visitor)
    {
        $this->visitor = $visitor;
    }

    /**
     * @return \Sp\FixtureDumper\Converter\VisitorInterface
     */
    public function getVisitor()
    {
        if (null === $this->visitor) {
            $this->visitor = $this->getDefaultVisitor();
        }

        return $this->visitor;
    }

    /**
     * @param \Sp\FixtureDumper\Converter\DefaultNavigator $navigator
     */
    public function setNavigator($navigator)
    {
        var_dump("foobar");
        $this->navigator = $navigator;
    }

    /**
     * @return \Sp\FixtureDumper\Converter\DefaultNavigator
     */
    public function getNavigator()
    {
        return $this->navigator;
    }

    /**
     * Prepares the given fixture for writing to a file.
     *
     * @param $fixture
     *
     * @return mixed
     */
    public function prepareForWrite($fixture)
    {
        return $fixture;
    }

    /**
     * Creates the filename for this fixture.
     *
     * @param \Doctrine\Common\Persistence\Mapping\ClassMetadata $metadata
     * @param bool                                               $multipleFiles
     *
     * @return mixed
     */
    abstract public function createFilename(ClassMetadata $metadata, $multipleFiles = true);

    /**
     * Generates the fixture for the specified metadata.
     *
     * @param \Doctrine\Common\Persistence\Mapping\ClassMetadata $metadata
     * @param array                                              $models
     * @param array                                              $options
     *
     * @return mixed
     */
    abstract protected function doGenerate(ClassMetadata $metadata, array $models = null, array $options = array());

    /**
     * @return \Sp\FixtureDumper\Converter\VisitorInterface
     */
    abstract protected function getDefaultVisitor();

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    protected function setDefaultOptions(OptionsResolver $resolver)
    {}

    /**
     * @param \Doctrine\Common\Persistence\Mapping\ClassMetadata $metadata
     *
     * @return array
     */
    protected function getModels(ClassMetadata $metadata)
    {
        return $this->manager->getRepository($metadata->getName())->findAll();
    }

    /**
     * Reads a property from an object.
     *
     * @param $object
     * @param $property
     *
     * @return mixed
     * @throws InvalidPropertyException
     */
    protected function readProperty($object, $property)
    {
        $camelProp = ucfirst($property);
        $getter = 'get'.$camelProp;
        $isser = 'is'.$camelProp;

        if (method_exists($object, $getter)) {
            return $object->$getter();
        } elseif (method_exists($object, $isser)) {
            return $object->$isser();
        } elseif (property_exists($object, $property)) {
            return $object->$property;
        }

        throw new InvalidPropertyException(sprintf('Neither property "%s" nor method "%s()" nor method "%s()" exists in class "%s"', $property, $getter, $isser, get_class($object)));
    }
}
