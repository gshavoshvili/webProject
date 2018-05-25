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
        $this->connections[]=$conn;
        echo "New connection! ({$conn->resourceId})\n";
       /* 
            if (count($this->connections)==2){
            $first = rand(0,1);
            $this->connections[$first]->send("START");
            echo "Game started: player $first is first";
        }
       */
        
    }

    public function onMessage(ConnectionInterface $from, $jsmessage) {
        $db = $this->ds;
        $json = json_decode($jsmessage);
        $unsafe_username = $json->username;
        $unsafe_match_link = $json->match;
        $username = mysqli_real_escape_string($db,$unsafe_username);
        $match_link = mysqli_real_escape_string($db,$unsafe_match_link);
        

        if(isset($username) && isset($match_link)){
            echo $match_link;
            echo $username;
            $match_query = "SELECT c.username as creator, o.username as opponent from matches m join users c on m.creator_id = c.id join users o on m.opponent_id = o.id where m.match_link = '$match_link' LIMIT 1";
            $match_query_result = mysqli_query($db, $match_query);
            $match_array = mysqli_fetch_assoc($match_query_result);
            echo 'dddd';
            echo $match_array;
            if(isset($match_array)){
                echo 'match found';
                if(!isset($this->matches[$match_link1])){
                    $this->matches[$match_link1] = new Match;
                }

                if($username == $match_array['creator'] && !isset($this->matches[$match_link1]->player1) ){
                    $this->matches[$match_link1]->setFirstPlayer($from);
                }

                elseif($username == $match_array['opponent'] && !isset($this->matches[$match_link1]->player2)){
                    $this->matches[$match_link1]->setSecondPlayer($from);
                }

                else{
                    $from->close();
                }


            }
            else{
                $from->close();
            }


            


            
           
        }
        
        else {
            $from->close();
        }
        
        
        
        
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->connections->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
