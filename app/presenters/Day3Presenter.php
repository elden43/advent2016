<?php

namespace App\Presenters;

class Triangle extends \Nette\Object
{
    /**
     * @var int
     */
    public $a;
    
    /**
     * @var int
     */
    public $b;
    
    /**
     * @var int
     */
    public $c;
    
    /**
     * 
     * @param Array $triangleSides     
     */
    public function __construct($triangleSides) {
        $this->a = $triangleSides[0];
        $this->b = $triangleSides[1];
        $this->c = $triangleSides[2];        
    }
    
    /**
     * @return bool
     */
    public function isValid(){
        $valid = true;
        if (($this->a + $this->b) <= $this->c){
            $valid = false;
        }
        if (($this->a + $this->c) <= $this->b){
            $valid = false;
        }
        if (($this->b + $this->c) <= $this->a){
            $valid = false;
        }
        return $valid;
    }
    
}

class Triangles extends \Nette\Object
{
    /**
     * @var Array
     */
    private $trianglesA;
    
    /**
     * @var Array
     */
    private $trianglesB;
    
    /**
     * @var int
     */
    public $resultCount;
    
    public function __construct($input,$inputType = 'A') {
        $this->prepareTriangleList($input,$inputType);
        if ($inputType == 'A'){
            $this->resultCount = $this->getValidCount($this->trianglesA);                
        }
        if ($inputType == 'B'){
            $this->resultCount = $this->getValidCount($this->trianglesB);                
        }
    }
    
    /**
     * pro A rozpadne radek na pole stran trojuhelniku, pro B projde pole z A a rzpadne po sloupcich (kazdy treti)
     * @param string $input
     * @param string $inputType prvni hvezda A, druha hvezda B
     */
    private function prepareTriangleList($input,$inputType){        
        $triangleRows = preg_split("/(\r\n|\n|\r)/", $input);
        foreach ($triangleRows as $triangleRow){
            $this->trianglesA[] = new Triangle(preg_split('/ +/', trim($triangleRow)));

        }
        
        if ($inputType == 'B'){
            $i = 0;
            foreach ($this->trianglesA as $triangleRow){
                $i++;
                if ($i % 3 == 0){
                    $this->trianglesB[] = new Triangle([$this->trianglesA[$i-3]->a,$this->trianglesA[$i-2]->a,$this->trianglesA[$i-1]->a]);
                    $this->trianglesB[] = new Triangle([$this->trianglesA[$i-3]->b,$this->trianglesA[$i-2]->b,$this->trianglesA[$i-1]->b]);
                    $this->trianglesB[] = new Triangle([$this->trianglesA[$i-3]->c,$this->trianglesA[$i-2]->c,$this->trianglesA[$i-1]->c]);                    
                }
            }
        }
    }  
    
    /**
     * 
     * @param Array $triangles
     * @return int
     */
    public function getValidCount($triangles){
        $validCount = 0;
        foreach ($triangles as $triangle){
            if ($triangle->isValid()){
                $validCount++;
            }
        }
        return $validCount;
    }
    
    public function log(){
        $log = '';
        foreach ($this->trianglesA as $triangle){
            if ($triangle->isValid()){
                $log .= "a: $triangle->a, b: $triangle->b, c: $triangle->c = VALID <br />"; 
            }
            else {
                $log .= "a: $triangle->a, b: $triangle->b, c: $triangle->c = INVALID <br />"; 
            }
        }
        return $log;
    }
}


class Day3Presenter extends BasePresenter
{
    public function renderFirst(){
        $triangles = new Triangles(file_get_contents(WWW_DIR . '/instructions/day3.txt'));
        $this->template->validCount = $triangles->resultCount;
        //$this->template->log = $triangles->log();
    }
    
    public function renderSecond(){
        $triangles = new Triangles(file_get_contents(WWW_DIR . '/instructions/day3.txt'),'B');
        $this->template->validCount = $triangles->resultCount;
        //$this->template->log = $triangles->log();
    }
}
