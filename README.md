# Task Management System

A simple and efficient task management application built with Laravel 11 and modular architecture. This application allows you to organize your tasks into projects and manage them with ease.

## Features

- 🏗️ **Project Management**: Create and organize projects with custom colors
- ✅ **Task Management**: Add, edit, delete, and reorder tasks within projects
- 🎨 **Drag & Drop**: Reorder tasks easily with drag-and-drop functionality
- 🔐 **User Authentication**: Secure login and registration system
- 📱 **Responsive Design**: Works perfectly on desktop and mobile devices
- 🎯 **Clean Interface**: Modern UI built with Tailwind CSS

## Prerequisites

Before you begin, ensure you have the following installed on your system:

- **PHP** (version 8.2 or higher)
- **Composer** (PHP dependency manager)
- **Node.js** and **npm** (for frontend assets)
- **SQLite** (comes with PHP) or **MySQL** database
- **Git** (for version control)

## Installation Guide for Beginners

### Step 1: Clone the Repository

```bash
git clone https://github.com/Darahat/Task_Manager.git
cd Task_Manager
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

### Step 3: Install Node.js Dependencies

```bash
npm install
```

### Step 4: Environment Setup

1. Copy the example environment file:
```bash
copy .env.example .env
```

2. Generate application key:
```bash
php artisan key:generate
```

3. Open `.env` file in a text editor and configure your database settings:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Step 5: Database Setup

1. Create a MySQL database named `task_management`:
```sql
CREATE DATABASE task_management;
```

2. Run database migrations:
```bash
php artisan migrate
```

3. (Optional) Seed the database with sample data:
```bash
php artisan db:seed
```

### Step 6: Build Frontend Assets

```bash
npm run build
```

### Step 7: Start the Application

```bash
php artisan serve
```

The application will be available at: `http://localhost:8000`

## Usage

### Getting Started

1. **Register**: Create a new account at `/register`
2. **Login**: Sign in to your account at `/login`
3. **Create Project**: Click "Create Project" on the dashboard
4. **Add Tasks**: Select a project and start adding tasks
5. **Manage Tasks**: Drag and drop to reorder, edit, or delete tasks

### Project Management

- Create projects with custom names, descriptions, and colors
- View all projects in a dropdown on the dashboard
- Navigate directly to project tasks

### Task Management

- Add tasks with titles and descriptions
- Mark tasks as completed/incomplete
- Reorder tasks using drag-and-drop
- Edit or delete existing tasks

## Application Structure

```
task_management/
├── app/                    # Core application files
├── Modules/               # Modular components
│   ├── Auth/             # Authentication module
│   └── TaskManagement/   # Task management module
├── database/             # Database migrations and seeders
├── resources/            # Frontend assets and views
│   ├── views/           # Blade templates
│   ├── css/             # Stylesheets
│   └── js/              # JavaScript files
└── routes/               # Application routes
```

## Technology Stack

- **Backend**: Laravel 11 (PHP Framework)
- **Frontend**: Blade Templates + Tailwind CSS + jQuery
- **Database**: MySQL (primary) or SQLite (alternative)
- **Build Tool**: Vite
- **Drag & Drop**: SortableJS
- **Architecture**: Modular (using nwidart/laravel-modules)

## Troubleshooting

### Common Issues

1. **Permission Errors**: Make sure `storage/` and `bootstrap/cache/` are writable
```bash
chmod -R 775 storage bootstrap/cache
```

2. **Database Connection**: Ensure your `.env` file has correct database settings

3. **Missing Dependencies**: Run `composer install` and `npm install` again

4. **Cache Issues**: Clear application cache
```bash
php artisan optimize:clear
```

### Development Commands

- **Clear Cache**: `php artisan optimize:clear`
- **Generate Key**: `php artisan key:generate`
- **Run Migrations**: `php artisan migrate`
- **Rollback Migrations**: `php artisan migrate:rollback`
- **Build Assets**: `npm run build` or `npm run dev`

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/new-feature`)
3. Commit your changes (`git commit -am 'Add new feature'`)
4. Push to the branch (`git push origin feature/new-feature`)
5. Create a Pull Request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

**Developed with love ❤️ by Darahat**
