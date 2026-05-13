# Right Hire CRM - Super Admin Authentication Module

An enterprise-grade, secure, MVC-based PHP authentication system for Super Admins. Built strictly with Core PHP, PDO, and standard modern UI practices.

## Features
- **MVC Architecture**: Custom core implementation.
- **Security First**: Prepared statements, CSRF protection, rate limiting, secure cookies.
- **OTP Verification**: Email-based Two-Factor Authentication.
- **Session Management**: Single-device active session logic with auto-timeout.
- **Modern UI**: Glassmorphism, Bootstrap 5, SweetAlert2.

## Requirements
- PHP >= 7.4
- Composer
- MySQL

## Installation

1. **Clone the repository** and run Composer:
   ```bash
   composer install
   ```

2. **Environment Variables**:
   Copy the example `.env` file and update database and SMTP credentials.
   ```bash
   cp .env.example .env
   ```

3. **Database Setup**:
   Import `database/schema.sql` into your MySQL server. This will also create the default Super Admin account:
   - **Email:** admin@righthirecrm.com
   - **Password:** Admin@123

4. **Web Server Setup**:
   Point your document root to the `public/` directory or ensure `mod_rewrite` is enabled to hit the main `.htaccess` file.

   To test using the built-in PHP server:
   ```bash
   php -S localhost:8000 -t public
   ```

## Folder Structure
- `app/`: MVC components (Models, Views, Controllers), Core, Middleware, Helpers, Services.
- `config/`: Configuration setup.
- `database/`: Schema file.
- `public/`: Entry point, assets.
- `routes/`: Route definitions.
- `storage/`: Log files.
