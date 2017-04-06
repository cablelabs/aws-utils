<?php
/**
 * Created by PhpStorm.
 * User: calston
 * Date: 10/28/16
 * Time: 2:03 PM
 */

namespace Kyrio\AWS\Route53;


use Aws\Exception\AwsException;

class HostedZone extends \Kyrio\AWS\Resource
{
    //id over arn is the yarn from amazon, at least for route53 but not far beyond
    private $id;
    private $fqdn;
    private $isPrivate;

    public function __construct()
    {

    }

    public function create(){

    }

    public function changeResourceRecordSets($changes, $comment = 'No Comment.'){
        try{
            $result = $this->awsClient->changeResourceRecordSets([
                'HostedZoneId' => $this->id,
                'ChangeBatch' => [
                    'Comment' => $comment,
                    'Changes' => $changes,
                ],
            ]);

            echo(print_r($result, true));
        }catch(AwsException $e){
            echo "AwsException: " . $e->getMessage() . "\n\t\t\n" . $e->getAwsErrorCode() . "\n\t\t\n" . $e->getResponse()->getBody() . "\n\n\n";
            return false;
        }

        return true;
    }

    public function addSubdomainAliases($fqdns){
        $changes = array();
        foreach($fqdns as $fqdn){
            $changes[] = [
                'Action' => 'CREATE',
                'ResourceRecordSet' => [
                    'Name' => $fqdn->getFQDN(),
                    'Type' => 'A',
                    'AliasTarget' => [
                        'DNSName' => $fqdn->getTargetHostedZoneName(),
                        'HostedZoneId' => $fqdn->getTargetHostedZoneNameId(),
                        'EvaluateTargetHealth' => false
                    ],
                    //'ResourceRecords' => [],   //for non alias records
                    'TTL' => 300,
                ]
            ];
        }

        return $this->changeResourceRecordSets($changes);
    }

    public function load($fqdn){
        try{
            $result = $this->awsClient->listHostedZonesByName([
                'DNSName' => $fqdn,
                'MaxItems' => 9,
            ]);

            //echo(var_dump($result));
            $this->fqdn = $result->get('DNSName');

            $hz = $result->get('HostedZones')[0];
            $this->id = substr($hz['Id'], strrpos($hz['Id'], "/")+1);

            if($hz['Config']['PrivateZone'] != ''){
                $this->isPrivate = true;
            }else{
                $this->isPrivate = false;
            }

        }catch(AwsException $e){
            echo "AwsException: " . $e->getMessage() . "\n";
            return false;
        }

        return true;
    }

    public function init($creds){
        $this->creds = $creds;
        $this->awsClient = new \Aws\Route53\Route53Client([
            'version' => 'latest',
            'region'  => 'us-east-1',
            'credentials' => new \Aws\Credentials\Credentials($this->creds->getKey(), $this->creds->getSecret())
        ]);

        return true;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getFqdn()
    {
        return $this->fqdn;
    }

    /**
     * @param mixed $fqdn
     */
    public function setFqdn($fqdn)
    {
        $this->fqdn = $fqdn;
    }

    /**
     * @return mixed
     */
    public function getIsPrivate()
    {
        return $this->isPrivate;
    }

    /**
     * @param mixed $isPrivate
     */
    public function setIsPrivate($isPrivate)
    {
        $this->isPrivate = $isPrivate;
    }
}