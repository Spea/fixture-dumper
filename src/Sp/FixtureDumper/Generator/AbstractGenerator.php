<?php

/*
 * This file is part of the FixtureDumper library.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\FixtureDumper\Generator;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\Common\Persistence\ObjectManager;
use Sp\FixtureDumper\Converter\DefaultNavigator;
use Sp\FixtureDumper\Converter\VisitorInterface;
use Sp\FixtureDumper\Converter\DefaultVisitor;

/**
 * Base class for generating fixtures.
 *
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
abstract class AbstractGenerator
{
    /**
     * @var NamingStrategyInterface
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
     * @param \Doctrine\Common\Persistence\ObjectManager|null   $manager
     * @param NamingStrategyInterface|null                      $namingStrategy
     * @param \Sp\FixtureDumper\Converter\VisitorInterface|null $visitor
     */
    public function __construct(ObjectManager $manager = null, NamingStrategyInterface $namingStrategy = null, VisitorInterface $visitor = null)
    {
        $this->manager = $manager;
        $this->namingStrategy = $namingStrategy ?: $this->getDefaultNamingStrategy();
        $this->visitor = $visitor ?: $this->getDefaultVisitor();
    }

    /**
     * @param \Doctrine\Common\Persistence\Mapping\ClassMetadata $metadata
     * @param array|null                                         $models
     * @param array                                              $options
     *
     * @return string
     */
    public function generate(ClassMetadata $metadata, array $models = null, array $options = array())
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired($this->getRequiredOptions());
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
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {}

    /**
     * @return array
     */
    public function getRequiredOptions()
    {
        return array();
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
     * @throws \RuntimeException
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    public function getManager()
    {
        if (null === $this->manager) {
            throw new \RuntimeException(sprintf('No manager was configured for the class "%s%, use "%s" to set one.', __CLASS__, 'setManager'));
        }

        return $this->manager;
    }

    /**
     * @param \Sp\FixtureDumper\Generator\NamingStrategyInterface $namingStrategy
     */
    public function setNamingStrategy($namingStrategy)
    {
        $this->namingStrategy = $namingStrategy;
    }

    /**
     * @return \Sp\FixtureDumper\Generator\NamingStrategyInterface
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
     * @return string
     */
    abstract protected function doGenerate(ClassMetadata $metadata, array $data, array $options = array());

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
            if ($metadata->isIdentifier($fieldName) && $metadata->usesIdGenerator()) {
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
                $assocValue = $this->namingStrategy->modelName($propertyValue, $this->getManager()->getClassMetadata(get_class($propertyValue)));
                $assocValue = $this->navigator->accept($this->getVisitor(), $assocValue, 'reference');
                $data[$assocName] = $assocValue;
            } else {
                $data[$assocName] = array();
                foreach ($propertyValue as $value) {
                    $assocValue = $this->namingStrategy->modelName($value, $this->getManager()->getClassMetadata(get_class($value)));
                    $assocValue = $this->navigator->accept($this->getVisitor(), $assocValue, 'reference');
                    $data[$assocName][] = $assocValue;
                }
            }
        }

        return $data;
    }

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
        return $this->getManager()->getRepository($metadata->getName())->findAll();
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
