# Laravel 12 Learning Management System

This is a Laravel 12-based Learning Management System (LMS) project, with role-based access control, authentication, and a modern development setup using Laravel Breeze, Spatie Permission, and Laravel UI.

---

## 🚀 Requirements

* PHP ^8.2
* Composer
* Node.js and NPM
* MySQL / SQLite
* Git (optional but recommended)

---

## 📦 Installed Packages

| Package                     | Purpose                                    |
| --------------------------- | ------------------------------------------ |
| `laravel/breeze`            | Simple authentication scaffolding (Breeze) |
| `laravel/ui`                | Legacy auth and UI scaffolding             |
| `spatie/laravel-permission` | Role and permission management             |
| `laravel/sail`              | Docker development environment             |
| `laravel/pint`              | Code formatting                            |
| `fakerphp/faker`            | Fake data generation                       |
| `nunomaduro/collision`      | Error reporting in the console             |
| `phpunit/phpunit`           | Unit testing framework                     |

---

## ⚙️ Setup Instructions

### 1. Clone & Install Dependencies

```bash
git clone https://github.com/your/repo.git
cd your-repo
composer install
npm install
```

### 2. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Update your `.env` file with correct database credentials.

---

### 3. Migrate and Seed

```bash
php artisan migrate --seed
```

---

### 4. Compile Assets

```bash
npm run dev
```

---

### 5. Start Development Server

```bash
php artisan serve
```

Access your app at [http://localhost:8000](http://localhost:8000)

---

## 🛡 Authentication & Permissions

* Authentication is handled via **Laravel Breeze** (`breeze:install`)
* Roles & Permissions via **Spatie Laravel Permission**

You can assign roles like so:

```php
use Spatie\Permission\Models\Role;

Role::create(['name' => 'admin']);
$user->assignRole('admin');
```

---

## 🔧 Useful Artisan Commands

```bash
php artisan migrate:fresh --seed         # Reset and reseed DB
php artisan route:list                   # Show registered routes
php artisan make:controller ExampleController
php artisan make:model Example -m        # With migration
php artisan queue:listen                 # Start queue listener
```

---

## 🧪 Running Tests

```bash
php artisan test
```

---

## ✅ Dev Tools

```bash
composer lint                # Laravel Pint
npm run dev                 # Vite development
npm run build               # Vite production
```

