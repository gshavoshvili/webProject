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
var clicked = [];
var myTurn = false;

//WebSocket connection
var conn = new WebSocket('ws://localhost:8080');
conn.onopen = function(e) {
    console.log("Connection established!");
    var i = 1;
    //setInterval(function(){conn.send(i); i++;},100);
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
canvas.addEventListener("click", clickHandler, false);
function clickHandler(e){
    if(myTurn) {
    var click = [e.offsetX,e.offsetY];
    conn.send(JSON.stringify(click));
    clicked.push(click);
    myTurn=false;
    }
}

function draw(){
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    clicked.forEach(function(e){
        ctx.fillRect(e[0],e[1],50,50);
    })
    requestAnimationFrame(draw);
}
draw();
</script>
</body>
</html>