<?php

namespace Sp\FixtureDumper\Converter\Handler;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
interface HandlerSubscriberInterface
{
    /**
     * @return array
     */
    function getSubscribedMethods();
}
