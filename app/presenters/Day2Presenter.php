<?php

namespace App\Presenters;

use Nette;

class Key extends \Nette\Object
{
    /**
     * @var string
     */
    public $name;
    
    /**
     * @var string
     */
    public $u;
    
    /**
     * @var string
     */
    public $r;
    
    /**
     * @var string
     */
    public $d;
    
    /**
     * @var string
     */
    public $l;
    
    public function __construct($name,$u,$r,$d,$l) {
        $this->name = $name;
        $this->u = $u;
        $this->r = $r;
        $this->d = $d;
        $this->l = $l;
    }    
}

class Keypad extends \Nette\Object
{
    /**
     * @var string
     */
    public $position;    
    
    /**
     * @var string
     */
    private $instructions;    
    
    /**
     * @var Array
     */
    private $keys;
    
    /**
     * @var Array
     */
    private $instructionList;
    
    /**
     * @var string
     */
    public $resultCode;
    
    /**
     * @var string
     */
    public $log;
        
    public function __construct($instructions,$keypadType = 'A',$startPosition = '5') {
        $this->position = $startPosition;
        $this->instructions = $instructions;
        $this->resultCode = null;
        $this->log = null;
        
        $this->prepareInstructionList();
        
        if ($keypadType == 'A'){
            $this->keys[] = new Key('1', null, '2', '4', null);
            $this->keys[] = new Key('2', null, '3', '5', '1');
            $this->keys[] = new Key('3', null, null, '6', '2');
            $this->keys[] = new Key('4', '1', '5', '7', null);
            $this->keys[] = new Key('5', '2', '6', '8', '4');
            $this->keys[] = new Key('6', '3', null, '9', '5');
            $this->keys[] = new Key('7', '4', '8', null, null);
            $this->keys[] = new Key('8', '5', '9', null, '7');
            $this->keys[] = new Key('9', '6', null, null, '8');
        }
        if ($keypadType == 'B'){
            $this->keys[] = new Key('1', null, null, '3', null);
            $this->keys[] = new Key('2', null, '3', '6', null);
            $this->keys[] = new Key('3', '1', '4', '7', '2');
            $this->keys[] = new Key('4', null, null, '8', '3');
            $this->keys[] = new Key('5', null, '6', null, null);
            $this->keys[] = new Key('6', '2', '7', 'A', '5');
            $this->keys[] = new Key('7', '3', '8', 'B', '6');
            $this->keys[] = new Key('8', '4', '9', 'C', '7');
            $this->keys[] = new Key('9', null, null, null, '8');
            $this->keys[] = new Key('A', '6', 'B', null, null);
            $this->keys[] = new Key('B', '7', 'C', 'D', 'A');
            $this->keys[] = new Key('C', '8', null, null, 'B');
            $this->keys[] = new Key('D', 'B', null, null, null);
        }
        
        $this->process();
    }
    
    private function process(){
        foreach ($this->instructionList as $instructinRow){
            for($i = 0; $i < strlen($instructinRow); $i++){
                $this->move(strtolower($instructinRow[$i]));
            }
            $this->resultCode = $this->resultCode . $this->position;
            $this->log .= "<br /><br />";
        }
    }
    
    /**
     * @param string $instruction
     */
    private function move($instruction){
        $this->log .= "Pozice: ".$this->position.", instrukce: ".$instruction;
        foreach ($this->keys as $key => $value){
            if ($value->name === $this->position){
                if ($value->$instruction){                    
                    $this->position = $value->$instruction;
                }   
                break;
            }
        }
        $this->log .= ", výsledek: ".$this->position."<br />";
    }
    
    private function prepareInstructionList(){        
        $this->instructionList = preg_split("/(\r\n|\n|\r)/", $this->instructions); 
    }  
}


class Day2Presenter extends BasePresenter
{
    public function renderFirst(){
        $keypad = new Keypad(file_get_contents(WWW_DIR . '/instructions/day2.txt'));
        $this->template->resultCode = $keypad->resultCode;
        $this->template->log = $keypad->log;
    }
    
    public function renderSecond(){
        $keypad = new Keypad(file_get_contents(WWW_DIR . '/instructions/day2.txt'),'B');
        $this->template->resultCode = $keypad->resultCode;
        $this->template->log = $keypad->log;
    }
}
