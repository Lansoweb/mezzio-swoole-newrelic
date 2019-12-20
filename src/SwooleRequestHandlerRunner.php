<?php

declare(strict_types=1);

namespace Los\MezzioSwooleNewrelic;

use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\Http\Response as SwooleHttpResponse;
use Zend\Expressive\Swoole\SwooleRequestHandlerRunner as ZendSwooleRequestHandlerRunner;

class SwooleRequestHandlerRunner extends ZendSwooleRequestHandlerRunner
{
    public function onRequest(SwooleHttpRequest $request, SwooleHttpResponse $response): void
    {
        if (ini_get('newrelic.license') === false) {
            throw new Exception\MissingLicense();
        }
        if (ini_get('newrelic.appname') === false && getenv('NEWRELIC_APPNAME') === false) {
            throw new Exception\MissingAppName();
        }

        $appName = getenv('NEWRELIC_APPNAME');
        if ($appName === false) {
            $appName = ini_get('newrelic.appname');
        }

        $transaction = new Transaction(
            $request,
            $appName,
            ini_get('newrelic.license')
        );
        $transaction->start();

        parent::onRequest($request, $response);

        $transaction->stop();
    }
}
