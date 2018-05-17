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
<canvas id="Canvas" width="700" height="600" style="border: 1px solid black"></canvas>
<script>
//Game variables
var canvas = document.getElementById("Canvas");
var ctx = canvas.getContext("2d");
var myFieldXOffset = 50.5;
var enemyFieldXOffset = 400.5;
var cellWidth = 25;
var dotRadius = 3.5;
var crossHalfWidth = 9;
var clicked = [];
var myTurn = false;

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
var myFieldTransposed = [ 
[3,3,3,0,0,0,0,0,0,0],
[0,0,0,0,0,0,0,2,0,0],
[0,0,0,2,2,0,0,2,0,0],
[0,3,0,0,0,0,0,0,0,0],
[0,3,0,0,0,1,0,0,0,0],
[0,3,0,0,0,0,0,0,0,0],
[0,0,0,4,0,0,4,4,4,4],
[0,0,0,4,0,0,0,0,0,0],
[0,0,0,4,0,0,0,0,0,0],
[0,0,0,4,0,0,0,0,0,0]
]

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
[0,0,0,0,0,0,0,0,0,0],
[0,0,0,0,0,0,0,0,0,0],
[0,0,0,0,0,0,0,0,0,0],
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
            enemyField[gridX][gridY] = 1;
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




function draw(){
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    //Make it look like a page of a notebook
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
    
    //Draw the players' zones 
    ctx.lineWidth=3;
    ctx.strokeStyle="darkblue";
    ctx.fillStyle="darkblue";



    //Draw the player's field
    ctx.font = "25px bold Courier New";
    ctx.fillText(" 1  2  3  4  5  6  7  8  9 10", myFieldXOffset,246.5);
    ctx.fillText(" A", myFieldXOffset-28,246.5+25*1);
    ctx.fillText(" B", myFieldXOffset-28,246.5+25*2); 
    ctx.fillText(" C", myFieldXOffset-28,246.5+25*3); 
    ctx.fillText(" D", myFieldXOffset-28,246.5+25*4); 
    ctx.fillText(" E", myFieldXOffset-28,246.5+25*5); 
    ctx.fillText(" F", myFieldXOffset-28,246.5+25*6); 
    ctx.fillText(" G", myFieldXOffset-28,246.5+25*7); 
    ctx.fillText(" H", myFieldXOffset-28,246.5+25*8); 
    ctx.fillText("  I", myFieldXOffset-30,246.5+25*9); 
    ctx.fillText("  J", myFieldXOffset-30,246.5+25*10);  
    ctx.strokeRect(myFieldXOffset,250.5,250,250);
    for (var i = 0; i<10; i++){
        for(var j = 0; j<10; j++){
            if(myField[i][j] > 0){
                var cornerX = myFieldXOffset + 25*i;
                var cornerY = 250.5 + 25*j;
                if( i>0 &&  myField[i-1][j]==0 ){
                    ctx.beginPath();
                    ctx.moveTo(cornerX,cornerY-1.5);
                    ctx.lineTo(cornerX,cornerY+cellWidth+1.5);
                    ctx.stroke();
                }
                if( i<9 &&  myField[i+1][j]==0 ){
                    ctx.beginPath();
                    ctx.moveTo(cornerX+25,cornerY-1.5);
                    ctx.lineTo(cornerX+25,cornerY+cellWidth+1.5);
                    ctx.stroke();
                }
                if( j>0 &&  myField[i][j-1]==0 ){
                    ctx.beginPath();
                    ctx.moveTo(cornerX-1.5,cornerY);
                    ctx.lineTo(cornerX+cellWidth+1.5,cornerY);
                    ctx.stroke();
                }
                if( j<9 &&  myField[i][j+1]==0 ){
                    ctx.beginPath();
                    ctx.moveTo(cornerX-1.5,cornerY+25);
                    ctx.lineTo(cornerX+cellWidth+1.5,cornerY+25);
                    ctx.stroke();
                }

            } 
        }
    }




    //Draw the enemy field
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
            var centerX = enemyFieldXOffset + 25*i + 12.5;
            var centerY = 250.5 + 25*j + 12.5;
            
            if(enemyField[i][j] == -1){
                ctx.beginPath();
                ctx.arc(centerX, centerY, dotRadius, 0, Math.PI * 2);
                ctx.fill();
            }
            else if (enemyField[i][j] == 1){
                ctx.beginPath();
                ctx.moveTo(centerX - crossHalfWidth, centerY - crossHalfWidth);
                ctx.lineTo(centerX + crossHalfWidth, centerY + crossHalfWidth);

                ctx.moveTo(centerX + crossHalfWidth, centerY - crossHalfWidth);
                ctx.lineTo(centerX - crossHalfWidth, centerY + crossHalfWidth);
                ctx.stroke();
            }
        }
    }




    clicked.forEach(function(e){
        ctx.fillRect(e[0],e[1],50,50);
    })
    requestAnimationFrame(draw);
}
draw();
</script>
</body>
</html>