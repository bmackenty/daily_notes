# 📚 Daily Notes - Learning Management System

## 🎯 What is Daily Notes?

Daily Learning Notes give you a quick, clear summary of what we covered in class each day. They're here to help you review important ideas, remember what you learned, and stay on track — even if you missed a lesson. Each note connects to our learning goals, key concepts, and extra resources so you can study in a way that works best for you.

## 🎯 Core Features

### For Teachers
- 📝 Create and manage daily learning summaries for each class section
- 📅 Comprehensive course management system with sections and meeting schedules
- 📊 Weekly planning tools with learning objectives and outcomes
- 📋 Detailed syllabus management and resource linking
- 👥 Section-based organization with student access control
- 🏷️ Tag-based organization for easy content discovery

### For Students
- 📖 Access to daily learning summaries from missed or attended classes
- 🔍 Search functionality across all notes and content
- 📱 Responsive design for mobile and desktop access
- 🎯 Clear connection to learning goals and key concepts
- 📚 Easy navigation through course materials and resources

## 📊 Course Management
- Create and manage multiple courses with detailed descriptions
- Define course objectives, assessment methods, and requirements
- Establish course policies and academic integrity guidelines
- Track prerequisites and required materials
- Manage multiple sections within each course

## 📝 Daily Learning Summaries
- Rich text formatting with TinyMCE editor for comprehensive content
- Support for images, tables, links, and multimedia content
- Code snippet integration for technical courses
- Automatic academic year assignment and filtering
- Tag-based organization for easy content discovery

## 👨‍🏫 Teacher Tools
- Weekly plan creation and management with learning objectives
- Resource linking and organization within notes
- Progress tracking across course sections
- Student access management and role-based permissions
- Course section administration and scheduling

## ⚙️ Administrative Features
- User role management (admin/teacher/student)
- Academic year management and filtering
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
- Remember Me functionality for persistent login sessions

## Configuration

The application uses environment variables for configuration.
Copy the `.env.example` file to `.env` and update the values:

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

### For Teachers
1. Access the application through your web browser
2. Create courses and sections for your classes
3. Add daily learning summaries after each class session
4. Include key concepts, learning objectives, and resources
5. Use tags to organize content for easy student discovery

### For Students
1. Access the application through your web browser
2. Browse courses and sections you're enrolled in
3. Review daily learning summaries from missed or attended classes
4. Use search functionality to find specific topics or concepts
5. Access linked resources and materials

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
