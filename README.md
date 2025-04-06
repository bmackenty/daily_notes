# 📚 Daily Notes - Features

## 🎯 Core Features
- 📝 Rich text editor for creating and managing daily lesson notes
- 📅 Comprehensive course management system
- 👥 Section-based organization with meeting schedules
- 📊 Weekly planning tools with learning objectives
- 📋 Detailed syllabus management


## 📊 Course Management
- Create and manage multiple courses
- Define course descriptions and objectives
- Set assessment methods and requirements
- Establish course policies and rules
- Track prerequisites and materials
- Manage academic integrity guidelines

## 📝 Note Taking Features
- Rich text formatting with TinyMCE
- Support for images, tables, and links
- Code snippet integration
- File attachments
- Automatic content saving

## 👨‍🏫 Teacher Tools
- Weekly plan creation and management
- Resource linking and organization
- Progress tracking
- Student access management
- Course section administration

## ⚙️ Administrative Features
- User role management (admin/teacher/student)
- System settings configuration
- Usage statistics and reporting
- Backup and maintenance tools


## 💻 Technical Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server

## 🔒 Security Features
- Secure authentication with account lockout and rate limiting
- Encrypted password storage and secure session handling
- Input validation and prepared statements
- Environment-based configuration and secure file handling


## Configuration

The application uses environment variables for configuration. Copy the `.env.example` file to `.env` and update the values:

```bash
cp .env.example .env
```

Edit the `.env` file with your database credentials and other configuration values.

### Security Note
- Never commit the `.env` file to version control
- Keep your database credentials secure
- The `.env` file is automatically ignored by git



## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/daily-notes.git
cd daily-notes
```

2. Create and configure the `.env` file:
```bash
cp .env.example .env
# Edit .env with your configuration
```

3. Set proper file permissions:
```bash
chmod 640 .env
chown www-data:www-data .env
```

4. Run the database migrations:
```bash
php database/migrate.php
```

5. Configure your web server:
- Ensure HTTPS is enabled
- Set proper security headers
- Configure PHP session handling

## Configuration

### Environment Variables
Required environment variables in `.env`:
```
DB_HOST=localhost
DB_NAME=dailynotes
DB_USER=your_db_user
DB_PASS=your_db_password
DB_CHARSET=utf8mb4

APP_ENV=production
APP_DOMAIN=yourdomain.com
```

### Security Settings
Default security settings (configurable in database):
- Max login attempts: 5
- Lockout duration: 15 minutes
- Rate limit window: 60 seconds
- Max rate limit: 10 attempts
- Session timeout: 1 hour
- Session regeneration: 30 minutes

## Usage

1. Access the application through your web browser
2. Register a new account or login with existing credentials
3. Create and manage your notes, courses, and teaching materials

## Security Best Practices

1. Always use HTTPS in production
2. Keep the application and dependencies updated
3. Regularly review and rotate credentials
4. Monitor security logs for suspicious activity
5. Implement IP-based blocking for repeated failed attempts
6. Use strong, unique passwords
7. Enable two-factor authentication if available

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.
