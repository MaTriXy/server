<?php

namespace Lollypop\GearBundle\EventListener;

/**
 * Description of HelperListener
 *
 * @author seshachalam
 */
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\RuntimeException;

abstract class HelperListener {

    const CACHE_BUST_KEY = 'cache_bust_key';
    
    protected function isJsonRequest(Request $request) {
        return 'json' === $request->getContentType() || $this->isJson($request->getContent());
    }

    protected function isJsonResponse(Response $response) {
        return $this->isJson($response->getContent());
    }

    protected function getGitLatestCommitHash() {
        $process = new Process('git rev-parse --short HEAD');
        $process->run();
        if (!$process->isSuccessful()) {
            throw new RuntimeException($process->getErrorOutput());
        }
        $hash = $process->getOutput();

        return $hash;
    }

    private function isJson($content) {
        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }
        if ($data === null) {
            return false;
        }

        return true;
    }

}
