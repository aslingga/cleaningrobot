<?php
class Floor {
    private $tiles;
    private $xAxis;
    private $yAxis;
    
    const GRID_CLEANED = 'C';
    const GRID_FREE_TO_CLEAN = 'S';
    const GRID_WALL = null;
    
    public function setSource() {
        
    }
    
    public function initiateGrid($grids) {
        $this->tiles = array();
        $this->tiles = $grids;
        
        /* $this->tiles[0][0] = 'S';
        $this->tiles[0][1] = 'S';
        $this->tiles[0][2] = 'S';
        $this->tiles[0][3] = 'S';
        
        $this->tiles[1][0] = 'S';
        $this->tiles[1][1] = 'S';
        $this->tiles[1][2] = 'C';
        $this->tiles[1][3] = 'S';
        
        $this->tiles[2][0] = 'S';
        $this->tiles[2][1] = 'S';
        $this->tiles[2][2] = 'S';
        $this->tiles[2][3] = 'S';
        
        $this->tiles[3][0] = 'S';
        $this->tiles[3][1] = null;
        $this->tiles[3][2] = 'S';
        $this->tiles[3][3] = 'S'; */
    }
    
    public function getXAxis() {
        return $this->xAxis;
    }
    
    public function getYAxis() {
        return $this->yAxis;
    }
    
    public function getMaxY() {
        return 4;
    }
    
    public function getMaxX() {
        return 4;
    }
    
    public function getGrid($x, $y) {
        return $this->tiles[$x][$y];
    }
    
    public function setGrid($x, $y, $value) {
        $this->tiles[$x][$y] = $value;
    }
    
    public function getTiles() {
        return $this->tiles;
    }
    
    public function printTiles() {
        for ($i = 0; $i < 4; $i++) {
            for ($j = 0; $j < 4; $j++) {
                echo $this->getGrid($i, $j);
            }
            
            echo '<br>';
        }
    }
}