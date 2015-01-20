<?php

namespace Lollypop\GearBundle\Event;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GCMCCSReponseEvent
 *
 * @author seshachalam
 */
use Lollypop\GearBundle\Entity\GCMCCSResponseMessage;
use Symfony\Component\EventDispatcher\Event;

class GCMCCSReponseEvent extends Event implements GCMCCSResponseInterface
{
    private $GCMCCSResponseMessage;
    
    public function __construct(GCMCCSResponseMessage $GCMCCSResponseMessage)
    {
        $this->GCMCCSResponseMessage = $GCMCCSResponseMessage;
    }
    public function getGCMCCSResponseMessage()
    {
        return $this->GCMCCSResponseMessage;
    }

}
