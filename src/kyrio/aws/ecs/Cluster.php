<?php
/**
 * Created by PhpStorm.
 * User: calston
 * Date: 7/26/16
 * Time: 4:50 PM
 */

namespace Kyrio\AWS\ECS;


class Cluster extends \Kyrio\AWS\Resource
{
    private $clusterName;

    private $services;
    private $containerInstances;


    public function __construct()
    {
        $this->services = array();
        $this->containerInstances = array();
    }

    public function create($clusterName){
        try{
            $result = $this->awsClient->createCluster(['clusterName' => strtolower($clusterName)]);

            //verify result and load ournewselves up
            if($result['cluster']['clusterName'] == $clusterName){
                return $this->load($clusterName);
            }else{
                echo "Cluster creation successful but appears to have been created with the wrong name: requested ($clusterName) but got ({$result['clusterName']})  \n";
                return false;
            }
        }catch(AwsException $e){
            echo($e->getMessage());
            return false;
        }
    }

    public function load($clusterName){
        $result = $this->awsClient->listServices([
            'cluster' => $clusterName,
            'maxResults' => 99,
            'nextToken'    => ''
        ]);

        foreach($result['serviceArns'] as $serviceArn) {
            $service = new \Kyrio\AWS\ECS\Service();
            $service->setArn($serviceArn);
            $service->setServiceName($service->getShortName());
            if(!$service->init($this->creds)){
                return false;
            }

            if(!$service->load($clusterName)){
                return false;
            }

            $this->services[$serviceArn] = $service;
        }

        $result = $this->awsClient->listContainerInstances([
            'cluster' => $clusterName,
            'maxResults' => 99,
            'nextToken'    => ''
        ]);

        foreach($result['containerInstanceArns'] as $ciArn) {
            $containerInstance = new \Kyrio\AWS\ECS\ContainerInstance();
            $containerInstance->setArn($ciArn);
            if(!$containerInstance->init($this->awsClient, $clusterName)){
                return false;
            }

            $this->containerInstances[$ciArn] = $containerInstance;
        }

        //set our cluster name since the load was successful
        $this->clusterName = $clusterName;

        return true;
    }

    public function init($creds)
    {
        $this->creds = $creds;
        $this->awsClient = new \Aws\Ecs\EcsClient([
            'version' => 'latest',
            'region'  => 'us-east-1',
            'credentials' => new \Aws\Credentials\Credentials($this->creds->getKey(), $this->creds->getSecret())
        ]);

        return true;
    }

    public function stopAllTasksNotInFamily($taskDefFamily)
    {
        foreach($this->services as $service){
            $service->stopAllTasksNotInFamily($this->getClusterName(), $taskDefFamily);
        }

        return true;
    }

    public function stopAllTasksInFamily($taskDefFamily)
    {
        foreach($this->services as $service){
            $service->stopAllTasksInFamily($this->getClusterName(), $taskDefFamily);
        }

        return true;
    }

    public function stopAllTasks()
    {
        foreach($this->services as $service){
            $service->stopAllTasks($this->getClusterName());
        }

        return true;
    }

    /**
     * @return array
     */
    public function getContainerInstances()
    {
        return $this->containerInstances;
    }

    /**
     * @return mixed
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param mixed $services
     */
    public function setServices($services)
    {
        $this->services = $services;
    }

    /**
     * @return mixed
     */
    public function getClusterName()
    {
        return $this->clusterName;
    }

    /**
     * @param mixed $clusterName
     */
    public function setClusterName($clusterName)
    {
        $this->clusterName = $clusterName;
    }

}