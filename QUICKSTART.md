# Quick Start Guide - Mobile API

## 🎯 What You Have

A complete REST API for mobile application with:
- ✅ 6 Controllers with 25+ endpoints
- ✅ Authentication (register, login, logout, refresh)
- ✅ Profile management (update, avatar, documents)
- ✅ Vacancies listing with filters
- ✅ Services listing with filters
- ✅ Orders viewing (read-only)
- ✅ Full chat system with WebSocket
- ✅ Complete documentation

## 📂 Files Created

### Controllers (`app/Http/Controllers/Api/`)
```
✓ AuthController.php      - Registration, login, token management
✓ ProfileController.php    - Profile, avatar, documents
✓ VacancyController.php    - Vacancy listing and filtering
✓ ServiceController.php    - Service listing and filtering
✓ OrderController.php      - Order viewing (read-only)
✓ ChatController.php       - Real-time chat with attachments
```

### Routes
```
✓ routes/api.php - All /api/v1/* endpoints configured
```

### Models Updated
```
✓ app/Models/Applicant.php - Added relationships and attributes
✓ app/Models/Vacancy.php    - Added salary fields and accessor
✓ app/Models/Service.php    - Added status, image, category
```

### Documentation
```
✓ API.md                        - Complete API reference
✓ WEBSOCKET.md                  - WebSocket integration guide
✓ MOBILE_INTEGRATION_GUIDE.md   - Architecture & best practices
✓ API_IMPLEMENTATION.md         - Implementation summary
```

## 🚀 Quick Test

### 1. Test Registration
```bash
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### 2. Test Login
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

Save the token from response!

### 3. Test Authenticated Endpoint
```bash
curl -X GET http://localhost:8000/api/v1/profile \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### 4. Test Vacancies (Public)
```bash
curl -X GET "http://localhost:8000/api/v1/vacancies?per_page=5" \
  -H "Accept: application/json"
```

### 5. Test Chat
```bash
# Create/get chat
curl -X POST http://localhost:8000/api/v1/chats/get-or-create \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{"organization_id": "YOUR_ORG_ID"}'

# Send message
curl -X POST http://localhost:8000/api/v1/chats/CHAT_ID/messages \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{"content": "Hello!"}'
```

## 📱 React Native Quick Setup

### 1. Install Dependencies
```bash
npm install axios laravel-echo pusher-js @react-native-async-storage/async-storage
```

### 2. Create API Client (`src/api/client.js`)
```javascript
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

const api = axios.create({
  baseURL: 'http://your-domain.com/api/v1',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
});

api.interceptors.request.use(async (config) => {
  const token = await AsyncStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default api;
```

### 3. Login Function
```javascript
import api from './api/client';
import AsyncStorage from '@react-native-async-storage/async-storage';

export const login = async (email, password) => {
  try {
    const response = await api.post('/auth/login', { email, password });
    const { token, applicant } = response.data.data;
    
    await AsyncStorage.setItem('auth_token', token);
    await AsyncStorage.setItem('user', JSON.stringify(applicant));
    
    return response.data;
  } catch (error) {
    console.error('Login error:', error.response?.data);
    throw error;
  }
};
```

### 4. Get Vacancies
```javascript
import api from './api/client';

export const getVacancies = async (filters = {}) => {
  try {
    const response = await api.get('/vacancies', { params: filters });
    return response.data.data;
  } catch (error) {
    console.error('Get vacancies error:', error);
    throw error;
  }
};

// Usage
const vacancies = await getVacancies({
  search: 'developer',
  city: 'Almaty',
  per_page: 10,
  page: 1
});
```

### 5. Setup WebSocket (See WEBSOCKET.md for full guide)
```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const echo = new Echo({
  broadcaster: 'reverb',
  key: 'your-app-key',
  wsHost: 'your-domain.com',
  wsPort: 8080,
  authEndpoint: 'https://your-domain.com/broadcasting/auth',
  auth: {
    headers: {
      Authorization: `Bearer ${token}`,
    },
  },
});

// Listen to chat
echo.private(`chat.${chatId}`)
  .listen('MessageSent', (event) => {
    console.log('New message:', event.message);
  });
```

## 🔍 API Endpoints Summary

### Public (No Auth)
```
POST   /auth/register
POST   /auth/login
GET    /vacancies
GET    /vacancies/{id}
GET    /vacancies/filters/cities
GET    /vacancies/filters/types
GET    /services
GET    /services/{id}
```

### Protected (Requires Auth Token)
```
POST   /auth/logout
GET    /auth/me
POST   /auth/refresh

GET    /profile
PUT    /profile
POST   /profile/password
POST   /profile/avatar
POST   /profile/documents
DELETE /profile/documents/{id}

GET    /chats
POST   /chats/get-or-create
GET    /chats/{id}/messages
POST   /chats/{id}/messages
POST   /chats/{id}/mark-read

GET    /orders
GET    /orders/{id}
GET    /orders/filters/statuses
```

## 📖 Documentation Files

1. **API.md** 
   - Complete endpoint documentation
   - Request/response examples
   - Authentication guide

2. **WEBSOCKET.md**
   - WebSocket setup for React Native
   - Real-time chat implementation
   - Event handling examples

3. **MOBILE_INTEGRATION_GUIDE.md**
   - Architecture overview
   - Best practices
   - Security guidelines
   - Performance tips

4. **API_IMPLEMENTATION.md**
   - What was implemented
   - File structure
   - Next steps

## ⚙️ Environment Setup

Add to your `.env`:
```env
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
SESSION_DOMAIN=localhost

BROADCAST_DRIVER=reverb
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=your-domain.com
REVERB_PORT=8080
REVERB_SCHEME=https
```

Run:
```bash
php artisan storage:link
php artisan config:cache
php artisan route:cache
```

## 🎨 Design Changes

The dashboard was also updated to be more professional:
- ✅ Removed all emojis
- ✅ Changed colorful gradients to clean gray/white
- ✅ Simplified design elements
- ✅ Made it more business-oriented

## 🐛 Common Issues

**401 Unauthorized**
- Token missing or invalid
- Add `Bearer` prefix to token

**422 Validation Error**
- Check request body format
- Verify required fields

**WebSocket Connection Failed**
- Check Reverb is running
- Verify host and port

**File Upload Failed**
- Check file size limits
- Verify MIME type

## 📞 Next Steps

For Mobile Development:
1. Read `MOBILE_INTEGRATION_GUIDE.md`
2. Follow `WEBSOCKET.md` for chat
3. Reference `API.md` for endpoints
4. Test each endpoint with Postman first
5. Build React Native screens
6. Implement WebSocket for chat

For Backend:
1. API is ready to use ✅
2. Test with provided cURL examples
3. Monitor logs for errors
4. Adjust as needed

## ✨ Features Summary

| Feature | Status | Endpoints |
|---------|--------|-----------|
| Authentication | ✅ Complete | 5 |
| Profile Management | ✅ Complete | 6 |
| Vacancies | ✅ Complete | 4 |
| Services | ✅ Complete | 2 |
| Orders | ✅ Complete | 3 |
| Chat | ✅ Complete | 5 |
| WebSocket | ✅ Complete | - |
| Documentation | ✅ Complete | 4 files |

**Total: 25 endpoints + WebSocket support**

---

🎉 **Your API is ready for mobile development!**

Start with the Quick Test section above, then dive into React Native integration using the documentation files.

Good luck! 🚀
