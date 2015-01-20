<?php

namespace Lollypop\GearBundle\EventListener;

/**
 * Description of ResponseListener
 *
 * @author seshachalam
 */
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ResponseListener extends HelperListener {

    public function onKernelResponse(FilterResponseEvent $event) {
        $request = $event->getRequest();
        $response = $event->getResponse();

        $this->setEtagsAndStuff($request, $response);
        $event->setResponse($response);
    }

    //set ETags and other stuff
    private function setEtagsAndStuff(Request $request, Response $response) {
        if (($request->headers->has(parent::CACHE_BUST_KEY) || $response->headers->has(parent::CACHE_BUST_KEY)) && !$this->isJsonResponse($response)) {
            $response->setMaxAge(600);
            $response->setPublic();
            $hash = $this->getGitLatestCommitHash();
            if (!empty($hash)) {
                $response->setEtag(md5($hash));
            }
            $response->isNotModified($request);
        }

        if ($request->headers->has(parent::CACHE_BUST_KEY) === true) {
            $request->headers->remove(parent::CACHE_BUST_KEY);
        }

        if ($response->headers->has(parent::CACHE_BUST_KEY) === true) {
            $response->headers->remove(parent::CACHE_BUST_KEY);
        }
    }

}
