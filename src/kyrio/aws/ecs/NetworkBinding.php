<?php
/**
 * Created by PhpStorm.
 * User: calston
 * Date: 7/31/16
 * Time: 11:59 AM
 */

namespace kyrio\aws\ecs;


class NetworkBinding
{
    private $bindIP;
    private $containerPort;
    private $hostPort;
    private $protocol;

    public function __construct($bindIP, $containerPort, $hostPort, $protocol)
    {
        $this->bindIP = $bindIP;
        $this->containerPort = $containerPort;
        $this->hostPort = $hostPort;
        $this->protocol = $protocol;
    }


    /**
     * @return mixed
     */
    public function getBindIP()
    {
        return $this->bindIP;
    }

    /**
     * @param mixed $bindIP
     */
    public function setBindIP($bindIP)
    {
        $this->bindIP = $bindIP;
    }

    /**
     * @return mixed
     */
    public function getContainerPort()
    {
        return $this->containerPort;
    }

    /**
     * @param mixed $containerPort
     */
    public function setContainerPort($containerPort)
    {
        $this->containerPort = $containerPort;
    }

    /**
     * @return mixed
     */
    public function getHostPort()
    {
        return $this->hostPort;
    }

    /**
     * @param mixed $hostPort
     */
    public function setHostPort($hostPort)
    {
        $this->hostPort = $hostPort;
    }

    /**
     * @return mixed
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @param mixed $protocol
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
    }
}