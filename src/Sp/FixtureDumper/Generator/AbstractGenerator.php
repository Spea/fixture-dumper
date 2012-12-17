<?php

namespace Sp\FixtureDumper\Generator;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\Common\Persistence\ObjectManager;
use Sp\FixtureDumper\Converter\DefaultNavigator;
use Sp\FixtureDumper\Converter\VisitorInterface;
use Sp\FixtureDumper\Converter\DefaultVisitor;

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
     */
    public function __construct(ObjectManager $manager, NamingStrategy $namingStrategy = null, VisitorInterface $visitor = null)
    {
        $this->manager = $manager;
        $this->namingStrategy = $namingStrategy ?: $this->getDefaultNamingStrategy();
        $this->visitor = $visitor ?: $this->getDefaultVisitor();
    }

    /**
     * @param \Doctrine\Common\Persistence\Mapping\ClassMetadata $metadata
     * @param array                                              $models
     * @param array                                              $options
     *
     * @return mixed
     */
    public function generate(ClassMetadata $metadata, array $models = null, array $options = array())
    {
        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);
        $options = $resolver->resolve($options);
        if (null === $models) {
            $models = $this->getModels($metadata);
        }

        $preparedData = array();
        foreach ($models as $model) {
            $data = array();
            $data['fields'] = $this->processFieldNames($metadata, $model);
            $data['associations'] = $this->processAssociationNames($metadata, $model);

            $preparedData[$this->namingStrategy->modelName($model, $metadata)] = $data;
        }

        return $this->doGenerate($metadata, $preparedData, $options);
    }

    /**
     * @param \Doctrine\Common\Persistence\Mapping\ClassMetadata $metadata
     * @param                                                    $model
     *
     * @return array
     */
    protected function processFieldNames(ClassMetadata $metadata, $model)
    {
        $data = array();
        foreach ($metadata->getFieldNames() as $fieldName) {
            if ($metadata->isIdentifier($fieldName)) {
                continue;
            }

            $data[$fieldName] = $this->navigator->accept($this->getVisitor(), $this->readProperty($model, $fieldName));
        }

        return $data;
    }

    /**
     * @param \Doctrine\Common\Persistence\Mapping\ClassMetadata $metadata
     * @param                                                    $model
     *
     * @return array
     */
    protected function processAssociationNames(ClassMetadata $metadata, $model)
    {
        $data = array();
        foreach ($metadata->getAssociationNames() as $assocName) {
            $propertyValue = $this->readProperty($model, $assocName);
            if (null === $propertyValue || $metadata->isAssociationInverseSide($assocName)) {
                continue;
            }

            if ($metadata->isSingleValuedAssociation($assocName)) {
                $assocValue = $this->namingStrategy->modelName($propertyValue, $this->manager->getClassMetadata(get_class($propertyValue)));
                $assocValue = $this->navigator->accept($this->getVisitor(), $assocValue, 'reference');
                $data[$assocName] = $assocValue;
            } else {
                $data[$assocName] = array();
                foreach ($propertyValue as $value) {
                    $assocValue = $this->namingStrategy->modelName($propertyValue, $this->manager->getClassMetadata(get_class($value)));
                    $assocValue = $this->navigator->accept($this->getVisitor(), $assocValue, 'reference');
                    $data[$assocName][] = $assocValue;
                }
            }
        }

        return $data;
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
        return $this->visitor;
    }

    /**
     * @param \Sp\FixtureDumper\Converter\DefaultNavigator $navigator
     */
    public function setNavigator($navigator)
    {
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
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function setManager($manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param \Sp\FixtureDumper\Generator\NamingStrategy $namingStrategy
     */
    public function setNamingStrategy($namingStrategy)
    {
        $this->namingStrategy = $namingStrategy;
    }

    /**
     * @return \Sp\FixtureDumper\Generator\NamingStrategy
     */
    public function getNamingStrategy()
    {
        return $this->namingStrategy;
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
     * @param array                                              $data
     * @param array                                              $options
     *
     * @return mixed
     */
    abstract protected function doGenerate(ClassMetadata $metadata, array $data, array $options = array());

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    protected function setDefaultOptions(OptionsResolver $resolver)
    {}

    /**
     * @return \Sp\FixtureDumper\Converter\VisitorInterface
     */
    protected function getDefaultVisitor()
    {
        return new DefaultVisitor();
    }


    /**
     * @return DefaultNamingStrategy
     */
    protected function getDefaultNamingStrategy()
    {
        return new DefaultNamingStrategy();
    }

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
