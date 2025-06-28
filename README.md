# Meal Sync

A modern meal management application built with Laravel 12 and React/TypeScript, featuring meal discovery, user favorites, and community comments.

## Tech Stack

- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: React 19, TypeScript, Inertia.js
- **Database**: SQLite (default), MySQL, PostgreSQL supported
- **Styling**: Tailwind CSS 4, Shadcn
- **Build Tool**: Vite 6

## Prerequisites

Before you begin, ensure you have the following installed:

- **PHP 8.2 or higher** with required extensions:
    - BCMath
    - Ctypes
    - cURL
    - DOM
    - Fileinfo
    - JSON
    - Mbstring
    - OpenSSL
    - PCRE
    - PDO
    - Tokenizer
    - XML
- **Composer** (dependency manager for PHP)
- **Node.js 18+ and npm** (for frontend dependencies)
- **Git** (version control)

## Quick Start

### 1. Clone the Repository

```bash
git clone <repository-url>
cd meal-sync
```

### 2. Install Dependencies

Install PHP dependencies:

```bash
composer install
```

Install Node.js dependencies:

```bash
npm install
```

### 3. Environment Setup

Create your environment file:

```bash
cp .env.example .env
```

Generate an application key:

```bash
php artisan key:generate
```

### 4. Database Setup

The application uses SQLite by default. Setup database of your choice

Run the migrations:

```bash
php artisan migrate --seed
```

This will create user
```bash
email: user@example.com
password: password
```

### 5. Import Sample Data

Import meals from TheMealDB API:

```bash
php artisan import:meals
```

### 6. Start Development

Start the development environment (this will run multiple services):

```bash
composer run dev
```

This command starts:

- Laravel development server (port 8000)
- Queue worker
- Laravel Pail (real-time logs)
- Vite development server (hot reload)

Visit your application at `http://localhost:8000`