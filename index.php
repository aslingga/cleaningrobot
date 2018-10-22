<?php
    include_once 'Robot.php';
    include_once 'Floor.php';
    
    $robot = new Robot(3, 0, 'N', 80);
    $floor = new Floor(4, 4);
    
    $floor->setSource();
    $floor->initiateGrid();
    
    $robot->setFloor($floor);
    $robot->setInstruction(null);
    $robot->clean();
    
    $floor->printTiles();
?>