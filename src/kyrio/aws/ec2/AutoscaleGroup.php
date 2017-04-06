<?php
/**
 * Created by PhpStorm.
 * User: calston
 * Date: 7/26/16
 * Time: 4:52 PM
 */

namespace Kyrio\AWS\EC2;


class AutoscaleGroup extends \Kyrio\AWS\Resource
{
    private $autoscalingGroupName;
    private $launchConfigName;

    private $desiredCapacity;
    private $minCapacity;
    private $maxCapacity;
    private $status;

    private $instances;
    private $loadBalancers;


    private $healthCheckGracePeriod;
    private $healthCheckType;

    private $createdTime;

    public function __construct()
    {
        $this->instances = array();
        $this->loadBalancers = array();
    }

    public function updateDesiredCapacity($desCap = 1){
        try {
            $result = $this->awsClient->updateAutoScalingGroup([
                'AutoScalingGroupName' => $this->getAutoscalingGroupName(),
                'DesiredCapacity' => $desCap,
            ]);

            return true;
        }catch(\Aws\AutoScaling\Exception\AutoScalingException $e){
            if(strpos($e->getMessage(), "already exists") !== false){
                return true;
            }else{
                echo("ASException: " . $e->getMessage() . "\n");
                return false;
            }
        }catch(AwsException $e){
            echo("AWSException: " . $e->getMessage() . "\n");
            return false;
        }
    }

    public function updateLaunchConfiguration($launchConfigName){
        try {
            $result = $this->awsClient->updateAutoScalingGroup([
                'AutoScalingGroupName' => $this->getAutoscalingGroupName(),
                'LaunchConfigurationName' => $launchConfigName,
            ]);

            return true;
        }catch(\Aws\AutoScaling\Exception\AutoScalingException $e){
            if(strpos($e->getMessage(), "already exists") !== false){
                return true;
            }else{
                echo("ASException: " . $e->getMessage() . "\n");
                return false;
            }
        }catch(AwsException $e){
            echo("AWSException: " . $e->getMessage() . "\n");
            return false;
        }
    }

    public function create($name, $launchConfigName, $elbNames, $subnetsString = 'subnet-4e640b16,subnet-8b99ffa1,subnet-b13b6bc7,subnet-4988d474'){
        try {
            $result = $this->awsClient->createAutoScalingGroup([
                'AutoScalingGroupName' => $name,
                'VPCZoneIdentifier' => $subnetsString,
                'HealthCheckGracePeriod' => 300,
                'HealthCheckType' => 'EC2',
                'LaunchConfigurationName' => $launchConfigName,
                'LoadBalancerNames' => $elbNames,
                'DesiredCapacity' => 2,
                'MinSize' => 1,
                'MaxSize' => 3,
            ]);

            $this->autoscalingGroupName = $name;
            $this->launchConfigName = $launchConfigName;

            return true;
        }catch(\Aws\AutoScaling\Exception\AutoScalingException $e){
            if(strpos($e->getMessage(), "already exists") !== false){
                return true;
            }else{
                echo("ASException: " . $e->getMessage() . "\n");
                return false;
            }
        }catch(AwsException $e){
            echo("AWSException: " . $e->getMessage() . "\n");
            return false;
        }
    }

    private function loadLoadBalancers($asGroupJSON)
    {
        foreach($asGroupJSON['LoadBalancerNames'] as $elbName){
            $elb = new ElasticLoadBalancer();
            $elb->init($this->creds);
            $elb->load($elbName);
            $this->loadBalancers[$elb->getArn()] = $elb;
        }

        return true;
    }

    public function load($creds, $asGroupJSON){
        $this->creds = $creds;

        $this->autoscalingGroupName = $asGroupJSON['AutoScalingGroupName'];
        $this->createdTime = $asGroupJSON['CreatedTime'];
        $this->desiredCapacity = $asGroupJSON['DesiredCapacity'];
        $this->launchConfigName = $asGroupJSON['LaunchConfigurationName'];
        $this->minCapacity = $asGroupJSON['MinSize'];
        $this->maxCapacity = $asGroupJSON['MaxSize'];
        //$this->status = $asGroupJSON['Status'];

        if(!$this->loadLoadBalancers($asGroupJSON)){
            return false;
        }

        return true;
    }

    public function initClient($awsClient, $creds){
        $this->creds = $creds;
        $this->awsClient = $awsClient;
        return true;
    }

    public function init($creds)
    {
        $this->creds = $creds;
        $this->awsClient = new \Aws\AutoScaling\AutoScalingClient([
            'version' => 'latest',
            'region'  => 'us-east-1',
            'credentials' => new \Aws\Credentials\Credentials($this->creds->getKey(), $this->creds->getSecret())
        ]);

        return true;
    }


    /**
     * @return mixed
     */
    public function getAutoscalingGroupName()
    {
        return $this->autoscalingGroupName;
    }

    /**
     * @return mixed
     */
    public function getLaunchConfigName()
    {
        return $this->launchConfigName;
    }

    /**
     * @return mixed
     */
    public function getDesiredCapacity()
    {
        return $this->desiredCapacity;
    }

    /**
     * @return mixed
     */
    public function getMinCapacity()
    {
        return $this->minCapacity;
    }

    /**
     * @return mixed
     */
    public function getMaxCapacity()
    {
        return $this->maxCapacity;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getInstances()
    {
        return $this->instances;
    }

    /**
     * @return mixed
     */
    public function getLoadBalancers()
    {
        return $this->loadBalancers;
    }

    /**
     * @return mixed
     */
    public function getHealthCheckGracePeriod()
    {
        return $this->healthCheckGracePeriod;
    }

    /**
     * @return mixed
     */
    public function getHealthCheckType()
    {
        return $this->healthCheckType;
    }

    /**
     * @return mixed
     */
    public function getCreatedTime()
    {
        return $this->createdTime;
    }
}