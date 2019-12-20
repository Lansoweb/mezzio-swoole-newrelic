<?php

declare(strict_types=1);

namespace Los\MezzioSwooleNewrelic;

use Psr\Container\ContainerInterface;
use Zend\Stratigility\Middleware\ErrorHandler;

class ErrorHandlerListenerDelegatorFactory
{
    public function __invoke(ContainerInterface $container, string $name, callable $callback, array $options = null)
    {
        /* @var ErrorHandler $errorHandler */
        $errorHandler = $callback();
        $errorHandler->attachListener(function($error) {
            newrelic_notice_error($error);
        });

        return $errorHandler;
    }
}
