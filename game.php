<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<canvas id="Canvas" width="600" height="600" style="border: 1px solid black"></canvas>
<script>
//Game variables
var canvas = document.getElementById("Canvas");
var ctx = canvas.getContext("2d");
var cellWidth = 25;
var dotRadius = 3.5;
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
var myField = [ 
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
    if (e.offsetX>325.5 && e.offsetX<575.5 && e.offsetY>250.5 && e.offsetY<500.5){
        var x = e.offsetX-325.5;
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
    ctx.strokeRect(25.5,250.5,250,250);
    ctx.strokeRect(325.5,250.5,250,250);
    ctx.fillStyle="darkblue";
    for (var i = 0; i<10; i++){
        for(var j = 0; j<10; j++){
            var centerX = 325.5 + 25*i + 12.5;
            var centerY = 250.5 + 25*j + 12.5;
            var crossHalfWidth = 9;
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