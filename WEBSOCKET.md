# WebSocket Integration Guide for React Native

## Overview

This guide explains how to integrate Laravel Reverb WebSocket functionality into your React Native mobile app for real-time chat features.

## Technology Stack

- **Backend**: Laravel Reverb (Laravel Echo Server)
- **Frontend**: React Native with Laravel Echo and Pusher JS
- **Protocol**: WebSocket

---

## 📦 Installation

Install required packages in your React Native project:

```bash
npm install laravel-echo pusher-js
# or
yarn add laravel-echo pusher-js
```

---

## 🔧 Configuration

### 1. WebSocket Configuration

Create a new file `src/config/echo.js`:

```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Make Pusher available globally for Laravel Echo
window.Pusher = Pusher;

const createEchoInstance = (authToken) => {
  return new Echo({
    broadcaster: 'reverb',
    key: process.env.REVERB_APP_KEY || 'your-app-key',
    wsHost: process.env.REVERB_HOST || 'your-domain.com',
    wsPort: process.env.REVERB_PORT || 8080,
    wssPort: process.env.REVERB_PORT || 8080,
    forceTLS: process.env.REVERB_SCHEME === 'https',
    enabledTransports: ['ws', 'wss'],
    authEndpoint: 'https://your-domain.com/broadcasting/auth',
    auth: {
      headers: {
        Authorization: `Bearer ${authToken}`,
        Accept: 'application/json',
      },
    },
  });
};

export default createEchoInstance;
```

### 2. Environment Variables

Add to your `.env` file:

```env
REVERB_APP_KEY=your-app-key
REVERB_HOST=your-domain.com
REVERB_PORT=8080
REVERB_SCHEME=https
```

Or use a `.env.js` file:

```javascript
export default {
  REVERB_APP_KEY: 'your-app-key',
  REVERB_HOST: 'your-domain.com',
  REVERB_PORT: 8080,
  REVERB_SCHEME: 'https',
};
```

---

## 🚀 Implementation

### 1. Create Echo Context

Create `src/contexts/EchoContext.js`:

```javascript
import React, { createContext, useContext, useEffect, useState } from 'react';
import createEchoInstance from '../config/echo';
import AsyncStorage from '@react-native-async-storage/async-storage';

const EchoContext = createContext(null);

export const EchoProvider = ({ children }) => {
  const [echo, setEcho] = useState(null);
  const [isConnected, setIsConnected] = useState(false);

  useEffect(() => {
    initializeEcho();

    return () => {
      if (echo) {
        echo.disconnect();
      }
    };
  }, []);

  const initializeEcho = async () => {
    try {
      const token = await AsyncStorage.getItem('auth_token');
      if (!token) {
        console.log('No auth token found');
        return;
      }

      const echoInstance = createEchoInstance(token);
      
      // Check connection status
      echoInstance.connector.pusher.connection.bind('connected', () => {
        console.log('WebSocket connected');
        setIsConnected(true);
      });

      echoInstance.connector.pusher.connection.bind('disconnected', () => {
        console.log('WebSocket disconnected');
        setIsConnected(false);
      });

      echoInstance.connector.pusher.connection.bind('error', (error) => {
        console.error('WebSocket error:', error);
        setIsConnected(false);
      });

      setEcho(echoInstance);
    } catch (error) {
      console.error('Error initializing Echo:', error);
    }
  };

  const reconnect = async () => {
    if (echo) {
      echo.disconnect();
    }
    await initializeEcho();
  };

  const disconnect = () => {
    if (echo) {
      echo.disconnect();
      setEcho(null);
      setIsConnected(false);
    }
  };

  return (
    <EchoContext.Provider value={{ echo, isConnected, reconnect, disconnect }}>
      {children}
    </EchoContext.Provider>
  );
};

export const useEcho = () => {
  const context = useContext(EchoContext);
  if (!context) {
    throw new Error('useEcho must be used within EchoProvider');
  }
  return context;
};
```

### 2. Wrap Your App

In your `App.js`:

```javascript
import React from 'react';
import { EchoProvider } from './src/contexts/EchoContext';
import Navigation from './src/navigation';

export default function App() {
  return (
    <EchoProvider>
      <Navigation />
    </EchoProvider>
  );
}
```

---

## 💬 Chat Implementation

### 1. Chat Screen Component

Create `src/screens/ChatScreen.js`:

```javascript
import React, { useEffect, useState, useCallback } from 'react';
import { View, Text, FlatList, TextInput, TouchableOpacity, StyleSheet } from 'react-native';
import { useEcho } from '../contexts/EchoContext';
import api from '../config/api';

const ChatScreen = ({ route }) => {
  const { chatId } = route.params;
  const { echo, isConnected } = useEcho();
  const [messages, setMessages] = useState([]);
  const [newMessage, setNewMessage] = useState('');
  const [loading, setLoading] = useState(false);

  // Load initial messages
  useEffect(() => {
    loadMessages();
  }, [chatId]);

  // Subscribe to chat channel
  useEffect(() => {
    if (!echo || !isConnected || !chatId) return;

    console.log(`Subscribing to chat.${chatId}`);
    
    const channel = echo.private(`chat.${chatId}`);

    // Listen for new messages
    channel.listen('MessageSent', (event) => {
      console.log('New message received:', event);
      handleNewMessage(event.message);
    });

    // Listen for message read events
    channel.listen('MessageRead', (event) => {
      console.log('Message read:', event);
      updateMessageReadStatus(event.messageId);
    });

    // Listen for typing indicator
    channel.listenForWhisper('typing', (e) => {
      console.log('User is typing:', e);
      // Handle typing indicator
    });

    // Cleanup
    return () => {
      console.log(`Leaving chat.${chatId}`);
      channel.stopListening('MessageSent');
      channel.stopListening('MessageRead');
      channel.stopListeningForWhisper('typing');
      echo.leave(`chat.${chatId}`);
    };
  }, [echo, isConnected, chatId]);

  const loadMessages = async () => {
    setLoading(true);
    try {
      const response = await api.get(`/chats/${chatId}/messages`);
      setMessages(response.data.data.messages.reverse());
    } catch (error) {
      console.error('Error loading messages:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleNewMessage = useCallback((message) => {
    setMessages(prev => [...prev, message]);
    
    // Mark as read if sender is not current user
    if (message.sender_type === 'manager') {
      markAsRead();
    }
  }, []);

  const updateMessageReadStatus = useCallback((messageId) => {
    setMessages(prev =>
      prev.map(msg =>
        msg.id === messageId ? { ...msg, is_read: true } : msg
      )
    );
  }, []);

  const sendMessage = async () => {
    if (!newMessage.trim()) return;

    try {
      const response = await api.post(`/chats/${chatId}/messages`, {
        content: newMessage,
      });

      // Message will be received via WebSocket, so we don't need to add it manually
      // But we can add it optimistically for better UX
      const sentMessage = response.data.data.message;
      setMessages(prev => [...prev, sentMessage]);
      setNewMessage('');
    } catch (error) {
      console.error('Error sending message:', error);
    }
  };

  const markAsRead = async () => {
    try {
      await api.post(`/chats/${chatId}/mark-read`);
    } catch (error) {
      console.error('Error marking as read:', error);
    }
  };

  const sendTypingIndicator = () => {
    if (echo && isConnected) {
      echo.private(`chat.${chatId}`).whisper('typing', {
        userId: 'current-user-id',
      });
    }
  };

  const renderMessage = ({ item }) => (
    <View
      style={[
        styles.messageContainer,
        item.sender_type === 'applicant' ? styles.myMessage : styles.theirMessage,
      ]}
    >
      {item.replied_to && (
        <View style={styles.replyContainer}>
          <Text style={styles.replyText}>{item.replied_to.content}</Text>
        </View>
      )}
      <Text style={styles.messageText}>{item.content}</Text>
      {item.attachments?.length > 0 && (
        <View style={styles.attachmentsContainer}>
          {item.attachments.map(att => (
            <Text key={att.id} style={styles.attachmentText}>
              📎 {att.file_name}
            </Text>
          ))}
        </View>
      )}
      <Text style={styles.timestamp}>
        {new Date(item.created_at).toLocaleTimeString()}
      </Text>
    </View>
  );

  return (
    <View style={styles.container}>
      {!isConnected && (
        <View style={styles.connectionBanner}>
          <Text style={styles.connectionText}>Connecting...</Text>
        </View>
      )}
      
      <FlatList
        data={messages}
        renderItem={renderMessage}
        keyExtractor={item => item.id.toString()}
        contentContainerStyle={styles.messagesList}
        onRefresh={loadMessages}
        refreshing={loading}
      />

      <View style={styles.inputContainer}>
        <TextInput
          style={styles.input}
          value={newMessage}
          onChangeText={text => {
            setNewMessage(text);
            sendTypingIndicator();
          }}
          placeholder="Type a message..."
          multiline
        />
        <TouchableOpacity
          style={styles.sendButton}
          onPress={sendMessage}
          disabled={!newMessage.trim()}
        >
          <Text style={styles.sendButtonText}>Send</Text>
        </TouchableOpacity>
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
  },
  connectionBanner: {
    backgroundColor: '#FFA500',
    padding: 8,
    alignItems: 'center',
  },
  connectionText: {
    color: '#fff',
    fontWeight: 'bold',
  },
  messagesList: {
    padding: 16,
  },
  messageContainer: {
    maxWidth: '80%',
    padding: 12,
    borderRadius: 12,
    marginBottom: 8,
  },
  myMessage: {
    alignSelf: 'flex-end',
    backgroundColor: '#007AFF',
  },
  theirMessage: {
    alignSelf: 'flex-start',
    backgroundColor: '#E5E5EA',
  },
  messageText: {
    fontSize: 16,
    color: '#000',
  },
  replyContainer: {
    backgroundColor: 'rgba(0,0,0,0.1)',
    padding: 8,
    borderRadius: 8,
    marginBottom: 8,
  },
  replyText: {
    fontSize: 14,
    fontStyle: 'italic',
  },
  attachmentsContainer: {
    marginTop: 8,
  },
  attachmentText: {
    fontSize: 14,
    color: '#007AFF',
  },
  timestamp: {
    fontSize: 12,
    color: '#666',
    marginTop: 4,
  },
  inputContainer: {
    flexDirection: 'row',
    padding: 16,
    borderTopWidth: 1,
    borderTopColor: '#E5E5EA',
  },
  input: {
    flex: 1,
    backgroundColor: '#F2F2F7',
    borderRadius: 20,
    paddingHorizontal: 16,
    paddingVertical: 8,
    marginRight: 8,
    maxHeight: 100,
  },
  sendButton: {
    backgroundColor: '#007AFF',
    borderRadius: 20,
    paddingHorizontal: 20,
    paddingVertical: 10,
    justifyContent: 'center',
  },
  sendButtonText: {
    color: '#fff',
    fontWeight: 'bold',
  },
});

export default ChatScreen;
```

### 2. Chat List with Unread Badge

Create `src/screens/ChatListScreen.js`:

```javascript
import React, { useEffect, useState } from 'react';
import { View, Text, FlatList, TouchableOpacity, StyleSheet } from 'react-native';
import { useEcho } from '../contexts/EchoContext';
import api from '../config/api';

const ChatListScreen = ({ navigation }) => {
  const { echo, isConnected } = useEcho();
  const [chats, setChats] = useState([]);

  useEffect(() => {
    loadChats();
  }, []);

  // Listen for new messages across all chats
  useEffect(() => {
    if (!echo || !isConnected) return;

    // Subscribe to personal channel for notifications
    const channel = echo.private('App.Models.User.{userId}');
    
    channel.notification((notification) => {
      console.log('New notification:', notification);
      if (notification.type === 'NewMessage') {
        updateChatPreview(notification.chatId);
      }
    });

    return () => {
      echo.leave('App.Models.User.{userId}');
    };
  }, [echo, isConnected]);

  const loadChats = async () => {
    try {
      const response = await api.get('/chats');
      setChats(response.data.data.chats);
    } catch (error) {
      console.error('Error loading chats:', error);
    }
  };

  const updateChatPreview = async (chatId) => {
    try {
      const response = await api.get(`/chats/${chatId}/messages`, {
        params: { per_page: 1 }
      });
      const lastMessage = response.data.data.messages[0];
      
      setChats(prev =>
        prev.map(chat =>
          chat.id === chatId
            ? {
                ...chat,
                last_message: lastMessage,
                unread_count: chat.unread_count + 1,
              }
            : chat
        )
      );
    } catch (error) {
      console.error('Error updating chat preview:', error);
    }
  };

  const renderChat = ({ item }) => (
    <TouchableOpacity
      style={styles.chatItem}
      onPress={() => navigation.navigate('Chat', { chatId: item.id })}
    >
      <View style={styles.chatInfo}>
        <Text style={styles.orgName}>{item.organization.name}</Text>
        <Text style={styles.lastMessage} numberOfLines={1}>
          {item.last_message?.content || 'No messages yet'}
        </Text>
      </View>
      {item.unread_count > 0 && (
        <View style={styles.unreadBadge}>
          <Text style={styles.unreadText}>{item.unread_count}</Text>
        </View>
      )}
    </TouchableOpacity>
  );

  return (
    <View style={styles.container}>
      <FlatList
        data={chats}
        renderItem={renderChat}
        keyExtractor={item => item.id.toString()}
        onRefresh={loadChats}
        refreshing={false}
      />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
  },
  chatItem: {
    flexDirection: 'row',
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#E5E5EA',
  },
  chatInfo: {
    flex: 1,
  },
  orgName: {
    fontSize: 16,
    fontWeight: 'bold',
    marginBottom: 4,
  },
  lastMessage: {
    fontSize: 14,
    color: '#666',
  },
  unreadBadge: {
    backgroundColor: '#007AFF',
    borderRadius: 12,
    minWidth: 24,
    height: 24,
    justifyContent: 'center',
    alignItems: 'center',
    paddingHorizontal: 8,
  },
  unreadText: {
    color: '#fff',
    fontSize: 12,
    fontWeight: 'bold',
  },
});

export default ChatListScreen;
```

---

## 🔔 Push Notifications Integration

For background notifications when app is closed:

```javascript
import messaging from '@react-native-firebase/messaging';

// Request permission
const requestPermission = async () => {
  const authStatus = await messaging().requestPermission();
  const enabled =
    authStatus === messaging.AuthorizationStatus.AUTHORIZED ||
    authStatus === messaging.AuthorizationStatus.PROVISIONAL;

  if (enabled) {
    console.log('Authorization status:', authStatus);
    const token = await messaging().getToken();
    // Send token to your backend
    await api.post('/profile/fcm-token', { token });
  }
};

// Handle background messages
messaging().setBackgroundMessageHandler(async remoteMessage => {
  console.log('Message handled in the background!', remoteMessage);
});

// Handle foreground messages
messaging().onMessage(async remoteMessage => {
  console.log('Message handled in the foreground!', remoteMessage);
  // Show local notification
});
```

---

## 🔐 Backend Channel Authorization

Ensure your Laravel backend has proper channel authorization in `routes/channels.php`:

```php
use App\Models\Chat;
use Illuminate\Support\Facades\Broadcast;

// Chat channel - only participants can join
Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    $chat = Chat::find($chatId);
    return $chat && (
        $chat->applicant_id === $user->applicant->id ||
        $chat->organization_id === $user->manager->current_organization_id
    );
});

// User private channel for notifications
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
```

---

## 🐛 Debugging

### Enable Pusher Logging

```javascript
import Pusher from 'pusher-js';

// Enable logging
Pusher.logToConsole = true;

const echo = new Echo({
  // ... config
});
```

### Check Connection Status

```javascript
if (echo) {
  console.log('Connection state:', echo.connector.pusher.connection.state);
  
  echo.connector.pusher.connection.bind('state_change', (states) => {
    console.log('State changed:', states.previous, '->', states.current);
  });
}
```

### Common Issues

1. **Connection fails**: Check REVERB_HOST and REVERB_PORT
2. **Authentication fails**: Verify token is valid and sent in headers
3. **Events not received**: Check channel name and event name match backend
4. **Memory leaks**: Always cleanup listeners in useEffect return

---

## 📊 Connection Status Indicator

```javascript
import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { useEcho } from '../contexts/EchoContext';

const ConnectionIndicator = () => {
  const { isConnected } = useEcho();

  return (
    <View style={[styles.indicator, { backgroundColor: isConnected ? '#10B981' : '#EF4444' }]}>
      <View style={styles.dot} />
      <Text style={styles.text}>{isConnected ? 'Connected' : 'Disconnected'}</Text>
    </View>
  );
};

const styles = StyleSheet.create({
  indicator: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 8,
    borderRadius: 20,
  },
  dot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: '#fff',
    marginRight: 8,
  },
  text: {
    color: '#fff',
    fontSize: 12,
    fontWeight: 'bold',
  },
});

export default ConnectionIndicator;
```

---

## 🎯 Best Practices

1. **Always cleanup**: Unsubscribe from channels when component unmounts
2. **Handle reconnection**: Implement reconnection logic for poor networks
3. **Optimize re-renders**: Use useCallback and useMemo for event handlers
4. **Error handling**: Always catch errors in WebSocket events
5. **Testing**: Test on real devices, not just simulators
6. **Battery optimization**: Disconnect when app is in background for extended time

---

## 📝 Summary

You now have a complete WebSocket integration for your React Native app with:
- ✅ Real-time message delivery
- ✅ Typing indicators
- ✅ Read receipts
- ✅ Connection status monitoring
- ✅ Proper cleanup and error handling

For more information, refer to:
- [Laravel Echo Documentation](https://laravel.com/docs/broadcasting#client-side-installation)
- [Pusher JS Documentation](https://pusher.com/docs/channels/using_channels/client-api-overview/)
- [Laravel Reverb Documentation](https://laravel.com/docs/reverb)
