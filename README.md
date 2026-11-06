# ShopCore

> 🚧 **Work in progress**: built step by step as a learning project following the [roadmap.sh Backend roadmap](https://roadmap.sh/backend).

ShopCore is a **headless e-commerce engine** built with Laravel. It provides a REST API that any frontend (web, mobile) can use to run an online store: catalog, inventory, cart, checkout and orders.

## Features (planned)

- **Product catalog** with categories and variants (size, color)
- **Inventory management** with overselling protection under concurrent orders
- **Cart & checkout** with coupons and discounts
- **Order lifecycle** managed by a state machine (pending → paid → shipped → delivered)
- **Payments** via external gateways, with webhook handling
- **Storefront API & Admin API**, documented with OpenAPI
- **Notifications** (email, SMS) via queues
- **Product search** with filters

## Tech Stack

- **PHP** 8.5
- **Laravel** 13
- **Pest** for testing
- **SQLite** for local development (PostgreSQL planned)
- Planned: Redis, Docker, GitHub Actions CI/CD

## Getting Started

```bash
git clone https://github.com/mostafaEssaAhmed755/shopcore.git
cd shopcore
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

## Running Tests

```bash
php artisan test
```
