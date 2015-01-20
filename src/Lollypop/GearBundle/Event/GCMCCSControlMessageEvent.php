<?php

namespace Lollypop\GearBundle\Event;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GCMCCSControlMessageEvent
 *
 * @author seshachalam
 */
use Symfony\Component\EventDispatcher\Event;

class GCMCCSControlMessageEvent extends Event implements GCMCCSControlMessageInterface
{

    public function __construct()
    {
        ;
    }

}
