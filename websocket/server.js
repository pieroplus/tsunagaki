const http = require("http");
const { Server } = require("socket.io");
const Redis = require("ioredis");

const redis = new Redis({
  host: process.env.REDIS_HOST || "redis",
  port: process.env.REDIS_PORT || 6379,
});

const server = http.createServer();
const io = new Server(server, {
  cors: {
    origin: "*",
  },
});

io.on("connection", (socket) => {
  console.log("Client connected:", socket.id);
  socket.on("disconnect", () => {
    console.log("Client disconnected:", socket.id);
  });
});

redis.psubscribe("*", () => {
  console.log("Subscribed to Redis events");
});

redis.on("pmessage", (pattern, channel, message) => {
  console.log(`Received event on "${channel}":`, message);
  try {
    const parsed = JSON.parse(message);
    io.emit(channel, parsed);
  } catch (err) {
    console.error("Invalid message:", err);
  }
});

const PORT = process.env.WEBSOCKET_PORT || 6001;
server.listen(PORT, () => {
  console.log(`WebSocket server listening on port ${PORT}`);
});
