# Authentication System Setup Guide

## 📋 Overview
This is a complete PHP authentication system for the Real Time Event Sync application. It includes:
- User registration with password hashing
- User login with credential verification
- Session management
- "Remember Me" functionality
- Modern UI design with responsive layout

## 🗂️ File Structure

```
frontend/
├── assets/
│   ├── css/
│   │   └── auth-style.css          # Authentication styling
│   └── js/
│       └── auth-validation.js      # Client-side validation
├── pages/
│   ├── login.php                   # Login page
│   ├── register.php                # Registration page
│   ├── dashboard.php               # Post-login dashboard
│   └── session_config.php          # Session management
├── components/
│   ├── header.php
│   ├── footer.php
│   └── navbar.php
└── ...

backend/
├── config/
│   └── db.php                      # Database connection
└── ...

database/
├── event-sync-db.sql               # Main schema
├── migration-create-users-table.sql # Users table migration
└── ...
```

## 🔧 Setup Instructions

### Step 1: Create the Users Table
Run the migration file in your MySQL database:

```sql
-- Open phpMyAdmin or use command line
-- Navigate to your event_sync_db database
-- Import or run the migration-create-users-table.sql file
```

**Or manually in phpMyAdmin:**
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Select `event_sync_db` database
3. Click on "SQL" tab
4. Copy and paste the contents of `database/migration-create-users-table.sql`
5. Click "Go" to execute

### Step 2: Verify Database Connection
The system uses the existing `Database` class in `backend/config/db.php`:
- Host: `localhost`
- Database: `event_sync_db`
- User: `root`
- Password: (empty)

Update these credentials in `backend/config/db.php` if your setup is different.

### Step 3: Access the Application
Navigate to:
- **Register**: `http://localhost/real_time_event_sync/frontend/pages/register.php`
- **Login**: `http://localhost/real_time_event_sync/frontend/pages/login.php`

## 📝 Usage

### Registration
1. Go to register.php
2. Enter full name, email, and password
3. Password must contain:
   - At least 8 characters
   - Uppercase letters (A-Z)
   - Lowercase letters (a-z)
   - Numbers (0-9)
4. Confirm password must match
5. Agree to Terms and Conditions
6. Click "Register"
7. Successfully registered users are redirected to login page

### Login
1. Go to login.php
2. Enter email and password
3. Optional: Check "Remember Me" to save email
4. Click "Login"
5. Successful login redirects to dashboard.php

### Remember Me
- When checked during login, email is saved in cookies for 30 days
- Cookie is HTTP-only for security
- Checkbox is auto-checked if remembered email is found

## 🔐 Security Features

### Password Hashing
- Uses `password_hash()` with DEFAULT algorithm (bcrypt)
- Uses `password_verify()` for authentication
- Passwords never stored in plain text

### Input Validation
- **Server-side validation** in PHP for all inputs
- **Client-side validation** in JavaScript for UX
- HTML special characters escaped before displaying

### Sessions
- Session hijacking protection (`httponly` cookies)
- Session starts on every page
- User must be logged out to access auth pages
- User must be logged in to access dashboard

### Database
- Uses prepared statements to prevent SQL injection
- Email field has UNIQUE constraint to prevent duplicates
- PDO for secure database operations

## 📋 File Descriptions

### `session_config.php`
Centralized session management:
```php
requireLogin()           // Redirect to login if not logged in
requireLogout()          // Redirect to dashboard if already logged in
isUserLoggedIn()         // Check if user is logged in
setUserSession()         // Set user session after login
destroyUserSession()     // Clear session on logout
```

### `login.php`
Handles user authentication:
- Email and password validation
- Database lookup for user
- Password verification using `password_verify()`
- Session creation on success
- "Remember Me" cookie handling

### `register.php`
Handles new user registration:
- Full name validation
- Email validation and duplicate check
- Password strength requirements
- Confirm password matching
- Password hashing using `password_hash()`
- Database insertion of new user

### `dashboard.php`
Post-login welcome page:
- Shows user information
- Displays quick actions (view events, add events, settings)
- Logout functionality
- Session verification

### `auth-style.css`
Modern authentication UI:
- Gradient backgrounds
- Responsive grid layout
- Smooth animations and transitions
- Mobile-first design
- Dark mode support

### `auth-validation.js`
Client-side validation:
- Email format validation
- Password strength meter
- Real-time error feedback
- Form submission handling (client-side only)
- Keyboard navigation support

## 🚀 Testing

### Test Registration Flow
1. Go to register.php
2. Try invalid inputs (check error messages):
   - Empty fields
   - Invalid email
   - Short password
   - Weak password
   - Mismatched passwords
3. Complete successful registration
4. Verify user appears in database

### Test Login Flow
1. Go to login.php
2. Try wrong credentials (should fail)
3. Use registered credentials (should succeed)
4. Verify session is created
5. Try accessing dashboard without login (should work, showing user info)
6. Click logout and verify session is destroyed

### Test Remember Me
1. Login and check "Remember Me"
2. Logout
3. Go back to login page
4. Email should be pre-filled
5. Checkbox should be checked

## 🛠️ Customization

### Change Database Credentials
Edit `backend/config/db.php`:
```php
private static $host = 'localhost';
private static $db   = 'event_sync_db';
private static $user = 'root';
private static $pass = '';
```

### Modify Validation Rules
Edit in `register.php` and `auth-validation.js`:
```php
// Minimum password length
if (strlen($password) < 8) { ... }

// Password requirements
if (!preg_match('/[A-Z]/', $password)) { ... }
```

### Customize Redirect URLs
Edit `login.php` line ~65:
```php
header('Location: dashboard.php'); // Change redirect destination
```

### Change Email for "Remember Me"
Edit time limit in `login.php` line ~45:
```php
setcookie('remember_email', ..., time() + (30 * 24 * 60 * 60)); 
// Change 30 to desired days
```

## 📱 Responsive Breakpoints

- **Desktop**: Full two-column layout (≥768px)
- **Tablet**: Single column layout (≤768px)
- **Mobile**: Optimized for small screens (≤480px)

## ❓ Troubleshooting

### "Database connection failed"
- Check MySQL is running
- Verify database credentials in `backend/config/db.php`
- Ensure `event_sync_db` database exists
- Run the migration file to create users table

### "Fatal error: require_once()"
- Check file paths are correct
- Ensure all files are in the correct directories
- Verify PHP include_path settings

### "Email already registered"
- The email exists in the database
- Try a different email or reset the database

### "Invalid email or password"
- Double-check credentials
- Verify user exists in database
- Check password is correct (case-sensitive)

### Sessions not persisting
- Verify `session.save_path` is writable
- Check PHP session settings
- Clear browser cookies and try again

## 📚 Additional Resources

### Password Hashing
- [PHP password_hash() docs](https://www.php.net/manual/en/function.password-hash.php)
- [PHP password_verify() docs](https://www.php.net/manual/en/function.password-verify.php)

### Sessions
- [PHP Session docs](https://www.php.net/manual/en/book.session.php)
- [OWASP Session Management](https://owasp.org/www-community/attacks/Session_fixation)

### SQL Injection Prevention
- [Prepared Statements](https://www.php.net/manual/en/pdo.prepared-statements.php)

## 📄 Database Schema

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## ✅ Checklist Before Production

- [ ] Change database password (currently empty)
- [ ] Enable HTTPS for cookie security
- [ ] Set `session.cookie_secure = 1` in php.ini when using HTTPS
- [ ] Implement rate limiting on login/register
- [ ] Add email verification for new accounts
- [ ] Implement password reset functionality
- [ ] Add CSRF tokens to forms
- [ ] Set up proper error logging (don't display DB errors to users)
- [ ] Implement account lockout after failed login attempts
- [ ] Add two-factor authentication (optional)

## 📞 Support

For issues or questions, verify:
1. Database connection is working
2. Users table exists with correct schema
3. File paths are correct
4. PHP version is 7.4 or higher
5. MySQL is running and accessible

---

**Last Updated**: February 28, 2026
**Version**: 1.0
