<?php

namespace Sp\FixtureDumper\Converter\Handler;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
use Sp\FixtureDumper\Converter\VisitorInterface;

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
