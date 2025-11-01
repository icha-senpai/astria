# Astria

Astria is a modular application framework built on **Laravel 12** and **Filament 4**, designed to provide a unified administration panel with plug-and-play module architecture. Each module can contribute features, pages, and resources to the central admin panel without breaking isolation or maintainability.

Astria's goal is to serve as a personal digital operating system — a unified, extensible control center for data, tools, content, and community systems.

## Features

- Centralized Filament admin panel (`/admin`)
- Laravel-native module loading
- Automatic discovery of module pages & resources
- Independent module routing and service providers
- Clear folder architecture under `/modules`
- Supports custom dashboards, tools, and integrations

## Technology Stack

| Component | Version |
|---|---|
| PHP | 8.3+ |
| Laravel | 12.x |
| Filament | 4.x |
| Livewire | 3.x |
| Composer | 2.x |

## Folder Structure

modules/
Core/
module.php
Filament/
CorePanelProvider.php
Pages/
Resources/
Providers/
routes/
resources/

## Getting Started

### Install dependencies

composer install
npm install && npm run build

## Environment setup

Copy .env.example

cp .env.example .env
php artisan key:generate
Configure database settings, then migrate:

php artisan migrate
Serve

php artisan serve
Access the admin panel at:

[Admin panel](http://localhost:8000/admin)

## Creating a Module

Use the Astria module generator:

php artisan astria:module {Name}
This scaffolds:

modules/Name/
  module.php
  Providers/...
  Filament/...
  routes/...
  resources/...
