# Academix

**A Laravel-Based Academic Management System**

Academix is a web-based academic management platform designed to simplify academic administration and improve communication between administrators, teachers, and students.

## Features

### Admin
- Manage academic information and users
- Manage faculties, classes, and subjects
- Manage teachers and students
- Publish and manage academic notices
- Manage assignments
- View attendance reports

### Teacher
- Dedicated teacher dashboard
- Manage notices and assignments
- Review student submissions
- Record and manage student attendance

### Student
- Dedicated student dashboard
- View academic notices
- Access and submit assignments
- Track attendance
- View and export attendance reports

## Technology Stack

| Technology | Purpose |
|---|---|
| PHP | Backend programming |
| Laravel | Web application framework |
| MySQL | Database |
| HTML | Page structure |
| CSS | Styling |
| JavaScript | Frontend functionality |
| Vite | Asset bundling |

## Installation

### Requirements
- PHP
- Composer
- MySQL
- Node.js and npm

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/aradhya0726/Academix.git
   ```

2. **Navigate to the project directory**
   ```bash
   cd Academix
   ```

3. **Install PHP dependencies**
   ```bash
   composer install
   ```

4. **Create the environment file**
   ```bash
   cp .env.example .env
   ```

5. **Generate the application key**
   ```bash
   php artisan key:generate
   ```

6. **Configure the database**

   Update your database credentials in the `.env` file.

7. **Run database migrations**
   ```bash
   php artisan migrate
   ```

8. **Install frontend dependencies**
   ```bash
   npm install
   npm run build
   ```

9. **Start the development server**
   ```bash
   php artisan serve
   ```

10. **Open the application**

    Visit http://127.0.0.1:8000 in your browser.

## Security

Never commit your `.env` file, passwords, API keys, database credentials, or other sensitive information.

## Project Status

Developed as an academic project and portfolio application.