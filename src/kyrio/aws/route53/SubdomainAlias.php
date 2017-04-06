<?php
/**
 * Created by PhpStorm.
 * User: calston
 * Date: 10/28/16
 * Time: 3:11 PM
 */

namespace kyrio\aws\route53;


use Kyrio\AWS\EC2\ElasticLoadBalancer;

class SubdomainAlias extends \Kyrio\AWS\Resource
{
    private $fqdn;

    private $targetFQDN;
    private $targetHostedZoneNameID;
    private $targetHostedZoneName;

    public function __construct($fqdn, $targetFQDN)
    {
        $this->fqdn = $fqdn;
        $this->targetFQDN = $targetFQDN;
        $this->targetHostedZoneName = $targetFQDN;
    }

    public function loadRoute53Target(){

    }

    public function loadELBTarget($elbName){
        //load elb to get id
        $elb = new ElasticLoadBalancer();
        $elb->init($this->creds);
        if(!$elb->load($elbName)){
            return false;
        }

        $this->targetHostedZoneNameID = $elb->getHostedZoneNameID();
        $this->targetHostedZoneName = $elb->getHostedZoneName();

        return true;
    }

    public function init($creds){
        $this->creds = $creds;
    }

    /**
     * @return mixed
     */
    public function getFqdn()
    {
        return $this->fqdn;
    }

    /**
     * @param mixed $fqdn
     */
    public function setFqdn($fqdn)
    {
        $this->fqdn = $fqdn;
    }

    /**
     * @return mixed
     */
    public function getTargetFQDN()
    {
        return $this->targetFQDN;
    }

    /**
     * @param mixed $targetFQDN
     */
    public function setTargetFQDN($targetFQDN)
    {
        $this->targetFQDN = $targetFQDN;
    }

    /**
     * @return mixed
     */
    public function getTargetHostedZoneNameID()
    {
        return $this->targetHostedZoneNameID;
    }

    /**
     * @param mixed $targetHostedZoneNameID
     */
    public function setTargetHostedZoneNameID($targetHostedZoneNameID)
    {
        $this->targetHostedZoneNameID = $targetHostedZoneNameID;
    }

    /**
     * @return mixed
     */
    public function getTargetHostedZoneName()
    {
        return $this->targetHostedZoneName;
    }

    /**
     * @param mixed $targetHostedZoneName
     */
    public function setTargetHostedZoneName($targetHostedZoneName)
    {
        $this->targetHostedZoneName = $targetHostedZoneName;
    }
}