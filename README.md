# Academix

**A Laravel-based Academic Management System**

Academix is a web-based platform designed to simplify academic administration and improve communication between administrators, teachers, and students.

## Features

### Admin
- Manage academic information and users
- Manage faculties, classes, and subjects
- Manage teachers and students
- Publish and manage notices
- Manage assignments
- View attendance reports

### Teacher
- Dedicated teacher dashboard
- Manage notices and assignments
- Manage student submissions
- Record and manage student attendance

### Student
- Dedicated student dashboard
- View academic notices
- Access and submit assignments
- Track attendance
- View and export attendance reports

## Technology Stack

- **Backend:** PHP, Laravel
- **Database:** MySQL
- **Frontend:** HTML, CSS, JavaScript
- **Build Tools:** Vite

## Installation

### Requirements
- PHP
- Composer
- MySQL
- Node.js and npm

### Setup

1. Clone the repository:

   ```bash
   git clone YOUR_REPOSITORY_URL
   ```

2. Enter the project directory:

   ```bash
   cd academix
   ```

3. Install PHP dependencies:

   ```bash
   composer install
   ```

4. Create your environment file:

   ```bash
   cp .env.example .env
   ```

5. Generate the application key:

   ```bash
   php artisan key:generate
   ```

6. Configure your database credentials in `.env`.

7. Run database migrations:

   ```bash
   php artisan migrate
   ```

8. Install frontend dependencies and build assets:

   ```bash
   npm install
   npm run build
   ```

9. Start the Laravel development server:

   ```bash
   php artisan serve
   ```

10. Open `http://127.0.0.1:8000` in your browser.

## Security

Never commit your `.env` file, passwords, API keys, database credentials, or other sensitive information.

## Project Status

Developed as an academic project and portfolio application.