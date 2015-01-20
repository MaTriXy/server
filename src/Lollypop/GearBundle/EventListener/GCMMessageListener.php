<?php

namespace Lollypop\GearBundle\EventListener;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GCMMessageListener
 *
 * @author seshachalam
 */
use Symfony\Bridge\Doctrine\RegistryInterface;
use Psr\Log\LoggerInterface;
use Lollypop\GearBundle\Services\MessageDealerInterface;
use Lollypop\GearBundle\Event\GCMCCSUpstreamInterface;
use Lollypop\GearBundle\Event\GCMCCSReceiptNotificationInterface;
use Lollypop\GearBundle\Event\GCMCCSResponseInterface;

class GCMMessageListener
{

    private $doctrine;
    private $logger;
    private $dealer;

    public function __construct(RegistryInterface $doctrine,
                                LoggerInterface $logger,
                                MessageDealerInterface $dealer)
    {
        $this->doctrine = $doctrine;
        $this->logger = $logger;
        $this->dealer = $dealer;
    }

    public function onGCMMsgReceived(GCMCCSUpstreamInterface $event)
    {   
        $this->logger->info('Hola: ' . 'Received event GCM_MSG_RECEIVED');
        $GCMMessage = $event->getGCMCCSUpstreamMessage();

        $this->dealer->deal($GCMMessage);
    }

    public function onGCMAckReceived(GCMCCSResponseInterface $event)
    {
        $GCMAckMessage = $event->getGCMCCSResponseMessage();

        //update the message status
    }

    public function onGCMNackReceived(GCMCCSResponseInterface $event)
    {
        $GCMNackMessage = $event->getGCMCCSResponseMessage();

        //update the message status

        switch($GCMNackMessage->getError()) {
            case 'BAD_ACK':
                $this->logger->info("Hola: The ACK message is improperly formed.");
                break;
            case 'BAD_REGISTRATION':
                $this->logger->info("Hola: The device has a registration ID, but it's invalid or expired.");
                break;
            case 'CONNECTION_DRAINING':
                $this->logger->info("Hola: The message couldn't be processed because the connection is draining. The message should be immediately retried over another connection.");
                break;
            case 'DEVICE_UNREGISTERED':
                $this->logger->info("Hola: The device is not registered.");
                break;
            case 'INTERNAL_SERVER_ERROR':
                $this->logger->info("Hola: The server encountered an error while trying to process the request.");
                break;
            case 'INVALID_JSON':
                $this->logger->info("Hola: The JSON message payload is not valid.");
                break;
            case 'QUOTA_EXCEEDED':
                $this->logger->info("Hola: The rate of messages to a particular registration ID (in other words, to a sender/device pair) is too high. If you want to retry the message, try using a slower rate.");
                break;
            case 'SERVICE_UNAVAILABLE':
                $this->logger->info("Hola: CCS is not currently able to process the message. The message should be retried over the same connection using exponential backoff with an initial delay of 1 second.");
                break;
            case 'BAD_ACK':
                $this->logger->info("Hola: The ACK message is improperly formed.");
                break;
            case 'AUTHENTICATION_FAILED':
                $this->logger->info("Hola: This is a 401 error indicating that there was an error authenticating the sender account.");
                break;
            case 'INVALID_TTL':
                $this->logger->info("Hola: There was an error in the supplied 'time to live' value.");
                break;
            case 'JSON_TYPE_ERROR':
                $this->logger->info("Hola: There was an error in the supplied JSON data type.");
                break;
            default :
                $this->logger->info("Hola:" . json_encode($GCMNackMessage));
                break;
        }
    }

    public function onGCMMsgPushed(GCMMessageInterface $event)
    {
        $GCMMessage = $event->getGCMCCSUpstreamMessage();
    }

    public function onGCMReceiptReceived(GCMCCSReceiptNotificationInterface $event)
    {
        $GCMReceiptMessage = $event->getGCMCCSReceiptNotificationMessage();
    }

    public function onGCMControlMessageReceived(GCMMessageInterface $event)
    {
        $GCMMessage = $event->getGCMCCSUpstreamMessage();
    }

}
