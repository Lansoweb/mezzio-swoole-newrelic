<?php

declare(strict_types=1);

namespace Los\MezzioSwooleNewrelic;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Router\RouteResult;

class NewRelicMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $this->detectTransactionName($request);

        return $handler->handle($request);
    }

    private function detectTransactionName(Request $request) : void
    {
        /** @var RouteResult $routeResult */
        $routeResult = $request->getAttribute(RouteResult::class);
        if (! $routeResult || ! $routeResult->getMatchedRoute()) {
            newrelic_name_transaction($request->getUri()->getPath() ? : '');
            return;
        }

        newrelic_name_transaction($routeResult->getMatchedRoute()->getPath());
    }
}
