<?php

namespace Sp\FixtureDumper\Generator;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
abstract class NelmioGenerator extends AbstractGenerator
{
    /**
     * {@inheritdoc}
     */
    protected function doGenerate(ClassMetadata $metadata, array $models = null, array $options = array())
    {
        $result = array();
        foreach ($models as $model) {
            $data = $this->processFieldNames($metadata, $model);
            $data = array_merge($data, $this->processAssociationNames($metadata, $model));

            $result[$this->namingStrategy->modelName($model, $metadata)] = $data;
        }

        return $this->prepareData($metadata, $result);
    }

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

    protected function processAssociationNames(ClassMetadata $metadata, $model)
    {
        $data = array();
        foreach ($metadata->getAssociationNames() as $assocName) {
            $propertyValue = $this->readProperty($model, $assocName);
            if (null === $propertyValue || $metadata->isAssociationInverseSide($assocName)) {
                continue;
            }

            if ($metadata->isSingleValuedAssociation($assocName)) {
                $data[$assocName] = '@'. $this->namingStrategy->modelName($propertyValue, $this->manager->getClassMetadata(get_class($propertyValue)));
            } else {
                $data[$assocName] = array();
                foreach ($propertyValue as $value) {
                    $data[$assocName][] = '@'. $this->namingStrategy->modelName($value, $this->manager->getClassMetadata(get_class($value)));
                }
            }
        }

        return $data;
    }

    abstract protected function prepareData(ClassMetadata $metadata, array $data);
}
