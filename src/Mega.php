<?php

namespace JM\Mega;

use Symfony\Component\Process\Process;
use Exception;

class Mega
{
    private function exec(array $command, $timeout = 60)
    {
        return (new Process($command))
            ->setTimeout($timeout)
            ->mustRun();
    }

    public function loginUsingUrl($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
            throw new Exception("Invalid URL provided, please provide a valid Mega URL.");
        }

        $this->logout();
        $this->exec([
            'mega-login',
            $url
        ]);

        return $this;
    }

    public function logout()
    {
        $this->exec([
            'mega-logout'
        ]);
    }

    public function download($remote_path, $local_path, $timeout = 3600)
    {
        $this->exec([
            'mega-get',
            $remote_path,
            $local_path
        ], $timeout);
    }

    public function getTotalSize()
    {
        $output = $this->exec(['mega-du'])
            ->getOutput();

        $output = explode('Total storage used:', $output)[1];
        $output = rtrim($output);
        $output = ltrim($output);
        $output = explode(' ', $output)[0];

        return (int) $output;
    }
}