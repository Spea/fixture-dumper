<?php

namespace Sp\FixtureDumper\Generator;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Symfony\Component\Yaml\Dumper;
use Sp\FixtureDumper\Converter\YamlVisitor;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class YamlFixtureGenerator extends AbstractGenerator
{

    /**
     * {@inheritdoc}
     */
    public function createFilename(ClassMetadata $metadata, $multipleFiles = true)
    {
        if ($multipleFiles) {
            return lcfirst($this->namingStrategy->fixtureName($metadata) .'.yml');
        }

        return 'fixtures.yml';
    }

    /**
     * {@inheritdoc}
     */
    protected function doGenerate(ClassMetadata $metadata, array $models = null, array $options = array())
    {
        $result = array();
        foreach ($models as $model) {
            $data = array();
            foreach ($metadata->getFieldNames() as $fieldName) {
                if ($metadata->isIdentifier($fieldName)) {
                    continue;
                }

                $data[$fieldName] = $this->navigator->accept($this->getVisitor(), $this->readProperty($model, $fieldName));
            }

            foreach ($metadata->getAssociationNames() as $assocName) {
                if (!$metadata->isSingleValuedAssociation($assocName)) {
                    continue;
                }

                $propertyValue = $this->readProperty($model, $assocName);
                if (null !== $propertyValue) {
                    $data[$assocName] = '@'. $this->namingStrategy->modelName($propertyValue, $this->manager->getClassMetadata(get_class($propertyValue)));
                }
            }

            $result[$this->namingStrategy->modelName($model, $metadata)] = $data;
        }

        $yaml = new Yaml();

        return $yaml->dump(array($metadata->getName() => $result), 3);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultVisitor()
    {
        return new YamlVisitor();
    }
}
