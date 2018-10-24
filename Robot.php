<?php
class Robot {
    private $floor;
    private $battery;
    private $facing;
    private $instructions;
    private $cursor;
    private $xPosition;
    private $yPosition;
    private $obstacle;
    private $facingCursor;
    private $batteryCommandConsume;
    private $obstacleOperation;
    private $directions;
    
    private $visitedGrid;
    private $cleanedGrid;
    private $finalPositiion;
    
    const CLEAN_COMMAND = 'C';
    const TURN_LEFT_COMMAND = 'TL';
    const TURN_RIGHT_COMMAND = 'TR';
    const ADVANCE_COMMAND = 'A';
    const BACK_COMMAND = 'B';
    const OBSTACLE_WALL_HIT = 1;
    const OBSTACLE_WALL_FREEZE = 0;
    
    const NORTH_DIRECTION = array (-1, 0);
    const EAST_DIRECTION = array (0, 1);
    const SOUTH_DIRECTION = array (1, 0);
    const WEST_DIRECTION = array (0, -1);
    
    public function __construct($start, $instructions, $battery) {
    	$this->facingCursor = array('N', 'E', 'S', 'W');
    	$this->batteryCommandConsume = array(
	        'TL' => 1, 
	        'TR' => 1, 
	        'A' => 2, 
	        'B' => 3, 
	        'C' => 5, 
	    );
    	$this->obstacleOperation = array(
	        array('TR', 'A'),
	        array('TL', 'B', 'TR', 'A'),
	        array('TL', 'TL', 'A'),
	        array('TR', 'B', 'TR', 'A'),
	        array('TL', 'TL', 'A')
	    );
    	$this->directions = array(
			'N' => array (-1, 0),
    		'E' => array (0, 1),
    		'S' => array (1, 0),
    		'W' => array (0, -1)
    	);
        $this->obstacle = self::OBSTACLE_WALL_FREEZE;
        
        $this->finalPositiion = array();
        $this->visitedGrid = array();
        $this->cleanedGrid = array();
        
        $this->setPosition($start['X'], $start['Y']);
        $this->setCursor($start['facing']);
        $this->setBattery($battery);
        $this->setInstruction($instructions);
    }
    
    public function setPosition($x, $y) {
        $this->xPosition = $x;
        $this->yPosition = $y;
    }
    
    public function getCursor() {
        return $this->cursor;
    }
    
    public function freeToMove() {
        return $this->obstacle == self::OBSTACLE_WALL_FREEZE;
    }
    
    public function hitObstacle() {
        $this->obstacle = self::OBSTACLE_WALL_HIT;        

        if ($this->getXPosition() <= 0) {
        	$this->setXPosition(0);
        }
        if ($this->getYPosition() <= 0) {
        	$this->setYPosition(0);
        }
        if ($this->getXPosition() >= $this->floor->getMaxX()) {
        	$this->setXPosition($this->floor->getMaxX() - 1);
        }
        if ($this->getYPosition() >= $this->floor->getMaxY()) {
        	$this->setYPosition($this->floor->getMaxY() - 1);
        }
    }
    
    // change the state to be from obstacle 
    public function freeFromObstacle() {
        $this->obstacle = self::OBSTACLE_WALL_FREEZE;
    }
    
    public function setCursor($facing) {
        $this->cursor = $facing;
    }
    
    // get the x position of the robot in the floor
    public function getXPosition() {
        return $this->xPosition;
    }
    
    public function setXPosition($value) {
        $this->xPosition = $value;
    }
    
    // move the the robot in the floor in the spesific x position
    public function moveXPosition($value) {
        $this->setXPosition($this->getXPosition() + $value);
    }
    
    // get the y position of the robot in the floor
    public function getYPosition() {
        return $this->yPosition;
    }
    
    public function setYPosition($value) {
        $this->yPosition = $value;
    }
    
    // move the the robot in the floor in the spesific y position
    public function moveYPosition($value) {
        $this->setYPosition($this->getYPosition() + $value);
    }
    
    // change the facing of the robot
    public function moveCursorDirection($instruction) {
        $facingVal = 0;
        $currentFacingVal = array_search($this->getCursor(), $this->facingCursor);
        
        if ($instruction == self::TURN_RIGHT_COMMAND) {
            $facingVal = 1;
        }
        else if ($instruction == self::TURN_LEFT_COMMAND) {
            $facingVal = -1;
        }
        
        if ($facingVal != 0) {
            $nextFacingVal = $currentFacingVal + $facingVal;
            
            if ($nextFacingVal == -1) {
                $nextFacingVal = count($this->facingCursor) - 1;
            }
            else if ($nextFacingVal == count($this->facingCursor)) {
                $nextFacingVal = 0;
            }
            
            $this->consumeBattery($instruction);
            $this->setCursor($this->facingCursor[$nextFacingVal]);
        }
    }
    
    // move robot according to the spesific instruction
    public function movePosition($instruction) {
    	// if the instruction is Advance
        if ($instruction == self::ADVANCE_COMMAND) {
            switch ($this->getCursor()) {
                case 'N':
                    $this->moveXPosition($this->directions['N'][0]);
                    $this->moveYPosition($this->directions['N'][1]);
                    break;
                case 'E':
                    $this->moveXPosition($this->directions['E'][0]);
                    $this->moveYPosition($this->directions['E'][1]);
                    break;
                case 'S':
                    $this->moveXPosition($this->directions['S'][0]);
                    $this->moveYPosition($this->directions['S'][1]);
                    break;
                case 'W':
                    $this->moveXPosition($this->directions['W'][0]);
                    $this->moveYPosition($this->directions['W'][1]);
                    break;
                default:
                    break;
            }
        }

        // if the instruction is Back
        if ($instruction == self::BACK_COMMAND) {            
            switch ($this->getCursor()) {
                case 'N':
                    $this->moveXPosition($this->directions['S'][0]);
                    $this->moveYPosition($this->directions['S'][1]);
                    break;
                case 'E':
                    $this->moveXPosition($this->directions['W'][0]);
                    $this->moveYPosition($this->directions['W'][1]);
                    break;
                case 'S':
                    $this->moveXPosition($this->directions['N'][0]);
                    $this->moveYPosition($this->directions['N'][1]);
                    break;
                case 'W':
                    $this->moveXPosition($this->directions['E'][0]);
                    $this->moveYPosition($this->directions['E'][1]);
                    break;
                default:
                    break;
            }
        }
        
        $this->consumeBattery($instruction);
                
        if ($this->reachObstacleOrWall()) {            
            $this->hitObstacle();
        }
        else {
            $this->freeFromObstacle();
        }
        
        if (!$this->freeToMove()) {
            foreach ($this->obstacleOperation as $operation) {                
                foreach ($operation as $instruction) {
                    if (!$this->stillHaveEnergy($instruction)) {
                        break 2;
                    }
                    
                    $this->action($instruction, $this->getXPosition(), $this->getYPosition());
                }
                
                if($this->freeToMove()) {
                    break;
                }
            }
        }
        
        $this->freeFromObstacle();
    }
    
    public function getBattery() {
        return $this->battery;
    }   
    
    public function setBattery($battery) {
        $this->battery = $battery;
    }
    
    // consume the energy for each instruction
    public function consumeBattery($instruction) {
        $before = $this->getBattery();
        $consumed = $this->batteryCommandConsume[$instruction];
        $after = $before - $consumed; 
        
        $this->setBattery($this->getBattery() - $this->batteryCommandConsume[$instruction]);
    }
    
    public function stillHaveEnergy($instruction) {
        return ($this->getBattery() > 0 && $this->getBattery() >= $this->batteryCommandConsume[$instruction]);
    }
    
    public function setInstruction($instructions) {
    	$this->instructions = $instructions;
    }
    
    public function getInstruction() {
    	return $this->instructions;
    }
    
    public function setFloor(&$floor) {
        $this->floor = $floor;
    }
    
    public function clean($x, $y) {
        $this->consumeBattery(self::CLEAN_COMMAND);
        if ($this->floor->getGrid($x, $y) == Floor::GRID_FREE_TO_CLEAN) {
        	$this->cleanGrid($x, $y);
        }
        $this->floor->setGrid($x, $y, self::CLEAN_COMMAND);
    }
    
    public function visitGrid($x, $y) {
    	$visitedGrid = array('X' => $x, 'Y' => $y);
    	
    	if (!in_array($visitedGrid, $this->visitedGrid)) {
    		array_push($this->visitedGrid, $visitedGrid);
    	}
    }
    
    public function cleanGrid($x, $y) {
    	$cleanedGrid = array('X' => $x, 'Y' => $y);
    	
    	if (!in_array($cleanedGrid, $this->cleanedGrid)) {
    		array_push($this->cleanedGrid, $cleanedGrid);
    	}
    }
    
    public function getVisitedGrid() {
    	return $this->visitedGrid;
    }
    
    public function getCleanedGrid() {
    	return $this->cleanedGrid;
    }
    
    public function getFinalPosition() {
    	$this->finalPositiion = array(
			'X' => $this->getXPosition(),
    		'Y' => $this->getYPosition(),
    		'facing' => $this->getCursor()
    	);
    	
    	return $this->finalPositiion;
    }
    
    // check if the robot is reach obstacle or wall
    public function reachObstacleOrWall() {        
        return (($this->getXPosition() < 0) ||
            ($this->getYPosition() < 0) ||
            ($this->getXPosition() >= $this->floor->getMaxX()) ||
            ($this->getYPosition() >= $this->floor->getMaxY()) || 
            ($this->floor->getGrid($this->getXPosition(), $this->getYPosition()) == Floor::GRID_WALL)
        );
    }
    
    public function action($instruction, $x, $y) {
        $this->visitGrid($x, $y);
        
        switch ($instruction) {
            case self::CLEAN_COMMAND:
                $this->clean($x, $y);
                break;
            case self::TURN_LEFT_COMMAND:
            case self::TURN_RIGHT_COMMAND:
                $this->moveCursorDirection($instruction);
                break;
            case self::ADVANCE_COMMAND:
            case self::BACK_COMMAND:
                $this->movePosition($instruction);
                break;
            default:
                break;
        }
    }
    
    public function startJob() {
        foreach ($this->getInstruction() as $instruction) {
            if (!$this->stillHaveEnergy($instruction)) {
                break;
            }
            
            $this->action($instruction, $this->getXPosition(), $this->getYPosition());
        }
    }
    
    // get the final state of the robot
    public function getResult() {
        return array(
        	'visited' => $this->getVisitedGrid(),
        	'cleaned' => $this->getCleanedGrid(),
            'final' => $this->getFinalPosition(),
            'battery' => $this->getBattery()
        );
    }
}