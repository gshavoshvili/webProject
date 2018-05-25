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
        $match_link = mysqli_real_escape_string($db,$unsafe_username);
        

        if(isset($username) && isset($match_link)){
            
            $match_link_creator_query = "SELECT match_link from matches where match_link = '$match_link' AND (creator_id = (select id from users where username = '$username') ) LIMIT 1";
            $result = mysqli_query($db, $match_link_creator_query);
            $match_link1 = mysqli_fetch_assoc($result);
            
            $match_link_opponent_query = "SELECT match_link from matches where match_link = '$match_link' AND opponent_id = (select id from users where username = '$username')) Limit 1 ";
            $result = mysqli_query($db, $match_link_creator_query);
            $match_link2 = mysqli_fetch_assoc($result);

            if(isset($match_link1)){
                
                if(isset($this->matches[$match_link1])){
                    $this->matches[$match_link1] -> setSecondPlayer($from);
                }
                else{
                    $this->matches[$match_link1] = new Match;
                    $this->matches[$match_link1] -> setFirstPlayer($from);
                }
            }

            elseif(isset($match_link2)){

                

            }


            
            elseif(){
            
            }
        }
        
        
        $numRecv = count($this->connections) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $jsmessage, $numRecv, $numRecv == 1 ? '' : 's');

        foreach ($this->connections as $client) {
            if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
                $client->send($jsmessage);
            }
        
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
