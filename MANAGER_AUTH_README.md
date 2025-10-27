# Alem Manager Authentication System

A modern, clean authentication system for managers with organization management capabilities.

## 🎨 Design Features

- **Modern UI**: Clean, minimalist design inspired by Jira
- **Accent Colors**: 
  - Primary (Buttons): `#EFFE6D` (Yellow)
  - Secondary: `#319885` (Teal)
- **No Shadows**: Flat, modern design aesthetic
- **Responsive**: Mobile-friendly layouts

## 🏗️ Architecture

The application follows a clean architecture pattern with:

### Repository Layer
- `ManagerRepository`: Handles manager data operations
- `OrganizationRepository`: Manages organization data

### Service Layer
- `AuthService`: Business logic for authentication
- `OrganizationService`: Organization management logic

### Controller Layer
- **Web Controllers** (`app/Http/Controllers/Manager/`):
  - `AuthController`: Login, registration, logout
  - `OrganizationController`: Create/join organizations
  - `DashboardController`: Main dashboard
  
- **API Controllers** (`app/Http/Controllers/Api/`):
  - `ApiController`: Base controller with response helpers
  - Future API endpoints will extend this

## 📁 File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   ├── ApiController.php (Base API controller)
│   │   │   └── Manager/
│   │   │       └── ManagerApiController.php
│   │   └── Manager/
│   │       ├── AuthController.php
│   │       ├── DashboardController.php
│   │       └── OrganizationController.php
│   └── Middleware/
│       └── EnsureManagerHasOrganization.php
├── Models/
│   ├── Manager.php
│   ├── Organization.php
│   ├── OrganizationUsers.php
│   └── User.php
├── Repositories/
│   ├── ManagerRepository.php
│   └── OrganizationRepository.php
└── Services/
    ├── AuthService.php
    └── OrganizationService.php

resources/views/
├── layouts/
│   ├── app.blade.php (Base layout)
│   └── auth.blade.php (Auth layout with sidebar)
└── manager/
    ├── auth/
    │   ├── login.blade.php
    │   └── register.blade.php
    ├── organization/
    │   ├── select.blade.php
    │   ├── create.blade.php
    │   └── join.blade.php
    └── dashboard.blade.php
```

## 🚀 Features

### Authentication
- ✅ Manager registration with email, phone, and password
- ✅ Login with email and password
- ✅ Remember me functionality
- ✅ Secure password hashing

### Organization Management
- ✅ Create new organizations with documents
- ✅ Join existing organizations via ID
- ✅ Multi-organization support (managers can belong to multiple orgs)
- ✅ Organization switching (like Telegram accounts)
- ✅ Automatic organization check after login

### User Flow
1. **Register** → Manager creates account
2. **Login** → System checks for organization membership
3. **If no organization** → Redirect to organization selection
4. **Choose**: Create Organization or Join Organization
5. **Dashboard** → Access manager features

### Middleware
- `EnsureManagerHasOrganization`: Ensures authenticated managers have an organization before accessing protected routes

## 🔐 Routes

### Guest Routes (Unauthenticated)
- `GET /manager/login` - Show login form
- `POST /manager/login` - Process login
- `GET /manager/register` - Show registration form
- `POST /manager/register` - Process registration

### Authenticated Routes
- `POST /manager/logout` - Logout
- `GET /manager/organization/select` - Choose create or join
- `GET /manager/organization/create` - Show create form
- `POST /manager/organization/create` - Process organization creation
- `GET /manager/organization/join` - Show join form
- `POST /manager/organization/join` - Process joining

### Protected Routes (Requires Organization)
- `GET /manager/dashboard` - Main dashboard
- `POST /manager/organization/switch` - Switch between organizations

## 🗄️ Database Models

### Manager
- Belongs to User (1:1)
- Belongs to many Organizations through OrganizationUsers
- Has methods for organization management

### Organization
- Has many Managers through OrganizationUsers
- Stores organization details and documents

### OrganizationUsers (Pivot)
- Links Managers to Organizations
- Supports many-to-many relationship

## 💡 Future Enhancements

### Organization Switching
The system is designed to support easy organization switching (like Telegram accounts):
- Session-based current organization tracking
- Quick switcher in header (ready for implementation)
- Seamless context switching between organizations

### API Ready
- Base API controllers are in place
- RESTful response helpers included
- Ready for mobile app or SPA integration

### Planned Features
- Vacancy management
- Applicant tracking
- Organization invitations with codes
- Role-based permissions within organizations
- Organization settings and customization

## 🎯 Key Design Principles

1. **Clean Architecture**: Separation of concerns with Repository → Service → Controller layers
2. **Modern UX**: Intuitive flows with helpful information
3. **Scalability**: Easy to add new features and API endpoints
4. **Security**: Proper authentication and authorization checks
5. **Flexibility**: Multi-organization support from day one

## 📝 Environment Setup

Make sure your `.env` file has the storage properly configured:

```env
FILESYSTEM_DISK=public
```

Run the storage link command (already executed):
```bash
php artisan storage:link
```

## 🎨 Color Palette

- **Primary Yellow**: `#EFFE6D` - Used for main CTAs and buttons
- **Secondary Teal**: `#319885` - Used for accents and highlights
- **Background Main**: `#FFFFFF` - Clean white
- **Background Secondary**: `#F8F9FA` - Subtle gray
- **Text Primary**: `#1A1A1A` - Almost black
- **Text Secondary**: `#6B7280` - Muted gray
- **Border**: `#E5E7EB` - Light border
- **Error**: `#EF4444` - Red for errors
- **Success**: `#10B981` - Green for success

## 🚀 Getting Started

1. Register a new manager account
2. Login with your credentials
3. Choose to create or join an organization
4. Start managing vacancies (coming soon)

---

Built with ❤️ for connecting CIS employees with European opportunities
