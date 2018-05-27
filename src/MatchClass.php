<?php
namespace MyApp;

abstract class States
{
    const CONNECTING = 0;
    const SETUP = 1;
    const PLAYING = 2;
    const OVER = 3;
}



class Match{
public $state;
public $player1;
public $player2;
public $ship4 = array(array(0, 0, 'allive'), array(0, 1, 'allive'), array(0, 2, 'allive'), array(0, 3, 'allive'));
public $turn;
    
/*public $array1 = array(
array($ship4,$ship4,$ship4,$ship4,0,0,0,0,0,0),
array(0,0,0,0,0,0,0,0,0,0),
array(0,0,0,0,0,0,0,0,0,0),
array(0,0,0,0,0,0,0,0,0,0),
array(0,0,0,0,0,0,0,0,0,0),
array(0,0,0,0,0,0,0,0,0,0),
array(0,0,0,0,0,0,0,0,0,0),
array(0,0,0,0,0,0,0,0,0,0),
array(0,0,0,0,0,0,0,0,0,0),
array(0,0,0,0,0,0,0,0,0,0)
);*/

/*public $array2 = array(
array($ship4,$ship4,$ship4,$ship4,0,0,0,0,0,0),
array(0,0,0,0,0,0,0,0,0,0),
array(0,0,0,0,0,0,0,0,0,0),
array(0,0,0,0,0,0,0,0,0,0),
array(0,0,0,0,0,0,0,0,0,0),
array(0,0,0,0,0,0,0,0,0,0),
array(0,0,0,0,0,0,0,0,0,0),
array(0,0,0,0,0,0,0,0,0,0),
array(0,0,0,0,0,0,0,0,0,0),
array(0,0,0,0,0,0,0,0,0,0)
);*/

public $array2 = array();
public $player1status = false;
public $player2status = false;


    function __construct(){
        $this->state = States::SETUP;
    }

    public function setFirstPlayer($conn){
        
        $this->player1=$conn;
        echo 'player1';
    }
    
    public function setSecondPlayer($conn)
    {
        $this->player2 = $conn;
        echo 'player2';
    }
    
    public function Shot($x, $y, $conn)
    {   
            if($conn==$player1){    //player1 plays on turn 0
                
                if ($turn == 0){
                    
                $ship = $array1[$x][$y]; //ship по которому прошло попадание
                if ($ship == 0 || $ship == -1 || $ship == 1) {
                    $ship[$x][$y] = -1;
                    $turn=1;

                } else {
                    $counter = 0;
                    foreach ($ship as $kek) {
                        
                        if ($kek[0] == $x && $kek[1] == $y && $kek[2] == 'allive') {
                            $kek[2] = 'dead';
                            $ship[$x][$y] = 1;
                        }
                        
                        if ($kek[2] == 'dead') {
                            
                            $counter++;
                            
                        }
                        
                    }
                    if ($counter == $ship.length) {
                        
                        
                        //this ship is dead, send to JS
                        
                    }
                    WinCheck();
                }
            
            }
            else {
                //Можем пожаловаться его маме
            }
        }

            if($conn==$player2){    //player2 plays on turn 1
                    
                if ($turn == 1){
                    
                $ship2 = $array2[$x][$y]; //ship по которому прошло попадание
                if ($ship2 == 0 || $ship2 == -1 || $ship2 == 1) {
                    $ship2[$x][$y] = -1;
                    $turn==0;

                } else {
                    $counter = 0;
                    foreach ($ship2 as $kek) {
                        
                        if ($kek[0] == $x && $kek[1] == $y && $kek[2] == 'allive') {
                            $kek[2] = 'dead';
                            $ship2[$x][$y] = 1;
                        }
                        
                        if ($kek[2] == 'dead') {
                            
                            $counter++;
                            
                        }
                        
                    }
                    if ($counter == $ship2.length) {
                        
                        
                        //this ship is dead, send to JS
                        
                    }
                    WinCheck();
                }
            
            }
            else {
                //Можем пожаловаться его маме
            }
        }
    }
    //if yes then check соответствующий array
    //substitute arraypole[x][y] with 1;
    /*
    0 - empty
    -1 - miss
    1 - player ship
    12 - player ship hit
    13 - player ship sunk
    2 - enemy hit
    3 - enemy sunk
    */
    
    public function GameStart(){
        $turn = rand(0,1);
    }
    
    
    public function SecondPlayerField()
    {   
        
    }
    
    public function Connection1()
    {
        $this->$player1status = true; //KTTC
    }
    
    public function Connection2()
    {
        $this->$player1status = true; //KTTC
    }
    
    public function WinCheck()
    {   if($turn == 0){
            $counter =0;
            foreach($ship as $kek){
                for($i = 0;$i<10;$i++){
                    if ($kek[$i] == 0 || $kek[$i] == 1 || $kek[$i] == -1){
                        $counter++;
                    }
                }
            }
            if ($counter == 0){
                //match is won
            }
        }
        if($turn==1){
            $counter =0;
            foreach($ship2 as $kek){
                for($i = 0;$i<10;$i++){
                    if ($kek[$i] == 0 || $kek[$i] == 1 || $kek[$i] == -1){
                        $counter++;
                    }
                }
            }
            if ($counter == 0){
                //match is won
            }
        }
    }
    public function GameStatus()
    {
        
    }
    
}
?>    