<?php

namespace App\Presenters;

class Decryptor
{    
    /**
     * @param string $input
     * @param int $moves
     * @param string $unknownCharacter
     * @return string
     */
    public static function caesarDecrypt($input,$moves,$unknownCharacter = ' '){
        $decrypted = '';
        $alphabet = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'];
        $realMoves = $moves % 26;
        
        for ($i = 0; $i < strlen($input); $i++){
            $key = array_search($input[$i], $alphabet);
            if ($key){
                $decrypted .= $alphabet[($key+$realMoves)%26];
            } else {
                $decrypted .= $unknownCharacter;
            }    
        }
        
        return $decrypted;
    }
}

class RoomList
{
    /**
     * @var array
     */
    private $rooms;
    
    /**
     * @param string $input
     */
    public function __construct($input) {                       
        $roomInfos = preg_split("/(\r\n|\n|\r)/", $input);        
        foreach ($roomInfos as $roomInfo){
            $this->rooms[] = new Room($roomInfo);
        }
    }
    
    /**
     * @return int
     */
    public function getSectorSum(){
        $sectorSum = 0;
        foreach ($this->rooms as $room){
            if ($room->real){
                $sectorSum += $room->sector;
            }
        }
        return $sectorSum;
    }
    
    /**
     * @return string
     */
    public function getDecryptedNames(){
        $namesLog = '';
        foreach ($this->rooms as $room){
            if ($room->real){
                $namesLog .= Decryptor::caesarDecrypt($room->text, $room->sector) . ' sector: ' . $room->sector . '<br />';
            }
        }
        
        return $namesLog;
    }
}

class Room
{
    /**
     * @var string
     */
    private $fullInfo;
    
    /**
     * @var string
     */
    public $text;
    
    /**
     * @var int
     */
    public $sector;
    
    /**
     * @var string
     */
    private $checksum;
    
    /**
     * @param string $roomInfo
     */
    
    /**
     * @var array
     */
    private $letters;
    
    /**
     * @var bool 
     */
    public $real;
    
    public function __construct($roomInfo) {
        $this->letters = [
            'a' => 0, 
            'b' => 0, 
            'c' => 0, 
            'd' => 0, 
            'e' => 0, 
            'f' => 0, 
            'g' => 0, 
            'h' => 0, 
            'i' => 0, 
            'j' => 0, 
            'k' => 0, 
            'l' => 0, 
            'm' => 0, 
            'n' => 0, 
            'o' => 0, 
            'p' => 0, 
            'q' => 0, 
            'r' => 0, 
            's' => 0, 
            't' => 0, 
            'u' => 0, 
            'v' => 0, 
            'w' => 0, 
            'x' => 0, 
            'y' => 0, 
            'z' => 0,             
        ];
        
        $this->fullInfo = $roomInfo;
        
        $roomInfoArray = $this->extract($roomInfo);
        
        $this->text = $this->extractText($roomInfoArray);
        $this->sector = $this->extractSector($roomInfoArray);        
        $this->checksum = $this->extractChecksum($roomInfoArray);       
        
        $this->countLetters();        
        $this->real = $this->isReal();
    }
    
    /**
     * @return boolean
     */
    public function isReal(){
        $comparationChecksum = $this->getRealChecksum();
        $isReal = false;
        
        if ($comparationChecksum == $this->checksum){
            $isReal = true;
        }
        
        return $isReal;
    }
    
    private function getRealChecksum(){
        $realChecksum = '';
        $actualValue = max($this->letters);
        
        while ($actualValue >= 0){
            if (strlen($realChecksum) < 5){
                foreach ($this->letters as $key=>$value){
                    if ($actualValue == $value){
                        $realChecksum .= $key;
                        if (strlen($realChecksum) == 5){
                            break;
                        }
                    }
                }
            }
            $actualValue--;
        }
        return $realChecksum;
    }
    
    private function countLetters(){
        for ($i = 0; $i < strlen($this->text); $i++){
            if ($this->text[$i] != '-'){
                $this->letters[$this->text[$i]]++;
            }
        }
    }
    
    /**
     * @param string $roomInfo
     * @return array
     */
    private function extract($roomInfo){
        $matches = null;
        preg_match('~(^.*)-([0-9]+)\[(.*)\]~', $roomInfo, $matches);
        return $matches;
    }
    
    /**
     * @param array $matches
     * @return string
     */
    private function extractText($matches){
        return $matches[1];
    }
    
    /**
     * 
     * @param array $matches
     * @return int
     */
    private function extractSector($matches){
        return (int) $matches[2];
    }
    
    /**
     * @param array $matches
     * @return string
     */
    private function extractChecksum($matches){
        return $matches[3];
    }
}

class Day4Presenter extends BasePresenter
{
    public function renderFirst(){
        $roomList = new RoomList(file_get_contents(WWW_DIR . '/instructions/day4.txt'));
        $this->template->sectorSum = $roomList->getSectorSum();
    }
    
    public function renderSecond(){
        $roomList = new RoomList(file_get_contents(WWW_DIR . '/instructions/day4.txt'));
        $this->template->names = $roomList->getDecryptedNames();
    }
}
