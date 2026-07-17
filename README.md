# Anil ERP

Production-oriented monorepo foundation for Hejab Anil's retail, CRM, inventory, accounting, HR, and manufacturing operations.

## Stack

- Laravel 12 / PHP 8.4 API with Sanctum and Spatie Permission
- Vue 3 / TypeScript / Vite with Pinia and Vue Router
- PostgreSQL 17, Redis 7, Nginx, and Docker Compose
- Pest and Vitest

## Prerequisites

Docker Engine with the Compose plugin is the only requirement for containerized development. PHP 8.4 with Composer 2 and Node.js 22 are needed only for host-based development.

## Automated setup

```bash
./scripts/setup.sh
```

The script creates untracked environment files, builds and starts the stack, waits on service health checks, and runs migrations. Open <http://localhost>. API/database/Redis readiness is available at <http://localhost/api/health>, while Laravel process liveness is at <http://localhost/up>.

The committed local Compose configuration has safe local-only defaults and does not require `backend/.env`. Override passwords in the root `.env` when the environment is shared. Never use the committed development application key or password defaults in production.

## Manual setup

```bash
cp .env.example .env
cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env
docker compose up -d --build
docker compose exec backend php artisan migrate
```

The Vite server sends `/api` and `/sanctum` to Nginx over HTTP; Nginx alone speaks FastCGI to PHP-FPM. Accessing either port 80 or Vite's port 5173 therefore reaches a valid HTTP upstream.

## Host-based development

```bash
cd backend && composer install && cp .env.example .env && php artisan key:generate
php artisan migrate && composer test && composer lint
cd ../frontend && npm install && npm test && npm run type-check && npm run lint
```

## Production images

`docker/php/Dockerfile` and `docker/frontend/Dockerfile` contain separate development/build/production targets. The standalone production definition requires secrets explicitly and has no source bind mounts:

```bash
APP_KEY='base64:replace-me' APP_URL='https://erp.example.com' POSTGRES_PASSWORD='replace-me' \
  docker compose -f compose.production.yaml up -d --build
```

Run migrations as a controlled release step: `docker compose -f compose.production.yaml exec backend php artisan migrate --force`. Terminate TLS at an ingress/load balancer or extend the Nginx service with managed certificates.

## Validation and documentation

Run `./scripts/validate-foundation.sh` before pushing. See the [architecture](docs/architecture.md), [database design](docs/database-design.md), [business rules](docs/business-rules.md), [API specification](docs/api-specification.md), [roadmap](docs/roadmap.md), and [coding standards](docs/coding-standards.md).

All application timestamps are UTC. Clients are responsible only for localized display. Populated `.env` files, credentials, customer data, and generated dependency directories must never be committed.
