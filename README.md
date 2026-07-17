# Anil ERP

Production-oriented monorepo foundation for Hejab Anil's retail, CRM, inventory, accounting, HR, and manufacturing operations.

## Stack

- Laravel 12 / PHP 8.4 API with Sanctum and Spatie Permission
- Vue 3 / TypeScript / Vite with Pinia and Vue Router
- PostgreSQL 17, Redis 7, Nginx, Docker Compose
- Pest and Vitest

## Quick start

1. Copy environment templates: `cp .env.example .env && cp backend/.env.example backend/.env && cp frontend/.env.example frontend/.env`.
2. Start services: `docker compose up -d --build`.
3. Install and initialize the API: `docker compose exec backend composer install && docker compose exec backend php artisan key:generate && docker compose exec backend php artisan migrate`.
4. Install the UI: `docker compose exec frontend npm install` (the development server starts automatically).
5. Open <http://localhost>; API health is available at <http://localhost/api/health>.

All application timestamps use UTC. Set only local display preferences in clients. Never commit populated `.env` files.

## Development

```bash
cd backend && composer install && php artisan test
cd frontend && npm install && npm run test && npm run type-check && npm run lint
```

See [architecture](docs/architecture.md), [roadmap](docs/roadmap.md), and [coding standards](docs/coding-standards.md).
