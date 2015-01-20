<?php

namespace Lollypop\GearBundle\Event;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GCMInterface
 *
 * @author seshachalam
 */
interface GCMCCSUpstreamInterface
{

    /**
     * @return \Lollypop\GearBundle\Entity\GCMCCSUpstreamMessage gcm upstream mssage
     */
    public function getGCMCCSUpstreamMessage();

}
