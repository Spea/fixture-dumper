<?php

namespace Sp\FixtureDumper\Converter\Nelmio;

use Sp\FixtureDumper\Converter\PhpVisitor;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class ArrayVisitor extends PhpVisitor
{
    public function visitReference($reference)
    {
        return "'@". parent::visitReference($reference) ."'";
    }
}
