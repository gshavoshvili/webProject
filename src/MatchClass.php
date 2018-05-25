<?php
namespace MyApp;
class Match{
public $player1;
public $player2;
public $array1 = array();
public $array2 = array();
public $player1status = false;
public $player2status = false;

    public function setFirstPlayer($conn){
        
        $this->player1=$conn;
        echo 'player1';
    }

    public function setSecondPlayer($conn){
        
        $this->player2=$conn;
        echo 'player2';
    }

    public function FirstPlayerField(){

    }

    public function SecondPlayerField(){

    }
    
    public function GameStatus(){

    }

    public function Connection1(){
        $this->$player1status=true;
    }
    
    public function Connection2 (){
        $this->$player1status=true;
    }

}
?>    