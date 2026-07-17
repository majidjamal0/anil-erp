#!/bin/sh
set -eu

cd "$(dirname "$0")/.."

required_files="
backend/artisan
backend/bootstrap/app.php
backend/bootstrap/providers.php
backend/bootstrap/cache/.gitignore
backend/app/Providers/AppServiceProvider.php
backend/config/app.php
backend/config/auth.php
backend/config/cache.php
backend/config/database.php
backend/config/filesystems.php
backend/config/logging.php
backend/config/mail.php
backend/config/permission.php
backend/config/queue.php
backend/config/sanctum.php
backend/config/services.php
backend/config/session.php
backend/public/index.php
backend/routes/api.php
backend/routes/console.php
backend/routes/web.php
backend/phpunit.xml
frontend/package.json
frontend/vite.config.ts
compose.yaml
nginx/default.conf
docs/database-design.md
docs/business-rules.md
docs/api-specification.md
"

for file in $required_files; do
    if [ ! -f "$file" ]; then
        echo "Missing required file: $file" >&2
        exit 1
    fi
done

find backend -name '*.php' -type f -print0 | xargs -0 -n 1 php -l >/dev/null
php -r 'foreach (["backend/composer.json", "frontend/package.json", "frontend/tsconfig.json"] as $file) { json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR); }'

grep -q "target:.*http://nginx" frontend/vite.config.ts
grep -q "fastcgi_pass backend:9000" nginx/default.conf
grep -q "condition: service_healthy" compose.yaml

if command -v docker >/dev/null 2>&1; then
    docker compose config --quiet
else
    echo "WARN: Docker is unavailable; skipped 'docker compose config --quiet'." >&2
fi

git diff --check
printf '%s\n' "Foundation static validation passed."
