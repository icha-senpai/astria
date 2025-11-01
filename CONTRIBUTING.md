
# Contributing to Astria

Thank you for considering contributing to Astria!

This project is in active development and follows a modular philosophy — each feature should live in a self-contained module when possible.

## How to Contribute

### 1. Fork & Clone

git clone <https://github.com/yourname/astria.git>
cd astria

### 2. Install Environment

composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate

### 3. Create a Feature Branch

git checkout -b feature/module-name

### 4. Follow Architecture Guidelines

Remove default filament routes if they exist otherwise you'll be gaslighting yourself

Do not place domain logic in app/ unless foundational

Each feature belongs in /modules/{Name}/

Filament pages, resources, and widgets should be autodiscovered

Routes go under modules/{Name}/routes/

### 5. Code Style

PSR-12 compliant

Use Laravel naming conventions

Keep modules independent & decoupled

### 6. Commit Messages

Use readable, conventional commits:

feat: add messaging module
fix: correct module autoload path
refactor: simplify route loader

### 7. Submit Pull Request

Open a PR against main with:

Description of changes

Module(s) affected or added

Screenshots (if UI related)

We'll review, request updates if needed, and merge when ready.

### Questions & Discussion

Use GitHub Issues to discuss architecture, features, and improvements.
