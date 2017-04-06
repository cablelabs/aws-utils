<?php
/**
 * Created by PhpStorm.
 * User: calston
 * Date: 7/26/16
 * Time: 4:51 PM
 */

namespace Kyrio\AWS\ECS;

use Aws\Ecs\Exception\EcsException;
use Aws\Exception\AwsException;

class TaskDef extends \Kyrio\AWS\Resource
{
    private $family;
    private $revision;
    private $latestRevision;
    private $status;

    private $taskRoleArn;

    private $containerDefs;

    private $msoPorts = [
        'cox' => '8080',
        'charter' => '8081',
        'comcast' => '8082',
        'wow' => '8083',
        'midco' => '8084',
        'mediacom' => '8085',
        'altice' => '8086',
        'testmso' => '8087',
        'shaw' => '8088'
    ];

    public function __construct()
    {
        $this->containerDefs = array();
    }

    public function create($msoFullName, $clusterColor, $family, $image, $instanceType, $accessKeyId, $secretAccessKey, $httpAuthUser, $httpAuthPass, $buildNumber = '', $frontEndBuildNumber = ''){
        try{
            $nodeEnv = strtolower($clusterColor) == 'qa' ? strtolower($msoFullName) . 'qa' : strtolower($msoFullName);
            $containerName = 'api-mx-' . strtolower($msoFullName);
            if($clusterColor == 'qa') {
                $containerName .= 'qa';
            }

            $result = $this->awsClient->registerTaskDefinition([
                'family' => $family,
                'networkMode' => 'bridge',
                'containerDefinitions' => [
                    [
                        'command' => ['npm', 'run', strtolower($msoFullName)],
                        'cpu' => 1024,
                        'memory' => 1024,
                        'name' => $containerName,
                        'essential' => true,
                        'image' => $image,
                        'logConfiguration' => [
                            'logDriver' => 'awslogs',
                            'options' => [
                                'awslogs-region' => 'us-east-1',
                                'awslogs-group' => $clusterColor . '-bsa-backend-' . strtolower($msoFullName)
                            ],
                        ],
                        'portMappings' => [
                            [
                                'containerPort' => 8080,
                                'hostPort' => $msoPorts[$msoFullName],
                                'protocol' => 'tcp',
                            ],
                        ],
                        'readonlyRootFilesystem' => false,
                        'environment' => [
                            [
                                'name' => 'ACCESS_KEY_ID',
                                'value' => $accessKeyId,
                            ],
                            [
                                'name' => 'SECRET_ACCESS_KEY',
                                'value' => $secretAccessKey,
                            ],
                            [
                                'name' => 'BASIC_AUTH_PASSWORD',
                                'value' => $httpAuthPass,
                            ],
                            [
                                'name' => 'BASIC_AUTH_USER_NAME',
                                'value' =>  $httpAuthUser,
                            ],
                            [
                                'name' => 'INSTANCE_TYPE',
                                'value' => $instanceType,
                            ],
                            [
                                'name' => 'LOG_LEVEL',
                                'value' => 'INFO',
                            ],
                            [
                                'name' => 'MY_PROVIDER_ID',
                                'value' => $msoFullName,
                            ],
                            [
                                'name' => 'NODE_ENV',
                                'value' => $nodeEnv,
                            ],
                            [
                                'name' => 'REGION',
                                'value' => 'us-east-1',
                            ],
                        ],
                    ],
                ]
            ]);

            //echo(var_dump($result));
            $this->family = $result['taskDefinition']['family'];
            $this->revision = $result['taskDefinition']['revision'];
            $this->setArn($result['taskDefinition']['taskDefinitionArn']);

            return true;
        }catch(AwsException $e){
            echo(print_r($e, true));
            echo($e->getMessage());
            echo($e->getTraceAsString());
            return false;
        }
    }

    public function createNew($msoFullName, $clusterColor, $family, $image, $instanceType, $accessKeyId, $secretAccessKey, $httpAuthUser, $httpAuthPass, $buildNumber = '', $frontEndBuildNumber = ''){
        try{
            $nodeEnv = strtolower($clusterColor) == 'qa' ? strtolower($msoFullName) . 'qa' : strtolower($msoFullName);
            $containerName = 'api-mx-' . strtolower($msoFullName);
            if($clusterColor == 'qa') {
                $containerName .= 'qa';
            }

            $result = $this->awsClient->registerTaskDefinition([
                'family' => $family,
                'networkMode' => 'bridge',
                'containerDefinitions' => [
                    [
                        'command' => ['npm', 'run', strtolower($msoFullName)],
                        'cpu' => 1024,
                        'memory' => 2048,
                        'name' => $containerName,
                        'essential' => true,
                        'image' => $image,
                        'logConfiguration' => [
                            'logDriver' => 'awslogs',
                            'options' => [
                                'awslogs-region' => 'us-east-1',
                                'awslogs-group' => $clusterColor . '-bsa-backend-' . strtolower($msoFullName)
                            ],
                        ],
                        'portMappings' => [
                            [
                                'containerPort' => 8080,
                                'hostPort' => $msoPorts[$msoFullName],
                                'protocol' => 'tcp',
                            ],
                        ],
                        'readonlyRootFilesystem' => false,
                        'environment' => [
                            [
                                'name' => 'ACCESS_KEY_ID',
                                'value' => $accessKeyId,
                            ],
                            [
                                'name' => 'SECRET_ACCESS_KEY',
                                'value' => $secretAccessKey,
                            ],
                            [
                                'name' => 'BASIC_AUTH_PASSWORD',
                                'value' => $httpAuthPass,
                            ],
                            [
                                'name' => 'BASIC_AUTH_USER_NAME',
                                'value' =>  $httpAuthUser,
                            ],
                            [
                                'name' => 'INSTANCE_TYPE',
                                'value' => $instanceType,
                            ],
                            [
                                'name' => 'LOG_LEVEL',
                                'value' => 'INFO',
                            ],
                            [
                                'name' => 'MY_PROVIDER_ID',
                                'value' => $msoFullName,
                            ],
                            [
                                'name' => 'NODE_ENV',
                                'value' => $nodeEnv,
                            ],
                            [
                                'name' => 'REGION',
                                'value' => 'us-east-1',
                            ],
                        ],
                    ],
                ]
            ]);

            //echo(var_dump($result));
            $this->family = $result['taskDefinition']['family'];
            $this->revision = $result['taskDefinition']['revision'];
            $this->setArn($result['taskDefinition']['taskDefinitionArn']);

            return true;
        }catch(AwsException $e){
            echo(print_r($e, true));
            echo($e->getMessage());
            echo($e->getTraceAsString());
            return false;
        }
    }

    public function load($taskDefinition){  //$taskDefinition can be arn, family, or family:revision per aws specs
        //get info on the taskDef
        try {
            $result = $this->awsClient->describeTaskDefinition([
                'taskDefinition' => $taskDefinition
            ]);
            $this->family = $result['taskDefinition']['family'];
            $this->revision = $result['taskDefinition']['revision'];
            $this->setArn($result['taskDefinition']['taskDefinitionArn']);

        }catch(EcsException $e){
            //echo("ecs exception Error getting details on task def: $taskDefinition" . "\n\n--------\n" . $e->getMessage());
        }
        catch(AwsException $e){
            //echo("Error getting details on task def: $taskDefinition" . "\n\n--------\n" . $e->getMessage());
        }

        //make same call again to get latest revision - find latest revision of this family
        try {
            $result = $this->awsClient->describeTaskDefinition([
                'taskDefinition' => $this->family
            ]);
            $this->latestRevision = $result['taskDefinition']['revision'];

        }catch(EcsException $e){
            //echo("2 ecs exception Error getting details on task def: $taskDefinition" . "\n\n--------\n" . $e->getMessage());
        }
        catch(AwsException $e){
            //echo("2 Error getting details on task def: $taskDefinition" . "\n\n--------\n" . $e->getMessage());
        }

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

    /**
     * @return mixed
     */
    public function getLatestRevision()
    {
        return $this->latestRevision;
    }

    /**
     * @param mixed $latestRevision
     */
    public function setLatestRevision($latestRevision)
    {
        $this->latestRevision = $latestRevision;
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
    public function getTaskRoleArn()
    {
        return $this->taskRoleArn;
    }

    /**
     * @param mixed $taskRoleArn
     */
    public function setTaskRoleArn($taskRoleArn)
    {
        $this->taskRoleArn = $taskRoleArn;
    }


    /**
     * @return mixed
     */
    public function getFamily()
    {
        return $this->family;
    }

    /**
     * @param mixed $family
     */
    public function setFamily($family)
    {
        $this->family = $family;
    }

    /**
     * @return mixed
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * @param mixed $revision
     */
    public function setRevision($revision)
    {
        $this->revision = $revision;
    }

    /**
     * @return array
     */
    public function getContainerDefs()
    {
        return $this->containerDefs;
    }

    /**
     * @param array $containerDefs
     */
    public function setContainerDefs(array $containerDefs)
    {
        $this->containerDefs = $containerDefs;
    }

}
