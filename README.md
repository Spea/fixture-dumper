# Fixture Dumper Extension for Doctrine

This library aims to provide a simple way to dump fixtures for the Doctrine ORM/ODM.

**Note:** This library is still in an early stage.

[![Build Status](https://travis-ci.org/Spea/fixture-dumper.png?branch=master)](https://travis-ci.org/Spea/fixture-dumper)

## Installation ##

This is installable via [Composer](https://getcomposer.org/) as [sp/fixture-dumper](https://packagist.org/packages/sp/fixture-dumper).

## Usage ##

### Basic Usage ###

Examples:

```php
$manager = ...; // entity or document manager
$registry = new \Sp\FixtureDumper\Converter\Handler\HandlerRegistry();
$registry->addSubscribingHandler(new \Sp\FixtureDumper\Converter\Handler\DateHandler());

// for creating fixtures classes
$generator = new \Sp\FixtureDumper\Generator\ClassFixtureGenerator($manager);

// for creating yml files which can be loaded with the alice fixtures library
$ymlGenerator = new \Sp\FixtureDumper\Generator\Alice\YamlFixtureGenerator($manager);

// for creating array files which can be loaded with the alice fixtures library
$arrayGenerator = new \Sp\FixtureDumper\Generator\Alice\ArrayFixtureGenerator($manager);

$generatorMap = new \PhpCollection\Map(array('php' => $generator, 'yml' => $ymlGenerator, 'array' => $arrayGenerator);
$dumper = new \Sp\FixtureDumper\ORMDumper($manager, $registry, $generatorMap));
// or
$dumper = new \Sp\FixtureDumper\MongoDBDumper($manager, $registry, $generatorMap));
// $dumper->setDumpMultipleFiles(false);

// the second argument specifies the generator type you want to use
$dumper->dump('/your/workspace/src/Acme/DemoBundle/DataFixtures/ORM', 'array');
```

#### Options

`AbstractDumper#dump` accepts a third `$options` argument that is an array
with the following keys:

- namespace: The namespace for the generated class to use
  This options is only required when using the ClassFixtureGenerator

## License ##

Released under the MIT License, see LICENSE.
