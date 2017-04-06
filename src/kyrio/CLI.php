<?php
namespace Kyrio;

require("vendor/autoload.php");

use Composer\Script\Event;

class CLI
{
    public static function getCreds(Event $event)
    {
        //grab some creds from command line, then check ENV
        if(isset($event->getArguments()[0]) && isset($event->getArguments()[1])){
            $key = $event->getArguments()[0];
            $secret = $event->getArguments()[1];
        }else{
            $key = getenv("AWS_ACCESS_KEY_ID");
            $secret = getenv("AWS_SECRET_ACCESS_KEY");
        }

        return new \Kyrio\AWS\AwsCredentials($key, $secret);
    }

    public static function killAllTasksNotInFamily(Event $event)
    {
        $taskDefFamily = $event->getArguments()[2];
        if($taskDefFamily == '' || strlen($taskDefFamily) <= 0){
            return 1;
        }

        $ecs = new \Kyrio\AWS\ECS\EcsManager();
        $ecs->init(CLI::getCreds($event));

        if(!$ecs->stopAllTasksNotInFamily($taskDefFamily)){
            return 1;
        }else{
            return 0;
        }
    }

    public static function killAllTasksInFamily(Event $event)
    {
        $taskDefFamily = $event->getArguments()[2];
        if($taskDefFamily == '' || strlen($taskDefFamily) <= 0){
            return 1;
        }

        $ecs = new \Kyrio\AWS\ECS\EcsManager();
        $ecs->init(CLI::getCreds($event));

        if(!$ecs->stopAllTasksInFamily($taskDefFamily)){
            return 1;
        }else{
            return 0;
        }
    }

    public static function killAllTasks(Event $event)
    {
        $ecs = new \Kyrio\AWS\ECS\EcsManager();
        $ecs->init(CLI::getCreds($event));

        if(!$ecs->stopAllTasks()){
            return 1;
        }else{
            return 0;
        }
    }
}
?>