# API Endpoints Documentation

Base URL: `/api/v1`

## 📋 Table of Contents
- [Organizations](#organizations)
- [Vacancies](#vacancies)
- [Services](#services)

---

## 🏢 Organizations

### List All Organizations
Get a paginated list of all organizations with vacancy count.

**Endpoint:** `GET /api/v1/organizations`

**Query Parameters:**
- `search` (optional) - Search by organization name or description
- `page` (optional) - Page number for pagination

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": "uuid",
      "name": "Organization Name",
      "description": "Organization description",
      "image": "path/to/image.jpg",
      "email": "contact@organization.com",
      "phone": "+7 777 123 4567",
      "vacancies_count": 5
    }
  ]
}
```

---

### Get Organization Details with Services
Get detailed information about a specific organization including all active services.

**Endpoint:** `GET /api/v1/organizations/{id}`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "uuid",
    "name": "Organization Name",
    "description": "Organization description",
    "image": "path/to/image.jpg",
    "email": "contact@organization.com",
    "phone": "+7 777 123 4567",
    "services": [
      {
        "id": "uuid",
        "title": "Service Name",
        "description": "Service description",
        "price": "150000.00",
        "duration_days": 30,
        "duration_min_days": 20,
        "duration_max_days": 40
      }
    ]
  }
}
```

---

## 💼 Vacancies

### List All Vacancies
Get a paginated list of vacancies with filtering and search.

**Endpoint:** `GET /api/v1/vacancies`

**Query Parameters:**
- `search` (optional) - Search by title or description
- `organization_id` (optional) - Filter by organization UUID
- `location` (optional) - Filter by location (partial match)
- `employment_type` (optional) - Filter by employment type (exact match)
- `min_salary` (optional) - Minimum salary filter
- `max_salary` (optional) - Maximum salary filter
- `sort_by` (optional) - Sort field: `created_at`, `updated_at`, `title` (default: `created_at`)
- `sort_order` (optional) - Sort order: `asc` or `desc` (default: `desc`)
- `per_page` (optional) - Items per page (default: 15)
- `page` (optional) - Page number

**Response:**
```json
{
  "success": true,
  "data": {
    "vacancies": [
      {
        "id": "uuid",
        "title": "Vacancy Title",
        "description": "Vacancy description",
        "details": {
          "location": "Almaty, Kazakhstan",
          "employment_type": "Full-time",
          "salary_from": 500000,
          "salary_to": 800000,
          "currency": "₸"
        },
        "organization": {
          "id": "uuid",
          "name": "Organization Name",
          "image": "path/to/logo.jpg"
        },
        "created_at": "2025-10-29T12:00:00.000Z",
        "updated_at": "2025-10-29T12:00:00.000Z"
      }
    ],
    "pagination": {
      "total": 50,
      "per_page": 15,
      "current_page": 1,
      "last_page": 4,
      "from": 1,
      "to": 15
    }
  }
}
```

---

### Get Single Vacancy
Get detailed information about a specific vacancy.

**Endpoint:** `GET /api/v1/vacancies/{id}`

**Response:**
```json
{
  "success": true,
  "data": {
    "vacancy": {
      "id": "uuid",
      "title": "Vacancy Title",
      "description": "Detailed vacancy description",
      "details": {
        "location": "Almaty, Kazakhstan",
        "employment_type": "Full-time",
        "salary_from": 500000,
        "salary_to": 800000,
        "currency": "₸"
      },
      "organization": {
        "id": "uuid",
        "name": "Organization Name",
        "description": "Organization description",
        "image": "path/to/logo.jpg",
        "email": "contact@organization.com",
        "phone": "+7 777 123 4567"
      },
      "created_at": "2025-10-29T12:00:00.000Z",
      "updated_at": "2025-10-29T12:00:00.000Z"
    }
  }
}
```

---

### Get Available Locations
Get a list of unique locations from all vacancies for filtering.

**Endpoint:** `GET /api/v1/vacancies/filters/cities`

**Response:**
```json
{
  "success": true,
  "data": {
    "locations": [
      "Almaty, Kazakhstan",
      "Astana, Kazakhstan",
      "Shymkent, Kazakhstan"
    ]
  }
}
```

---

### Get Employment Types
Get a list of unique employment types from all vacancies for filtering.

**Endpoint:** `GET /api/v1/vacancies/filters/types`

**Response:**
```json
{
  "success": true,
  "data": {
    "employment_types": [
      "Full-time",
      "Part-time",
      "Contract",
      "Remote"
    ]
  }
}
```

---

## 🛠️ Services

### List All Services
Get a paginated list of services with filtering and search.

**Endpoint:** `GET /api/v1/services`

**Query Parameters:**
- `search` (optional) - Search by service title or description
- `organization_id` (optional) - Filter by organization UUID
- `min_price` (optional) - Minimum price filter
- `max_price` (optional) - Maximum price filter
- `sort_by` (optional) - Sort field: `created_at`, `updated_at`, `price`, `title`, `popular` (default: `created_at`)
- `sort_order` (optional) - Sort order: `asc` or `desc` (default: `desc`)
- `per_page` (optional) - Items per page (default: 15)
- `page` (optional) - Page number

**Response:**
```json
{
  "success": true,
  "data": {
    "services": [
      {
        "id": "uuid",
        "title": "Service Name",
        "description": "Service description",
        "price": "150000.00",
        "duration_days": 30,
        "duration_min_days": 20,
        "duration_max_days": 40,
        "organization": {
          "id": "uuid",
          "name": "Organization Name",
          "image": "path/to/logo.jpg"
        },
        "created_at": "2025-10-29T12:00:00.000Z",
        "updated_at": "2025-10-29T12:00:00.000Z"
      }
    ],
    "pagination": {
      "total": 30,
      "per_page": 15,
      "current_page": 1,
      "last_page": 2,
      "from": 1,
      "to": 15
    }
  }
}
```

---

### Get Single Service
Get detailed information about a specific service.

**Endpoint:** `GET /api/v1/services/{id}`

**Response:**
```json
{
  "success": true,
  "data": {
    "service": {
      "id": "uuid",
      "title": "Service Name",
      "description": "Detailed service description",
      "price": "150000.00",
      "duration_days": 30,
      "duration_min_days": 20,
      "duration_max_days": 40,
      "organization": {
        "id": "uuid",
        "name": "Organization Name",
        "description": "Organization description",
        "image": "path/to/logo.jpg",
        "email": "contact@organization.com",
        "phone": "+7 777 123 4567"
      },
      "created_at": "2025-10-29T12:00:00.000Z",
      "updated_at": "2025-10-29T12:00:00.000Z"
    }
  }
}
```

---

### Get Service Categories
Get a list of unique service categories for filtering.

**Note:** This endpoint currently returns an empty array as the category field is not yet implemented in the database. It's kept for future compatibility.

**Endpoint:** `GET /api/v1/services/filters/categories`

**Response:**
```json
{
  "success": true,
  "data": {
    "categories": []
  }
}
```

---

## 🔧 Technical Details

### Vacancy Details Structure
The `details` field in vacancies is a JSONB object that can contain:
- `location` (string) - Job location
- `employment_type` (string) - Type of employment (Full-time, Part-time, Contract, Remote)
- `salary_from` (number) - Minimum salary
- `salary_to` (number) - Maximum salary
- `currency` (string) - Currency symbol (default: ₸)

### Error Responses
All endpoints return error responses in the following format:
```json
{
  "success": false,
  "message": "Error message description"
}
```

### HTTP Status Codes
- `200` - Success
- `404` - Resource not found
- `500` - Server error

---

## 📝 Notes

1. All UUIDs are in standard UUID format
2. Dates are returned in ISO 8601 format (UTC)
3. Pagination is available on all list endpoints
4. Filtering parameters can be combined
5. The `details` field in vacancies allows flexible data storage for future extensions
6. Services currently don't have status, image, or category fields - these are planned for future implementation
7. All prices are in decimal format with 2 decimal places
