# 🎟️ Event Booking System

A full-stack web application for browsing and booking events, built with Laravel 12 as an individual academic project at Griffith University.

---

## ✨ Features

- **User Authentication** — Register, log in, and log out securely via Laravel Breeze
- **Event Listings** — Browse events by category and tags
- **Event Booking** — Reserve a spot at an event (with validation to prevent overbooking)
- **Dashboard** — View and manage your bookings in one place
- **Category Filtering** — Filter events without page reload
- **Tag System** — Events tagged for easy discovery
- **Automated Testing** — PHPUnit test suite covering core functionality

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.2, Laravel 12 |
| Authentication | Laravel Breeze |
| Frontend | Blade Templates, Vite |
| Database | SQLite |
| Testing | PHPUnit |

---

## 🚀 Getting Started

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & npm

### Installation

```bash
# Clone the repository
git clone https://github.com/Jisoo0723/final-assignment.git
cd final-assignment

# Install dependencies
composer install
npm install && npm run build

# Set up environment
cp .env.example .env
php artisan key:generate

# Set up database
php artisan migrate:fresh --seed

# Start the server
php artisan serve
```

Visit `http://localhost:8000`

---

## 🧪 Running Tests

```bash
php artisan test
```

---

## 📁 Project Structure

```
app/
├── Http/Controllers/   # Request handling
├── Models/             # Eloquent models
database/
├── migrations/         # Database schema
├── seeders/            # Sample data
resources/
├── views/              # Blade templates
```

---

## 👩‍💻 Author

**Jisu Choi**  
Master of Information Technology — Griffith University  
[linkedin.com/in/jisuchoi-dev](https://linkedin.com/in/jisuchoi-dev)
