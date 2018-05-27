<?php
namespace MyApp;
include "MatchClass.php";
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {
    protected $connections;
    protected $matches;

    public function __construct() {
        $this->connections = array();
        $this->matches = array();
        $this->ds = mysqli_connect('localhost', 'root', '', 'registration');
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        echo "New connection! ({$conn->resourceId})\n";
        $this->connections[$conn->resourceId]=null;
        $conn->send("i'm here");

    }

    public function onMessage(ConnectionInterface $from, $msg) {
        echo "Received a message from ({$from->resourceId})\n";
        $match;
        $player;
        $db = $this->ds;
        $json = json_decode($msg);
        print_r($json);
        if($this->connections[$from->resourceId]===null){
            $unsafe_username = $json->username;
            $unsafe_match_link = $json->match;
            $username = mysqli_real_escape_string($db,$unsafe_username);
            $match_link = mysqli_real_escape_string($db,$unsafe_match_link);
            

            if(isset($username) && isset($match_link)){
                echo $match_link;
                echo $username;
                $match_query = "SELECT c.username as creator, o.username as opponent from matches m left outer join users c on m.creator_id = c.id left outer join users o on m.opponent_id = o.id where m.match_link = '$match_link'";
                $match_query_result = mysqli_query($db, $match_query);
                $match_array = mysqli_fetch_assoc($match_query_result);
                echo 'dddd';
                print_r($match_array);
                if(isset($match_array)){
                    echo 'match found';
                    if(!isset($this->matches[$match_link])){
                        $match = new Match;
                        $this->matches[$match_link] = $match;
                        $this->connections[$from->resourceId] = $match;
                    }

                    if($username == $match_array['creator'] && !isset($this->matches[$match_link]->player1) ){
                        
                        $match->setFirstPlayer($from);
                    }

                    elseif($username == $match_array['opponent'] && !isset($this->matches[$match_link]->player2)){

                        $match->setSecondPlayer($from);
                    }

                    else{
                        $from->close();
                    }
                    echo 232323232;


                }
                else{
                    $from->close();
                }


                


                
            
            }
        }
        else {
            echo "The player already has a match assigned\n";
            $match = $this->connections[$from->resourceId];
            if($from == $match->player1){
                $player = 0;
            }
            else {
                $player = 1;
            }
            echo "He's player $player of the match\n";
            if ($match->state==States::SETUP){
                echo "He sent the list of the player's ships\n";
                if($player==0){
                    echo 1;
                    $match->FirstPlayerField($json);
                    
                }
                else{
                    $match->SecondPlayerField($json);
                }
            }





            else {
                $from->close();
            }

        }
        

        
        
        
        
        
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        echo "Connection {$conn->resourceId} has disconnected\n";
        $index = array_search($conn,$this->connections);
        unset($this->connections[$index]);

        
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }




}
