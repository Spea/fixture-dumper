<?php

/*
 * This file is part of the FixtureDumper library.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\FixtureDumper\Converter\Alice;

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
