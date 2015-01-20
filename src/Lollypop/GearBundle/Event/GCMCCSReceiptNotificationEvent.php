<?php

namespace Lollypop\GearBundle\Event;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GCMCCSReceiptNotificationEvent
 *
 * @author seshachalam
 */
use Lollypop\GearBundle\Entity\GCMCCSReceiptNotificationMessage;
use Symfony\Component\EventDispatcher\Event;

class GCMCCSReceiptNotificationEvent extends Event implements GCMCCSReceiptNotificationInterface
{

    private $GCMCCSReceiptNotificationMessage;

    public function __construct(GCMCCSReceiptNotificationMessage $GCMCCSReceiptNotificationMessage)
    {
        $this->GCMCCSReceiptNotificationMessage = $GCMCCSReceiptNotificationMessage;
    }

    public function getGCMCCSReceiptNotificationMessage()
    {
        return $this->GCMCCSReceiptNotificationMessage;
    }

}
