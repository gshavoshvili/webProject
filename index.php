<title>Document</title>
</head>
<body>
+<canvas id="Canvas" width="600" height="600" style="border: 1px solid black"></canvas>
  <script>
  var conn = new WebSocket('ws://localhost:8080');
conn.onopen = function(e) {
  console.log("Connection established!");
  var i = 1;
-    setInterval(function(){conn.send(i); i++;},100);
+    //setInterval(function(){conn.send(i); i++;},100);
};

conn.onmessage = function(e) {
-    console.log(e.data);
+    clicked.push(JSON.parse(e.data));
};

+//GAME
+
+var canvas = document.getElementById("Canvas");
+var ctx = canvas.getContext("2d");
+var clicked = [];
+canvas.addEventListener("click", clickHandler, false);
+function clickHandler(e){
+    var click = [e.offsetX,e.offsetY];
+    conn.send(JSON.stringify(click));
+    clicked.push(click);
+}
+
+function draw(){
+    ctx.clearRect(0, 0, canvas.width, canvas.height);
+    clicked.forEach(function(e){
+        ctx.fillRect(e[0],e[1],50,50);
+    })
+    requestAnimationFrame(draw);
+}
+draw();
  </script>
</body>
</html>
