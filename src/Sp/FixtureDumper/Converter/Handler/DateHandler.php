<?php

/*
 * This file is part of the FixtureDumper library.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\FixtureDumper\Converter\Handler;

use Sp\FixtureDumper\Converter\VisitorInterface;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class DateHandler implements HandlerSubscriberInterface
{

    /**
     * {@inheritdoc}
     */
    public function getSubscribedMethods()
    {
        $methods = array();

        foreach (array('yml', 'php', 'array') as $format) {
            $methods[] = array(
                'format' => $format,
                'type' => 'DateTime',
                'method' => 'convertTo'. ucfirst($format)
            );
        }

        return $methods;
    }

    public function convertToYml(VisitorInterface $visitor, \DateTime $data)
    {
        return $data->format("Y-m-d H:i:s");
    }

    public function convertToPhp(VisitorInterface $visitor, \DateTime $data)
    {
        return sprintf("new \\DateTime('%s')", $data->format("Y-m-d H:i:s"));
    }

    public function convertToArray(VisitorInterface $visitor, \DateTime $data)
    {
        return $this->convertToPhp($visitor, $data);
    }
}
