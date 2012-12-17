<?php

namespace Sp\FixtureDumper\Converter\Alice;

use Sp\FixtureDumper\Converter\DefaultVisitor;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class YamlVisitor extends DefaultVisitor
{
    public function visitReference($reference)
    {
        return '@'. parent::visitReference($reference);
    }
}
