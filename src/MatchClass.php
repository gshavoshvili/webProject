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
public $ships1;
public $ships2;

public $turn;
public $player1FieldReady;
public $player2FieldReady;    

public $player1status = false; // TO BE CHANGED
public $player2status = false; // TO BE CHANGED


    function __construct(){
        $this->state = States::SETUP;
        $this->player1FieldReady = false;
        $this->player2FieldReady = false;    
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
                $conn->close();
                return;
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
                $conn->close();
                return;
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
    
    public function FirstPlayerField($json)
    {   if(count($json)==10){

            $this->array1 = array(
                array(0,0,0,0,0,0,0,0,0,0),
                array(0,0,0,0,0,0,0,0,0,0),
                array(0,0,0,0,0,0,0,0,0,0),
                array(0,0,0,0,0,0,0,0,0,0),
                array(0,0,0,0,0,0,0,0,0,0),
                array(0,0,0,0,0,0,0,0,0,0),
                array(0,0,0,0,0,0,0,0,0,0),
                array(0,0,0,0,0,0,0,0,0,0),
                array(0,0,0,0,0,0,0,0,0,0),
                array(0,0,0,0,0,0,0,0,0,0)
                );

            $this->ships1 = array();
                
            $shipLengths = array(
                0=>4,
                1=>3,
                2=>3,
                3=>2,
                4=>2,
                5=>2,
                6=>1,
                7=>1,
                8=>1,
                9=>1
            );
            for ($n = 0; $n<10; $n++){
                $ship = $json[$n];
                $shipArr = array();
                $x = (int) $ship[0];
                $y = (int) $ship[1];        
                $rot = (int) $ship[2];
                $length = $shipLengths[$n];

                $canPlace = true;
                
                for ($i = 0; $i<$length;$i++){
                    if($this->array1[$x+(($rot==0)?$i:0)][$y+(($rot==1)?$i:0)]==0){
                        $this->array1[$x+(($rot==0)?$i:0)][$y+(($rot==1)?$i:0)]=$shipArr;
                        $shipArr[] = array($x,$y,'alive');
                    }
                    else{
                        $canPlace = false;
                        break;
                    }
                }
                if(!$canPlace){
                    $player1->close();return;
                }
                else{
                    $this->ships1[]=$shipArr;
                    if($rot==0){
                        for ($i = ($x>0?-1:0); $i<= $length -(($x+$length<10)?0:1) ;$i++){
                           if($y>0){
                             
                                $this->array1[$x+$i][$y-1]=-1;
                           }
                           if($y<9) {
                               
                               $this->array1[$x+$i][$y+1]=-1;
                           }
                        }
                    if($x>0){
                        
                        $this->array1[$x-1][$y]=-1;
                    } 
                    if($x+$length<10){
                        
                        $this->array1[$x+$length][$y]=-1;
                    } 
        
                    } 
        
                    else{
                        for ($i = (($y)>0?-1:0); $i<= $length -(($y+$length<10)?0:1) ;$i++){
                           if($x>0){
                                
                                $this->array1[$x-1][$y+$i]=-1;
                           } 
                           if($x<9){
                                
                                $this->array1[$x+1][$y+$i]=-1;
                           } 
                        }
                    if($y>0){
                        $this->array1[$x][$y-1]=-1;
                    } 
                    if($y+$length<10) {
                        
                        $this->array1[$x][$y+$length]=-1;
                    }
                    }





                }





            }

            for($i = 0; $i<10; $i++){
                for($j = 0; $j<10; $j++){
                    if($this->array1[$i][$j] == -1){
                        $this->array1[$i][$j]=0;
                    }
                }
            }

            $this->player1FieldReady = true;
            print_r($this->array1);
            print_r($this->ships1);

            if($this->player2FieldReady){
                GameStart();
            }
            else{
                $player1->send("GOOD");
            }


        }



        else{
            $player1->close();return;
        }

    }
    
    public function SecondPlayerField($json)
    {   if(count($json)==10){

            $this->array2 = array(
                array(0,0,0,0,0,0,0,0,0,0),
                array(0,0,0,0,0,0,0,0,0,0),
                array(0,0,0,0,0,0,0,0,0,0),
                array(0,0,0,0,0,0,0,0,0,0),
                array(0,0,0,0,0,0,0,0,0,0),
                array(0,0,0,0,0,0,0,0,0,0),
                array(0,0,0,0,0,0,0,0,0,0),
                array(0,0,0,0,0,0,0,0,0,0),
                array(0,0,0,0,0,0,0,0,0,0),
                array(0,0,0,0,0,0,0,0,0,0)
                );

            $this->ships2 = array();
                
            $shipLengths = array(
                0=>4,
                1=>3,
                2=>3,
                3=>2,
                4=>2,
                5=>2,
                6=>1,
                7=>1,
                8=>1,
                9=>1
            );
            for ($n = 0; $n<10; $n++){
                $ship = $json[$n];
                $shipArr = array();
                $x = (int) $ship[0];
                $y = (int) $ship[1];        
                $rot = (int) $ship[2];
                $length = $shipLengths[$n];

                $canPlace = true;
                
                for ($i = 0; $i<$length;$i++){
                    if($this->array2[$x+(($rot==0)?$i:0)][$y+(($rot==1)?$i:0)]==0){
                        $this->array2[$x+(($rot==0)?$i:0)][$y+(($rot==1)?$i:0)]=$shipArr;
                        $shipArr[] = array($x,$y,'alive');
                    }
                    else{
                        $canPlace = false;
                        break;
                    }
                }
                if(!$canPlace){
                    $player2->close();return;
                }
                else{
                    $this->ships2[]=$shipArr;
                    if($rot==0){
                        for ($i = ($x>0?-1:0); $i<= $length -(($x+$length<10)?0:1) ;$i++){
                        if($y>0){
                            
                                $this->array2[$x+$i][$y-1]=-1;
                        }
                        if($y<9) {
                            
                            $this->array2[$x+$i][$y+1]=-1;
                        }
                        }
                    if($x>0){
                        
                        $this->array2[$x-1][$y]=-1;
                    } 
                    if($x+$length<10){
                        
                        $this->array2[$x+$length][$y]=-1;
                    } 
        
                    } 
        
                    else{
                        for ($i = (($y)>0?-1:0); $i<= $length -(($y+$length<10)?0:1) ;$i++){
                        if($x>0){
                                
                                $this->array2[$x-1][$y+$i]=-1;
                        } 
                        if($x<9){
                                
                                $this->array2[$x+1][$y+$i]=-1;
                        } 
                        }
                    if($y>0){
                        $this->array2[$x][$y-1]=-1;
                    } 
                    if($y+$length<10) {
                        
                        $this->array2[$x][$y+$length]=-1;
                    }
                    }
                }





            }
            for($i = 0; $i<10; $i++){
                for($j = 0; $j<10; $j++){
                    if($this->array2[$i][$j] == -1){
                        $this->array2[$i][$j]=0;
                    }
                }
            }
            $this->player2FieldReady = true;
            print_r($this->array2);
            print_r($this->ships2);
            if($this->player1FieldReady){
                GameStart();
            }
            else{
                $player2->send("GOOD");
            }



        }



        else{
            $player2->close();return;
        }
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