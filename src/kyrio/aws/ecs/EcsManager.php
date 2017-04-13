<?php
/**
 * Created by PhpStorm.
 * User: calston
 * Date: 7/26/16
 * Time: 5:07 PM
 */

namespace Kyrio\AWS\ECS;


class EcsManager
{
    protected $ecsClient;

    private $creds;
    private $clusters;
    private $qaCluster;

    public function __construct()
    {
        $this->clusters = array();
    }

    public function init($creds)
    {
        $this->creds = $creds;

        $this->ecsClient = new \Aws\Ecs\EcsClient([
            'version' => 'latest',
            'region'  => 'us-east-1',
            'credentials' => new \Aws\Credentials\Credentials($this->creds->getKey(), $this->creds->getSecret())
        ]);

        //Download the contents of the object.
        $result = $this->ecsClient->listClusters([
            'maxResults' => 99,
            'nextToken'    => ''
        ]);

        // Print the body of the result by indexing into the result object.
        foreach($result['clusterArns'] as $clusterArn) {
            $cluster = new \Kyrio\AWS\ECS\Cluster();
            $cluster->setArn($clusterArn);
            $cluster->setClusterName($cluster->getShortName());
            if(!$cluster->init($this->creds)){
                return false;
            }

            if(!$cluster->load($cluster->getShortName())){
                return false;
            }

            $this->clusters[$clusterArn] = $cluster;
        }

        return true;
    }

    public function stopAllTasksNotInFamily($taskDefFamily)
    {
        foreach($this->clusters as $cluster){
            $cluster->stopAllTasksNotInFamily($taskDefFamily);
        }

        return true;
    }

    public function stopAllTasksInFamily($taskDefFamily)
    {
        foreach($this->clusters as $cluster){
            $cluster->stopAllTasksInFamily($taskDefFamily);
        }

        return true;
    }

    public function stopAllTasks()
    {
        foreach($this->clusters as $cluster){
            $cluster->stopAllTasks();
        }

        return true;
    }


    /**
     * @return mixed
     */
    public function getEcsClient()
    {
        return $this->ecsClient;
    }

    /**
     * @return mixed
     */
    public function getClusters()
    {
        return $this->clusters;
    }

    public function getQACluster()
    {
        return array($this->cluster['arn:aws:ecs:us-east-1:565703495208:cluster/qa-cluster']);
    }

    public function getProdCluster()
    {
        return ;
    }
}
