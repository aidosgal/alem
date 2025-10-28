# Alem Mobile API Documentation

## Base URL
```
https://your-domain.com/api/v1
```

## Authentication

The API uses Laravel Sanctum for authentication with Bearer tokens.

### Headers
All authenticated requests must include:
```
Authorization: Bearer {your-token}
Accept: application/json
Content-Type: application/json
```

---

## 🔐 Authentication

### Register
Create a new applicant account.

**Endpoint:** `POST /auth/register`

**Request Body:**
```json
{
  "first_name": "John",
  "last_name": "Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "+77001234567",
  "city": "Almaty",
  "date_of_birth": "1990-01-01"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": {
      "id": 1,
      "email": "john@example.com"
    },
    "applicant": {
      "id": 1,
      "first_name": "John",
      "last_name": "Doe",
      "full_name": "John Doe",
      "phone": "+77001234567",
      "city": "Almaty",
      "date_of_birth": "1990-01-01",
      "avatar": null
    },
    "token": "1|abcdef123456..."
  }
}
```

### Login
Authenticate an existing user.

**Endpoint:** `POST /auth/login`

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "email": "john@example.com"
    },
    "applicant": {
      "id": 1,
      "first_name": "John",
      "last_name": "Doe",
      "full_name": "John Doe",
      "phone": "+77001234567",
      "city": "Almaty",
      "date_of_birth": "1990-01-01",
      "avatar": null
    },
    "token": "2|xyz789..."
  }
}
```

### Get Current User
Get authenticated user information.

**Endpoint:** `GET /auth/me`

**Headers:** Requires authentication

**Response (200):**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "email": "john@example.com"
    },
    "applicant": {
      "id": 1,
      "first_name": "John",
      "last_name": "Doe",
      "full_name": "John Doe",
      "phone": "+77001234567",
      "city": "Almaty",
      "date_of_birth": "1990-01-01",
      "avatar": null,
      "balance": 0
    }
  }
}
```

### Refresh Token
Refresh the authentication token.

**Endpoint:** `POST /auth/refresh`

**Headers:** Requires authentication

**Response (200):**
```json
{
  "success": true,
  "message": "Token refreshed successfully",
  "data": {
    "token": "3|new-token..."
  }
}
```

### Logout
Revoke the current token.

**Endpoint:** `POST /auth/logout`

**Headers:** Requires authentication

**Response (200):**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

---

## 👤 Profile Management

### Get Profile
Get applicant profile with documents.

**Endpoint:** `GET /profile`

**Headers:** Requires authentication

**Response (200):**
```json
{
  "success": true,
  "data": {
    "applicant": {
      "id": 1,
      "first_name": "John",
      "last_name": "Doe",
      "full_name": "John Doe",
      "email": "john@example.com",
      "phone": "+77001234567",
      "city": "Almaty",
      "date_of_birth": "1990-01-01",
      "avatar": null,
      "balance": 0,
      "documents": [
        {
          "id": 1,
          "type": "passport",
          "file_path": "documents/abc123.pdf",
          "file_url": "/storage/documents/abc123.pdf",
          "uploaded_at": "2025-10-28T10:00:00.000000Z"
        }
      ]
    }
  }
}
```

### Update Profile
Update applicant profile information.

**Endpoint:** `PUT /profile`

**Headers:** Requires authentication

**Request Body:**
```json
{
  "first_name": "John",
  "last_name": "Smith",
  "phone": "+77001234567",
  "city": "Astana",
  "date_of_birth": "1990-01-01",
  "email": "john.smith@example.com"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Profile updated successfully",
  "data": {
    "applicant": {
      "id": 1,
      "first_name": "John",
      "last_name": "Smith",
      "full_name": "John Smith",
      "email": "john.smith@example.com",
      "phone": "+77001234567",
      "city": "Astana",
      "date_of_birth": "1990-01-01",
      "avatar": null
    }
  }
}
```

### Update Password
Change user password.

**Endpoint:** `POST /profile/password`

**Headers:** Requires authentication

**Request Body:**
```json
{
  "current_password": "oldpassword123",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Password updated successfully",
  "data": {
    "token": "4|new-token-after-password-change..."
  }
}
```

### Upload Avatar
Upload profile avatar image.

**Endpoint:** `POST /profile/avatar`

**Headers:** Requires authentication, `Content-Type: multipart/form-data`

**Request Body (Form Data):**
- `avatar`: Image file (jpeg, png, jpg, max 2MB)

**Response (200):**
```json
{
  "success": true,
  "message": "Avatar uploaded successfully",
  "data": {
    "avatar": "avatars/abc123.jpg",
    "avatar_url": "/storage/avatars/abc123.jpg"
  }
}
```

### Upload Document
Upload a document (passport, ID, etc.).

**Endpoint:** `POST /profile/documents`

**Headers:** Requires authentication, `Content-Type: multipart/form-data`

**Request Body (Form Data):**
- `type`: Document type (string, e.g., "passport", "id_card")
- `document`: File (pdf, jpg, jpeg, png, max 5MB)

**Response (201):**
```json
{
  "success": true,
  "message": "Document uploaded successfully",
  "data": {
    "document": {
      "id": 1,
      "type": "passport",
      "file_path": "documents/xyz789.pdf",
      "file_url": "/storage/documents/xyz789.pdf",
      "uploaded_at": "2025-10-28T10:00:00.000000Z"
    }
  }
}
```

### Delete Document
Delete a previously uploaded document.

**Endpoint:** `DELETE /profile/documents/{id}`

**Headers:** Requires authentication

**Response (200):**
```json
{
  "success": true,
  "message": "Document deleted successfully"
}
```

---

## 💼 Vacancies

### List Vacancies
Get a paginated list of active vacancies with optional filters.

**Endpoint:** `GET /vacancies`

**Query Parameters:**
- `search` (optional): Search in title or description
- `city` (optional): Filter by city
- `type` (optional): Filter by vacancy type
- `min_salary` (optional): Minimum salary
- `max_salary` (optional): Maximum salary
- `organization_id` (optional): Filter by organization
- `sort_by` (optional): Field to sort by (default: `created_at`)
- `sort_order` (optional): Sort order `asc` or `desc` (default: `desc`)
- `per_page` (optional): Results per page (default: 15)
- `page` (optional): Page number

**Example Request:**
```
GET /vacancies?search=developer&city=Almaty&min_salary=200000&per_page=10&page=1
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "vacancies": [
      {
        "id": 1,
        "title": "Senior Developer",
        "description": "We are looking for...",
        "requirements": "5+ years experience...",
        "type": "full-time",
        "city": "Almaty",
        "address": "123 Tech Street",
        "salary_from": 250000,
        "salary_to": 350000,
        "salary_display": "250 000 - 350 000 ₸",
        "status": "active",
        "organization": {
          "id": 1,
          "name": "Tech Company",
          "logo": "/storage/logos/company.png"
        },
        "created_at": "2025-10-28T10:00:00.000000Z",
        "updated_at": "2025-10-28T10:00:00.000000Z"
      }
    ],
    "pagination": {
      "total": 50,
      "per_page": 10,
      "current_page": 1,
      "last_page": 5,
      "from": 1,
      "to": 10
    }
  }
}
```

### Get Single Vacancy
Get detailed information about a specific vacancy.

**Endpoint:** `GET /vacancies/{id}`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "vacancy": {
      "id": 1,
      "title": "Senior Developer",
      "description": "We are looking for...",
      "requirements": "5+ years experience...",
      "type": "full-time",
      "city": "Almaty",
      "address": "123 Tech Street",
      "salary_from": 250000,
      "salary_to": 350000,
      "salary_display": "250 000 - 350 000 ₸",
      "status": "active",
      "organization": {
        "id": 1,
        "name": "Tech Company",
        "description": "Leading tech company...",
        "logo": "/storage/logos/company.png",
        "address": "123 Tech Street, Almaty",
        "phone": "+77001234567"
      },
      "created_at": "2025-10-28T10:00:00.000000Z",
      "updated_at": "2025-10-28T10:00:00.000000Z"
    }
  }
}
```

### Get Cities for Filter
Get list of unique cities from active vacancies.

**Endpoint:** `GET /vacancies/filters/cities`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "cities": ["Almaty", "Astana", "Shymkent"]
  }
}
```

### Get Types for Filter
Get list of unique vacancy types.

**Endpoint:** `GET /vacancies/filters/types`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "types": ["full-time", "part-time", "contract", "remote"]
  }
}
```

---

## 🛍️ Services

### List Services
Get a paginated list of active services with optional filters.

**Endpoint:** `GET /services`

**Query Parameters:**
- `search` (optional): Search in name or description
- `organization_id` (optional): Filter by organization
- `min_price` (optional): Minimum price
- `max_price` (optional): Maximum price
- `category` (optional): Filter by category
- `sort_by` (optional): Field to sort by or `popular` for popularity (default: `created_at`)
- `sort_order` (optional): Sort order `asc` or `desc` (default: `desc`)
- `per_page` (optional): Results per page (default: 15)
- `page` (optional): Page number

**Example Request:**
```
GET /services?search=cleaning&min_price=5000&sort_by=popular&per_page=10
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "services": [
      {
        "id": 1,
        "name": "Office Cleaning",
        "description": "Professional office cleaning service",
        "price": 15000,
        "status": "active",
        "image": "/storage/services/cleaning.jpg",
        "organization": {
          "id": 1,
          "name": "Clean Pro",
          "logo": "/storage/logos/cleanpro.png"
        },
        "created_at": "2025-10-28T10:00:00.000000Z",
        "updated_at": "2025-10-28T10:00:00.000000Z"
      }
    ],
    "pagination": {
      "total": 25,
      "per_page": 10,
      "current_page": 1,
      "last_page": 3,
      "from": 1,
      "to": 10
    }
  }
}
```

### Get Single Service
Get detailed information about a specific service.

**Endpoint:** `GET /services/{id}`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "service": {
      "id": 1,
      "name": "Office Cleaning",
      "description": "Professional office cleaning service with experienced staff...",
      "price": 15000,
      "status": "active",
      "image": "/storage/services/cleaning.jpg",
      "organization": {
        "id": 1,
        "name": "Clean Pro",
        "description": "Professional cleaning company...",
        "logo": "/storage/logos/cleanpro.png",
        "address": "456 Service Ave, Almaty",
        "phone": "+77001234567"
      },
      "created_at": "2025-10-28T10:00:00.000000Z",
      "updated_at": "2025-10-28T10:00:00.000000Z"
    }
  }
}
```

---

## 📦 Orders (Read-Only)

### List Orders
Get a paginated list of applicant's orders with filters.

**Endpoint:** `GET /orders`

**Headers:** Requires authentication

**Query Parameters:**
- `status_id` (optional): Filter by status ID
- `organization_id` (optional): Filter by organization
- `from_date` (optional): Filter by start date (YYYY-MM-DD)
- `to_date` (optional): Filter by end date (YYYY-MM-DD)
- `sort_by` (optional): Field to sort by (default: `created_at`)
- `sort_order` (optional): Sort order `asc` or `desc` (default: `desc`)
- `per_page` (optional): Results per page (default: 15)
- `page` (optional): Page number

**Example Request:**
```
GET /orders?status_id=2&from_date=2025-10-01&per_page=10
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "orders": [
      {
        "id": 1,
        "price": 45000,
        "status": {
          "id": 2,
          "name": "В процессе",
          "color": "#3B82F6"
        },
        "organization": {
          "id": 1,
          "name": "Clean Pro",
          "logo": "/storage/logos/cleanpro.png"
        },
        "services_count": 3,
        "created_at": "2025-10-28T10:00:00.000000Z",
        "updated_at": "2025-10-28T10:00:00.000000Z"
      }
    ],
    "pagination": {
      "total": 15,
      "per_page": 10,
      "current_page": 1,
      "last_page": 2,
      "from": 1,
      "to": 10
    }
  }
}
```

### Get Single Order
Get detailed information about a specific order.

**Endpoint:** `GET /orders/{id}`

**Headers:** Requires authentication

**Response (200):**
```json
{
  "success": true,
  "data": {
    "order": {
      "id": 1,
      "price": 45000,
      "status": {
        "id": 2,
        "name": "В процессе",
        "color": "#3B82F6",
        "description": "Заказ выполняется"
      },
      "organization": {
        "id": 1,
        "name": "Clean Pro",
        "logo": "/storage/logos/cleanpro.png",
        "address": "456 Service Ave, Almaty",
        "phone": "+77001234567"
      },
      "services": [
        {
          "id": 1,
          "name": "Office Cleaning",
          "description": "Professional office cleaning",
          "price": 15000,
          "quantity": 1
        },
        {
          "id": 2,
          "name": "Window Cleaning",
          "description": "Clean all windows",
          "price": 30000,
          "quantity": 1
        }
      ],
      "created_at": "2025-10-28T10:00:00.000000Z",
      "updated_at": "2025-10-28T10:00:00.000000Z"
    }
  }
}
```

### Get Order Statuses
Get list of unique statuses from applicant's orders (for filtering).

**Endpoint:** `GET /orders/filters/statuses`

**Headers:** Requires authentication

**Response (200):**
```json
{
  "success": true,
  "data": {
    "statuses": [
      {
        "id": 1,
        "name": "Новый",
        "color": "#10B981"
      },
      {
        "id": 2,
        "name": "В процессе",
        "color": "#3B82F6"
      }
    ]
  }
}
```

---

## 💬 Chat

### List Chats
Get all chats for the authenticated applicant.

**Endpoint:** `GET /chats`

**Headers:** Requires authentication

**Response (200):**
```json
{
  "success": true,
  "data": {
    "chats": [
      {
        "id": 1,
        "organization": {
          "id": 1,
          "name": "Tech Company",
          "logo": "/storage/logos/company.png"
        },
        "last_message": {
          "id": 42,
          "content": "Hello, how can we help you?",
          "sender_type": "manager",
          "has_attachments": false,
          "created_at": "2025-10-28T10:00:00.000000Z"
        },
        "unread_count": 2,
        "updated_at": "2025-10-28T10:00:00.000000Z"
      }
    ]
  }
}
```

### Get or Create Chat
Get existing chat or create a new one with an organization.

**Endpoint:** `POST /chats/get-or-create`

**Headers:** Requires authentication

**Request Body:**
```json
{
  "organization_id": 1
}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "chat": {
      "id": 1,
      "organization": {
        "id": 1,
        "name": "Tech Company",
        "logo": "/storage/logos/company.png"
      },
      "created_at": "2025-10-28T10:00:00.000000Z"
    }
  }
}
```

### Get Chat Messages
Get paginated messages for a specific chat.

**Endpoint:** `GET /chats/{chatId}/messages`

**Headers:** Requires authentication

**Query Parameters:**
- `per_page` (optional): Results per page (default: 50)
- `page` (optional): Page number

**Response (200):**
```json
{
  "success": true,
  "data": {
    "messages": [
      {
        "id": 1,
        "content": "Hello, I need help with my order",
        "sender_type": "applicant",
        "is_read": true,
        "replied_to": null,
        "attachments": [],
        "created_at": "2025-10-28T10:00:00.000000Z"
      },
      {
        "id": 2,
        "content": "Sure, I can help you with that",
        "sender_type": "manager",
        "is_read": true,
        "replied_to": {
          "id": 1,
          "content": "Hello, I need help with my order",
          "sender_type": "applicant",
          "attachments": []
        },
        "attachments": [
          {
            "id": 1,
            "file_path": "chat-attachments/abc123.pdf",
            "file_url": "/storage/chat-attachments/abc123.pdf",
            "file_type": "application/pdf",
            "file_name": "order_details.pdf"
          }
        ],
        "created_at": "2025-10-28T10:05:00.000000Z"
      }
    ],
    "pagination": {
      "total": 50,
      "per_page": 50,
      "current_page": 1,
      "last_page": 1
    }
  }
}
```

### Send Message
Send a message in a chat with optional reply and attachments.

**Endpoint:** `POST /chats/{chatId}/messages`

**Headers:** Requires authentication, `Content-Type: multipart/form-data`

**Request Body (Form Data):**
- `content` (optional if attachments): Message text
- `replied_to_id` (optional): ID of message to reply to
- `attachments[]` (optional): Array of files (max 5, each max 10MB)

**Example with JSON (no attachments):**
```json
{
  "content": "Thank you for your help!",
  "replied_to_id": 2
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Message sent successfully",
  "data": {
    "message": {
      "id": 3,
      "content": "Thank you for your help!",
      "sender_type": "applicant",
      "is_read": false,
      "replied_to": {
        "id": 2,
        "content": "Sure, I can help you with that",
        "sender_type": "manager",
        "attachments": []
      },
      "attachments": [],
      "created_at": "2025-10-28T10:10:00.000000Z"
    }
  }
}
```

### Mark Messages as Read
Mark all unread manager messages in a chat as read.

**Endpoint:** `POST /chats/{chatId}/mark-read`

**Headers:** Requires authentication

**Response (200):**
```json
{
  "success": true,
  "message": "Messages marked as read"
}
```

---

## ❌ Error Responses

### Validation Error (422)
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

### Unauthorized (401)
```json
{
  "success": false,
  "message": "Invalid credentials"
}
```

### Not Found (404)
```json
{
  "success": false,
  "message": "Resource not found"
}
```

### Server Error (500)
```json
{
  "success": false,
  "message": "Internal server error"
}
```

---

## 📱 React Native Integration Tips

### Storing the Token
```javascript
import AsyncStorage from '@react-native-async-storage/async-storage';

// Save token after login
await AsyncStorage.setItem('auth_token', token);

// Get token for requests
const token = await AsyncStorage.getItem('auth_token');
```

### Making API Requests with Axios
```javascript
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

const api = axios.create({
  baseURL: 'https://your-domain.com/api/v1',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
});

// Add token to requests
api.interceptors.request.use(async (config) => {
  const token = await AsyncStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Handle errors
api.interceptors.response.use(
  response => response,
  async error => {
    if (error.response?.status === 401) {
      // Token expired, logout user
      await AsyncStorage.removeItem('auth_token');
      // Navigate to login screen
    }
    return Promise.reject(error);
  }
);

export default api;
```

### Uploading Files
```javascript
import api from './api';

const uploadAvatar = async (uri) => {
  const formData = new FormData();
  formData.append('avatar', {
    uri,
    type: 'image/jpeg',
    name: 'avatar.jpg',
  });

  const response = await api.post('/profile/avatar', formData, {
    headers: {
      'Content-Type': 'multipart/form-data',
    },
  });

  return response.data;
};
```

### Pagination Example
```javascript
const [vacancies, setVacancies] = useState([]);
const [page, setPage] = useState(1);
const [loading, setLoading] = useState(false);
const [hasMore, setHasMore] = useState(true);

const loadVacancies = async () => {
  if (loading || !hasMore) return;
  
  setLoading(true);
  try {
    const response = await api.get('/vacancies', {
      params: { page, per_page: 15 }
    });
    
    const newVacancies = response.data.data.vacancies;
    setVacancies(prev => [...prev, ...newVacancies]);
    setHasMore(response.data.data.pagination.current_page < response.data.data.pagination.last_page);
    setPage(prev => prev + 1);
  } catch (error) {
    console.error('Error loading vacancies:', error);
  } finally {
    setLoading(false);
  }
};
```

---

## 🔄 Rate Limiting

The API implements rate limiting:
- 60 requests per minute for authenticated users
- 30 requests per minute for unauthenticated users

If you exceed the limit, you'll receive a `429 Too Many Requests` response.

---

## 📝 Notes

1. All dates are returned in ISO 8601 format (UTC timezone)
2. All prices are in KZT (Kazakhstani Tenge)
3. File URLs are relative and need to be prefixed with your domain
4. For real-time chat updates, use WebSocket (see WEBSOCKET.md)
5. Keep your authentication token secure and never commit it to version control

---

## 🆘 Support

For API support or questions, please contact the development team or create an issue in the project repository.
