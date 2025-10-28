# Mobile API Integration Summary for Claude AI

## Overview
This document provides a comprehensive guide for integrating the Alem platform REST API into a React Native mobile application. The API is built with Laravel and provides complete functionality for applicants to manage their profile, browse vacancies and services, chat with organizations, and view their orders.

## Base Information
- **API Base URL**: `https://your-domain.com/api/v1`
- **Authentication**: Laravel Sanctum (Bearer Token)
- **Response Format**: JSON
- **API Version**: v1

## Architecture

### Technology Stack
- **Backend**: Laravel 11 with Sanctum authentication
- **WebSocket**: Laravel Reverb for real-time chat
- **Database**: MySQL/PostgreSQL
- **Storage**: Local/S3 for file uploads
- **Broadcasting**: Laravel Echo + Pusher protocol

### Data Models
1. **User** - Authentication account
2. **Applicant** - Profile linked to User
3. **Organization** - Companies/employers
4. **Vacancy** - Job postings
5. **Service** - Services offered by organizations
6. **Order** - Service orders placed by applicants
7. **Chat** - Conversation between applicant and organization
8. **Message** - Individual chat messages with reply support
9. **Document** - Uploaded documents (passport, etc.)

## API Endpoints Structure

### 🔐 Authentication (`/auth`)
- `POST /register` - Create new applicant account
- `POST /login` - Authenticate user
- `POST /logout` - Revoke token
- `GET /me` - Get current user info
- `POST /refresh` - Refresh auth token

**Key Features**:
- Password hashing with bcrypt
- Token-based authentication
- Automatic applicant profile creation
- Token refresh mechanism

### 👤 Profile Management (`/profile`)
- `GET /` - Get profile with documents
- `PUT /` - Update profile information
- `POST /password` - Change password
- `POST /avatar` - Upload profile picture
- `POST /documents` - Upload documents
- `DELETE /documents/{id}` - Delete document

**Key Features**:
- Multi-field profile updates
- Image upload with validation
- Document management (PDF, images)
- Balance display

### 💼 Vacancies (`/vacancies`)
- `GET /` - List vacancies with filters
- `GET /{id}` - Get single vacancy details
- `GET /filters/cities` - Get available cities
- `GET /filters/types` - Get vacancy types

**Filters Available**:
- Search (title, description)
- City, Type
- Salary range (min/max)
- Organization
- Sort by multiple fields
- Pagination

### 🛍️ Services (`/services`)
- `GET /` - List services with filters
- `GET /{id}` - Get single service details

**Filters Available**:
- Search (name, description)
- Price range (min/max)
- Organization, Category
- Sort by date or popularity
- Pagination

### 📦 Orders (`/orders`) - Read Only
- `GET /` - List applicant's orders
- `GET /{id}` - Get order details
- `GET /filters/statuses` - Get status options

**Features**:
- View order history
- Filter by status, date range, organization
- See order services breakdown
- Track order status with colors
- No creation/modification (managed by organizations)

### 💬 Chat (`/chats`)
- `GET /` - List all chats
- `POST /get-or-create` - Get or create chat with organization
- `GET /{chatId}/messages` - Get chat messages
- `POST /{chatId}/messages` - Send message
- `POST /{chatId}/mark-read` - Mark messages as read

**Features**:
- Real-time messaging via WebSocket
- Reply to messages
- File attachments (images, PDFs, docs)
- Read receipts
- Unread message counts
- Message pagination

## WebSocket Integration

### Connection Setup
```javascript
// Using Laravel Echo + Pusher
const echo = new Echo({
  broadcaster: 'reverb',
  key: 'your-app-key',
  wsHost: 'your-domain.com',
  wsPort: 8080,
  authEndpoint: '/broadcasting/auth',
  auth: {
    headers: {
      Authorization: `Bearer ${token}`
    }
  }
});
```

### Real-time Events
1. **MessageSent** - New message in chat
2. **MessageRead** - Message read by recipient
3. **UserTyping** - Typing indicator (whisper event)

### Channel Types
- **Private Channels**: `chat.{chatId}` - Chat messages
- **Presence Channels**: Future feature for online status

## Security Features

### Authentication
- Token-based authentication with Sanctum
- Tokens stored securely (AsyncStorage in React Native)
- Automatic token refresh
- Token revocation on logout

### Authorization
- Route middleware protection
- User can only access own data
- Chat participants verification
- Document ownership validation

### Data Validation
- Server-side validation on all inputs
- File type and size restrictions
- Email format validation
- Password strength requirements

## File Upload Specifications

### Avatar Upload
- **Formats**: JPEG, PNG, JPG
- **Max Size**: 2MB
- **Storage**: `storage/avatars/`

### Document Upload
- **Formats**: PDF, JPEG, PNG
- **Max Size**: 5MB
- **Storage**: `storage/documents/`

### Chat Attachments
- **Formats**: JPEG, PNG, PDF, DOC, DOCX
- **Max Size**: 10MB per file
- **Max Count**: 5 files per message
- **Storage**: `storage/chat-attachments/`

## Response Format

### Success Response
```json
{
  "success": true,
  "message": "Optional message",
  "data": {
    // Response data
  }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field": ["Validation error message"]
  }
}
```

### Pagination Response
```json
{
  "success": true,
  "data": {
    "items": [...],
    "pagination": {
      "total": 100,
      "per_page": 15,
      "current_page": 1,
      "last_page": 7,
      "from": 1,
      "to": 15
    }
  }
}
```

## React Native Implementation Guide

### Recommended Libraries
```json
{
  "axios": "^1.6.0",
  "laravel-echo": "^1.15.0",
  "pusher-js": "^8.3.0",
  "@react-native-async-storage/async-storage": "^1.19.0",
  "react-native-image-picker": "^7.0.0",
  "react-native-document-picker": "^9.0.0"
}
```

### Project Structure
```
src/
├── api/
│   ├── client.js          # Axios instance
│   ├── auth.js            # Auth endpoints
│   ├── vacancies.js       # Vacancy endpoints
│   ├── services.js        # Service endpoints
│   ├── orders.js          # Order endpoints
│   ├── chat.js            # Chat endpoints
│   └── profile.js         # Profile endpoints
├── contexts/
│   ├── AuthContext.js     # Authentication state
│   └── EchoContext.js     # WebSocket connection
├── screens/
│   ├── Auth/
│   ├── Profile/
│   ├── Vacancies/
│   ├── Services/
│   ├── Orders/
│   └── Chat/
└── utils/
    ├── storage.js         # AsyncStorage helpers
    └── validation.js      # Form validation
```

### Key Implementation Points

#### 1. API Client Setup
```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: 'https://your-domain.com/api/v1',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
});

// Request interceptor for auth token
api.interceptors.request.use(async (config) => {
  const token = await AsyncStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Response interceptor for error handling
api.interceptors.response.use(
  response => response,
  async error => {
    if (error.response?.status === 401) {
      // Handle token expiration
      await AsyncStorage.removeItem('auth_token');
      // Navigate to login
    }
    return Promise.reject(error);
  }
);
```

#### 2. Authentication Flow
```javascript
// Registration
const register = async (data) => {
  const response = await api.post('/auth/register', data);
  await AsyncStorage.setItem('auth_token', response.data.data.token);
  return response.data;
};

// Login
const login = async (email, password) => {
  const response = await api.post('/auth/login', { email, password });
  await AsyncStorage.setItem('auth_token', response.data.data.token);
  return response.data;
};

// Logout
const logout = async () => {
  await api.post('/auth/logout');
  await AsyncStorage.removeItem('auth_token');
};
```

#### 3. File Upload
```javascript
import { launchImageLibrary } from 'react-native-image-picker';

const uploadAvatar = async () => {
  const result = await launchImageLibrary({
    mediaType: 'photo',
    quality: 0.8,
  });

  if (!result.didCancel) {
    const formData = new FormData();
    formData.append('avatar', {
      uri: result.assets[0].uri,
      type: result.assets[0].type,
      name: result.assets[0].fileName,
    });

    const response = await api.post('/profile/avatar', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    
    return response.data;
  }
};
```

#### 4. Chat with WebSocket
```javascript
// Subscribe to chat
useEffect(() => {
  if (!echo || !chatId) return;

  const channel = echo.private(`chat.${chatId}`);
  
  channel.listen('MessageSent', (event) => {
    setMessages(prev => [...prev, event.message]);
  });

  return () => {
    channel.stopListening('MessageSent');
    echo.leave(`chat.${chatId}`);
  };
}, [echo, chatId]);

// Send message
const sendMessage = async (content, attachments = []) => {
  const formData = new FormData();
  formData.append('content', content);
  
  attachments.forEach((file, index) => {
    formData.append(`attachments[${index}]`, file);
  });

  await api.post(`/chats/${chatId}/messages`, formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  });
};
```

## Testing Recommendations

### Unit Tests
- API client configuration
- Authentication flow
- Data parsing and validation
- Error handling

### Integration Tests
- Login/logout flow
- Profile updates
- Message sending
- File uploads

### E2E Tests
- Complete user journey
- Chat functionality
- Real-time updates
- Offline handling

## Performance Optimization

### API Calls
- Implement request debouncing for search
- Cache static data (cities, types)
- Use pagination for lists
- Implement pull-to-refresh

### WebSocket
- Reconnection logic for poor networks
- Message queue for offline sending
- Lazy load old messages
- Disconnect when app in background

### Images
- Image compression before upload
- Lazy loading in lists
- Cache images locally
- Use thumbnail versions

## Error Handling

### Network Errors
```javascript
try {
  const response = await api.get('/vacancies');
} catch (error) {
  if (error.code === 'ECONNABORTED') {
    // Timeout
  } else if (error.response) {
    // Server error response
    const status = error.response.status;
    const message = error.response.data.message;
  } else if (error.request) {
    // No response received
    // Show offline message
  } else {
    // Request setup error
  }
}
```

### Validation Errors
```javascript
if (error.response?.status === 422) {
  const errors = error.response.data.errors;
  // Display field-specific errors
  Object.keys(errors).forEach(field => {
    showFieldError(field, errors[field][0]);
  });
}
```

## Best Practices

### Security
- Never log tokens or sensitive data
- Store tokens in secure storage (Keychain/Keystore)
- Implement token refresh before expiry
- Validate all user inputs client-side

### UX
- Show loading states
- Implement optimistic updates
- Handle offline scenarios gracefully
- Provide clear error messages
- Cache for offline viewing

### Performance
- Lazy load components
- Use FlatList for long lists
- Implement virtual scrolling
- Debounce search inputs
- Compress images before upload

## Troubleshooting

### Common Issues

**1. 401 Unauthorized**
- Check token is stored and sent
- Verify token hasn't expired
- Ensure Bearer prefix in header

**2. WebSocket Connection Fails**
- Check host and port configuration
- Verify SSL certificate for WSS
- Check authorization endpoint

**3. File Upload Fails**
- Check file size limits
- Verify MIME type
- Ensure multipart/form-data header

**4. Pagination Not Working**
- Check page parameter starts at 1
- Verify per_page parameter
- Check hasMore condition

## Additional Resources

- **Full API Documentation**: See `API.md`
- **WebSocket Guide**: See `WEBSOCKET.md`
- **Laravel Sanctum**: https://laravel.com/docs/sanctum
- **Laravel Echo**: https://laravel.com/docs/broadcasting
- **React Native**: https://reactnative.dev

## Support

For questions or issues:
1. Check API documentation
2. Review error messages and logs
3. Test endpoints with Postman/Insomnia
4. Contact backend development team

---

**Last Updated**: October 28, 2025
**API Version**: 1.0.0
