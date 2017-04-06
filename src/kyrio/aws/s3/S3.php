<?php
/**
 * Created by PhpStorm.
 * User: calston
 * Date: 10/27/16
 * Time: 4:00 AM
 */

namespace Kyrio\AWS\S3;


use Kyrio\AWS\Resource;

class S3 extends Resource
{
    public function __construct()
    {
    }

    public function init($creds)
    {
        $this->creds = $creds;

        $this->awsClient = new \Aws\S3\S3Client([
            'version' => 'latest',
            'region'  => 'us-east-1',
            'credentials' => new \Aws\Credentials\Credentials($this->creds->getKey(), $this->creds->getSecret())
        ]);

        return true;
    }

    public function getObject($bucket, $key){
        $result = $this->awsClient->getObject([
            'Bucket' => $bucket,
            'Key' => $key
        ]);

        return $result;
    }


    public function putObjectFromFile($bucket, $key, $filename){
        $result = $this->awsClient->putObject([
            'Bucket' => $bucket,
            'Key' => $key,
            'SourceFile' => $filename,
        ]);

        return true;
    }

    public function putObject($bucket, $key, $fileContents){
        $this->awsClient->putObject([
            'Body' => $fileContents,
            'Bucket' => $bucket,
            'Key' => $key,
        ]);

        return true;
    }

}