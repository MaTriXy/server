<?php

namespace Lollypop\GearBundle\Event;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GCMCCSDownstreamEvent
 *
 * @author seshachalam
 */
use Lollypop\GearBundle\Entity\GCMCCSDownstreamMessage;
use Symfony\Component\EventDispatcher\Event;

class GCMCCSDownstreamEvent extends Event implements GCMCCSDownstreamInterface
{

    private $GCMCCSDownstreamMessage;

    public function __construct(GCMCCSDownstreamMessage $GCMCCSDownstreamMessage)
    {
        $this->GCMCCSDownstreamMessage = $GCMCCSDownstreamMessage;
    }

    public function getGCMCCSDownstreamMessage()
    {
        return $this->GCMCCSDownstreamMessage;
    }

}
