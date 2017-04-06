<?php
/**
 * Created by PhpStorm.
 * User: calston
 * Date: 7/26/16
 * Time: 4:52 PM
 */

namespace Kyrio\AWS\EC2;

use \Aws\ElasticLoadBalancing\ElasticLoadBalancingClient;
use Aws\Exception\AwsException;

class ElasticLoadBalancer extends \Kyrio\AWS\Resource
{
    private $elbName;
    private $dnsName;
    private $hostedZoneNameID;
    private $hostedZoneName;


    private $instances;

    public function __construct()
    {
        $this->instances = array();
    }

    public function create($clusterName, $elbName, $securityGroups, $subnets){
        try {
            $result = $this->awsClient->createLoadBalancer([
                'Listeners' => [
                    [
                        'InstancePort' => 80,
                        'InstanceProtocol' => 'HTTP',
                        'LoadBalancerPort' => 80,
                        'Protocol' => 'HTTP'
                    ],
                    [
                        'InstancePort' => 8080,
                        'InstanceProtocol' => 'HTTP',
                        'LoadBalancerPort' => 8080,
                        'Protocol' => 'HTTP'
                    ],
                    [
                        'InstancePort' => 8081,
                        'InstanceProtocol' => 'TCP',
                        'LoadBalancerPort' => 8081,
                        'Protocol' => 'TCP'
                    ],
                    [
                        'InstancePort' => 8082,
                        'InstanceProtocol' => 'TCP',
                        'LoadBalancerPort' => 8082,
                        'Protocol' => 'TCP'
                    ],
                    [
                        'InstancePort' => 8083,
                        'InstanceProtocol' => 'TCP',
                        'LoadBalancerPort' => 8083,
                        'Protocol' => 'TCP'
                    ],
                    [
                        'InstancePort' => 8084,
                        'InstanceProtocol' => 'TCP',
                        'LoadBalancerPort' => 8084,
                        'Protocol' => 'TCP'
                    ],
                    [
                        'InstancePort' => 8085,
                        'InstanceProtocol' => 'TCP',
                        'LoadBalancerPort' => 8085,
                        'Protocol' => 'TCP'
                    ],
                ],
                'LoadBalancerName' => $elbName,
                'SecurityGroups' => $securityGroups,
                'Subnets' => $subnets,
                'Tags' => [
                    [
                        'Key' => 'cluster',
                        'Value' => $clusterName,
                    ],
                ],
            ]);
            //echo(var_dump($result));

            $this->elbName = $elbName;
            $this->dnsName = $result['DNSName'];
            echo("Created ELB with DNSName: " . $this->dnsName . "\n");
        }catch(AwsException $e){
            echo($e->getMessage());
            return false;
        }

        try{
            //add health check
            $result = $this->awsClient->configureHealthCheck([
                'HealthCheck' => [
                    'HealthyThreshold' => 2,
                    'Interval' => 10,
                    'Target' => 'HTTP:8080/',
                    'Timeout' => 5,
                    'UnhealthyThreshold' => 4,
                ],
                'LoadBalancerName' => $this->elbName
            ]);
            echo("Added health check to elb: " . $this->dnsName . "\n");
        }catch(AwsException $e){
            echo($e->getMessage());
            return false;
        }

//        try {
//            $result = $this->awsClient->modifyLoadBalancerAttributes([
//                'CrossZoneLoadBalancing' => [
//                    'Enabled' => true,
//                ],
//                'LoadBalancerName' => $this->elbName,
//            ]);
//            echo("Set cross zone balancing to true for elb: " . $this->dnsName . "\n");
//        }catch(AwsException $e){
//            echo($e->getMessage());
//            return false;
//        }

        return true;
    }

    public function load($elbName){
        $result = $this->awsClient->describeLoadBalancers([
            'LoadBalancerNames' => [$elbName],
            'PageSize' => 99
        ]);
        $elb = $result['LoadBalancerDescriptions'][0];
        $this->dnsName = $elb['DNSName'];
        $this->elbName = $elb['LoadBalancerName'];//elbName;
        $this->hostedZoneNameID = $elb['CanonicalHostedZoneNameID'];
        $this->hostedZoneName = $elb['CanonicalHostedZoneName'];

        return $this->initInstances($this->getCreds(), $elb['Instances']);
    }

    /**
     * This method inits an existing elb
     *
     * @param $creds
     * @param $elbName
     * @return bool
     */
    public function init($creds)
    {
        $this->creds = $creds;
        $this->awsClient = new \Aws\ElasticLoadBalancing\ElasticLoadBalancingClient([
            'version' => 'latest',
            'region'  => 'us-east-1',
            'credentials' => new \Aws\Credentials\Credentials($this->creds->getKey(), $this->creds->getSecret())
        ]);

       return true;
    }

    /**
     * This method will load the ec2 instances associated with this elb
     *
     * @param $creds
     * @param $instances
     * @return bool
     */
    private function initInstances($creds, $instances)
    {
        //echo(var_dump($instances));
        //exit;

        foreach($instances as $key => $instance){
            //echo "key: " . $key . " " . " - value: " . $instance['InstanceId'] . "\n";
            $ec2 = new \Kyrio\AWS\EC2\Ec2Instance();
            $ec2->setInstanceID($instance['InstanceId']);
            $ec2->init($creds);

            $this->instances[$instance['InstanceId']] = $ec2;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getInstances()
    {
        return $this->instances;
    }


    /**
     * @return mixed
     */
    public function getElbName()
    {
        return $this->elbName;
    }

    /**
     * @return mixed
     */
    public function getDnsName()
    {
        return $this->dnsName;
    }

    /**
     * @return mixed
     */
    public function getHostedZoneNameID()
    {
        return $this->hostedZoneNameID;
    }

    /**
     * @param mixed $hostedZoneNameID
     */
    public function setHostedZoneNameID($hostedZoneNameID)
    {
        $this->hostedZoneNameID = $hostedZoneNameID;
    }

    /**
     * @return mixed
     */
    public function getHostedZoneName()
    {
        return $this->hostedZoneName;
    }

    /**
     * @param mixed $hostedZoneName
     */
    public function setHostedZoneName($hostedZoneName)
    {
        $this->hostedZoneName = $hostedZoneName;
    }


}