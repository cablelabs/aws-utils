<?php
require("vendor/autoload.php");

//grab some creds from command line, then check ENV
if(isset($argv[1]) && isset($argv[2])){
    $key = $argv[1];
    $secret = $argv[2];
}else{
    $key = getenv("AWS_ACCESS_KEY_ID");
    $secret = getenv("AWS_SECRET_ACCESS_KEY");
}
echo "key: " . $key . "  *  -----  *  " . "secret: " . $secret . "\n";

//create some aws creds
$creds = new \Kyrio\AWS\AwsCredentials($key, $secret);

//ecs breakdown
$ecs = new \Kyrio\AWS\ECS\EcsManager();
$ecs->init($creds);

$testService = null;
foreach($ecs->getClusters() as $cluster) {
    echo $cluster->getClusterName() . "\n";

    foreach($cluster->getContainerInstances() as $containerInstance){
        echo "\tContainer Instance: " . $containerInstance->getArn() . "\n";
        echo "\tContainer Instance Private IP: ";
            $ec2ID = $containerInstance->getEc2InstanceID();
            $ec2 = new \Kyrio\AWS\EC2\Ec2Instance();
            $ec2->setInstanceID($ec2ID);
            $ec2->init($creds);

            echo $ec2->getPrivateIP();
        echo "\n";
    }

    foreach ($cluster->getServices() as $service) {
        echo "\t\tService: " . $service->getServiceName() . "\n";
        echo "\t\tDesired Tasks: " . $service->getDesiredTaskCount() . "\n";


        foreach($service->getTasks() as $task){

            echo "\t\t\tTask Arn: " . $task->getArn() . "\n";
            echo "\t\t\tLast Status: " . $task->getLastStatus() . "\n";
            echo "\t\t\tTaskDef Arn: " . $task->getTaskDef()->getArn() . "\n";
            echo "\t\t\tTaskDef Family: " . $task->getTaskDef()->getFamily() . "\n";
            echo "\t\t\tTask Def Latest Revision: " . $task->getTaskDef()->getLatestRevision() . "\n";

            foreach($task->getContainers() as $container){
                echo "\t\t\tContainer: " . $container->getName() . "\n";

                foreach($container->getNetworkBindings() as $nb){
                    echo "\t\t\t" . $nb->getHostPort() . "\n";
                }
            }
        }l
    }
}


////route53 test
//$zone = new \Kyrio\AWS\Route53\HostedZone();
//$zone->init($creds);
//$zone->load("testmso.bsa.kyrio.com.");
//
//echo "fqdn: " . $zone->getFqdn() . "  , zone id: " . $zone->getId() . "\n\n";
//
//$aliases = array();
////$aliases[] = new \Kyrio\AWS\Route53\SubdomainAlias('r53.bsa.kyrio.com','dualstack.blue-elb-testmso-bsa-850903080.us-east-1.elb.amazonaws.com.');
////$aliases[] = new \Kyrio\AWS\Route53\SubdomainAlias('blue.r53.bsa.kyrio.com','proxy.bsa.kyrio.com.');
//$alias = new \Kyrio\AWS\Route53\SubdomainAlias('testmso.bsa.kyrio.com.','dualstack.blue-elb-testmso-bsa-850903080.us-east-1.elb.amazonaws.com.');
//$alias->init($creds);
//$alias->loadELBTarget('blue-elb-testmso-bsa');
//echo "alias ids: " . $alias->getTargetHostedZoneNameID() . "    " . $alias->getTargetHostedZoneName() . "\n\n";
//$aliases[] = $alias;
//
//
//$zone->addSubdomainAliases($aliases);
//$zone->create("qa-bsa-acme");

////as grp
//$as = new \Kyrio\AWS\EC2\AutoscaleGroup();
//$as->init($creds);
//$as->create("qa-as-bsa-acme", "qa-launch-bsa-acme-t2Med-3.9", array("qa-elb-acme-bsa"));

//classic ec2 breakdown
//$ec2m = new Kyrio\AWS\EC2\Ec2Manager();
//$ec2m->init($creds);
//
//foreach($ec2m->getAutoscaleGroups() as $as){
//    echo "AS Group: " . $as->getAutoscalingGroupName() . "\n";
//    foreach($as->getLoadBalancers() as $elb){
//        echo "\tELB Name: " . $elb->getElbName() . "\n";
//        foreach($elb->getInstances() as $ec2){
//            echo "\t\tEc2 Instance Id: " . $ec2->getInstanceID() . "\n";
//        }
//    }
//}
//echo "ELB Count: " . $ec2m->getElbCount() . "\n";


//create elb test
//$elb = new \Kyrio\AWS\EC2\ElasticLoadBalancer();
//$elb->init($creds);
//$elb->create("qa-bsa-acme", "qa-elb-acme-bsa");


//create cluster test
//$cluster = new \Kyrio\AWS\ECS\Cluster();
//$cluster->init($creds);
//$cluster->create("qa-bsa-acme");


//ssh -i ~/.ssh/kyrio_devops.pem centos%ci.bsa.kyrio.com+172.31.2.72 -l ec2-user





//send sns msg test
//$snsMsg = new \Kyrio\AWS\sns\Message();
//$snsMsg->init($creds);
//$snsMsg->setSubject("Example 1 Subject");
//$snsMsg->setBody("Example 1 Body");
//$snsMsg->setTopicArn('');
//echo $snsMsg->send();
//exit;
