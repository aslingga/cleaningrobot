<?php
class Floor {
    private $tiles;
    private $xAxis;
    private $yAxis;
    
    const GRID_CLEANED = 'C';
    const GRID_FREE_TO_CLEAN = 'S';
    const GRID_WALL = null;
    
    public function initiateGrid($grids) {
        $this->tiles = array();
        $this->tiles = $grids;
    }
    
    public function getXAxis() {
        return $this->xAxis;
    }
    
    public function getYAxis() {
        return $this->yAxis;
    }
    
    public function getMaxY() {
        return count($this->tiles);
    }
    
    public function getMaxX() {
        return count($this->tiles[0]);
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
}