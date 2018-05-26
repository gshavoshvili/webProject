<?php
namespace MyApp;
class Match
{
    public $player1;
    public $player2;
    
    public $ship4 = array(array(0, 0, 'allive'), array(0, 1, 'allive'), array(0, 2, 'allive'), array(0, 3, 'allive'));
    
    $ship4 = array(     
        array(0,0,'allive'),
        array(0,1,'allive'),
        array(0,2,'allive'),
        array(0,3,'allive')
    );

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
    public $player1status = false;
    public $player2status = false;
    
    public function setFirstPlayer($conn)
    {
        $this->player1 = $conn;
        echo 'player1';
    }
    
    public function setSecondPlayer($conn)
    {
        $this->player2 = $conn;
        echo 'player2';
    }
    
    public function Shot($x, $y)
    {
        
        $ship = $array1[$x][$y]; //ship по которому прошло попадание
        if ($ship == 0 || $ship == -1 || $ship == 1) {
            $ship[$x][$y] = -1;
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
    {   $counter =0;

        foreach($ship as $kek){
            for($i = 0;$i<10;i++){
                if ($kek[i] == 0 || $kek[i] == 1 || $kek[i] == -1){
                    $counter++;
                }
            }
        }
        if ($counter == 0){
            //match is won
        }
    }
    public function GameStatus()
    {
        
    }
    
}
?>    