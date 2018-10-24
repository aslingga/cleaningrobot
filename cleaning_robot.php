<?php
    include_once 'Robot.php';
    include_once 'Floor.php';
    
    $source = $argv[1];
    $destination = $argv[2];
    
    $source = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $source);
    
    $sourceArr = json_decode($source, true);    
    $map = $sourceArr['map'];
    $start = $sourceArr['start'];
    $instructions = $sourceArr['commands'];
    $battery = $sourceArr['battery'];
    
    $robot = new Robot($start, $instructions, $battery);
    $floor = new Floor();
    
    $floor->initiateGrid($map);
    $robot->setFloor($floor);
    $robot->startJob();
    
    $jsonDestination = json_encode($robot->getResult(), JSON_PRETTY_PRINT);
    file_put_contents($destination, $jsonDestination);
?>