# Laravel SaaS Application

A modern, full-stack Software-as-a-Service (SaaS) platform built with Laravel. This application provides a robust foundation for building scalable SaaS products with authentication, user management, subscription handling, and modern development practices.

![Laravel](https://img.shields.io/badge/Laravel-10.x-red?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=flat-square&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat-square&logo=mysql)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

## 📋 Table of Contents 

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
- [Running the Application](#running-the-application)
- [Project Structure](#project-structure)
- [Authentication](#authentication)
- [Testing](#testing)
- [Deployment](#deployment)
- [API Documentation](#api-documentation)
- [Contributing](#contributing)
- [License](#license)

## Overview

This Laravel SaaS application is designed as a complete solution for building Software-as-a-Service products. It includes all the essential features needed to launch and scale a SaaS business, from user authentication to subscription management.

Built with Laravel 10.x, this application follows modern PHP development practices and leverages Laravel's powerful ecosystem to provide a maintainable, scalable, and secure foundation.

## ✨ Features

### Core Functionality
- 🔐 **User Authentication** - Secure registration, login, and logout
- 👥 **User Management** - Complete user profile management
- 📧 **Email Verification** - Verify user email addresses
- 🔑 **Password Reset** - Secure password recovery system
- 🎨 **Responsive UI** - Mobile-friendly Blade templates
- 🛡️ **Authorization** - Role-based access control (RBAC)
- 💳 **Subscription Management** - Multi-tier subscription plans
- 📊 **Dashboard** - User and admin dashboards
- 🔔 **Notifications** - In-app and email notifications
- ⚙️ **Settings** - User preferences and application settings

### Technical Features
- 🏗️ **Scalable Architecture** - Well-organized MVC structure
- 🧪 **Testing Ready** - PHPUnit test suite included
- 📝 **Form Validation** - Comprehensive input validation
- 🚀 **Performance Optimized** - Query optimization and caching
- 🔧 **Environment Configuration** - Easy multi-environment setup
- 📱 **Responsive Design** - Bootstrap/Tailwind CSS integration
- 🗄️ **Database Migrations** - Version-controlled database schema
- 🌱 **Database Seeders** - Sample data for development
- 📬 **Queue System** - Background job processing
- 🔒 **Security Features** - CSRF protection, XSS prevention
- 🌐 **Multi-language Support** - Internationalization ready
- 📈 **Analytics Integration** - Track user behavior and metrics

## 🛠️ Tech Stack

- **Framework**: Laravel 10.x
- **Language**: PHP 8.1+
- **Database**: MySQL 5.7+ / PostgreSQL 12+
- **Frontend**: Blade Templates, Vite, Bootstrap/Tailwind CSS
- **Authentication**: Laravel Breeze/Sanctum
- **Session**: Database/Redis
- **Cache**: Redis/Memcached
- **Queue**: Redis/Database
- **Mail**: SMTP/Mailgun/SES
- **Storage**: Local/S3
- **Testing**: PHPUnit, Laravel Dusk

## 🔧 Requirements

### Server Requirements
- PHP >= 8.1
- Composer >= 2.0
- MySQL >= 5.7 or PostgreSQL >= 12
- Node.js >= 16.x (for asset compilation)
- NPM >= 8.x

### PHP Extensions
- BCMath PHP Extension
- Ctype PHP Extension
- cURL PHP Extension
- DOM PHP Extension
- Fileinfo PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PCRE PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

## 📦 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/sounakm177/laravel-sass.git
cd laravel-sass
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install NPM Dependencies

```bash
npm install
```

### 4. Environment Configuration

Copy the example environment file:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

### 5. Configure Environment Variables

Edit the `.env` file with your configuration:

```env
# Application
APP_NAME="Laravel SaaS"
APP_ENV=local
APP_KEY=base64:your_generated_key
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_saas
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@laravelsaas.com
MAIL_FROM_NAME="${APP_NAME}"

# Session & Cache
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_DRIVER=file

# Queue
QUEUE_CONNECTION=sync

# Redis (Optional)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## 🗄️ Database Setup

### 1. Create Database

```bash
# MySQL
mysql -u root -p -e "CREATE DATABASE laravel_saas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"

# PostgreSQL
createdb laravel_saas
```

### 2. Run Migrations

```bash
php artisan migrate
```

### 3. Seed Database (Optional)

```bash
php artisan db:seed
```

### 4. Fresh Migration with Seed (Development)

```bash
php artisan migrate:fresh --seed
```

## 🚀 Running the Application

### Development Server

Start the Laravel development server:

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

### Compile Assets

Development mode (with hot reload):
```bash
npm run dev
```

Production build:
```bash
npm run build
```

Watch for changes:
```bash
npm run watch
```

### Running Queue Workers

If using queues:
```bash
php artisan queue:work
```

### Running Scheduler

Add to crontab for scheduled tasks:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

For development:
```bash
php artisan schedule:work
```

## 📁 Project Structure

```
laravel-sass/
├── app/
│   ├── Console/            # Artisan commands
│   ├── Exceptions/         # Exception handlers
│   ├── Http/
│   │   ├── Controllers/    # Application controllers
│   │   ├── Middleware/     # HTTP middleware
│   │   └── Requests/       # Form request classes
│   ├── Models/             # Eloquent models
│   ├── Providers/          # Service providers
│   ├── Services/           # Business logic services
│   └── Traits/             # Reusable traits
├── bootstrap/              # Framework bootstrap
├── config/                 # Configuration files
├── database/
│   ├── factories/          # Model factories
│   ├── migrations/         # Database migrations
│   └── seeders/            # Database seeders
├── public/                 # Public assets
│   ├── css/
│   ├── js/
│   └── index.php
├── resources/
│   ├── css/                # CSS/SASS files
│   ├── js/                 # JavaScript files
│   ├── views/              # Blade templates
│   │   ├── auth/           # Authentication views
│   │   ├── layouts/        # Layout templates
│   │   ├── dashboard/      # Dashboard views
│   │   └── components/     # Reusable components
│   └── lang/               # Language files
├── routes/
│   ├── web.php             # Web routes
│   ├── api.php             # API routes
│   ├── console.php         # Console routes
│   └── channels.php        # Broadcast channels
├── storage/
│   ├── app/                # Application files
│   ├── framework/          # Framework files
│   └── logs/               # Log files
├── tests/
│   ├── Feature/            # Feature tests
│   └── Unit/               # Unit tests
├── .env.example            # Example environment file
├── artisan                 # Artisan CLI
├── composer.json           # PHP dependencies
├── package.json            # Node dependencies
├── phpunit.xml             # PHPUnit configuration
└── vite.config.js          # Vite configuration
```

## 🔐 Authentication

This application uses Laravel Breeze/Sanctum for authentication.

### Available Routes

```php
// Guest routes
GET  /login              # Login form
POST /login              # Login submit
GET  /register           # Registration form
POST /register           # Registration submit
GET  /forgot-password    # Password reset request
POST /forgot-password    # Send reset link
GET  /reset-password     # Reset password form
POST /reset-password     # Reset password submit

// Authenticated routes
GET  /dashboard          # User dashboard
POST /logout             # Logout
GET  /profile            # User profile
PUT  /profile            # Update profile
```

### Middleware

```php
// In routes/web.php
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    // ... other protected routes
});

// Admin routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
});
```

## 🧪 Testing

### Run All Tests

```bash
php artisan test
```

### Run Specific Test Suite

```bash
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit
```

### Run with Coverage

```bash
php artisan test --coverage
```

### Run Specific Test File

```bash
php artisan test tests/Feature/AuthenticationTest.php
```

### Create New Tests

```bash
# Feature test
php artisan make:test UserTest

# Unit test
php artisan make:test UserTest --unit
```

## 🔧 Common Artisan Commands

### Cache Management

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### Database Commands

```bash
# Migration status
php artisan migrate:status

# Rollback last migration
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset

# Fresh migration
php artisan migrate:fresh

# Fresh migration with seed
php artisan migrate:fresh --seed
```

### Generate Code

```bash
# Controller
php artisan make:controller UserController
php artisan make:controller UserController --resource
php artisan make:controller API/UserController --api

# Model with migration
php artisan make:model Post -m

# Model with migration, factory, and seeder
php artisan make:model Post -mfs

# Request validation
php artisan make:request StoreUserRequest

# Middleware
php artisan make:middleware AdminMiddleware

# Seeder
php artisan make:seeder UserSeeder

# Factory
php artisan make:factory PostFactory

# Migration
php artisan make:migration create_posts_table
```

## 🚢 Deployment

### Production Checklist

#### 1. Environment Configuration

```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
```

#### 2. Install Dependencies

```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

#### 3. Optimize Application

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

#### 4. Database Setup

```bash
php artisan migrate --force
```

#### 5. File Permissions

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### 6. Security

- Generate new `APP_KEY`
- Configure SSL certificate
- Set up proper file permissions
- Enable HTTPS
- Configure firewall
- Set up regular backups

### Deployment Platforms

#### Shared Hosting

1. Upload files via FTP/SFTP
2. Point document root to `/public`
3. Configure database
4. Set environment variables
5. Run migrations

#### VPS (DigitalOcean, Linode, etc.)

```bash
# Install LEMP stack
sudo apt update
sudo apt install nginx mysql-server php8.1-fpm

# Clone repository
git clone your-repo.git

# Configure Nginx
# Run deployment commands
```

#### Laravel Forge

1. Connect your server
2. Create new site
3. Deploy from Git repository
4. Configure environment
5. Enable SSL
6. Set up deployments

#### AWS/Google Cloud

- Use EC2/Compute Engine
- Configure load balancer
- Set up RDS/Cloud SQL
- Use S3/Cloud Storage
- Configure auto-scaling

### Web Server Configuration

#### Nginx

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/laravel-sass/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### Apache

Ensure `.htaccess` file exists in the `public` directory:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

## 📚 API Documentation

### Authentication Endpoints

```
POST /api/register       - Register new user
POST /api/login          - User login
POST /api/logout         - User logout (requires auth)
GET  /api/user           - Get authenticated user (requires auth)
```

### Example API Request

```bash
# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'

# Get user (with token)
curl -X GET http://localhost:8000/api/user \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## 🔍 Troubleshooting

### Common Issues

**1. 500 Internal Server Error**
```bash
# Check permissions
chmod -R 775 storage bootstrap/cache

# Clear caches
php artisan config:clear
php artisan cache:clear

# Check logs
tail -f storage/logs/laravel.log
```

**2. Database Connection Error**
```bash
# Verify credentials in .env
# Test connection
php artisan tinker
DB::connection()->getPdo();
```

**3. Asset Compilation Issues**
```bash
# Clear node modules
rm -rf node_modules package-lock.json
npm install
npm run build
```

**4. Queue Not Processing**
```bash
# Check queue configuration
php artisan queue:failed
php artisan queue:retry all
```

## 📖 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel News](https://laravel-news.com)
- [Laracasts](https://laracasts.com)
- [Laravel Daily](https://laraveldaily.com)

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Coding Standards

- Follow PSR-12 coding standards
- Write meaningful commit messages
- Add tests for new features
- Update documentation as needed
- Run `php artisan test` before committing

## 🔒 Security

If you discover any security-related issues,  please email the repository owner instead of using the issue tracker.

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 👨‍💻 Author

**Sounak Mondal**
- GitHub: [@sounakm177](https://github.com/sounakm177)

## 🙏 Acknowledgments

- Laravel Framework
- Laravel Community
- All open-source contributors

## 📞 Support

If you encounter any issues or have questions:
- Open an issue on [GitHub Issues](https://github.com/sounakm177/laravel-sass/issues)
- Check the [Laravel Documentation](https://laravel.com/docs)
- Visit [Laravel Forums](https://laracasts.com/discuss)

## 🗺️ Roadmap

- [ ] Implement subscription management with Stripe
- [ ] Add two-factor authentication
- [ ] Create admin panel
- [ ] Implement API rate limiting
- [ ] Add multi-tenancy support
- [ ] Integrate real-time notifications
- [ ] Add comprehensive analytics dashboard
- [ ] Implement team/organization features
- [ ] Add export functionality (PDF, CSV)
- [ ] Create mobile app integration

---

Made with ❤️ using Laravel
