# Remember Me Functionality

This document describes the "Keep Me Logged In" system implemented in the Daily Notes application.

## Overview

The Remember Me system allows users to stay logged in across browser sessions for up to 30 days, providing a better user experience while maintaining security.

## Features

- **Secure Token Generation**: Uses cryptographically secure random tokens
- **Token Hashing**: Tokens are hashed before database storage
- **Automatic Cleanup**: Expired tokens are automatically removed
- **Security Headers**: Secure cookie settings with HttpOnly and SameSite
- **Session Integration**: Seamlessly integrates with existing session management

## How It Works

### 1. Login Process
When a user logs in and checks "Keep me logged in":
1. A cryptographically secure random token is generated
2. The token is hashed using SHA-256
3. The hashed token is stored in the `remember_tokens` table
4. A secure HTTP-only cookie is set with the original token
5. The token expires after 30 days

### 2. Automatic Login
When a user returns to the site:
1. The system checks for a remember me cookie
2. If found, the token is validated against the database
3. If valid, the user is automatically logged in
4. The token is refreshed for security

### 3. Security Measures
- Tokens are hashed before database storage
- Cookies are HTTP-only (not accessible via JavaScript)
- Tokens expire after 30 days
- Expired tokens are automatically cleaned up
- All tokens are removed on logout

## Database Schema

### New Fields in `users` Table
```sql
ALTER TABLE users 
ADD COLUMN remember_token VARCHAR(255) NULL,
ADD COLUMN remember_token_expires TIMESTAMP NULL;
```

### New `remember_tokens` Table
```sql
CREATE TABLE remember_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token_hash (token_hash),
    INDEX idx_expires (expires_at),
    INDEX idx_user_id (user_id)
);
```

## Implementation Details

### Files Modified
- `app/Controllers/AuthController.php` - Login/logout handling
- `app/Utils/SessionManager.php` - Session initialization with token checking
- `app/Views/auth/login.php` - Login form with remember me checkbox

### New Files
- `app/Utils/RememberMe.php` - Core remember me functionality
- `database/migrate_remember_me.sql` - Database migration script
- `scripts/cleanup_expired_tokens.php` - Cleanup script

## Usage

### For Users
1. Check the "Keep me logged in for 30 days" checkbox during login
2. The system will remember your login for 30 days
3. You can still manually logout to remove the remember me token

### For Administrators
1. Run the migration script to update the database schema
2. Optionally set up a cron job to run the cleanup script daily
3. Monitor the logs for any remember me related activities

## Security Considerations

### Token Security
- Tokens are 64 characters long (32 bytes of random data)
- Tokens are hashed before database storage
- Tokens expire after 30 days
- Tokens are refreshed on each use

### Cookie Security
- Cookies are HTTP-only (not accessible via JavaScript)
- Cookies use secure settings in production
- SameSite attribute is set to 'Lax' for CSRF protection

### Database Security
- Tokens are stored as SHA-256 hashes
- Foreign key constraints ensure data integrity
- Automatic cleanup prevents database bloat

## Maintenance

### Regular Cleanup
Run the cleanup script periodically to remove expired tokens:
```bash
php scripts/cleanup_expired_tokens.php
```

### Cron Job Setup
Add to your crontab for daily cleanup:
```bash
0 2 * * * /usr/bin/php /path/to/your/app/scripts/cleanup_expired_tokens.php
```

### Monitoring
Check the application logs for:
- Successful remember me logins
- Token cleanup activities
- Any errors related to remember me functionality

## Troubleshooting

### Common Issues
1. **Tokens not working**: Check if the database migration was run
2. **Cookies not set**: Verify cookie settings and domain configuration
3. **Automatic login not working**: Check session configuration and remember me integration

### Debug Mode
Enable debug logging to troubleshoot remember me issues:
```php
// In your configuration
'debug' => true,
'log_level' => 'DEBUG'
```

## Future Enhancements

Potential improvements for the remember me system:
- Configurable token expiration times
- Multiple device support
- Token revocation for security incidents
- Integration with password change notifications
- Audit logging for remember me activities
