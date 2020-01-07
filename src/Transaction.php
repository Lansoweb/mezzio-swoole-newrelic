<?php

declare(strict_types=1);

namespace Los\MezzioSwooleNewrelic;

use Swoole\Http\Request;

class Transaction
{
    /** @var Request */
    protected $request;

    /** @var string */
    protected $appName;

    /** @var string */
    protected $license;

    public function __construct(Request $request, string $appName, string $license)
    {
        $this->request = $request;
        $this->appName = $appName;
        $this->license = $license;
    }

    public function start() : void
    {
        if (! extension_loaded('newrelic')) {
            return;
        }

        $snapshot = $this->fillGlobalVars($this->request);
        try {
            newrelic_start_transaction($this->appName, $this->license);
            newrelic_background_job(false);
        } finally {
            $this->restoreGlobalVars($snapshot);
        }
    }

    public function stop() : void
    {
        if (! extension_loaded('newrelic')) {
            return;
        }

        newrelic_end_transaction();
    }

    protected function fillGlobalVars(Request $request) : array
    {
        $snapshot   = [$_SERVER, $_GET, $_POST, $_COOKIE, $_FILES, $_REQUEST];
        $_SERVER    = $this->extractServerVars($request);
        $_GET       = (array) $request->get;
        $_POST      = (array) $request->post;
        $_COOKIE    = (array) $request->cookie;
        $_FILES     = (array) $request->files;
        $_REQUEST   = $_COOKIE + $_POST + $_GET;

        return $snapshot;
    }

    protected function restoreGlobalVars(array $snapshot)
    {
        list($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES, $_REQUEST) = $snapshot;
    }

    protected function extractServerVars(Request $request) : array
    {
        $result = [];
        foreach ((array) $request->server as $key => $value) {
            $key = strtoupper($key);
            $result[$key] = $value;
        }
        foreach ((array) $request->header as $key => $value) {
            $key = strtoupper($key);
            $key = strtr($key, '-', '_');
            $key = 'HTTP_' . $key;
            $result[$key] = $value;
        }

        return $result;
    }
}
