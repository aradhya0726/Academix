# LTU Academics
### Academic Management System

LTU Academics is a web-based academic management system designed to simplify and organize academic and administrative activities within an educational institution.

The system provides separate functionalities for administrators, teachers, and students through a centralized platform.

---

## 1. Features

### Administrator
- Manage users and user roles.
- Manage faculties, classes, and subjects.
- Manage academic information.
- Publish and manage notices.
- Manage assignments and academic activities.
- Monitor attendance and academic records.
- Access administrative reports.

### Teacher
- Access assigned academic information.
- Manage assignments.
- Review student submissions.
- Manage student attendance.
- View relevant notices and class information.

### Student
- Access personal academic information.
- View class and subject information.
- View notices and announcements.
- View assignments and submit work.
- Check attendance records.
- Manage personal profile information.

*Available permissions and features depend on the assigned user role.*

---

## 2. Technology Stack

The project uses the following technologies:

- **Backend:** PHP and Laravel
- **Frontend:** HTML, CSS, JavaScript
- **CSS Framework:** Tailwind CSS
- **Frontend Build Tool:** Vite
- **Database:** MySQL
- **Dependency Management:** Composer and npm

---

## 3. System Requirements

Before installing the project, make sure the development or hosting environment has:

- PHP 8.3 or compatible version required by the project
- Composer
- MySQL
- Node.js and npm
- PHP extensions required by Laravel and the installed dependencies
- A web server such as Apache or Nginx for hosted deployment

Check the actual PHP and Laravel requirements in `composer.json` before installation.

---

## 4. Installation Guide

### Step 1: Obtain the Project

Extract the supplied project source code or clone the repository.

Open a terminal in the project directory:

```bash
cd LTU-Academics
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

### Step 3: Configure Environment

Create the environment file:

```bash
cp .env.example .env
```

Open `.env` and configure the database connection:

```env
APP_NAME="LTU Academics"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ltu-academic
DB_USERNAME=root
DB_PASSWORD=
```

Update the database name, username, and password according to your MySQL configuration.

**Important:** Never distribute a real `.env` file containing passwords, secret keys, or other private configuration.

### Step 4: Generate Application Key

```bash
php artisan key:generate
```

### Step 5: Create the Database

Create a MySQL database using your preferred database management tool.

For example:

```sql
CREATE DATABASE `ltu-academic`;
```

### Step 6: Set Up Database Tables

For a fresh installation, run:

```bash
php artisan migrate
```

If an authorized database backup is supplied, it may instead be imported into the configured database using a MySQL-compatible database management tool.

Do not import a backup and run migrations blindly without checking whether the database already contains the required tables.

### Step 7: Create Storage Link

```bash
php artisan storage:link
```

### Step 8: Install Frontend Dependencies

```bash
npm install
```

Build the frontend assets:

```bash
npm run build
```

### Step 9: Start the Application

```bash
php artisan serve
```

Open the application in your browser:

```text
http://127.0.0.1:8000
```

---

## 5. Development Mode

To run the frontend development server:

```bash
npm run dev
```

In another terminal, run:

```bash
php artisan serve
```

---

## 6. Database Configuration

LTU Academics uses MySQL for storing application data.

Database configuration is managed through the `.env` file.

Before installation or restoration:

1. Create the target database.
2. Configure the database credentials.
3. Import the supplied database backup if required, or run the migrations for a fresh installation.
4. Verify that the database connection works.
5. Check that the application can access the required tables.

Always maintain a separate database backup before performing restoration or migration operations.

---

## 7. Security Recommendations

For production deployment:

- Set `APP_ENV=production`.
- Set `APP_DEBUG=false`.
- Use HTTPS.
- Configure strong database credentials.
- Keep Laravel and dependencies updated.
- Protect uploaded files and sensitive documents.
- Restrict access according to user roles.
- Back up the database regularly.
- Never expose `.env`, private keys, or database credentials.
- Configure secure session and cookie settings.

---

## 8. Project Maintenance

The following commands may be useful during maintenance:

Clear application caches:

```bash
php artisan optimize:clear
```

Check migration status:

```bash
php artisan migrate:status
```

Run automated tests:

```bash
php artisan test
```

Build production frontend assets:

```bash
npm run build
```

---

## 9. Important Notes

- Hosting and server configuration are separate from the supplied source code.
- Database credentials must be configured by the installing institution.
- A database backup should be kept securely.
- The supplied source code should be reviewed before production deployment.
- Third-party packages and libraries remain subject to their respective licences.

---

## 10. Project Information

| Information | Details |
|---|---|
| Project Name | LTU Academics |
| Project Type | Academic Management System |
| Backend Framework | Laravel |
| Programming Language | PHP |
| Database | MySQL |
| Frontend Build Tool | Vite |

---

## 11. Support and Documentation

Refer to the Laravel documentation for framework-related installation and configuration information:

https://laravel.com/docs

For frontend dependencies and build instructions, refer to the relevant package documentation.

---

**LTU Academics**
*Academic Management Made Easier.*
