<?php
/**
 * Created by PhpStorm.
 * User: calston
 * Date: 7/31/16
 * Time: 10:40 AM
 */

namespace Kyrio\AWS\ECS;


class Container extends \Kyrio\AWS\Resource
{
    private $name;
    private $reason;

    private $networkBindings;

    public function __construct()
    {
        $this->networkBindings = array();
    }

    public function init($ecsClient)
    {
        return true;
    }

    public function addNetworkBinding($nb){
        $this->networkBindings[] = $nb;
    }

    /**
     * @return mixed
     */
    public function getNetworkBindings()
    {
        return $this->networkBindings;
    }


    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }


    /**
     * @return mixed
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param mixed $reason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    }

}