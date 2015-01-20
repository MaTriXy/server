<?php

namespace Lollypop\GearBundle\Services;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GCMHTTPPusher
 *
 * @author seshachalam
 */
use Mmoreram\GearmanBundle\Driver\Gearman;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Psr\Log\LoggerInterface;
use Lollypop\GearBundle\Services\PusherInterface;
use Lollypop\GearBundle\GearEvents;
use Lollypop\GearBundle\Entity\GCMRegID;

/**
 * @Gearman\Work(
 *     name = "GCMHTTPPusher",
 *     service="gcm.http_message.pusher",
 *     description = "Pushes a message to gcm servers via http")
 */
class GCMHTTPPusher {

    private $dispatcher;
    private $doctrine;
    private $logger;
    private $pusher;

    public function __construct(EventDispatcherInterface $dispatcher, RegistryInterface $doctrine, LoggerInterface $logger, PusherInterface $pusher) {
        $this->dispatcher = $dispatcher;
        $this->doctrine = $doctrine;
        $this->logger = $logger;
        $this->pusher = $pusher;
    }

    /**
     * method to run as a job
     *
     * @param \GearmanJob $job Object with job parameters
     *
     * @return boolean
     *
     * @Gearman\Job(
     *     name = "push_http",
     *     description = "This pushes messages to gcm via http")
     */
    public function pushViaHttp(\GearmanJob $job) {
        $random = $job->workload();
        //do update the leaderboard
        //broadcast this to all the users in current message's user's location
        $em = $this->doctrine->getManager();
        $messageRepo = $em->getRepository('LollypopGearBundle:Message');
        $message = $messageRepo->findOneBy(array('uri' => $random));
        $board = $message->getBoard();
        
        $userRegId = $message->getGcmReg()->getId();
        $gcmRegIDRepo = $em->getRepository('LollypopGearBundle:GCMRegID');

        $page = 1;
        while (true) {
            $registrationIds = [];
            $gcmRegIDs = $gcmRegIDRepo->getRegIdsOfBoard($board, [$userRegId], $page);
            if (empty($gcmRegIDs)) {
                break;
            }
            foreach ($gcmRegIDs as $regId) {
                $registrationIds[] = $regId['value'];
            }
            //get receipents from the message and push using pusher
            $result = $this->pusher->broadcast($registrationIds, array('t' => 'm', 's_id' => $message->getUri(), 'l' => $message->getGcmReg()->getUser()->getCurrentLocation()->getGeoHash(), 'c' => $message->getContent()), 'message', true, 0, 'com.abbiya.broadr');
            //parse through the result and update gcm reg ids according to the pusher service
            if (!empty($result)) {
                $em = $this->doctrine->getManager();
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
                        $this->logger->info('Hola: invalid ' . $invalidRegId);
                        $regIdObj = $GCMRegIDRepo->findOneBy(array('value' => trim($invalidRegId)));
                        if ($regIdObj) {
                            $messages = $messageRepo->findBy(array('gcm_reg' => $regIdObj));
                            foreach ($messages as $message) {
                                $message->setGcmReg(null);
                            }
                            $em->flush();
                            $em->remove($regIdObj);
                        }
                    }
                }

                if (isset($result[GCMRegID::UNAVAILABLE_REG_IDS])) {
                    //Schedule to resend messages to unavailable devices
                    $unavailableIds = (array) $result[GCMRegID::UNAVAILABLE_REG_IDS];
                    foreach ($unavailableIds as $regId) {
                        $this->logger->info('Hola: unavailable ' . $regId);
                    }
                }

                $em->flush();
            }
            $page++;
        }
    }

}
