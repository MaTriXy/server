<?php

namespace Lollypop\GearBundle\Services;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MessageDealerInterface
 *
 * @author seshachalam
 */
use Lollypop\GearBundle\Entity\GCMCCSUpstreamMessage;

interface MessageDealerInterface
{
    public function deal(GCMCCSUpstreamMessage $upstreamMessage);
}
