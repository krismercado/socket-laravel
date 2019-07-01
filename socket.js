const Express = require("express")();
const Http = require("http").Server(Express);
const Socketio = require("socket.io")(Http);

var position = {
    x: 200,
    y: 200
};

var connections = [];

Http.listen(3000, () => {
    console.log("Listening at :3000...");
});

Socketio.on("connection", socket => {
    connections.push(socket);
    console.log("Connected: %s sockets connected", connections.length);
    console.log(socket.id);
    Socketio.emit("clients", connections.length);

    socket.on("position", data => {
      console.log(data);
      Socketio.emit("positions", data);
    });

    socket.on("hidden", data => {
      Socketio.emit("isHidden", data);
    });

    socket.on('disconnect', function(){
      // then broadcast it to all various connections
      Socketio.emit('incomplete', 'A user has disconnected. Please exit the browser and restart node.');

  });
});
