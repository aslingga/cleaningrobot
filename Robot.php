<?php
class Robot {
    private $floor;
    private $battery;
    private $facing;
    private $instructions;
    private $cursor;
    private $xPosition;
    private $yPosition;
    
    const CLEAN_COMMAND = 'C';
    const TURN_LEFT_COMMAND = 'TL';
    const TURN_RIGHT_COMMAND = 'TR';
    const ADVANCE_COMMAND = 'TL';
    const BACK_COMMAND = 'TL';
    
    public function __construct($xStart, $yStart, $facing, $battery) {
        $this->setPosition($xStart, $yStart);
        $this->setCursor($facing);
        $this->setBattery($battery);
    }
    
    public function setPosition($x, $y) {
        $this->xPosition = $x;
        $this->yPosition = $y;
    }
    
    public function setCursor($facing) {
        $this->cursor = $facing;
    }
    
    public function moveCursorFacing($instruction) {
        
    }
    
    public function movePosition($instruction) {
        
    }
    
    public function setBattery($battery) {
        $this->battery = $battery;
    }
    
    public function setInstruction($instructions) {
        $this->instructions = array('TL', 'A', 'C', 'A', 'C', 'TR', 'A', 'C');
    }
    
    public function setFloor(&$floor) {
        $this->floor = $floor;
    }
    
    public function action($instruction, $x, $y) {
        switch ($instruction) {
            case self::CLEAN_COMMAND:
                $this->floor->setGrid($x, $y, self::CLEAN_COMMAND);
                break;
            case self::TURN_LEFT_COMMAND:
            case self::TURN_RIGHT_COMMAND:
                $this->moveCursorFacing($instruction);
                break;
            case self::ADVANCE_COMMAND:
            case self::BACK_COMMAND:
                $this->movePosition($instruction);
                break;
            default:
                break;
        }
    }
    
    public function clean() {
        foreach ($this->instructions as $instruction) {
            $this->action($instruction, $this->xPosition, $this->yPosition);
        }
        
        echo '<br>';        
        echo '<br>';
    }
}