<?php

namespace Lollypop\GearBundle\EventListener;

/**
 * Description of RequestListener
 *
 * @author seshachalam
 */
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RequestListener extends HelperListener {

    public function onKernelRequest(GetResponseEvent $event) {
        $request = $event->getRequest();
        $request->headers->set(parent::CACHE_BUST_KEY, true);

        if ($request->isMethod(Request::METHOD_GET) && !$request->isXmlHttpRequest() && !$this->isJsonRequest($request)) {
            $response = $this->checkEtagsAndStuff($request);

            if ($response !== null && $response instanceof Response) {
                $event->setResponse($response);
            }
        }
    }

    private function checkEtagsAndStuff(Request $request) {
        $hash = $this->getGitLatestCommitHash();
        if (!empty($hash)) {
            $response = new Response();
            $response->setMaxAge(600);
            $response->setPublic();
            $response->setETag(md5($hash));
            if ($response->isNotModified($request)) {
                return $response;
            }
        }

        return null;
    }

}
