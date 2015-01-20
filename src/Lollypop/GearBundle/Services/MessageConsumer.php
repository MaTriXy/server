<?php

namespace Lollypop\GearBundle\Services;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Mmoreram\GearmanBundle\Driver\Gearman;
use Lollypop\GearBundle\GearEvents;
use Lollypop\GearBundle\Entity\GCMCCSUpstreamMessage;
use Lollypop\GearBundle\Entity\GCMCCSDownstreamMessage;
use Lollypop\GearBundle\Entity\GCMCCSResponseMessage;
use Lollypop\GearBundle\Entity\GCMCCSReceiptNotificationMessage;
use Lollypop\GearBundle\Event\GCMCCSUpstreamEvent;
use Lollypop\GearBundle\Event\GCMCCSDownstreamEvent;
use Lollypop\GearBundle\Event\GCMCCSReponseEvent;
use Lollypop\GearBundle\Event\GCMCCSReceiptNotificationEvent;

/**
 * @Gearman\Work(
 *     name = "MessageConsumer",
 *     service="message.consumer",
 *     description = "Consumes a message from ccs")
 */
class MessageConsumer {

    private $dispatcher;
    private $logger;
    private $channel;

    public function __construct(EventDispatcherInterface $dispatcher, LoggerInterface $logger, $channel = 'ack_channel') {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
        $this->channel = $channel;
    }

    public function setChannel($channel) {
        $this->channel = $channel;
        return $this;
    }

    public function getChannel() {
        return $this->channel;
    }

    /**
     * method to run as a job
     *
     * @param \GearmanJob $job Object with job parameters
     *
     * @return boolean
     *
     * @Gearman\Job(
     *     name = "gcm_msg",
     *     description = "This consumes the messages from gcm ccs")
     */
    public function consumeMessages(\GearmanJob $job) {
        $load = $job->workload();
        $this->logger->info('Hola: ' . 'Message Received');
        $this->logger->info('Hola: ' . $load);
        $payload = json_decode($load, true);
        $GCMCCSUpstreamMessage = new GCMCCSUpstreamMessage($payload['category'], $payload['data'], $payload['message_id'], $payload['from']);
        $GCMCCSUpstreamMessage->setChannel($payload['channel'])->setType('msg');

        $gcmMessageReceivedEvent = new GCMCCSUpstreamEvent($GCMCCSUpstreamMessage);

        $this->logger->info('Hola: ' . 'Dispatching event GCM_MSG_RECEIVED');
        $this->dispatcher->dispatch(GearEvents::GCM_MSG_RECEIVED, $gcmMessageReceivedEvent);

        return true;
    }

    /**
     * Test method to run as a job
     *
     * @param \GearmanJob $job Object with job parameters
     *
     * @return boolean
     *
     * @Gearman\Job(
     *     name = "gcm_ack",
     *     description = "This consumes the ack messages from gcm ccs")
     */
    public function consumeAckMessages(\GearmanJob $job) {
        $load = $job->workload();
        $this->logger->info('Hola: ' . 'Ack message Received');
        $this->logger->info('Hola: ' . $load);
        $payload = json_decode($load, true);
        //prepare below object
        $GCMCCSResponseMessage = new GCMCCSResponseMessage();

        $gcmAckEvent = new GCMCCSReponseEvent($GCMCCSResponseMessage);

        $this->logger->info('Hola: ' . 'Dispatching event GCM_ACK_RECEIVED');
        $this->dispatcher->dispatch(GearEvents::GCM_ACK_RECEIVED, $gcmAckEvent);

        return true;
    }

    /**
     * Test method to run as a job
     *
     * @param \GearmanJob $job Object with job parameters
     *
     * @return boolean
     *
     * @Gearman\Job(
     *     name = "gcm_nack",
     *     description = "This consumes the nack messages from gcm ccs")
     */
    public function consumeNackMessages(\GearmanJob $job) {
        $load = $job->workload();
        $this->logger->info('Hola: ' . 'Nack message Received');
        $this->logger->info('Hola: ' . $load);
        $payload = json_decode($load, true);
        //prepare below object
        $GCMCCSResponseMessage = new GCMCCSResponseMessage();

        $gcmNackEvent = new GCMCCSReponseEvent($GCMCCSResponseMessage);

        $this->logger->info('Hola: ' . 'Dispatching event GCM_NACK_RECEIVED');
        $this->dispatcher->dispatch(GearEvents::GCM_NACK_RECEIVED, $gcmNackEvent);

        return true;
    }

    /**
     * Test method to run as a job
     *
     * @param \GearmanJob $job Object with job parameters
     *
     * @return boolean
     *
     * @Gearman\Job(
     *     name = "gcm_receipt",
     *     description = "This consumes the receipt messages from gcm ccs")
     */
    public function consumeReceiptMessages(\GearmanJob $job) {
        $load = $job->workload();
        $this->logger->info('Hola: ' . 'Receipt message Received');
        $this->logger->info('Hola: ' . $load);
        $payload = json_decode($load, true);
        //create below object
        $GCMCCSReceiptNotificationMessage = new GCMCCSReceiptNotificationMessage();

        $gcmReceiptReceivedEvent = new GCMCCSReceiptNotificationEvent($GCMCCSReceiptNotificationMessage);

        $this->logger->info('Hola: ' . 'Dispatching event GCM_RECEIPT_RECEIVED');
        $this->dispatcher->dispatch(GearEvents::GCM_RECEIPT_RECEIVED, $gcmReceiptReceivedEvent);

        return true;
    }

    /**
     * Test method to run as a job
     *
     * @param \GearmanJob $job Object with job parameters
     *
     * @return boolean
     *
     * @Gearman\Job(
     *     name = "gcm_control",
     *     description = "This consumes the control messages from gcm ccs")
     */
    public function consumeControlMessages(\GearmanJob $job) {
        $load = $job->workload();
        $this->logger->info('Hola: ' . 'Receipt message Received');
        $this->logger->info('Hola: ' . $load);
        $payload = json_decode($load, true);

        $message = \Swift_Message::newInstance()
                ->setSubject('Control message alert')
                ->setFrom('abbiya@gmail.com ')
                ->setTo('abbiya@gmail.com   ')
                ->setBody('control message received')
        ;
        $this->get('mailer')->send($message);

        return true;
    }

}
