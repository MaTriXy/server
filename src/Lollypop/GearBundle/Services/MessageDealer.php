<?php

namespace Lollypop\GearBundle\Services;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MessageDealer
 *
 * @author seshachalam
 */
use Symfony\Bridge\Doctrine\RegistryInterface;
use Psr\Log\LoggerInterface;
use League\Geotools\Geotools;
use League\Geotools\Coordinate\Coordinate;
use Lollypop\GearBundle\Entity\User;
use Lollypop\GearBundle\Entity\Location;
use Lollypop\GearBundle\Entity\Board;
use Lollypop\GearBundle\Entity\Message;
use Lollypop\GearBundle\Entity\GCMRegID;
use Lollypop\GearBundle\Entity\Comment;
use FOS\UserBundle\Doctrine\UserManager;
use Lollypop\GearBundle\Services\ConnectionInterface;
use Lollypop\GearBundle\Entity\GCMCCSDownstreamMessage;
use Lollypop\GearBundle\Entity\GCMCCSUpstreamMessage;
use Mmoreram\GearmanBundle\Service\GearmanClient;

class MessageDealer implements MessageDealerInterface {

    private $userManager;
    private $redis;
    private $doctrine;
    private $logger;
    private $gearman;

    public function __construct(UserManager $userManager, ConnectionInterface $connection, RegistryInterface $doctrine, LoggerInterface $logger, GearmanClient $gearman) {
        $this->userManager = $userManager;
        $this->redis = $connection;
        $this->doctrine = $doctrine;
        $this->logger = $logger;
        $this->gearman = $gearman;
    }

    public function deal(GCMCCSUpstreamMessage $upstreamMessage) {
        $contentArr = $upstreamMessage->getData();
        if (!isset($contentArr['uuid'])) {
            return;
        }
        $random = $contentArr['uuid'];
        $this->logger->info("Hola " . $random);
//prepare ack data
        $GCMCCSDownstreamMessage = new GCMCCSDownstreamMessage($upstreamMessage->getFrom(), $random, array('m_id' => $upstreamMessage->getMessageId(), 's_id' => $random), 0, false, true);

        $from = $upstreamMessage->getFrom();
        $packageName = $upstreamMessage->getCategory();

        $em = $this->doctrine->getManager();
//insert into db
        $gcmRepo = $em->getRepository('LollypopGearBundle:GCMRegID');

        $gcmRegID = $gcmRepo->findOneBy(array('value' => $from));

        if (!isset($contentArr['t'])) {
            return true;
        }
        $this->logger->info("Hola " . $contentArr['t']);
        if ($gcmRegID) {
            $user = $gcmRegID->getUser();
            switch ($contentArr['t']) {
                case 'm':
//message post
                    $boardRepo = $em->getRepository('LollypopGearBundle:Board');
                    $location = $user->getCurrentLocation();
                    $boardHash = $location->getBoardHash();
                    $board = $boardRepo->findOneBy(array('name' => $boardHash));

                    if (!$board) {
                        $board = new Board();
                        $board->setLocation($location)
                                ->setName($boardHash);
                        $em->persist($board);
                    }
                    $relatedBoard = $gcmRegID->getBoard();
                    if (!$relatedBoard) {
                        $board->addGcmRegId($gcmRegID);
                        $gcmRegID->setBoard($board);
                    }

//message from mobile
                    if (!isset($contentArr['c'])) {
                        return;
                    }
                    $message = new Message();
                    $message->setContent($contentArr['c'])
                            ->setBoard($board)
                            ->setUri($random)->setGcmReg($gcmRegID)
                            ->setLocation($location)
                            ->setWieght(0);
                    $em->persist($message);
                    $em->flush();
                    $GCMCCSDownstreamMessage->setExtras(array('t' => 'm'));
                    $this->gearman->doBackgroundJob('LollypopGearBundleServicesGCMHTTPPusher~push_http', $random);
                    break;
                case 'l':
//location update
                    $this->logger->info('Hola: in regid and location');
                    if (!isset($contentArr['lt']) || !isset($contentArr['ln'])) {
                        return;
                    }
                    $latitude = $contentArr['lt'];
                    $longitude = $contentArr['ln'];
                    $address = $contentArr['a'];
                    $location = new Location();
                    $geoTools = new Geotools();
                    $previousLocation = $user->getCurrentLocation();
                    $coordinate = new Coordinate(array($latitude, $longitude));
                    $encoded = $geoTools->geohash()->encode($coordinate);
                    $hash = $encoded->getGeohash();

                    $location->setGeoHash($hash)
                            ->setAddress($address)
                            ->setLatitude($latitude)
                            ->setLongitude($longitude);
                    $em->persist($location);

                    $boardHash = $location->getBoardHash();

                    $boardRepo = $em->getRepository('LollypopGearBundle:Board');
                    $board = $boardRepo->findOneBy(array('name' => $boardHash));
                    if (!$board) {
                        $board = new Board();
                        $board->setLocation($location)
                                ->setName($boardHash)
                                ->addGcmRegId($gcmRegID);
                        $em->persist($board);
                    }

                    $board->addGcmRegId($gcmRegID);
                    $gcmRegID->setBoard($board);

//                    if ($previousLocation) {
//                        $previousCoordinate = new Coordinate(array($previousLocation->getLatitude(),
//                            $previousLocation->getLongitude()));
//                        $distance = $geoTools->distance()->setFrom($previousCoordinate)->setTo($coordinate);
//                        if ($distance->in('km')->haversine() > 2) {
//                            $user->setCurrentLocation($location)
//                                    ->addLocation($location);
//                            $em->persist($user);
//                        }
//                    } else {
//                        $user->setCurrentLocation($location)
//                                ->addLocation($location);
//                        $em->persist($user);
//                    }

                    $user->setCurrentLocation($location)
                            ->addLocation($location);
                    $em->persist($user);
                    $em->flush();
                    $GCMCCSDownstreamMessage->setExtras(array('t' => 'l'));
                    break;
                case 'a':
//active message
                    $user->setActive(true)
                            ->setActiveAt(new \DateTime());
                    $em->flush();
                    $GCMCCSDownstreamMessage->setExtras(array('t' => 'a'));
                    break;
                case 'lk':
//like of a message
//update the weight of message
                    $mId = $contentArr['mid'];
                    $GCMCCSDownstreamMessage->setExtras(array('t' => 'lk'));
                    break;
                case 'c':
                    if (!isset($contentArr['mid']) || !isset($contentArr['c'])) {
                        return;
                    }

                    $mId = $contentArr['mid'];
                    $content = $contentArr['c'];
                    $location = $user->getCurrentLocation();

                    $messageRepo = $em->getRepository('LollypopGearBundle:Message');
                    $this->logger->info('Hola ' . $mId);
                    $message = $messageRepo->findOneBy(array('uri' => trim($mId)));
                    if ($message) {
                        $this->logger->info('Hola creating a comment');
                        $comment = new Comment();
                        $comment->setMessage($message)->setContent($content)->setUri($random)->setWieght(0)->setLocation($location);
                        $em->persist($comment);
                        $message->addComment($comment);
                    }
                    $em->flush();
                    $this->logger->info('Hola after a comment');
                    $GCMCCSDownstreamMessage->setExtras(array('t' => 'c'));
                    break;
                case 'r':
//registration
                    $firstName = ""; //$contentArr['fn'];
                    $lastName = ""; //$contentArr['ln'];
                    if (!isset($contentArr['em'])) {
                        return;
                    }
                    $email = $contentArr['em'];
                    $userRepo = $em->getRepository('LollypopGearBundle:User');
                    $user = $userRepo->findOneBy(array('email' => $email));

                    if ($user) {
                        $gcmRegIds = $user->getGcmRegIds();
                        $checkArr = array();
                        foreach ($gcmRegIds as $RegId) {
                            $temp = $RegId->getValue();
                            if ($temp !== $from) {
                                $checkArr[] = $temp;
                            }
                        }
                    } else {
                        $newUser = $this->createNewUser($email, $firstName, $lastName);
                        $gcmRegID->setUser($newUser);
                        $newUser->addGcmRegId($gcmRegID);
                        $em->persist($newUser);
                    }
                    $em->flush();
                    $GCMCCSDownstreamMessage->setExtras(array('t' => 'r', 'o' => 'Thank you for installing. Please be grateful to your place'));
                    break;

                default :
                    break;
            }
        } else {
//unregistered user so register
            $this->logger->info(json_encode($contentArr));
            switch ($contentArr['t']) {
                case 'r':
                    $firstName = ""; //$contentArr['fn'];
                    $lastName = ""; //$contentArr['ln'];
                    if (!isset($contentArr['em'])) {
                        return;
                    }
                    $email = $contentArr['em'];

                    $userRepo = $em->getRepository('LollypopGearBundle:User');
                    $user = $userRepo->findOneBy(array('email' => $email));

                    $gcmReg = new GCMRegID();
                    $gcmReg->setValue($from);
                    if ($user) {
                        $gcmReg->setUser($user);
                        $em->persist($gcmReg);
                        $user->addGcmRegId($gcmReg);
                        $em->persist($user);
                        $gcmRegIds = $user->getGcmRegIds();
                        $checkArr = array();
                        foreach ($gcmRegIds as $RegId) {
                            $checkArr[] = $RegId->getValue();
                        }

                        if (($key = array_search($from, $checkArr)) !== false) {
                            unset($checkArr[$key]);
                        }
                    } else {
                        $newUser = $this->createNewUser($email, $firstName, $lastName);
                        $em->persist($newUser);

                        $gcmReg->setUser($newUser);
                        $em->persist($gcmReg);

                        $newUser->addGcmRegId($gcmReg);
                        $em->persist($newUser);
                    }
                    $em->flush();
                    $GCMCCSDownstreamMessage->setExtras(array('t' => 'r', 'o' => 'Thank you for installing. Please be grateful to your place'));
                    break;
                default :
                    $this->logger->info('in no regid');
                    break;
            }
        }

        $ackData = json_encode($GCMCCSDownstreamMessage, JSON_PRETTY_PRINT);

        $this->logger->info('Hola: ' . $ackData);
        $this->redis->connect()->publish($upstreamMessage->getChannel(), $ackData);
//send ack message
        $this->logger->info('Hola: ' . 'Published to redis ' . $upstreamMessage->getChannel());

        $this->logger->info('Hola end of dealer');
    }

    private function createNewUser($email, $firstName, $lastName) {
        
        $newUser = $this->userManager->createUser();
        $newUser->setActive(true)
                ->setActiveAt(new \DateTime())
                ->setUserName($email)
                ->setEmail($email)
                ->setFirstName($firstName)
                ->setLastName($lastName)
                ->setIsPasswordSet(false)
                ->addRole('ROLE_USER') //need to check with rams
                ->setEnabled(true);
        $this->userManager->updateUser($newUser, false);
        
        return $newUser;
    }

}
