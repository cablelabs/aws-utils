<?php
/**
 * Created by PhpStorm.
 * User: calston
 * Date: 7/26/16
 * Time: 4:22 PM
 */

namespace Kyrio\AWS;


class Resource
{
    private $arn;
    private $id;

    protected $creds;
    protected $awsClient;

    public function __construct()
    {

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
    protected function getCreds()
    {
        return $this->creds;
    }

    /**
     * @param mixed $creds
     */
    protected function setCreds($creds)
    {
        $this->creds = $creds;
    }

    /**
     * @return mixed
     */
    protected function getAwsClient()
    {
        return $this->awsClient;
    }

    /**
     * @param mixed $awsClient
     */
    protected function setAwsClient($awsClient)
    {
        $this->awsClient = $awsClient;
    }

    public function getShortName()
    {
        return substr($this->arn, strrpos($this->arn, "/")+1);
    }

    /**
     * @return mixed
     */
    public function getArn()
    {
        return $this->arn;
    }

    /**
     * @param mixed $arn
     */
    public function setArn($arn)
    {
        $this->arn = $arn;
    }
}