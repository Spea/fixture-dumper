<?php

/*
 * This file is part of the FixtureDumper library.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\FixtureDumper\Mapping\MongoDB;

use InvalidArgumentException;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use \Doctrine\ODM\MongoDB\Mapping\ClassMetadata as BaseClassMetadata;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class ClassMetadataProxy implements ClassMetadata
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var array
     */
    public $origFieldMappings;

    /**
     * @var \Doctrine\ODM\MongoDB\Mapping\ClassMetadata
     */
    protected $classMetadata;

    /**
     * @var array
     */
    protected $fieldMappings = array();

    /**
     * @var bool
     */
    protected $initialized = false;

    /**
     * @var array
     */
    protected $associationMappings = array();

    /**
     * @param \Doctrine\ODM\MongoDB\Mapping\ClassMetadata $classMetadata
     */
    public function __construct(BaseClassMetadata $classMetadata)
    {
        $this->name = $classMetadata->name;
        $this->origFieldMappings = $classMetadata->fieldMappings;
        $this->classMetadata = $classMetadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssociationNames()
    {
        $this->initMappings();

        return array_keys($this->associationMappings);
    }

    /**
     * {@inheritdoc}
     */
    public function getAssociationTargetClass($assocName)
    {
        $this->initMappings();

        if ( ! isset($this->associationMappings[$assocName])) {
            throw new InvalidArgumentException("Association name expected, '" . $assocName ."' is not an association.");
        }

        return $this->associationMappings[$assocName]['targetDocument'];
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldNames()
    {
        $this->initMappings();

        return array_keys($this->fieldMappings);
    }

    /**
     * Initialize the mappings from the class metadata.
     * The mapping will be split in field mappings and association mappings.
     *
     * @return \Sp\FixtureDumper\Mapping\MongoDB\ClassMetadataProxy
     */
    protected function initMappings()
    {
        if ($this->initialized) {
            return;
        }

        foreach ($this->classMetadata->fieldMappings as $key => $mapping) {
            if (isset($mapping['reference'])) {
                $this->associationMappings[$key] = $mapping;
            } else {
                $this->fieldMappings[$key] = $mapping;
            }
        }

        $this->initialized = true;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->classMetadata->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->classMetadata->getIdentifier();
    }

    /**
     * {@inheritdoc}
     */
    public function getReflectionClass()
    {
        return $this->classMetadata->getReflectionClass();
    }

    /**
     * {@inheritdoc}
     */
    public function isIdentifier($fieldName)
    {
        return $this->classMetadata->isIdentifier($fieldName);
    }

    /**
     * {@inheritdoc}
     */
    public function hasField($fieldName)
    {
        return $this->classMetadata->hasField($fieldName);
    }

    /**
     * {@inheritdoc}
     */
    public function hasAssociation($fieldName)
    {
        return $this->classMetadata->hasAssociation($fieldName);
    }

    /**
     * {@inheritdoc}
     */
    public function isSingleValuedAssociation($fieldName)
    {
        return $this->classMetadata->isSingleValuedAssociation($fieldName);
    }

    /**
     * {@inheritdoc}
     */
    public function isCollectionValuedAssociation($fieldName)
    {
        return $this->classMetadata->isCollectionValuedAssociation($fieldName);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifierFieldNames()
    {
        return $this->classMetadata->getIdentifierFieldNames();
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeOfField($fieldName)
    {
        return $this->classMetadata->getTypeOfField($fieldName);
    }

    /**
     * {@inheritdoc}
     */
    public function isAssociationInverseSide($assocName)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssociationMappedByTargetField($assocName)
    {
        return $this->classMetadata->getAssociationMappedByTargetField($assocName);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifierValues($object)
    {
        return $this->classMetadata->getIdentifierValues($object);
    }

}
