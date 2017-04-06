<?php
/**
 * Created by PhpStorm.
 * User: calston
 * Date: 7/31/16
 * Time: 23:55
 */

namespace Kyrio\AWS\EC2;

use \Aws\AutoScaling\AutoScalingClient;
use Kyrio\AWS\EC2\AutoscaleGroup;

class Ec2Manager
{
    private $creds;

    private $elbCount;

    protected $launchConfigs;
    protected $elasticLoadBalancers;
    protected $autoscaleGroups;
    protected $ec2Instances;

    public function __construct()
    {
        $this->autoscaleGroups = array();
        $this->ec2Instances = array();
        $this->elasticLoadBalancers = array();
        $this->launchConfigs = array();

        $this->elbCount = 0;
    }

    public function init($creds)
    {
        $this->creds = $creds;

        if(!$this->initAutoscaleGroups()){
            return false;
        }

        return true;
    }

    private function initAutoscaleGroups()
    {
        $asClient = new \Aws\AutoScaling\AutoScalingClient([
            'version' => 'latest',
            'region'  => 'us-east-1',
            'credentials' => new \Aws\Credentials\Credentials($this->creds->getKey(), $this->creds->getSecret())
        ]);

        //Download the contents of the object.
        $result = $asClient->describeAutoScalingGroups([
            'AutoScalingGroupNames' => [],
            'MaxRecords' => 99,
        ]);

        //echo(var_dump($result));
        //exit;

        foreach($result['AutoScalingGroups'] as $asGroup) {
            $as = new \Kyrio\AWS\EC2\AutoscaleGroup();
            $as->init($this->creds);
            $as->load($this->creds, $asGroup);
            $as->setArn($asGroup['AutoScalingGroupARN']);

            $this->elbCount += count($as->getLoadBalancers());
            $this->autoscaleGroups[$asGroup['AutoScalingGroupARN']] = $as;
        }

        return true;
    }

    public function getEc2InstanceCount(){
        $count = 0;
        foreach($this->autoscaleGroups as $asg){
            foreach($asg->getLoadBalancers() as $elb){
                $count += count($elb->getInstances());
            }
        }
        return $count;
    }

    public function getAutoscaleGroupCount(){
        return count($this->autoscaleGroups);
    }

    /**
     * @return int
     */
    public function getElbCount(): int
    {
        return $this->elbCount;
    }

    /**
     * @return array
     */
    public function getAutoscaleGroups()
    {
        return $this->autoscaleGroups;
    }
}