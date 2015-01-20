<?php

namespace Lollypop\GearBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Lollypop\GearBundle\Form\Type\PushType;
use Lollypop\GearBundle\Entity\GCMRegID;

class DefaultController extends Controller {

    public function indexAction(Request $request) {
        return $this->render('LollypopGearBundle:Default:index.html.twig');
    }

    public function getMessagesAction(Request $request, $hash) {
        $kcy = $this->get('hashids');
        $numbers = $kcy->decode($hash);
        $responseArray = [];
        if (!empty($numbers)) {
            $messageId = $numbers[0];
            $em = $this->getDoctrine()->getManager();
            $messageRepo = $em->getRepository('LollypopGearBundle:Message');
            $message = $messageRepo->find($messageId);
            $comms = array();
            if ($message) {
                $commentRepo = $em->getRepository('LollypopGearBundle:Comment');
                $paginatedCommentsQuery = $commentRepo->getCommentsOf($message, false);

                $paginator = $this->get('knp_paginator');
                $pageNum = $request->query->get('page', 1);
                $resultsPerPage = 10;
                $pagination = $paginator->paginate($paginatedCommentsQuery, $pageNum, $resultsPerPage);

                $responseArray = array('error' => false, 'message' => $message, 'pagination' => $pagination);
            }
        } else {
            $responseArray = array('error' => true);
        }

        if ($responseArray['error'] == true) {
            throw new \RuntimeException('No message found');
        }

        return $this->render('LollypopGearBundle:Default:getMessages.html.twig', array('content' => $responseArray));
    }

    public function pushAction(Request $request) {
        $form = $this->createForm(new PushType());
        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $data = $form->getData();
            $to = $data['to'];

            $boardRepo = $em->getRepository('LollypopGearBundle:Board');
            $board = $boardRepo->findOneBy(array('name' => $data['board']));
            $gcmRegIDRepo = $em->getRepository('LollypopGearBundle:GCMRegID');
            $excludeIds = $data['exclude'];
            if (empty($excludeIds)) {
                $excludeIds = [];
            } else {
                $excludeIds = explode(',', $excludeIds);
            }

            $pusher = $this->get('pusher');
            $page = 1;
            while (true) {
                $registrationIds = [];
                if (empty($board)) {
                    $registrationIds = explode(',', $to);
                } else {
                    $gcmRegIDs = $gcmRegIDRepo->getRegIdsOfBoard($board, $excludeIds, $page);
                    if (empty($gcmRegIDs)) {
                        break;
                    }
                    foreach ($gcmRegIDs as $regId) {
                        $registrationIds[] = $regId['value'];
                    }
                }

                $result = $pusher->broadcast($registrationIds, $data['data'], $data['collapse_key'], $data['delay_while_idle'], $data['ttl'], $data['package']);

                //parse through the result and update gcm reg ids according to the pusher service
                if (!empty($result)) {
                    $em = $this->getDoctrine()->getManager();
                    $GCMRegIDRepo = $em->getRepository('LollypopGearBundle:GCMRegID');
                    if (isset($result[GCMRegID::NEW_REG_IDS])) {
                        //Update $oldRegistrationId to $newRegistrationId in DB
                        $newRegIds = (array) $result[GCMRegID::NEW_REG_IDS];
                        foreach ($newRegIds as $oldKey => $newKey) {
                            $oldRegIdObj = $GCMRegIDRepo->findOneBy(array('value' => trim($oldKey)));
                            $newRegIdObj = $GCMRegIDRepo->findOneBy(array('value' => trim($newKey)));

                            if ($oldRegIdObj && $newRegIdObj) {
                                $messageRepo = $em->getRepository('LollypopGearBundle:Message');
                                $messages = $messageRepo->findBy(array('gcm_reg' => $oldRegIdObj));
                                foreach ($messages as $message) {
                                    $message->setGcmReg($newRegIdObj);
                                }
                                $em->flush();
                                $em->remove($oldRegIdObj);
                            }
                        }
                    }

                    if (isset($result[GCMRegID::INVALID_REG_IDS])) {
                        //Remove $invalidRegistrationId from DB
                        $invalidRegIds = (array) $result[GCMRegID::INVALID_REG_IDS];
                        foreach ($invalidRegIds as $invalidRegId) {
                            $regIdObj = $GCMRegIDRepo->findOneBy(array('value' => trim($invalidRegId)));
                            if ($regIdObj) {
                                $em->remove($regIdObj);
                            }
                        }
                    }

                    if (isset($result[GCMRegID::UNAVAILABLE_REG_IDS])) {
                        //Schedule to resend messages to unavailable devices
                        $unavailableIds = (array) $result[GCMRegID::UNAVAILABLE_REG_IDS];
                    }

                    $em->flush();
                }
                $page++;
                if (!empty($to)) {
                    break;
                }
            }

            return new JsonResponse($result);
        }
        // Render form to capture values needed for Merchant API testing
        return $this->render('LollypopGearBundle:Default:push.html.twig', array('form' => $form->createView()));
    }

    public function searchMessagesAction(Request $request, $board) {
        $em = $this->getDoctrine()->getManager();
        $boardRepo = $em->getRepository('LollypopGearBundle:Board');
        $board = $boardRepo->findOneBy(array('name' => $board));

        $query = $request->query->get('q');

        $messageRepo = $em->getRepository('LollypopGearBundle:Message');
        $messages = $messageRepo->getMessagesOfBoard($query, $board);

        ladybug_dump_die($messages);
    }

}
