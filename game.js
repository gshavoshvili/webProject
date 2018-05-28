
//Game variables
var canvas = document.getElementById("Canvas");
var ctx = canvas.getContext("2d");
var mousePosition = {x:0,y:0};
var myFieldXOffset = 50.5;
var enemyFieldXOffset = 400.5;
var cellWidth = 25;
var dotRadius = 3.5;
var crossHalfWidth = 9;



var States = {
    "CONNECTING" : 0,
    "CONNECTED_WAITING" : 1,
    "SETUP" : 2,
    "SETUP_WAITING":3,
    "PLAYING":4,
    "WON":5,
    "LOST":6
};

var state = States.CONNECTING;
console.log(state);

var myTurn = false;

var coordinatesImg = new Image();
coordinatesImg.src="../assets/fieldCoordinates.png"

//Drag&Drop
var dragging = null;
var dragOffset = null;
var ships;



console.log(username);

var myField;
var enemyField;
function setMyField(){

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
    
    myField = [];
    for(var i = 0; i < myFieldTransposed.length; i++){
        myField.push([]);
    };
    
    for(var i = 0; i < myFieldTransposed.length; i++){
        for(var j = 0; j < myFieldTransposed.length; j++){
            myField[j].push(myFieldTransposed[i][j]);
        };
    };
    
    function Ship(x,y,cells){
        this.initPos = [x,y];
        this.pos = [x,y];
        this.width = cellWidth * cells;
        this.height = cellWidth;
        this.cells = [];
        this.around = [];
        this.rot = 0;
        this.placed = false;
    }



    ships = [
        new Ship(400.5, 275.5, 4),
        new Ship(400.5,325.5,3),
        new Ship(500.5,325.5,3),
        new Ship(400.5,375.5,2),
        new Ship(475.5,375.5,2),
        new Ship(550.5,375.5,2),
        new Ship(400.5,425.5,1),
        new Ship(450.5,425.5,1),
        new Ship(500.5,425.5,1),
        new Ship(550.5,425.5,1),
    ];

    





}


function getRandomIntInclusive(min, max) {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min + 1)) + min; //The maximum is inclusive and the minimum is inclusive 
  }

function randomize(){
    setMyField();
    for(var i = 0; i<ships.length;i++){
        var ship = ships[i];
        var x;
        var y;
        mouseDownHandler({offsetX:ship.pos[0]+1, offsetY:ship.pos[1]+1})
        if (getRandomIntInclusive(0,1)){
            rotateShip(ship);
            console.log('rot');
        }
        do {
            x = getRandomIntInclusive(myFieldXOffset-cellWidth/2+1,myFieldXOffset-cellWidth/2+250-(dragging.width-25)-1);
            y = getRandomIntInclusive(250.5-cellWidth/2+1, 500.5-cellWidth/2-(dragging.height-25)-1);
            console.log([x,y,i]);
        }
        while (!mouseMoveHandler({offsetX:x, offsetY:y}))
        mouseUpHandler();
    }

}



canvas.addEventListener('mousedown',mouseDownHandler, false);
function mouseDownHandler(e){
    
    if(state == States.SETUP){

        // If pressed on a ship, start dragging it 

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
                });
                dragging.placed = false;
                
                break;
            }
        }
    }
}
canvas.addEventListener('mousemove',mouseMoveHandler, false);
function mouseMoveHandler(e){
    // Calculate if the ship can be placed
    mousePosition.x = e.offsetX;
    mousePosition.y = e.offsetY;
    if (dragging!=null){
        
        dragging.cells=[];
        
        
        dragging.pos=[e.offsetX-dragOffset[0],e.offsetY-dragOffset[1]];
        if (dragging.pos[0]>myFieldXOffset-cellWidth/2 && dragging.pos[0]<myFieldXOffset-cellWidth/2+250-(dragging.width-25) && dragging.pos[1]>250.5-cellWidth/2 && dragging.pos[1]<500.5-cellWidth/2-(dragging.height-25)){
            var x = Math.floor((dragging.pos[0]-myFieldXOffset+cellWidth/2)/cellWidth);
            var y = Math.floor((dragging.pos[1]-250.5+cellWidth/2)/cellWidth);
            console.log([x,y]);
            var canPlace = true;
            
            for (var i = 0; i<((dragging.rot==0)?dragging.width:dragging.height)/25;i++){
                if(myField[x+((dragging.rot==0)?i:0)][y+((dragging.rot==1)?i:0)]==0){
                    dragging.cells.push([x+((dragging.rot==0)?i:0),y+((dragging.rot==1)?i:0)])
                }
                else{
                    canPlace = false;
                    break;
                }
               }
            if(!canPlace){
                dragging.cells=[];
                return false;
            }
            return true;
        }
    }
}
canvas.addEventListener('mouseup',mouseUpHandler, false);
function mouseUpHandler(e){
    // Place the ship or return to initPos
    if(dragging!=null ){
        if (dragging.cells.length==0){
            dragging.pos=dragging.initPos;
            if(dragging.rot==1){
                rotateShip(dragging);
            }
            
        }
        else{
            dragging.pos=[dragging.cells[0][0]*25+myFieldXOffset,dragging.cells[0][1]*25+250.5]
            
            // dots around
            if(dragging.rot==0){
                for (var i = ((dragging.cells[0][0])>0?-1:0); i<= dragging.width/25 -((dragging.cells[0][0]+dragging.width/25<10)?0:1) ;i++){
                   if(dragging.cells[0][1]>0){
                        dragging.around.push([dragging.cells[0][0]+i,dragging.cells[0][1]-1]);
                        myField[dragging.cells[0][0]+i][dragging.cells[0][1]-1]--;
                   }
                   if(dragging.cells[0][1]<9) {
                       dragging.around.push([dragging.cells[0][0]+i,dragging.cells[0][1]+1]);
                       myField[dragging.cells[0][0]+i][dragging.cells[0][1]+1]--;
                   }
                }
            if(dragging.cells[0][0]>0){
                dragging.around.push([dragging.cells[0][0]-1,dragging.cells[0][1]]);
                myField[dragging.cells[0][0]-1][dragging.cells[0][1]]--;
            } 
            if(dragging.cells[0][0]+dragging.width/25<10){
                dragging.around.push([dragging.cells[0][0]+dragging.width/25,dragging.cells[0][1]]);
                myField[dragging.cells[0][0]+dragging.width/25][dragging.cells[0][1]]--;
            } 

            } 

            else{
                for (var i = ((dragging.cells[0][1])>0?-1:0); i<= dragging.height/25 -((dragging.cells[0][1]+dragging.height/25<10)?0:1) ;i++){
                   if(dragging.cells[0][0]>0){
                        dragging.around.push([dragging.cells[0][0]-1,dragging.cells[0][1]+i]);
                        myField[dragging.cells[0][0]-1][dragging.cells[0][1]+i]--;
                   } 
                   if(dragging.cells[0][0]<9){
                        dragging.around.push([dragging.cells[0][0]+1,dragging.cells[0][1]+i]);
                        myField[dragging.cells[0][0]+1][dragging.cells[0][1]+i]--;
                   } 
                }
            if(dragging.cells[0][1]>0){
                dragging.around.push([dragging.cells[0][0],dragging.cells[0][1]-1]);
                myField[dragging.cells[0][0]][dragging.cells[0][1]-1]--;
            } 
            if(dragging.cells[0][1]+dragging.height/25<10) {
                dragging.around.push([dragging.cells[0][0],dragging.cells[0][1]+dragging.height/25])
                myField[dragging.cells[0][0]][dragging.cells[0][1]+dragging.height/25]--;
            }
            }
            // end dots      
            
            dragging.cells.forEach(function(e){
                myField[e[0]][e[1]]=1;
            });
            dragging.placed = true;
        }
        dragging=null;
    }
}

var alreadyRotated = false;
canvas.addEventListener('keydown',keyDownHandler, false);
function keyDownHandler(e){
    if(e.code == 'KeyR'){
        if(state == States.SETUP && !alreadyRotated && dragging!=null){
            rotateShip(dragging);
            mouseMoveHandler({offsetX:mousePosition.x, offsetY:mousePosition.y});
            alreadyRotated=true;
        }
    }
    
}
canvas.addEventListener('keyup',keyUpHandler, false);
function keyUpHandler(e){
    if(state == States.SETUP && e.code == 'KeyR'){
        alreadyRotated=false;
    }
    else if (state == States.SETUP && e.code == 'KeyF'){
        randomize();
    }
    else if (state == States.SETUP && e.code == 'KeyS'){
        if(ships.every((e)=>e.placed)){
            sendField();
        }
        
    }
    
}

function sendField(){

    var data = ships.map((e)=>[e.cells[0][0],e.cells[0][1],e.rot])
    console.log(data);
    conn.send(JSON.stringify(data));
}

function clearMisses(){
    for(var i = 0; i<10; i++){
        for (var j = 0; j<10; j++){
            if(myField[i][j]<0){
                myField[i][j]=0;
            }
        }
    }
}

//WebSocket connection
var conn = new WebSocket('ws://localhost:8080');
var info = {
    username:username,
    match: match
 }
 console.log(JSON.stringify(info));
conn.onopen = function(e) {
    
     conn.send(JSON.stringify(info));
};

conn.onmessage = function(e) {
    if(e.data=='START'){
        state=States.PLAYING;
        clearMisses();
        myTurn = false;
        console.log('enemy starts');
    }
    else if(e.data=='USTART'){
        state=States.PLAYING;
        clearMisses();
        myTurn=true;
        console.log('I start');
    }
    else if (e.data == 'GOOD'){
        state=States.SETUP_WAITING;
        clearMisses();
    }
    else if (e.data == 'SETUP'){
        state=States.SETUP;
        console.log('setup');
    }
    else if (e.data == 'CONNECTED'){
        state=States.CONNECTED_WAITING;
        console.log('connected');
    }
    else if (e.data == 'UWIN'){
        state=States.WON;
        console.log('VICTORY!');
    }
    else if (e.data == 'ULOSE'){
        state=States.LOST;
        console.log('HEROIC DEFEAT!');
    }
    else if (state == States.PLAYING){
        var arr = JSON.parse(e.data);
        console.log(arr);
        arr.forEach( function(order) {
            if(order == 'URTURN'){
                myTurn = true;
                console.log('my turn');
            }
            else if (order == 'ENDTURN'){
                myTurn = false;
                console.log('turn over');
            }
            else {
                if(myTurn){
                    enemyField[order[0]][order[1]] = order[2];
                }
                else {
                    myField[order[0]][order[1]] = order[2];
                }
            }
        } );
    }
    else {
        console.log(e.data);
    }
    
};
    


//Game logic


setMyField();
enemyField = [ 
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


canvas.addEventListener("click", clickHandler, false);
function clickHandler(e){
    if (state == States.PLAYING && myTurn && e.offsetX>enemyFieldXOffset && e.offsetX<enemyFieldXOffset+250 && e.offsetY>250.5 && e.offsetY<500.5){
        var x = e.offsetX-enemyFieldXOffset;
        var y = e.offsetY-250.5;
        var gridX = Math.floor(x/cellWidth);
        var gridY = Math.floor(y/cellWidth);
        if (enemyField[gridX][gridY]==0){
            var click = [gridX,gridY];
            conn.send(JSON.stringify(click));
        }
        
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
//Make it look like a notebook
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

function drawMyField(){
    ctx.lineWidth=3;
    ctx.drawImage(coordinatesImg,myFieldXOffset-46,204.5);
    ctx.strokeStyle="darkblue";
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

    if(state == States.SETUP){
        ctx.strokeStyle="darkblue"
        ctx.lineWidth=3;
        
        for(var i = 0; i<ships.length;i++){
            var ship = ships[i]; 
            if(ship.cells.length==0){
                ctx.strokeStyle="darkblue";
                ctx.strokeRect(ship.pos[0],ship.pos[1],ship.width,ship.height );
            }
            else if (!ship.placed) {
                ctx.strokeStyle="green";
                ctx.strokeRect(ship.cells[0][0]*25+myFieldXOffset,ship.cells[0][1]*25+250.5,ship.width,ship.height );
            }
        }
    }

}


function drawEnemyField(){
    ctx.lineWidth=3;
    ctx.drawImage(coordinatesImg,enemyFieldXOffset-46,204.5);
    ctx.strokeStyle="darkblue";
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

    drawBG();
    
    

    if(state != States.CONNECTING){
        drawMyField();
    }
    

    if(state == States.PLAYING || state == States.WON || state == States.LOST){
        drawEnemyField();
    }
    if(state==States.CONNECTED_WAITING || state==States.SETUP_WAITING){
        ctx.font = "32px Arial";
        ctx.fillStyle="darkblue";
        ctx.fillText("Waiting for the opponent...",178,150);
    }


    
    requestAnimationFrame(draw);
}
draw();