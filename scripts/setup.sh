#!/bin/sh
set -eu

cd "$(dirname "$0")/.."

[ -f .env ] || cp .env.example .env
[ -f backend/.env ] || cp backend/.env.example backend/.env
[ -f frontend/.env ] || cp frontend/.env.example frontend/.env

if ! command -v docker >/dev/null 2>&1; then
    echo "Docker with the Compose plugin is required." >&2
    exit 1
fi

docker compose up -d --build
docker compose exec -T backend php artisan migrate --force

echo "Anil ERP is ready at http://localhost"
