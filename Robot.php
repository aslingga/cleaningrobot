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
    
    const CLEAN_COMMAND = 'C';
    const TURN_LEFT_COMMAND = 'TL';
    const TURN_RIGHT_COMMAND = 'TR';
    const ADVANCE_COMMAND = 'A';
    const BACK_COMMAND = 'B';
    const FACING_CURSOR = array('N', 'E', 'S', 'W');
    const OBSTACLE_WALL_HIT = 1;
    const OBSTACLE_WALL_FREEZE = 0;
    
    const NORTH_DIRECTION = array (-1, 0);
    const EAST_DIRECTION = array (0, 1);
    const SOUTH_DIRECTION = array (1, 0);
    const WEST_DIRECTION = array (0, -1);
    
    const BATTERY_COMMAND_CONSUME = array(
        'TL' => 1, 
        'TR' => 1, 
        'A' => 2, 
        'B' => 3, 
        'C' => 5, 
    );
    
    const OBSTACLE_OPERATION = array(
        array('TR', 'A'),
        array('TL', 'B', 'TR', 'A'),
        array('TL', 'TL', 'A'),
        array('TR', 'B', 'TR', 'A'),
        array('TL', 'TL', 'A')
    );
    
    public function __construct($xStart, $yStart, $facing, $battery) {
        $this->obstacle = self::OBSTACLE_WALL_FREEZE;
        $this->setPosition($xStart, $yStart);
        $this->setCursor($facing);
        $this->setBattery($battery);
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
    }
    
    public function freeFromObstacle() {
        $this->obstacle = self::OBSTACLE_WALL_FREEZE;
    }
    
    public function setCursor($facing) {
        $this->cursor = $facing;
    }
    
    public function getXPosition() {
        return $this->xPosition;
    }
    
    public function setXPosition($value) {
        $this->xPosition = $value;
    }
    
    public function moveXPosition($value) {
        $this->setXPosition($this->getXPosition() + $value);
    }
    
    public function getYPosition() {
        return $this->yPosition;
    }
    
    public function setYPosition($value) {
        $this->yPosition = $value;
    }
    
    public function moveYPosition($value) {
        $this->setYPosition($this->getYPosition() + $value);
    }
    
    public function moveCursorDirection($instruction) {
        $facingVal = 0;
        $currentFacingVal = array_search($this->getCursor(), self::FACING_CURSOR);
        
        if ($instruction == self::TURN_RIGHT_COMMAND) {
            $facingVal = 1;
        }
        else if ($instruction == self::TURN_LEFT_COMMAND) {
            $facingVal = -1;
        }
        
        if ($facingVal != 0) {
            $nextFacingVal = $currentFacingVal + $facingVal;
            
            if ($nextFacingVal == -1) {
                $nextFacingVal = count(self::FACING_CURSOR) - 1;
            }
            else if ($nextFacingVal == count(self::FACING_CURSOR)) {
                $nextFacingVal = 0;
            }
            
            $this->consumeBattery($instruction);
            $this->setCursor(self::FACING_CURSOR[$nextFacingVal]);
        }
        
        echo '<br>Console log: Cursor moved to ' . $this->getCursor() . ' for instruction ' . $instruction . ' (' . $this->getXPosition() . ', ' . $this->getYPosition() . ') <br>';
    }
    
    public function movePosition($instruction) {
        if ($instruction == self::ADVANCE_COMMAND) {
            switch ($this->getCursor()) {
                case 'N':
                    $this->moveXPosition(self::NORTH_DIRECTION[0]);
                    $this->moveYPosition(self::NORTH_DIRECTION[1]);
                    break;
                case 'E':
                    $this->moveXPosition(self::EAST_DIRECTION[0]);
                    $this->moveYPosition(self::EAST_DIRECTION[1]);
                    break;
                case 'S':
                    $this->moveXPosition(self::SOUTH_DIRECTION[0]);
                    $this->moveYPosition(self::SOUTH_DIRECTION[1]);
                    break;
                case 'W':
                    $this->moveXPosition(self::WEST_DIRECTION[0]);
                    $this->moveYPosition(self::WEST_DIRECTION[1]);
                    break;
                default:
                    break;
            }
        }
        
        if ($instruction == self::BACK_COMMAND) {            
            switch ($this->getCursor()) {
                case 'N':
                    $this->moveXPosition(self::SOUTH_DIRECTION[0]);
                    $this->moveYPosition(self::SOUTH_DIRECTION[1]);
                    break;
                case 'E':
                    $this->moveXPosition(self::WEST_DIRECTION[0]);
                    $this->moveYPosition(self::WEST_DIRECTION[1]);
                    break;
                case 'S':
                    $this->moveXPosition(self::NORTH_DIRECTION[0]);
                    $this->moveYPosition(self::NORTH_DIRECTION[1]);
                    break;
                case 'W':
                    $this->moveXPosition(self::EAST_DIRECTION[0]);
                    $this->moveYPosition(self::EAST_DIRECTION[1]);
                    break;
                default:
                    break;
            }
        }
        
        $this->consumeBattery($instruction);
        
        echo '<br>Console log: In moving position (' . $this->reachObstacleOrWall() . ') for instruction ' . $instruction . ' (' . $this->getXPosition() . ', ' . $this->getYPosition() . ') <br>';
                
        if ($this->reachObstacleOrWall()) {
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
            
            $this->hitObstacle();
        }
        else {
            $this->freeFromObstacle();
        }
        
        if (!$this->freeToMove()) {
            foreach (self::OBSTACLE_OPERATION as $operation) {                
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
    
    public function consumeBattery($instruction) {
        $before = $this->getBattery();
        $consumed = self::BATTERY_COMMAND_CONSUME[$instruction];
        $after = $before - $consumed; 
        
        $this->setBattery($this->getBattery() - self::BATTERY_COMMAND_CONSUME[$instruction]);
        echo '<br>Console log: Battery left (' . $before . ' -  ' . $consumed . ') to be ' . $after . ' for instruction ' . $instruction . ' (' . $this->getXPosition() . ', ' . $this->getYPosition() . ') <br>';
    }
    
    public function stillHaveEnergy($instruction) {
        /* return ($this->getBattery() > 0 && $this->getBattery() >= self::BATTERY_COMMAND_CONSUME[$instruction]); */
        return ($this->getBattery() > 0);
    }
    
    public function setInstruction($instructions) {
        $this->instructions = array('TL', 'A', 'C', 'A', 'C', 'TR', 'A', 'C');
        /* $this->instructions = array('TR', 'A', 'C', 'A', 'C', 'TR', 'A', 'C'); */
    }
    
    public function setFloor(&$floor) {
        $this->floor = $floor;
    }
    
    public function clean($x, $y) {
        $this->consumeBattery(self::CLEAN_COMMAND);
        $this->floor->setGrid($x, $y, self::CLEAN_COMMAND);
        
        echo '<br>Console log: Grid is cleaned for instruction ' . self::CLEAN_COMMAND . ' (' . $this->getXPosition() . ', ' . $this->getYPosition() . ') <br>';
    }
    
    public function reachObstacleOrWall() {
        echo '<br>Console log: Reach Obstacle or Wall ' . $this->getXPosition() . $this->getYPosition() . ' <br>';
        
        return (($this->getXPosition() < 0) ||
            ($this->getYPosition() < 0) ||
            ($this->getXPosition() > $this->floor->getMaxX()) ||
            ($this->getYPosition() > $this->floor->getMaxY()) || 
            ($this->floor->getGrid($this->getXPosition(), $this->getYPosition()) == Floor::GRID_WALL)
        );
    }
    
    public function action($instruction, $x, $y) {
        echo '<br>Console log: <u><strong>Execute instruction ' . $instruction . ' (' . $this->getXPosition() . ', ' . $this->getYPosition() . ')</strong></u><br>';
        
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
        foreach ($this->instructions as $instruction) {
            if (!$this->stillHaveEnergy($instruction)) {
                break;
            }
            
            $this->action($instruction, $this->getXPosition(), $this->getYPosition());
        }
    }
    
    public function getResult() {
        $final = array(
            'X' => $this->getXPosition(),
            'Y' => $this->getYPosition(),
            'facing' => $this->getCursor()
        );        
        
        return array(
            'final' => $final,
            'battery' => $this->getBattery()
        );
    }
}