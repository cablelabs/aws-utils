<?php
/**
 * Created by PhpStorm.
 * User: calston
 * Date: 7/26/16
 * Time: 4:51 PM
 */

namespace Kyrio\AWS\ECS;

class Task extends \Kyrio\AWS\Resource
{
    private $taskDef;

    private $containerInstanceArn;
    private $containers;

    private $desiredStatus;
    private $lastStatus;

    private $createdAt;
    private $startedAt;
    private $startedBy;
    private $stoppedAt;
    private $stoppedReason;

    public function __construct()
    {
        $this->containers = array();
    }

    public function load($clusterName){
        $result = $this->awsClient->describeTasks([
            'cluster' => $clusterName,
            'tasks' => [$this->getArn()],
            'maxResults' => 99,
            'nextToken'    => ''
        ]);

        $task = $result['tasks'][0];
        $this->containerInstanceArn = $task['containerInstanceArn'];
        $this->lastStatus = $task['lastStatus'];

        //get this task's taskDef
        $this->taskDef = new TaskDef();
        $this->taskDef->init($this->creds);
        $this->taskDef->load($task['taskDefinitionArn']);

        $this->loadContainers($task['containers']);

        return true;
    }

    public function init($creds)
    {
        $this->creds = $creds;
        $this->awsClient = new \Aws\Ecs\EcsClient([
            'version' => 'latest',
            'region' => 'us-east-1',
            'credentials' => new \Aws\Credentials\Credentials($this->creds->getKey(), $this->creds->getSecret())
        ]);

        return true;
    }

    private function loadContainers($containers){
        foreach($containers as $container){
            $c = new Container();
            $c->setArn($container['containerArn']);
            $c->setName($container['name']);
            if(isset($container['reason'])) {
                $c->setReason($container['reason']);
            }

//            foreach($container['networkBindings'] as $nb){
//                $binding = new NetworkBinding($nb['bindIP'], $nb['containerPort'], $nb['hostPort'], $nb['protocol']);
//                $c->addNetworkBinding($binding);
//            }

            $this->containers[$container['containerArn']] = $c;
        }

        return true;
    }

    public function stop($clusterName)
    {
        return $this->awsClient->stopTask([
            'cluster' => $clusterName,
            'reason' => 'Manually forced.',
            'task'    => $this->getArn()
        ]);
    }

    /**
     * @return mixed
     */
    public function getTaskDef()
    {
        return $this->taskDef;
    }

    /**
     * @param mixed $taskDef
     */
    public function setTaskDef($taskDef)
    {
        $this->taskDef = $taskDef;
    }



    /**
     * @return mixed
     */
    public function getContainerInstanceArn()
    {
        return $this->containerInstanceArn;
    }

    /**
     * @return array
     */
    public function getContainers()
    {
        return $this->containers;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return mixed
     */
    public function getDesiredStatus()
    {
        return $this->desiredStatus;
    }

    /**
     * @return mixed
     */
    public function getLastStatus()
    {
        return $this->lastStatus;
    }

    /**
     * @return mixed
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * @return mixed
     */
    public function getStartedBy()
    {
        return $this->startedBy;
    }

    /**
     * @return mixed
     */
    public function getStoppedAt()
    {
        return $this->stoppedAt;
    }

    /**
     * @return mixed
     */
    public function getStoppedReason()
    {
        return $this->stoppedReason;
    }
}