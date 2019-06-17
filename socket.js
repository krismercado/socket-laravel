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

    socket.emit("position", position);
    socket.on("hidden", data => {
      console.log('hidden: '+data);
    });
    socket.on("move", data => {
        switch(data) {
            case "left":
                console.log('left');
                position.x -= 5;
                Socketio.emit("position", position);
                break;
            case "right":
                console.log('right');
                position.x += 5;
                Socketio.emit("position", position);
                break;
            case "up":
                console.log('up');
                position.y -= 5;
                Socketio.emit("position", position);
                break;
            case "down":
                console.log('down');
                position.y += 5;
                Socketio.emit("position", position);
                break;
        }
    });
});
