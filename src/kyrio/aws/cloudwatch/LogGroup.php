<?php
/**
 * Created by PhpStorm.
 * User: calston
 * Date: 10/27/16
 * Time: 11:36 PM
 */

namespace Kyrio\AWS\Cloudwatch;


class LogGroup extends \Kyrio\AWS\Resource
{

    private $creationTime;
    private $logGroupName;
    private $metricFilterCount;
    private $retentionInDays;
    private $storedBytes;

    public function __construct()
    {

    }

    public function create($logGroupName){
        try {
            $result = $this->awsClient->createLogGroup([
                'logGroupName' => $logGroupName,
            ]);

            $this->logGroupName = $logGroupName;
            return true;
        }catch(\Aws\CloudWatchLogs\Exception\CloudWatchLogsException $e) {
            echo("CloudWatchLogsException: " . $e->getMessage() . "\n");
            if(strpos($e->getMessage(), "already exists") !== false){
                return true;
            }else {
                return false;
            }
        }catch(AwsException $e){
            echo("AWSException: " . $e->getMessage() . "\n");
            return false;
        }
    }

    public function load(){

    }

    public function init($creds){
        $this->creds = $creds;
        $this->awsClient = new \Aws\CloudWatchLogs\CloudWatchLogsClient([
            'version' => 'latest',
            'region'  => 'us-east-1',
            'credentials' => new \Aws\Credentials\Credentials($this->creds->getKey(), $this->creds->getSecret())
        ]);

        return true;
    }
}