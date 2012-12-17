<?php

namespace Sp\FixtureDumper\Generator;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sp\FixtureDumper\Util\ClassUtils;
use CG\Generator\Writer;
use CG\Generator\PhpClass;
use CG\Core\DefaultGeneratorStrategy;
use CG\Generator\PhpMethod;
use CG\Generator\PhpParameter;
use Sp\FixtureDumper\Converter\PhpVisitor;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class ClassFixtureGenerator extends AbstractGenerator
{
    /**
     * {@inheritdoc}
     */
    public function createFilename(ClassMetadata $metadata, $multipleFiles = true)
    {
        if ($multipleFiles) {
            return $this->namingStrategy->fixtureName($metadata) .'.php';
        }

        return 'Fixtures.php';
    }

    public function prepareForWrite($fixture)
    {
        return "<?php\n\n". $fixture;
    }

    /**
     * {@inheritdoc}
     */
    protected function doGenerate(ClassMetadata $metadata, array $data, array $options = array())
    {
        static $docBlock;
        if (empty($docBlock)) {
            $writer = new Writer();
            $writer
                ->writeln('/**')
                ->writeln(' * This code was generated automatically by the FixtureDumper library, manual changes to it')
                ->writeln(' * may be lost upon next generation.')
                ->writeln(' */');

            $docBlock = $writer->getContent();
        }

        $fixtureClass = new PhpClass($options['namespace'] .'\\'. $this->namingStrategy->fixtureName($metadata));
        $fixtureClass->setDocblock($docBlock);
        $fixtureClass->setParentClassName('Doctrine\\Common\\DataFixtures\\AbstractFixture');
        $fixtureClass->addUseStatement($metadata->getName());

        if (count($metadata->getAssociationNames()) !== 0) {
            $this->addDependentFixtureInterface($fixtureClass, $metadata, $options);
        }

        $this->generateLoadMethod($fixtureClass, $metadata, $data);

        $generator = new DefaultGeneratorStrategy();
        $content = $generator->generate($fixtureClass);

        return $content;
    }

    protected function generateLoadMethod(PhpClass $class, ClassMetadata $metadata, array $data)
    {
        $writer = new Writer();
        $method = PhpMethod::create('load');
        $managerParameter = PhpParameter::create('manager');
        $managerParameter->setType('Doctrine\\Common\\Persistence\\ObjectManager');
        $method->addParameter($managerParameter);
        $class->setMethod($method);

        foreach ($data as $modelName => $modelData) {
            $this->generateModel($modelName, $modelData, $metadata, $writer);
            $writer->writeln("");
        }

        $writer->writeln('$manager->flush();');
        $method->setBody($writer->getContent());
    }

    protected function generateModel($modelName, $modelData, ClassMetadata $metadata, Writer $writer)
    {
        $writer->writeln(sprintf("$%s = new %s();", $modelName, ClassUtils::getClassName($metadata->getName())));
        foreach ($modelData['fields'] as $key => $value) {
            $setter = sprintf('set%s', ucfirst($key));
            $writer->writeln(sprintf("$%s->%s(%s);", $modelName, $setter, $value));
        }

        foreach ($modelData['associations'] as $assocName => $reference) {
            $setter = sprintf('set%s', ucfirst($assocName));
            $reference = sprintf("\$this->getReference('%s')", $reference);
            $writer->writeln(sprintf("$%s->%s(%s);", $modelName, $setter, $reference));
        }

        $writer->writeln(sprintf('$manager->persist($%s);', $modelName));
        $writer->writeln(sprintf('$this->addReference(\'%1$s\', $%1$s);', $modelName));
    }

    protected function addDependentFixtureInterface(PhpClass $class, ClassMetadata $metadata, array $options)
    {
        $class->addInterfaceName('Doctrine\\Common\\DataFixtures\\DependentFixtureInterface');
        $writer = new Writer();
        $method = PhpMethod::create('getDependencies');

        $writer->writeln("return array(");
        $assocCount = count($metadata->getAssociationNames());
        $associations = array();
        foreach ($metadata->getAssociationNames() as $assocName) {
            $targetClass = $metadata->getAssociationTargetClass($assocName);
            $associations[] = sprintf("'%s\\%s'", $options['namespace'], $this->namingStrategy->fixtureName($this->manager->getClassMetadata($targetClass)));
        }

        $writer->indent();
        $writer->writeln(implode(",\n", $associations));
        $writer->outdent();
        $writer->writeln(");");

        $method->setBody($writer->getContent());

        $class->setMethod($method);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultVisitor()
    {
        return new PhpVisitor();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultNamingStrategy()
    {
        return new ClassNamingStrategy();
    }

    /**
     * {@inheritdoc}
     */
    protected function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(array('namespace'));
    }
}
