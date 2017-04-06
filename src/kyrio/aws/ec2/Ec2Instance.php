<?php
/**
 * Created by PhpStorm.
 * User: calston
 * Date: 7/31/16
 * Time: 12:47 PM
 */

namespace Kyrio\AWS\EC2;

use \Aws\Credentials\Credentials;

class Ec2Instance extends \Kyrio\AWS\Resource
{
    protected $instanceID;

    private $launchTime;
    private $keyName;
    private $privateIP;

    public function __construct()
    {
    }

    public function init($creds)
    {
        $ec2Client = new \Aws\Ec2\Ec2Client([
            'version' => 'latest',
            'region'  => 'us-east-1',
            'credentials' => new \Aws\Credentials\Credentials($creds->getKey(), $creds->getSecret())
        ]);

        $result = $ec2Client->describeInstances([
            'InstanceIds' => [$this->instanceID],
            'DryRun' => false
        ]);

        $instance = $result['Reservations'][0]['Instances'][0];

        $this->launchTime = $instance['LaunchTime'];
        $this->keyName = $instance['KeyName'];
        $this->privateIP = $instance['NetworkInterfaces'][0]['PrivateIpAddress'];

        return true;
    }


    /**
     * @return mixed
     */
    public function getPrivateIP()
    {
        return $this->privateIP;
    }

    /**
     * @param mixed $privateIP
     */
    public function setPrivateIP($privateIP)
    {
        $this->privateIP = $privateIP;
    }


    /**
     * @return mixed
     */
    public function getLaunchTime()
    {
        return $this->launchTime;
    }

    /**
     * @param mixed $launchTime
     */
    public function setLaunchTime($launchTime)
    {
        $this->launchTime = $launchTime;
    }

    /**
     * @return mixed
     */
    public function getKeyName()
    {
        return $this->keyName;
    }

    /**
     * @param mixed $keyName
     */
    public function setKeyName($keyName)
    {
        $this->keyName = $keyName;
    }


    /**
     * @return mixed
     */
    public function getInstanceID()
    {
        return $this->instanceID;
    }

    /**
     * @param mixed $instanceID
     */
    public function setInstanceID($instanceID)
    {
        $this->instanceID = $instanceID;
    }

}