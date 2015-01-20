<?php

namespace Lollypop\GearBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Lollypop\GearBundle\Entity\Message;
use GuzzleHttp\Client;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiController
 *
 * @author seshachalam
 */
class ApiController extends Controller {

    public function getCommentsAction(Request $request, $messageUri) {
        $em = $this->getDoctrine()->getManager();
        $messageRepo = $em->getRepository('LollypopGearBundle:Message');
        $message = $messageRepo->findOneBy(array('uri' => $messageUri));

        if ($message) {
            $comments = $message->getComments();
            $kcy = $this->get('hashids');
            $hash = $kcy->encode($message->getId());
            if (!empty($comments)) {
                return new JsonResponse(array('error' => false, 'message' => $message->getContent(), 'address' => $message->getLocation() === null ? null : $message->getLocation()->getAddress(), 'comments' => $comments->toArray(), 'link' => $this->getShortUrl($this->generateUrl('lollypop_gear_messages', array('hash' => $hash), UrlGeneratorInterface::ABSOLUTE_URL))));
            }
        } else {
            return new JsonResponse(array('error' => true, 'message' => 'No message found', 'comments' => array()));
        }
        return new JsonResponse(array('error' => true, 'message' => 'No comments yet!!!', 'comments' => array()));
    }

    public function getCommentsDeltaAction(Request $request, $messageUri, $lastCommentUri = 0, $direction = 0) {
        //direction false is down / true is up
        $em = $this->getDoctrine()->getManager();
        $messageRepo = $em->getRepository('LollypopGearBundle:Message');
        $message = $messageRepo->findOneBy(array('uri' => $messageUri));

        $lastComment = null;
        $commentRepo = $em->getRepository('LollypopGearBundle:Comment');
        if ($lastCommentUri !== 0) {
            $lastComment = $commentRepo->findOneBy(array('uri' => $lastCommentUri));
        }

        if ($message) {
            $comments = $commentRepo->getCommentsAfter($message, $lastComment, $direction);
            $kcy = $this->get('hashids');
            $hash = $kcy->encode($message->getId());
            if (!empty($comments)) {
                return new JsonResponse(array('error' => false, 'message' => $message->getContent(), 'address' => $message->getLocation() === null ? null : $message->getLocation()->getAddress(), 'comments' => $comments, 'link' => $this->getShortUrl($this->generateUrl('lollypop_gear_messages', array('hash' => $hash), UrlGeneratorInterface::ABSOLUTE_URL))));
            }
        } else {
            return new JsonResponse(array('error' => true, 'message' => 'No message found', 'comments' => array()));
        }
        if ((bool) $direction) {
            return new JsonResponse(array('error' => true, 'message' => 'No new comments yet!!!', 'end' => false, 'comments' => array(), 'link' => $this->getShortUrl($this->generateUrl('lollypop_gear_messages', array('hash' => $hash), UrlGeneratorInterface::ABSOLUTE_URL))));
        } else {
            return new JsonResponse(array('error' => true, 'message' => 'No more comments !!!', 'end' => true, 'comments' => array(), 'link' => $this->getShortUrl($this->generateUrl('lollypop_gear_messages', array('hash' => $hash), UrlGeneratorInterface::ABSOLUTE_URL))));
        }
    }

    public function getMessagesAction(Request $request, $board) {
        $em = $this->getDoctrine()->getManager();
        if (empty($board)) {
            $registrationId = $request->headers->get('X-Auth');
            $gcmRegRepo = $em->getRepository('LollypopGearBundle:GCMRegID');
            $gcmReg = $gcmRegRepo->findOneBy(array('value' => $registrationId));
            $board = $gcmReg->getBoard();
        } else {
            $boardRepo = $em->getRepository('LollypopGearBundle:Board');
            $board = $boardRepo->findOneBy(array('name' => $board));
        }

        if ($board) {
            $messages = $board->getMessages();

            return new JsonResponse(array('error' => false, 'messages' => $messages->toArray()));
        }
        return new JsonResponse(array('error' => true, 'message' => 'No messages yet!!!', 'messages' => array()));
    }

    public function getMessagesDeltaAction(Request $request, $board, $lastMessageUri = 0, $direction = 0) {
        //direction false is down / true is up
        $em = $this->getDoctrine()->getManager();
        if (empty($board)) {
            $registrationId = $request->headers->get('X-Auth');
            $gcmRegRepo = $em->getRepository('LollypopGearBundle:GCMRegID');
            $gcmReg = $gcmRegRepo->findOneBy(array('value' => $registrationId));
            $board = $gcmReg->getBoard();
        } else {
            $board = substr($board, 0, 4);
            $boardRepo = $em->getRepository('LollypopGearBundle:Board');
            $board = $boardRepo->findOneBy(array('name' => $board));
        }
        $lastMessage = null;
        $messageRepo = $em->getRepository('LollypopGearBundle:Message');
        if ($lastMessageUri !== 0) {
            $lastMessage = $messageRepo->findOneBy(array('uri' => $lastMessageUri));
        }

        if ($board) {
            //get the messages after the lastmessage from board
            $messages = $messageRepo->getMessagesAfter($board, $lastMessage, $direction);

            if (!empty($messages)) {
                return new JsonResponse(array('error' => false, 'messages' => $messages));
            }
        }
        if ((bool) $direction) {
            return new JsonResponse(array('error' => true, 'message' => 'No new messages yet!!!', 'end' => false, 'messages' => array()));
        } else {
            return new JsonResponse(array('error' => true, 'message' => 'No more messages!!!', 'end' => true, 'messages' => array()));
        }
    }

    public function saveMessagesIfNotExistsAction(Request $request) {
        $content = json_decode($request->getContent(), true);

        $messageContent = $content['m'];

        $uuids = $messageContent['uuids'];

        $em = $this->getDoctrine()->getManager();
        $messageRepo = $em->getRepository('LollypopGearBundle:Message');
        //find non existant messages
        $existingMessages = $messageRepo->findNotInMessages($uuids);
        $newUuids = array_diff($uuids, $existingMessages);

        foreach ($newUuids as $newUuid) {
            $message = new Message();
            //$message->setUri($newUuid)->setContent($messageContent[$newUuid]['c'])->setCreatedAt(new \DateTime())->set
        }

        $message = $messageRepo->findOneBy(array('uri' => $messageUri));
        if ($message === null) {
            $em->$message;
        }
    }

    public function saveCommentsIfNotExistsAction(Request $request) {
        $content = $request->getContent();
        $messageUri = $content[''];
        $em = $this->getDoctrine()->getManager();
        $messageRepo = $em->getRepository('LollypopGearBundle:Message');
        $message = $messageRepo->findOneBy(array('uri' => $messageUri));
        if ($message === null) {
            $em->$message;
        }
    }

    private function getShortUrl($longUrl) {
        $content = json_encode(['longUrl' => $longUrl]);
        $client = new Client();
        $request = $client->createRequest('POST', $this->container->getParameter('google_url_shortener_endpoint'), ['body' => $content]);
        $request->setHeader('Content-Type', 'application/json');
        $query = $request->getQuery();
        $query->set('key', $this->container->getParameter('google_api_key'));

        $response = $client->send($request);

        if (200 == $response->getStatusCode()) {
            $body = $response->getBody();
            $body = json_decode($body, true);

            if ($body !== null) {
                if (isset($body['id'])) {
                    return $body['id'];
                }
            }
        }
        return $longUrl;
    }

}
