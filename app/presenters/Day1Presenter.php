<?php

namespace App\Presenters;

use Nette;

class Map extends \Nette\Object
{
    /**
     * @var int
     */
    public $x;

    /**
     * @var int
     */
    public $y;
    
    /**
     * N (1), E (2), S (3), W (4)
     * @var int
     */
    private $orientation;
    
    /**
     * @var string
     */
    private $instructions;
    
    /**
     * @var Array
     */
    private $instructionList;
    
    /**
     * @var Array
     */
    public $visitedLocationsList;
    
    /**
     * @var bool
     */
    private $visitedTwiceSearch;
    
    /**
     * @var bool 
     */
    private $hqFound;
    
    /**
     * @param string $instructions
     * @param bool $visitedTwiceSearch
     */
    public function __construct($instructions, $visitedTwiceSearch = false){
        $this->instructions = $instructions;
        $this->x = 0;
        $this->y = 0;   
        $this->instructionList = [];
        $this->visitedLocationsList = [];
        $this->orientation = 1;
        $this->visitedTwiceSearch = $visitedTwiceSearch;
        $this->hqFound = false;
        
        $this->prepareInstructionList();
    }
    
    private function prepareInstructionList(){        
        $this->instructionList = preg_split("/\,/", $this->instructions); 
    }        

    /**
     * @param string $instruction
     */
    private function changeOrientation($instruction){
        if ($instruction[0] == 'L'){
            $this->orientation--;
        }
        if ($instruction[0] == 'R'){
            $this->orientation++;
        }
        
        //vyrovnani ruzice kompasu
        if ($this->orientation === 0){
            $this->orientation = 4;
        }
        if ($this->orientation === 5){
            $this->orientation = 1;
        }
    }
    
    /**
     * @param string $instruction
     */
    private function doStep($instruction){
        $stepCount = (int) substr($instruction, 1);
        switch ($this->orientation){
            case 1: 
                $i = $this->y;
                $i2 = $this->y + $stepCount;
                for ($i; $i < $i2; $i++){
                    $this->y++;
                    if ($this->visitedTwiceSearch){
                        $this->hqCheck();                        
                        if ($this->hqFound){
                            break;
                        }
                        $this->saveLocation();
                    }
                }
                break;
            case 2: 
                $i = $this->x;
                $i2 = $this->x + $stepCount;
                for ($i; $i < $i2; $i++){
                    $this->x++;
                    if ($this->visitedTwiceSearch){
                        $this->hqCheck();                        
                        if ($this->hqFound){
                            break;
                        }
                        $this->saveLocation();
                    }
                }
                break;
            case 3: 
                $i = $this->y;
                $i2 = $this->y - $stepCount;
                for ($i; $i > $i2; $i--){
                    $this->y--;
                    if ($this->visitedTwiceSearch){
                        $this->hqCheck();                        
                        if ($this->hqFound){
                            break;
                        }
                        $this->saveLocation();
                    }
                }
                break;
            case 4: 
                $i = $this->x;
                $i2 = $this->x - $stepCount;
                for ($i; $i > $i2; $i--){
                    $this->x--;
                    if ($this->visitedTwiceSearch){
                        $this->hqCheck();                        
                        if ($this->hqFound){
                            break;
                        }
                        $this->saveLocation();
                    }
                }                
                break;
        }            
    }
    
    public function process(){
        foreach ($this->instructionList as $instruction){
            $this->changeOrientation(trim($instruction));
            $this->doStep(trim($instruction));     
            if ($this->hqFound){
                 break;
            }
        }        
    }
    
    private function saveLocation(){
        $this->visitedLocationsList[] = $this->x . '#' . $this->y;
    }
    
    private function hqCheck(){
        if (in_array($this->x . '#' . $this->y, $this->visitedLocationsList)){
            $this->hqFound = true;
        }
    }
    
    /**
     * @return int
     */    
    public function getShortestPath(){
        return abs($this->x) + abs($this->y);
    }
}

class Day1Presenter extends BasePresenter
{    
    public function renderFirst(){
        $map = new Map(file_get_contents(WWW_DIR . '/instructions/day1_1.txt'),false);
        $map->process();
        
        $this->template->shortestPath = $map->getShortestPath();        
    }
    
    public function renderSecond(){
        $map = new Map(file_get_contents(WWW_DIR . '/instructions/day1_1.txt'),true);
        $map->process();
        
        $this->template->shortestPath = $map->getShortestPath();        
    }
            
}
