<?php
/**
 * Created by PhpStorm.
 * User: calston
 * Date: 7/31/16
 * Time: 12:48 PM
 */

namespace Kyrio\AWS\ECS;


class ContainerInstance extends \Kyrio\AWS\Resource
{
    private $ec2InstanceID;
    private $ec2Instance;

    private $agentConnected;
    private $pendingTasksCount;
    private $runningTasksCount;
    private $status;

    public function __construct()
    {
    }

    public function init($ecsClient, $clusterName)
    {
        $result = $ecsClient->describeContainerInstances([
            'cluster' => $clusterName,
            'containerInstances' => [$this->getArn()],
            'maxResults' => 99,
            'nextToken'    => ''
        ]);

        $containerInstance = $result['containerInstances'][0];
        $this->ec2InstanceID = $containerInstance['ec2InstanceId'];
        $this->agentConnected = $containerInstance['agentConnected'];
        $this->pendingTasksCount = $containerInstance['pendingTasksCount'];
        $this->runningTasksCount = $containerInstance['runningTasksCount'];
        $this->status = $containerInstance['status'];

        return true;
    }


    /**
     * @return mixed
     */
    public function getEc2InstanceID()
    {
        return $this->ec2InstanceID;
    }

    /**
     * @param mixed $ec2InstanceID
     */
    public function setEc2InstanceID($ec2InstanceID)
    {
        $this->ec2InstanceID = $ec2InstanceID;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getAgentConnected()
    {
        return $this->agentConnected;
    }

    /**
     * @param mixed $agentConnected
     */
    public function setAgentConnected($agentConnected)
    {
        $this->agentConnected = $agentConnected;
    }

    /**
     * @return mixed
     */
    public function getPendingTasksCount()
    {
        return $this->pendingTasksCount;
    }

    /**
     * @param mixed $pendingTasksCount
     */
    public function setPendingTasksCount($pendingTasksCount)
    {
        $this->pendingTasksCount = $pendingTasksCount;
    }

    /**
     * @return mixed
     */
    public function getRunningTasksCount()
    {
        return $this->runningTasksCount;
    }

    /**
     * @param mixed $runningTasksCount
     */
    public function setRunningTasksCount($runningTasksCount)
    {
        $this->runningTasksCount = $runningTasksCount;
    }
}