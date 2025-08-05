# User Registration System with Database Integration

This project extends the portfolio with a complete user registration and bio data management system that stores data in a MySQL database.

## Features

- ✅ User Registration and Login
- ✅ Protected Bio Data Form
- ✅ MySQL Database Storage (lab5)
- ✅ Session Management
- ✅ File Upload for Profile Pictures
- ✅ Form Validation (Client & Server-side)
- ✅ Responsive Design
- ✅ Auto-save Functionality

## Project Structure

```
Lab-Report-2/
├── index.html              # Home page
├── portfolio.html           # Portfolio page (updated with new project)
├── login.html              # Login page
├── register.html           # Registration page
├── protected-bio.html      # Protected bio form
├── auth.css               # Authentication pages styling
├── auth.js                # Authentication JavaScript
├── protected-bio.css      # Bio form styling
├── protected-bio.js       # Bio form JavaScript
├── config.php             # Database configuration
├── register.php           # Registration handler
├── login.php              # Login handler
├── save-bio.php           # Save bio data handler
├── get-bio.php            # Retrieve bio data handler
├── logout.php             # Logout handler
├── database_setup.sql     # Database setup script
├── uploads/               # Profile pictures directory
└── README.md              # This file
```

## Setup Instructions

### 1. Database Setup

1. **Install MySQL Server** if not already installed
2. **Create Database**: Run the following SQL commands:
   ```sql
   CREATE DATABASE lab5;
   USE lab5;
   ```
3. **Run Setup Script**: Execute the `database_setup.sql` file in your MySQL client or run:
   ```bash
   mysql -u your_username -p lab5 < database_setup.sql
   ```

### 2. PHP Configuration

1. **Update Database Credentials**: Edit `config.php` and update:
   ```php
   private $username = 'your_mysql_username';
   private $password = 'your_mysql_password';
   ```

2. **Create Uploads Directory**: Make sure the `uploads/` directory exists and has write permissions:
   ```bash
   mkdir uploads
   chmod 755 uploads
   ```

### 3. Web Server Setup

1. **Local Development**: Use XAMPP, WAMP, or MAMP
2. **Place Files**: Put all files in your web server's document root (e.g., `htdocs/`)
3. **Start Services**: Start Apache and MySQL services

### 4. Access the Application

1. Open `http://localhost/your-project-folder/portfolio.html`
2. Click on the "User Registration System" project card
3. Register a new account or login with existing credentials
4. Fill out the bio form and submit

## Database Schema

### Users Table
- `id` (INT, AUTO_INCREMENT, PRIMARY KEY)
- `full_name` (VARCHAR(255), NOT NULL)
- `email` (VARCHAR(255), UNIQUE, NOT NULL)
- `password` (VARCHAR(255), NOT NULL) - Hashed
- `created_at` (TIMESTAMP)
- `updated_at` (TIMESTAMP)

### Bio Data Table
- `id` (INT, AUTO_INCREMENT, PRIMARY KEY)
- `user_email` (VARCHAR(255), FOREIGN KEY)
- `first_name` (VARCHAR(255), NOT NULL)
- `last_name` (VARCHAR(255), NOT NULL)
- `email` (VARCHAR(255), NOT NULL)
- `phone` (VARCHAR(20))
- `date_of_birth` (DATE, NOT NULL)
- `gender` (ENUM: 'male', 'female', 'other', NOT NULL)
- `address` (TEXT)
- `city` (VARCHAR(100))
- `country` (VARCHAR(100))
- `occupation` (VARCHAR(255))
- `education` (ENUM: 'high_school', 'bachelor', 'master', 'phd', 'other')
- `bio` (TEXT)
- `profile_picture` (VARCHAR(255))
- `newsletter` (BOOLEAN)
- `terms` (BOOLEAN)
- `created_at` (TIMESTAMP)
- `updated_at` (TIMESTAMP)

## User Flow

1. **Portfolio Access**: User clicks on "User Registration System" project from portfolio
2. **Authentication**: 
   - New users register with full name, email, and password
   - Existing users login with email and password
3. **Bio Form**: After authentication, users access the protected bio form
4. **Data Storage**: Form submissions are stored in the MySQL database
5. **Data Retrieval**: Existing bio data is loaded when users return

## Security Features

- ✅ Password hashing using PHP's `password_hash()`
- ✅ SQL injection prevention using prepared statements
- ✅ Session-based authentication
- ✅ Input validation and sanitization
- ✅ Protected routes (bio form requires login)
- ✅ File upload security (extension validation)

## Browser Compatibility

- ✅ Chrome (recommended)
- ✅ Firefox
- ✅ Safari
- ✅ Edge

## Technologies Used

- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Server**: Apache (XAMPP/WAMP/MAMP)

## Troubleshooting

### Common Issues

1. **Database Connection Error**:
   - Check MySQL service is running
   - Verify credentials in `config.php`
   - Ensure database `lab5` exists

2. **File Upload Issues**:
   - Check `uploads/` directory exists
   - Verify directory permissions (755)
   - Check PHP upload limits in `php.ini`

3. **Session Issues**:
   - Clear browser cookies/local storage
   - Check PHP session configuration

### Error Logs

Check PHP error logs for detailed error information:
- XAMPP: `xampp/apache/logs/error.log`
- System logs: `/var/log/apache2/error.log`

## Future Enhancements

- [ ] Password reset functionality
- [ ] Email verification
- [ ] Admin panel for user management
- [ ] Data export functionality
- [ ] Advanced file upload with image resizing
- [ ] Multi-language support

## License

This project is for educational purposes as part of Web Lab assignments.
