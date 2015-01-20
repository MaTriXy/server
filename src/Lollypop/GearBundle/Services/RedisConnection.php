<?php

namespace Lollypop\GearBundle\Services;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RedisConnectionService
 *
 * @author seshachalam
 */
use Psr\Log\LoggerInterface;

class RedisConnection implements ConnectionInterface
{

    private $logger;
    public $redis;
    private $host;
    private $port;

    public function __construct(LoggerInterface $logger, $host = 'localhost',
                                $port = 6379)
    {
        $this->logger = $logger;
        $this->host = $host;
        $this->port = $port;
        if($this->redis == null) {
            $this->redis = new \Redis();
        }
    }

    /**
     * 
     * @return type
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * 
     * @param type $host
     * @return \Lollypop\GearBundle\Services\RedisConnection
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * 
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * 
     * @param type $port
     * @return \Lollypop\GearBundle\Services\RedisConnection
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * 
     * @return \Redis
     */
    public function connect()
    {
        if(!$this->isConnected()) {
            $this->redis->connect($this->host, $this->port);
        }
        $this->logger->info('Hola: ' . 'Redis returining instance');
        return $this->redis;
    }

    /**
     * @return void closes the connection
     */
    public function disconnect()
    {
        $this->redis->close();
        $this->logger->error('Hola: ' . 'Redis disconnect');
    }

    /**
     * 
     * @return boolean checks if connected
     */
    public function isConnected()
    {
        if($this->redis) {
            try {
                return ('pong' === $this->redis->ping());
            } catch(\Exception $e) {
                $this->logger->error('Hola: ' . $e->getMessage());
                return false;
            }
        } else {
            $this->logger->error('Hola: ' . 'Not connected to redis');
            return false;
        }
    }

    public function __destruct()
    {
        unset($this->redis);
        $this->redis = null;
        $this->logger->error('Hola: ' . 'Redis desctruct');
    }

}
