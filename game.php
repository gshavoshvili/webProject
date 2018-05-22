<?php 
 session_start();
 if(isset($_GET['match']) && isset($_SESSION['username'])){
    //variables needed for future
    $db = mysqli_connect('localhost', 'root', '', 'registration');
    $match_link = $_GET['match'];
    $username = $_SESSION['username'];
    //checking if sent ID is valid
    $match_link_check_query = "SELECT match_link FROM matches WHERE match_link='$match_link' LIMIT 1";
    $match_result = mysqli_query($db, $match_link_check_query);
    $match_link_array = mysqli_fetch_assoc($match_result);

    if (isset($match_link_array['match_link'])) {

        $username_check_query = "SELECT username from users, matches WHERE users.username='$username' AND users.id = matches.id AND matches.match_link = '$match_link' LIMIT 1 ";
        $username_result = mysqli_query($db,$username_check_query);
        $username_array = mysqli_fetch_assoc($username_result);
        if (isset($username_array['username'])) {
            
        }
        else {

        $opponent_fill_query = "UPDATE matches m SET opponent_id=(SELECT users.id FROM users WHERE users.username = '$username' and users.id = m.id) WHERE m.match_link = '$match_link'";      

        }


    }

    else {header("Location: index.php"); die();}

    
            
 }
 else {header("Location: index.php"); die();}




?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
    body{
        background:grey;
    }
    </style>
</head>
<body>

<!-- Tabindex to make it focusable -->
<canvas tabindex="1" id="Canvas" width="700" height="600" style="border: 1px solid black"></canvas>
<script>
//Game variables
var canvas = document.getElementById("Canvas");
var ctx = canvas.getContext("2d");
var mousePosition = {x:0,y:0};
var myFieldXOffset = 50.5;
var enemyFieldXOffset = 400.5;
var cellWidth = 25;
var dotRadius = 3.5;
var crossHalfWidth = 9;
var state = 0;
var myTurn = false;


//Drag&Drop
var dragging = null;
var dragOffset = null;
var ships = [{
initPos:  [200.5,100.5],
pos: [200.5,100.5],
width: cellWidth,
height:cellWidth*4,
cell:null, // RENAME
cells: [],
around: [],
rot:1
},
{
initPos:  [250.5,100.5],
pos: [250.5,100.5],
width: cellWidth*3,
height:cellWidth,
cell:null, // RENAME
cells: [],
around: [],
rot:0
},
{
initPos:  [250.5,175.5],
pos: [250.5,175.5],
width: cellWidth*3,
height:cellWidth,
cell:null, // RENAME
cells: [],
around: [],
rot:0
}


]


canvas.addEventListener('mousedown',mouseDownHandler, false);
function mouseDownHandler(e){
    for(var i = 0; i<ships.length;i++){
        var ship=ships[i];
        if (e.offsetX>ship.pos[0] && e.offsetX<ship.pos[0]+ship.width && e.offsetY>ship.pos[1] && e.offsetY<ship.pos[1]+ship.height){
        dragging=ship;
        dragOffset=[e.offsetX-ship.pos[0],e.offsetY-ship.pos[1]];
        dragging.around.forEach(function(e){
            myField[e[0]][e[1]]++;
        });
        dragging.around=[];
        dragging.cells.forEach(function(e){
            myField[e[0]][e[1]]=0;
        })
        dragging.cell=dragging.cells[0];
        break;
        }
    }
    
}
canvas.addEventListener('mousemove',mouseMoveHandler, false);
function mouseMoveHandler(e){
    mousePosition.x = e.offsetX;
    mousePosition.y = e.offsetY;
    if (dragging!=null){
        if(dragging.cell!=null){
            myField[dragging.cell[0]][dragging.cell[1]] = 0;
            dragging.cell=null;
            dragging.cells=[];
        }
        
        dragging.pos=[e.offsetX-dragOffset[0],e.offsetY-dragOffset[1]];
        if (dragging.pos[0]>myFieldXOffset-cellWidth/2 && dragging.pos[0]<myFieldXOffset-cellWidth/2+250-(dragging.width-25) && dragging.pos[1]>250.5-cellWidth/2 && dragging.pos[1]<500.5-cellWidth/2-(dragging.height-25)){
            var x = Math.floor((dragging.pos[0]-myFieldXOffset+cellWidth/2)/cellWidth);
            var y = Math.floor((dragging.pos[1]-250.5+cellWidth/2)/cellWidth);
            var canPlace = true;
            dragging.cell = [x,y];
            dragging.cells=[];
            for (var i = 0; i<((dragging.rot==0)?dragging.width:dragging.height)/25;i++){
                if(myField[dragging.cell[0]+((dragging.rot==0)?i:0)][dragging.cell[1]+((dragging.rot==1)?i:0)]==0){
                    dragging.cells.push([dragging.cell[0]+((dragging.rot==0)?i:0),dragging.cell[1]+((dragging.rot==1)?i:0)])
                }
                else{
                    canPlace = false;
                    break;
                }
               }
            if(!canPlace){
                dragging.cells=[];
                dragging.cell=null;
            }
        }
    }
}
canvas.addEventListener('mouseup',mouseUpHandler, false);
function mouseUpHandler(e){
    if(dragging!=null ){
        if (dragging.cell==null){
            dragging.pos=dragging.initPos;
            if(dragging.rot==1){
                rotateShip(dragging);
            }
            
        }
        else{
            dragging.pos=[dragging.cell[0]*25+myFieldXOffset,dragging.cell[1]*25+250.5]
            
            // dots around
            if(dragging.rot==0){
                for (var i = ((dragging.cell[0])>0?-1:0); i<= dragging.width/25 -((dragging.cell[0]+dragging.width/25<10)?0:1) ;i++){
                   if(dragging.cell[1]>0){
                        dragging.around.push([dragging.cell[0]+i,dragging.cell[1]-1]);
                        myField[dragging.cell[0]+i][dragging.cell[1]-1]--;
                   }
                   if(dragging.cell[1]<9) {
                       dragging.around.push([dragging.cell[0]+i,dragging.cell[1]+1]);
                       myField[dragging.cell[0]+i][dragging.cell[1]+1]--;
                   }
                }
            if(dragging.cell[0]>0){
                dragging.around.push([dragging.cell[0]-1,dragging.cell[1]]);
                myField[dragging.cell[0]-1][dragging.cell[1]]--;
            } 
            if(dragging.cell[0]+dragging.width/25<10){
                dragging.around.push([dragging.cell[0]+dragging.width/25,dragging.cell[1]]);
                myField[dragging.cell[0]+dragging.width/25][dragging.cell[1]]--;
            } 

            } 

            else{
                for (var i = ((dragging.cell[1])>0?-1:0); i<= dragging.height/25 -((dragging.cell[1]+dragging.height/25<10)?0:1) ;i++){
                   if(dragging.cell[0]>0){
                        dragging.around.push([dragging.cell[0]-1,dragging.cell[1]+i]);
                        myField[dragging.cell[0]-1][dragging.cell[1]+i]--;
                   } 
                   if(dragging.cell[0]<9){
                        dragging.around.push([dragging.cell[0]+1,dragging.cell[1]+i]);
                        myField[dragging.cell[0]+1][dragging.cell[1]+i]--;
                   } 
                }
            if(dragging.cell[1]>0){
                dragging.around.push([dragging.cell[0],dragging.cell[1]-1]);
                myField[dragging.cell[0]][dragging.cell[1]-1]--;
            } 
            if(dragging.cell[1]+dragging.height/25<10) {
                dragging.around.push([dragging.cell[0],dragging.cell[1]+dragging.height/25])
                myField[dragging.cell[0]][dragging.cell[1]+dragging.height/25]--;
            }
            }
            // end dots      
            
            dragging.cells.forEach(function(e){
                myField[e[0]][e[1]]=1;
            });
            dragging.cell=null;
        }
        dragging=null;
    }
}

var alreadyRotated = false;
canvas.addEventListener('keydown',keyDownHandler, false);
function keyDownHandler(e){
    if(e.code == 'KeyR'){
        if(!alreadyRotated && dragging!=null){
            rotateShip(dragging);
            mouseMoveHandler({offsetX:mousePosition.x, offsetY:mousePosition.y});
            alreadyRotated=true;
        }
    }
    
}
canvas.addEventListener('keyup',keyUpHandler, false);
function keyUpHandler(e){
    if(e.code == 'KeyR'){
        alreadyRotated=false;
    }
    
}

//WebSocket connection
var conn = new WebSocket('ws://localhost:8080');
conn.onopen = function(e) {
    console.log("Connection established!");
    var i = 1;
};

conn.onmessage = function(e) {
    if(e.data=='START'){
        myTurn=true;
    }
    else {
        clicked.push(JSON.parse(e.data));
        myTurn=true;
    }
    
};
    


//Game logic


/*
The game works with the (x,y) system 
but the matrices work with (y,x) so 
we have to transpose the matrices entered
by hand
*/

/*
0 - empty
-1 - miss
1 - player ship
12 - player ship hit
13 - player ship sunk
2 - enemy hit
3 - enemy sunk


*/
var myFieldTransposed = [ 
[0,0,0,0,0,0,0,0,0,0],
[0,0,0,0,0,0,0,0,0,0],
[0,0,0,0,0,0,0,0,0,0],
[0,0,0,0,0,0,0,0,0,0],
[0,0,0,0,0,0,0,0,0,0],
[0,0,0,0,0,0,0,0,0,0],
[0,0,0,0,0,0,0,0,0,0],
[0,0,0,0,0,0,0,0,0,0],
[0,0,0,0,0,0,0,0,0,0],
[0,0,0,0,0,0,0,0,0,0]
];




/*                        [ 
[1,12,12,0,0,0,-1,0,0,0],
[0,0,0,0,0,0,0,1,0,0],
[-1,-1,-1,1,1,0,0,1,0,0],
[-1,13,-1,0,0,0,0,0,0,0],
[-1,13,-1,0,0,1,0,0,-1,0],
[-1,13,-1,0,0,0,0,0,0,0],
[-1,-1,-1,1,0,0,1,12,12,4],
[0,0,0,1,0,0,0,0,0,0],
[0,0,0,1,0,0,-1,0,0,0],
[0,0,0,1,0,0,0,0,0,0]
];*/

var myField = [];
for(var i = 0; i < myFieldTransposed.length; i++){
    myField.push([]);
};

for(var i = 0; i < myFieldTransposed.length; i++){
    for(var j = 0; j < myFieldTransposed.length; j++){
        myField[j].push(myFieldTransposed[i][j]);
    };
};

var enemyField = [ 
[0,0,0,0,0,0,0,0,0,0],
[0,0,0,0,0,0,0,0,0,0],
[0,0,0,0,0,0,0,0,0,0],
[0,0,0,0,0,0,0,0,0,0],
[0,0,0,0,0,0,0,0,0,0],
[0,0,0,0,0,0,0,3,0,0],
[0,0,0,0,0,0,0,3,0,0],
[0,0,0,0,0,0,0,3,0,0],
[0,0,0,0,0,0,0,0,0,0],
[0,0,0,0,0,0,0,0,0,0]
]



canvas.addEventListener("click", clickHandler, false);
function clickHandler(e){
    if (e.offsetX>enemyFieldXOffset && e.offsetX<enemyFieldXOffset+250 && e.offsetY>250.5 && e.offsetY<500.5){
        var x = e.offsetX-enemyFieldXOffset;
        var y = e.offsetY-250.5;
        var gridX = Math.floor(x/cellWidth);
        var gridY = Math.floor(y/cellWidth);
        if (enemyField[gridX][gridY] == -1){
            enemyField[gridX][gridY] = 2;
        }
        else {
            enemyField[gridX][gridY] = -1;
            }
        console.log(enemyField);
    }
    if(myTurn) {
    var click = [e.offsetX,e.offsetY];
    conn.send(JSON.stringify(click));
    clicked.push(click);
    myTurn=false;
    }
}


function rotateShip(ship){

    var temp = ship.width;
    ship.width = ship.height;
    ship.height= temp;
    if(ship.rot == 0){
        ship.rot = 1;
        temp = dragOffset[1];
        dragOffset[1] = dragOffset[0];
        dragOffset[0] = 25-temp;
    }
    else{
        ship.rot = 0;
        temp = dragOffset[1];
        dragOffset[1] = 25-dragOffset[0];
        dragOffset[0] = temp;
    }
            
}

function drawBG(){
ctx.clearRect(0, 0, canvas.width, canvas.height);


ctx.fillStyle="white";
ctx.fillRect(0, 0, canvas.width, canvas.height);
ctx.lineWidth=1;
ctx.strokeStyle="black";
var currentlyAt = cellWidth;
while( currentlyAt!=canvas.width){
    ctx.beginPath()
    ctx.moveTo(currentlyAt+0.5,0);
    ctx.lineTo(currentlyAt+0.5,canvas.height);
    ctx.stroke();
    currentlyAt+=cellWidth;
}
currentlyAt = cellWidth;
while( currentlyAt!=canvas.width){
    ctx.beginPath()
    ctx.moveTo(0,currentlyAt+0.5);
    ctx.lineTo(canvas.width,currentlyAt+0.5);
    ctx.stroke();
    currentlyAt+=cellWidth;
}

}

function drawCross(centerX,centerY,color){
    ctx.strokeStyle=color;
    ctx.beginPath();
    ctx.moveTo(centerX - crossHalfWidth, centerY - crossHalfWidth);
    ctx.lineTo(centerX + crossHalfWidth, centerY + crossHalfWidth);
    ctx.moveTo(centerX + crossHalfWidth, centerY - crossHalfWidth);
    ctx.lineTo(centerX - crossHalfWidth, centerY + crossHalfWidth);
    ctx.stroke();
}

function draw(){

    //Make it look like a page of a notebook
    drawBG();
    
    
    //Draw the players' zones 
    ctx.lineWidth=3;
    ctx.strokeStyle="darkblue";
    ctx.fillStyle="darkblue";



    //Draw the player's field
    ctx.font = "25px bold Courier New";
    ctx.fillText(" 1  2  3  4  5  6  7  8  9 10", myFieldXOffset,246.5);
    ctx.font = "bold 25px 'Courier New'";
    
    ctx.fillText(" B", myFieldXOffset-35,246.5+25*2); 
    ctx.fillText(" C", myFieldXOffset-35,246.5+25*3); 
    ctx.fillText(" D", myFieldXOffset-35,246.5+25*4); 
    ctx.fillText(" E", myFieldXOffset-35,246.5+25*5); 
    ctx.fillText(" F", myFieldXOffset-35,246.5+25*6); 
    ctx.fillText(" A", myFieldXOffset-35,246.5+25*1);
    ctx.fillText(" G", myFieldXOffset-35,246.5+25*7); 
    ctx.fillText(" H", myFieldXOffset-35,246.5+25*8); 
    ctx.fillText(" I", myFieldXOffset-35,246.5+25*9); 
    ctx.fillText(" J", myFieldXOffset-35,246.5+25*10);  
    ctx.strokeRect(myFieldXOffset,250.5,250,250);
    for (var i = 0; i<10; i++){
        for(var j = 0; j<10; j++){
            var cell = myField[i][j];
            var cornerX = myFieldXOffset + 25*i;
            var cornerY = 250.5 + 25*j;
            var centerX = cornerX + 12.5;
            var centerY = cornerY + 12.5;
            if(cell <0){
                ctx.fillStyle="darkblue";
                ctx.beginPath();
                ctx.arc(centerX, centerY, dotRadius, 0, Math.PI * 2);
                ctx.fill();
            }
            else if(cell > 0){
                if(cell == 13){
                    ctx.strokeStyle="black";
                }
                else{
                    ctx.strokeStyle="darkblue";
                }
                if( i>0 &&  myField[i-1][j]<=0 ){
                    ctx.beginPath();
                    ctx.moveTo(cornerX,cornerY-1.5);
                    ctx.lineTo(cornerX,cornerY+cellWidth+1.5);
                    ctx.stroke();
                }
                if( i<9 &&  myField[i+1][j]<=0 ){
                    ctx.beginPath();
                    ctx.moveTo(cornerX+25,cornerY-1.5);
                    ctx.lineTo(cornerX+25,cornerY+cellWidth+1.5);
                    ctx.stroke();
                }
                if( j>0 &&  myField[i][j-1]<=0 ){
                    ctx.beginPath();
                    ctx.moveTo(cornerX-1.5,cornerY);
                    ctx.lineTo(cornerX+cellWidth+1.5,cornerY);
                    ctx.stroke();
                }
                if( j<9 &&  myField[i][j+1]<=0 ){
                    ctx.beginPath();
                    ctx.moveTo(cornerX-1.5,cornerY+25);
                    ctx.lineTo(cornerX+cellWidth+1.5,cornerY+25);
                    ctx.stroke();
                }

                if (cell == 12 || cell ==13){
                if(cell==12){
                    drawCross(centerX,centerY,'red');
                }
                else {
                    drawCross(centerX,centerY,'black');
                }

            }

            } 
        }
    }


    ctx.strokeStyle="darkblue"
    ctx.lineWidth=3;
    
    for(var i = 0; i<ships.length;i++){
        var ship = ships[i]; 
        if(ship.cell==null){
        ctx.strokeStyle="darkblue";
        ctx.strokeRect(ship.pos[0],ship.pos[1],ship.width,ship.height );
        }
        else{
            ctx.strokeStyle="green";
            ctx.strokeRect(ship.cell[0]*25+myFieldXOffset,ship.cell[1]*25+250.5,ship.width,ship.height );
        }
    }






    //Draw the enemy field
    ctx.strokeStyle="darkblue";
    ctx.fillText(" 1  2  3  4  5  6  7  8  9 10", enemyFieldXOffset,246.5);
    ctx.fillText(" A", enemyFieldXOffset-28,246.5+25*1);
    ctx.fillText(" B", enemyFieldXOffset-28,246.5+25*2); 
    ctx.fillText(" C", enemyFieldXOffset-28,246.5+25*3); 
    ctx.fillText(" D", enemyFieldXOffset-28,246.5+25*4); 
    ctx.fillText(" E", enemyFieldXOffset-28,246.5+25*5); 
    ctx.fillText(" F", enemyFieldXOffset-28,246.5+25*6); 
    ctx.fillText(" G", enemyFieldXOffset-28,246.5+25*7); 
    ctx.fillText(" H", enemyFieldXOffset-28,246.5+25*8); 
    ctx.fillText("  I", enemyFieldXOffset-30,246.5+25*9); 
    ctx.fillText("  J", enemyFieldXOffset-30,246.5+25*10);  
    ctx.strokeRect(enemyFieldXOffset,250.5,250,250);
    for (var i = 0; i<10; i++){
        for(var j = 0; j<10; j++){
            var cell = enemyField[i][j];
            var cornerX = enemyFieldXOffset + 25*i;
            var cornerY = 250.5 + 25*j;
            var centerX = cornerX + 12.5;
            var centerY = cornerY + 12.5;

            
            if(cell == -1){
                ctx.fillStyle="darkblue";
                ctx.beginPath();
                ctx.arc(centerX, centerY, dotRadius, 0, Math.PI * 2);
                ctx.fill();
            }
            else if (cell == 2 || cell ==3){
                if(cell==2){
                    drawCross(centerX,centerY,'red');
                }
                else {
                    ctx.strokeStyle="black";
                    if( i>0 &&  enemyField[i-1][j]<=0 ){
                    ctx.beginPath();
                    ctx.moveTo(cornerX,cornerY-1.5);
                    ctx.lineTo(cornerX,cornerY+cellWidth+1.5);
                    ctx.stroke();
                }
                if( i<9 &&  enemyField[i+1][j]<=0 ){
                    ctx.beginPath();
                    ctx.moveTo(cornerX+25,cornerY-1.5);
                    ctx.lineTo(cornerX+25,cornerY+cellWidth+1.5);
                    ctx.stroke();
                }
                if( j>0 &&  enemyField[i][j-1]<=0 ){
                    ctx.beginPath();
                    ctx.moveTo(cornerX-1.5,cornerY);
                    ctx.lineTo(cornerX+cellWidth+1.5,cornerY);
                    ctx.stroke();
                }
                if( j<9 &&  enemyField[i][j+1]<=0 ){
                    ctx.beginPath();
                    ctx.moveTo(cornerX-1.5,cornerY+25);
                    ctx.lineTo(cornerX+cellWidth+1.5,cornerY+25);
                    ctx.stroke();
                }
                drawCross(centerX,centerY,'black');
                }
                
            }
        }
    }





    requestAnimationFrame(draw);
}
draw();
</script>
</body>
</html>