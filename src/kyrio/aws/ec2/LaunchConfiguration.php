<?php
/**
 * Created by PhpStorm.
 * User: calston
 * Date: 7/26/16
 * Time: 4:52 PM
 */

namespace Kyrio\AWS\EC2;


class LaunchConfiguration extends \Kyrio\AWS\Resource
{
    private $launchConfigurationName;
    private $userData;
    private $securityGroups;

    public function __construct()
    {
    }

    public function create($launchConfigurationName, $userData, $securityGroups, $snapshotID, $imageID, $iamProfile, $instanceType, $keyName){
        try{
            $this->awsClient->createLaunchConfiguration([
                'AssociatePublicIpAddress' => true,
                'BlockDeviceMappings' => [
                    [
                        'DeviceName' => '/dev/xvda',
                        'Ebs' => [
                            'DeleteOnTermination' => true,
                            'SnapshotId' => $snapshotID
                        ],
                    ],
                ],
                'EbsOptimized' => false,
                'IamInstanceProfile' => $iamProfile,
                'ImageId' => $imageID,
                'InstanceMonitoring' => [
                    'Enabled' => true,
                ],
                'InstanceType' => $instanceType,
                'KeyName' => $keyName,
                'LaunchConfigurationName' => $launchConfigurationName,
                'SecurityGroups' => $securityGroups,
                'UserData' => base64_encode($userData),
            ]);

            //set us as is
            $this->launchConfigurationName = $launchConfigurationName;
            $this->userData = $userData;
            $this->securityGroups = $securityGroups;

            return true;
        }catch(AwsException $e){
            echo($e->getMessage());
            return false;
        }
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
    public function getLaunchConfigurationName()
    {
        return $this->launchConfigurationName;
    }

    /**
     * @param mixed $launchConfigurationName
     */
    public function setLaunchConfigurationName($launchConfigurationName)
    {
        $this->launchConfigurationName = $launchConfigurationName;
    }

    /**
     * @return mixed
     */
    public function getUserData()
    {
        return $this->userData;
    }

    /**
     * @param mixed $userData
     */
    public function setUserData($userData)
    {
        $this->userData = $userData;
    }

    /**
     * @return mixed
     */
    public function getSecurityGroups()
    {
        return $this->securityGroups;
    }

    /**
     * @param mixed $securityGroups
     */
    public function setSecurityGroups($securityGroups)
    {
        $this->securityGroups = $securityGroups;
    }
}