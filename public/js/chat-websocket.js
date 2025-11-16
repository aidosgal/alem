// WebSocket Client for Chat notifications

class WebSocketClient {
  constructor(url) {
    this.ws = null;
    this.url = url;
    this.reconnectInterval = 3000;
    this.shouldReconnect = true;
    this.onRefreshCallback = null;
  }

  connect() {
    this.ws = new WebSocket(this.url);

    this.ws.onopen = () => {
      console.log(`Connected to ${this.url}`);
    };

    this.ws.onmessage = (event) => {
      console.log('Received:', event.data);
      
      if (event.data === 'refresh' && this.onRefreshCallback) {
        this.onRefreshCallback();
      }
    };

    this.ws.onerror = (error) => {
      console.error('WebSocket error:', error);
    };

    this.ws.onclose = () => {
      console.log('Connection closed');
      
      if (this.shouldReconnect) {
        console.log(`Reconnecting in ${this.reconnectInterval}ms...`);
        setTimeout(() => this.connect(), this.reconnectInterval);
      }
    };
  }

  onRefresh(callback) {
    this.onRefreshCallback = callback;
  }

  disconnect() {
    this.shouldReconnect = false;
    if (this.ws) {
      this.ws.close();
    }
  }

  send(data) {
    if (this.ws && this.ws.readyState === WebSocket.OPEN) {
      const message = typeof data === 'string' ? data : JSON.stringify(data);
      this.ws.send(message);
    } else {
      console.error('WebSocket is not connected');
    }
  }

  // Send signal to notify a chat room
  notifyChat(chatId) {
    this.send({
      type: 'notify_chat',
      target: chatId
    });
  }

  // Send signal to notify a specific user
  notifyUser(userId) {
    this.send({
      type: 'notify_user',
      target: userId
    });
  }
}

// Chat Room Client
class ChatClient extends WebSocketClient {
  constructor(chatId, userId, serverUrl = 'ws://localhost:1080') {
    super(`${serverUrl}/chat/${chatId}?user=${userId}`);
  }
}

// User Client
class UserClient extends WebSocketClient {
  constructor(userId, serverUrl = 'ws://localhost:1080') {
    super(`${serverUrl}/user/${userId}`);
  }
}
