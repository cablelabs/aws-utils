<?php
/**
 * Created by PhpStorm.
 * User: calston
 * Date: 7/26/16
 * Time: 4:48 PM
 */

namespace Kyrio\AWS\ECS;


class Service extends \Kyrio\AWS\Resource
{
    protected $serviceName;
    protected $taskDef;

    protected $status;
    protected $minHealthyPercent;
    protected $maxPercent;

    protected $taskDefinition;
    protected $tasks;
    protected $desiredTaskCount;
    protected $pendingTaskCount;
    protected $runningTaskCount;

    public function __construct()
    {
        $this->tasks = array();
    }

    public function create($clusterName, $serviceName, $taskDefArn){
        try {
            $result = $this->awsClient->createService([
                'clientToken' => '',
                'cluster' => $clusterName,
                'deploymentConfiguration' => [
                    'maximumPercent' => 200,
                    'minimumHealthyPercent' => 50,
                ],
                'desiredCount' => 1,
                'serviceName' => $serviceName,
                'taskDefinition' => $taskDefArn,
                'status' => 'ACTIVE',
            ]);

            return true;
        }catch(\Aws\Ecs\Exception\EcsException $e) {
            if(strpos($e->getMessage(), "idempotent") !== false){
                return true;
            }else{
                echo("EcsException: " . $e->getMessage() . "\n");
                return false;
            }
        }catch(AwsException $e){
            echo("AWSException: " . $e->getMessage() . "\n");
            return false;
        }
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

    public function load($clusterName){
        $result = $this->awsClient->listTasks([
            'cluster' => $clusterName,
            'serviceName' => $this->serviceName,
            'maxResults' => 99,
            'nextToken'    => ''
        ]);

        //get existing tasks
        foreach($result['taskArns'] as $taskArn) {
            $task = new Task();
            $task->setArn($taskArn);
            if(!$task->init($this->creds)){
                return false;
            }
            if(!$task->load($clusterName)){
                return false;
            }

            $this->tasks[$taskArn] = $task;
        }

        //get task def
        $details = $this->awsClient->describeServices([
            'cluster' => $clusterName,
            'services' => [$this->serviceName]
        ]);
        $this->taskDefinition = $details['services'][0]['taskDefinition'];
        $this->taskDef = new TaskDef();
        $this->taskDef->init($this->creds);
        $this->taskDef->load($this->taskDefinition);

        //other service items
        $this->status = $details['services'][0]['status'];
        $this->minHealthyPercent = $details['services'][0]['deploymentConfiguration']['minimumHealthyPercent'];
        $this->maxPercent = $details['services'][0]['deploymentConfiguration']['maximumPercent'];

        $this->desiredTaskCount = $details['services'][0]['desiredCount'];
        $this->pendingTaskCount = $details['services'][0]['pendingCount'];
        $this->runningTaskCount = $details['services'][0]['runningCount'];

        return true;
    }

    public function update($clusterName, $taskDef, $desiredCount)
    {
        try {
            $result = $this->awsClient->updateService([
                'cluster' => $clusterName,
//                'deploymentConfiguration' => [
//                    'maximumPercent' => 200,
//                    'minimumHealthyPercent' => 50,
//                ],
                'desiredCount' => $desiredCount,
                'service' => $this->getServiceName(),
                'taskDefinition' => $taskDef,
                'status' => 'ACTIVE',
            ]);

            return true;
        }catch(\Aws\Ecs\Exception\EcsException $e) {
            if(strpos($e->getMessage(), "idempotent") !== false){
                return true;
            }else{
                echo("EcsException: " . $e->getMessage() . "\n");
                return false;
            }
        }catch(AwsException $e){
            echo("AWSException: " . $e->getMessage() . "\n");
            return false;
        }
    }

    public function stopAllTasksNotInFamily($clusterName, $taskDefFamily)
    {
        foreach($this->tasks as $task){
            echo "Checking task def (" . $this->taskDefinition . ") against taskDefFamily (" . $taskDefFamily . ") for task " . $task->getArn() . "\n";
            if(strpos($task->getTaskDef()->getFamily(), $taskDefFamily) === false) {
                echo("\tNo Match, stopping task...\n");
                $task->stop($clusterName);
                sleep(5);
            }
        }

        return true;
    }

    public function stopAllTasksInFamily($clusterName, $taskDefFamily)
    {
        foreach($this->tasks as $task){
            echo "Checking task def (" . $this->taskDefinition . ") against taskDefFamily (" . $taskDefFamily . ") for task " . $task->getArn() . "\n";
            if(strpos($task->getTaskDef()->getFamily(), $taskDefFamily) !== false) {
                echo("\tMatch, stopping task...\n");
                $task->stop($clusterName);
                sleep(5);
            }
        }

        return true;
    }

    public function stopAllTasks($clusterName)
    {
        foreach($this->tasks as $task){
            $task->stop($clusterName);
            sleep(5);
        }

        return true;
    }

    public function getTaskDefinitionShortName()
    {
        return substr($this->taskDefinition, strrpos($this->taskDefinition, "/")+1);
    }


    /**
     * @return mixed
     */
    public function getTaskDefinition()
    {
        return $this->taskDefinition;
    }

    /**
     * @param mixed $taskDefinition
     */
    public function setTaskDefinition($taskDefinition)
    {
        $this->taskDefinition = $taskDefinition;
    }

    /**
     * @return mixed
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }


    /**
     * @param mixed $serviceName
     */
    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;
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
    public function getMinHealthyPercent()
    {
        return $this->minHealthyPercent;
    }

    /**
     * @return mixed
     */
    public function getMaxPercent()
    {
        return $this->maxPercent;
    }

    /**
     * @return mixed
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * @return mixed
     */
    public function getDesiredTaskCount()
    {
        return $this->desiredTaskCount;
    }

    /**
     * @return mixed
     */
    public function getPendingTaskCount()
    {
        return $this->pendingTaskCount;
    }

    /**
     * @return mixed
     */
    public function getRunningTaskCount()
    {
        return $this->runningTaskCount;
    }

}