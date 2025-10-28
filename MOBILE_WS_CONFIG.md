# Mobile WebSocket Configuration Guide

## Environment Configuration

### Development (.env.development)
```env
API_BASE_URL=http://192.168.1.XXX:8000/api/v1
WS_HOST=192.168.1.XXX
WS_PORT=8080
WS_USE_TLS=false
REVERB_APP_KEY=your_app_key_from_laravel_env
```

### Production (.env.production)
```env
API_BASE_URL=https://yourdomain.com/api/v1
WS_HOST=yourdomain.com
WS_PORT=443
WS_USE_TLS=true
REVERB_APP_KEY=your_app_key_from_laravel_env
```

## React Native Implementation

### Install Dependencies
```bash
npm install laravel-echo pusher-js
# or
yarn add laravel-echo pusher-js
```

### Create Echo Instance (src/services/websocket.js)

```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Make Pusher available globally for Echo
window.Pusher = Pusher;

const createEchoInstance = (authToken) => {
  const isDevelopment = __DEV__;
  
  const config = {
    broadcaster: 'pusher', // Use 'pusher' even though backend is Reverb
    key: process.env.REVERB_APP_KEY,
    wsHost: isDevelopment 
      ? process.env.WS_HOST // e.g., '192.168.1.100'
      : process.env.WS_HOST, // e.g., 'yourdomain.com'
    wsPort: isDevelopment ? 8080 : 443,
    wssPort: isDevelopment ? 8080 : 443,
    forceTLS: !isDevelopment, // false for dev, true for production
    enabledTransports: isDevelopment ? ['ws', 'wss'] : ['wss'],
    disableStats: true,
    authEndpoint: `${process.env.API_BASE_URL}/broadcasting/auth`,
    auth: {
      headers: {
        Authorization: `Bearer ${authToken}`,
        Accept: 'application/json',
      },
    },
  };

  return new Echo(config);
};

export default createEchoInstance;
```

### Usage in Your App

```javascript
import { useEffect, useState } from 'react';
import createEchoInstance from './services/websocket';

function ChatScreen({ chatId, authToken }) {
  const [echo, setEcho] = useState(null);
  const [messages, setMessages] = useState([]);

  useEffect(() => {
    // Initialize Echo
    const echoInstance = createEchoInstance(authToken);
    setEcho(echoInstance);

    // Join private channel for this chat
    const channel = echoInstance.private(`chat.${chatId}`);

    // Listen for new messages
    channel.listen('.message.sent', (event) => {
      console.log('New message received:', event.message);
      setMessages(prev => [...prev, event.message]);
    });

    // Listen for typing indicators
    channel.listenForWhisper('typing', (e) => {
      console.log(`User ${e.user_id} is typing...`);
    });

    // Cleanup on unmount
    return () => {
      channel.stopListening('.message.sent');
      channel.stopListeningForWhisper('typing');
      echoInstance.leave(`chat.${chatId}`);
      echoInstance.disconnect();
    };
  }, [chatId, authToken]);

  const sendTypingNotification = () => {
    if (echo) {
      echo.private(`chat.${chatId}`)
        .whisper('typing', {
          user_id: currentUserId,
        });
    }
  };

  return (
    // Your chat UI
  );
}
```

## Important Notes

### 1. Finding Your Local IP (Development)

**On Linux/Mac:**
```bash
# Method 1
ip addr show | grep "inet " | grep -v 127.0.0.1

# Method 2
ifconfig | grep "inet " | grep -v 127.0.0.1

# Method 3
hostname -I
```

**On Windows:**
```cmd
ipconfig | findstr IPv4
```

Your IP will look like `192.168.1.100` or `10.0.0.5`

### 2. Production WebSocket Flow

```
Mobile App (wss://yourdomain.com:443)
    ↓
Nginx (Listen on :443, SSL Termination)
    ↓
Proxy to /app endpoint
    ↓
Reverb Server (localhost:8080)
    ↓
Laravel Application
```

### 3. Laravel .env Configuration

**Development (.env):**
```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=123456
REVERB_APP_KEY=your_app_key
REVERB_APP_SECRET=your_app_secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST=localhost
VITE_REVERB_PORT=8080
VITE_REVERB_SCHEME=http
```

**Production (.env):**
```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=123456
REVERB_APP_KEY=your_app_key
REVERB_APP_SECRET=your_app_secret
REVERB_HOST=yourdomain.com
REVERB_PORT=443
REVERB_SCHEME=https

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST=yourdomain.com
VITE_REVERB_PORT=443
VITE_REVERB_SCHEME=https
```

### 4. The WebSocket Key

The `REVERB_APP_KEY` is the **same** in both development and production. It's generated when you set up Laravel Reverb:

```bash
php artisan reverb:install
```

This key is found in your `.env` file and should be copied to your React Native app's environment configuration.

### 5. Testing WebSocket Connection

**Development:**
```javascript
// Test connection
console.log('Connecting to WS:', process.env.WS_HOST, process.env.WS_PORT);

echo.connector.pusher.connection.bind('connected', () => {
  console.log('✅ WebSocket connected!');
});

echo.connector.pusher.connection.bind('error', (err) => {
  console.error('❌ WebSocket error:', err);
});
```

**Production:**
- Ensure SSL certificate is valid
- Check that Nginx is proxying `/app` correctly
- Verify Reverb is running: `sudo supervisorctl status alem-reverb`
- Check firewall allows port 443

### 6. Common Issues

**Issue: "Connection refused" in development**
- Solution: Use your machine's IP (192.168.x.x), not localhost
- Make sure Reverb is running: `php artisan reverb:start`

**Issue: "SSL certificate error" in production**
- Solution: Ensure Let's Encrypt certificate is installed and valid
- Check Nginx SSL configuration

**Issue: "Authentication failed"**
- Solution: Verify authToken is valid and included in headers
- Check `/api/v1/broadcasting/auth` endpoint is accessible

**Issue: "WebSocket closes immediately"**
- Solution: Check Nginx proxy timeout settings
- Verify `proxy_read_timeout 86400;` in Nginx config

## Summary

| Environment | WS Host | WS Port | Use TLS | Transport |
|-------------|---------|---------|---------|-----------|
| **Development** | `192.168.1.XXX` | `8080` | `false` | `ws`, `wss` |
| **Production** | `yourdomain.com` | `443` | `true` | `wss` only |

**The key is the same** in both environments - it's your `REVERB_APP_KEY` from Laravel's `.env` file.
