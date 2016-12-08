<?php

namespace App\Presenters;

use Nette;

class RoomDecoder extends Nette\Object
{
    /**
     * @var string
     */
    public $password;
    
    /**
     * @var string
     */
    public $id;
    
    public function __construct($id) {
        $this->id = $id;       
        $this->password = '';        
    }
    
    public function hackPassword(){
        $i = 0;
        while (strlen($this->password) < 8){
            $i++;
            if (substr(md5($this->id.$i),0,5) === '00000'){
                $this->password .= md5($this->id.$i)[5];
            }
        }
    }
    
    public function hackSecondPassword(){
        $i = 0;
        $this->password = 'xxxxxxxx';
        while (strpos($this->password,'x') !== false){
            $i++;
            if (substr(md5($this->id.$i),0,5) === '00000'){
                if (((int)md5($this->id.$i)[5] < 8) && ($this->password[(int)md5($this->id.$i)[5]] === 'x')){
                    $this->password[md5($this->id.$i)[5]] = md5($this->id.$i)[6];
                }
            }
        }
    }
}

class Day5Presenter extends Nette\Application\UI\Presenter
{
    public function renderFirst(){
        $roomDecoder = new RoomDecoder('abbhdwsy');
        $roomDecoder->hackPassword();
        $this->template->password = $roomDecoder->password;
    }
    
    public function renderSecond(){
        $roomDecoder = new RoomDecoder('abbhdwsy');
        $roomDecoder->hackSecondPassword();
        $this->template->password = $roomDecoder->password;
    }
}
