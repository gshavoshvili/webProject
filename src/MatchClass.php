<?php
namespace MyApp;
//$this->player1->send("i'm here");
//$this->player2->send("i'm here");
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
        
        if(isset($this->player2)){
            $this->state = States::SETUP;
            $this->player1->send("SETUP");
            $this->player2->send("SETUP");
            echo "Started setup\n";
        }
        else{
            $this->player1->send("CONNECTED");
        }

    }
    
    public function setSecondPlayer($conn)
    {
        $this->player2 = $conn;

        if(isset($this->player1)){
            $this->state = States::SETUP;
            $this->player1->send("SETUP");
            $this->player2->send("SETUP");
            echo "Started setup\n";
        }
        else{
            $this->player2->send("CONNECTED");
        }
    }
    
    public function Shot($json, $conn)
    {       

            $x;
            $y;
            $arr1 = array();
            $arr2 = array();
            if(count($json)==2){
               $x = (int) $json[0];
               $y = (int) $json[1];
            }

            else{
                $conn->close();
                return;
            }

            if($conn==$this->player1){    //player1 plays on turn 0
                
                if ($this->turn == 0){
                    
                $ship = &$this->array2[$x][$y]; //ship по которому прошло попадание
                print_r($ship);
                if ($ship == 0 || $ship == -1 || $ship == 1) {
                    $this->array2[$x][$y] = -1;
                    $this->turn=1;
                    $arr1[] = array($x,$y,-1);
                    $arr2[] = array($x,$y,-1); 
                    $arr2[] = 'URTURN';
                    $arr1[] = 'ENDTURN';

                } else {
                    echo "It's a hit!";
                    $counter = 0;
                    foreach ($ship as &$kek) {
                        
                        if ($kek[0] == $x && $kek[1] == $y && $kek[2] == 'alive') {
                            $kek[2] = 'dead';
                            $this->array2[$x] = array_replace($this->array2[$x],array($y=>1));
                            print_r($ship);

                        }
                        
                        if ($kek[2] == 'dead') {
                            
                            $counter++;
                            echo '++\n';
                        }
                        
                    }
                    echo "Counter: $counter\n";
                    if ($counter == count($ship)) {
                        
                        
                        
                        
                        foreach($ship as &$kek){
                            $arr1[] = array($kek[0],$kek[1],3);
                            $arr2[] = array($kek[0],$kek[1],13);
                        }
                       
                        $this->WinCheck();
                        
                    }
                    else {
                        $arr1[] = array($x,$y,2);
                        $arr2[] = array($x,$y,12);
                    }
                    
                        
                    
                    
                }
            
            }
            else {
                $conn->close();
                return;
            }
        }

            if($conn==$this->player2){    //player2 plays on turn 1
                    
                if ($this->turn == 1){
                    
                $ship2 = &$this->array1[$x][$y]; //ship по которому прошло попадание
                print_r($ship2);
                if ($ship2 == 0 || $ship2 == -1 || $ship2 == 1) {
                    $this->array1[$x][$y] = -1;
                    $this->turn=0;
                    
                    $arr1[] = array($x,$y,-1);
                    $arr2[] = array($x,$y,-1); 
                    $arr1[] = 'URTURN';
                    $arr2[] = 'ENDTURN';

                } else {
                    $counter = 0;
                    foreach ($ship2 as &$kek) {
                        
                        if ($kek[0] == $x && $kek[1] == $y && $kek[2] == 'alive') {
                            $kek[2] = 'dead';
                            $this->array1[$x] = array_replace($this->array1[$x],array($y=>1));
                            print_r($ship2);
                        }
                        
                        if ($kek[2] == 'dead') {
                            
                            $counter++;
                            
                        }
                        
                    }
                    if ($counter == count($ship2)) {
                        
                        
                        echo "He sunk the ship!\n";
                        print_r($ship2);
                        foreach($ship2 as &$kek){
                            $arr1[] = array($kek[0],$kek[1],13);
                            $arr2[] = array($kek[0],$kek[1],3);
                        }
                        $this->WinCheck();
                        
                    }
                    else {
                        $arr1[] = array($x,$y,12);
                        $arr2[] = array($x,$y,2);
                    }
                    

                    
                    
                }
            
            }
            else {
                $conn->close();
                return;
            }
        }

        $this->player1->send(json_encode($arr1));
        $this->player2->send(json_encode($arr2));



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
        
        $this->turn = rand(0,1);
        if($this->turn==0){
            $this->player1->send("USTART");
            $this->player2->send("START");
        }
        if($this->turn==1){
            $this->player2->send("USTART");
            $this->player1->send("START");
        }
        $this->state = States::PLAYING;     
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
                        $shipArr[] = array($x+(($rot==0)?$i:0),$y+(($rot==1)?$i:0),'alive');
                    }
                    else{
                        $canPlace = false;
                        break;
                    }
                }
                $this->ships1[$n]=$shipArr;
                for ($i = 0; $i<count($shipArr); $i++){
                    $this->array1[$shipArr[$i][0]][$shipArr[$i][1]]=&$this->ships1[$n];
                }
                if(!$canPlace){
                    $this->player1->close();return;
                }
                else{
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
            //print_r($this->array1);
            //print_r($this->ships1);

            if($this->player2FieldReady){
                $this->GameStart();
            }
            else{
                $this->player1->send("GOOD");
            }


        }



        else{
            $this->player1->close();return;
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
                        $shipArr[] = array($x+(($rot==0)?$i:0),$y+(($rot==1)?$i:0),'alive');
                    }
                    else{
                        $canPlace = false;
                        break;
                    }
                }
                $this->ships2[$n]=$shipArr;
                for ($i = 0; $i<count($shipArr); $i++){
                    $this->array2[$shipArr[$i][0]][$shipArr[$i][1]]=&$this->ships2[$n];
                }
                if(!$canPlace){
                    $this->player2->close();return;
                }
                else{
                    
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
                $this->GameStart();
            }
            else{
                $this->player2->send("GOOD");
            }



        }



        else{
            $this->player2->close();return;
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
    {   if($this->turn == 0){
            $counter =0;
            foreach($this->array2 as $kek){
                for($i = 0;$i<10;$i++){
                    if ($kek[$i] == 0 || $kek[$i] == 1 || $kek[$i] == -1){
                        
                    }
                    else {$counter++;echo "counter: $counter\n";}
                }
            }
            if ($counter == 0){
                $this->player1->send("UWIN");
                $this->player2->send("ULOSE");
                $this->state = States::OVER;
            }
        }
        if($this->turn==1){
            $counter =0;
            foreach($this->array1 as $kek){
                for($i = 0;$i<10;$i++){
                    if ($kek[$i] == 0 || $kek[$i] == 1 || $kek[$i] == -1){
                        
                    }
                    else{
                        $counter++;
                        echo "counter: $counter\n";
                    }
                }
            }
            if ($counter == 0){
                
                $this->player2->send("UWIN");
                $this->player1->send("ULOSE");
                $this->state = States::OVER;
            }
        }
    }
    public function GameStatus()
    {
        
    }
    
}
?>    