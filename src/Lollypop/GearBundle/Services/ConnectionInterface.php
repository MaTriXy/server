<?php

namespace Lollypop\GearBundle\Services;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ConnectionInterface
 *
 * @author seshachalam
 */
interface ConnectionInterface
{
    /**
     * @return string host
     */
    public function getHost();

    /**
     * @return \Lollypop\GearBundle\Services\ConnectionInterface $this
     */
    public function setHost($host);
    
    /**
     * @return int port
     */
    public function getPort();

    /**
     * @return \Lollypop\GearBundle\Services\ConnectionInterface $this
     */
    public function setPort($port);

    /**
     * @return \Redis redis instance
     */
    public function connect();
    
    /**
     * @return void disconnects
     */
    public function disconnect();

    /**
     * @return bool checks if connected
     */
    public function isConnected();

}
