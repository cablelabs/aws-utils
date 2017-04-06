<?php
/**
 * Created by PhpStorm.
 * User: calston
 * Date: 8/20/16
 * Time: 7:07 PM
 */

namespace Kyrio\AWS\sns;


class Message extends \Kyrio\AWS\Resource
{
    private $creds;
    private $snsClient;

    private $topicArn;
    private $subject;
    private $body;

    public function __construct()
    {

    }

    public function init($creds)
    {
        $this->creds = $creds;

        $this->snsClient = new \Aws\Sns\SnsClient([
            'version' => 'latest',
            'region'  => 'us-east-1',
            'credentials' => new \Aws\Credentials\Credentials($this->creds->getKey(), $this->creds->getSecret())
        ]);
    }

    public function send()
    {
        $result = $this->snsClient->publish([
            'TopicArn' => $this->topicArn,
            'Message' => $this->body,
            'Subject' => $this->subject
        ]);

        return $result['MessageId'];
    }


    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return mixed
     */
    public function getTopicArn()
    {
        return $this->topicArn;
    }

    /**
     * @param mixed $topicArn
     */
    public function setTopicArn($topicArn)
    {
        $this->topicArn = $topicArn;
    }


}