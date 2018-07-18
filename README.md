# Battleships
## First of all how to run this ?

To run our project, move the files to your local server and import our pre-made database registration.sql (MySQL), then run …bin/SocketServer.php with php.exe through the terminal.  Now that everything is running, to simulate 2 different users from the web, you should create 2 accounts and log into both . To "trick" the session system you can use incognito windows (ctrl + shift + n on Chrome).
For matchmaking, we are using a URL generation system. As one user, generate a link and paste it into the other user's address bar. If you have done everything correctly both users now will connect to the  SocketServer and the game will start.
## How to play ?
You can drag and drop ships manually, or you can press F to place them randomly, after you finish positioning, press S.

## How it works ?
When any logged user generates a link a new record – match is created in the DB. This match record contains a unique id generated for the link and both players’ ids. The person who it was created by is added to creators_id column and now if another person tries to connect to the game we check: 
1) Whether he is logged in 
2) Whether he has a unique match id in his address bar.
3) A match with his unique link exists and it has an empty place

If all conditions are true he will be allowed to connect and his users id will be added to match’s empty slot.
Our client is written in JavaScript. Because it is extremely easy to fabricate game state for the client, the true game state resides on the server and all actions are checked. If we notice cheating, the user will be kicked.
