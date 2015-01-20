<?php

namespace Lollypop\GearBundle\Services;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Pusher
 *
 * @author seshachalam
 */
use Psr\Log\LoggerInterface;
use CodeMonkeysRu\GCM;
use Lollypop\GearBundle\Entity\GCMRegID;
use Symfony\Bridge\Doctrine\RegistryInterface;

class Pusher implements PusherInterface {

    private $caBundleCrt;
    private $googleApiKey;
    private $logger;
    private $doctrine;

    public function __construct(RegistryInterface $doctrine, LoggerInterface $logger, $googleApiKey, $caBundleCrt) {
        $this->doctrine = $doctrine;
        $this->logger = $logger;
        $this->googleApiKey = $googleApiKey;
        $this->caBundleCrt = $caBundleCrt;
    }

    /**
     *
     * @param string $googleApiKey
     * @return \Lollypop\GearBundle\Services\Pusher
     */
    public function setGoogleApiKey($googleApiKey) {
        $this->googleApiKey = $googleApiKey;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getGoogleApiKey() {
        return $this->googleApiKey;
    }

    /**
     *
     * @param array $receivers
     * @param array $data
     * @param string $collapseKey
     * @param boolean $delayWhileIdle
     * @param int $ttl
     * @param string $packageName
     * @param boolean $dryRun
     * @return array
     * @throws \Exception
     */
    public function broadcast(array $receivers, $data, $collapseKey, $delayWhileIdle, $ttl, $packageName, $dryRun = false) {
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        $sender = new GCM\Sender($this->googleApiKey, false, $this->caBundleCrt);
        $message = new GCM\Message($receivers, $data);

        $message
                ->setCollapseKey($collapseKey)
                ->setDelayWhileIdle((Boolean)$delayWhileIdle)
                ->setTtl($ttl)
                ->setRestrictedPackageName($packageName)
                ->setDryRun($dryRun)
        ;

        try {
            $response = $sender->send($message);
            $returnArray = array();
            if ($response->getNewRegistrationIdsCount() > 0) {
                //Update $oldRegistrationId to $newRegistrationId in DB
                //TODO
                $returnArray[GCMRegID::NEW_REG_IDS] = $response->getNewRegistrationIds();
            }

            if ($response->getFailureCount() > 0) {
                //Remove $invalidRegistrationId from DB
                //TODO
                $returnArray[GCMRegID::INVALID_REG_IDS] = $response->getInvalidRegistrationIds();

                //Schedule to resend messages to unavailable devices
                $returnArray[GCMRegID::UNAVAILABLE_REG_IDS] = $response->getUnavailableRegistrationIds();
                //TODO
            }

            return $returnArray;
        } catch (GCM\Exception $e) {

            switch ($e->getCode()) {
                case GCM\Exception::ILLEGAL_API_KEY:
                case GCM\Exception::AUTHENTICATION_ERROR:
                case GCM\Exception::MALFORMED_REQUEST:
                case GCM\Exception::UNKNOWN_ERROR:
                case GCM\Exception::MALFORMED_RESPONSE:
                    //Deal with it
                    break;
            }

            throw new \Exception($e->getMessage());
        }
    }

}
