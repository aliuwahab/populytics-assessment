#!/usr/bin/env bash

set -euo pipefail

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$PROJECT_ROOT"

echo "=> Installing PHP dependencies"
composer install --no-interaction --prefer-dist

echo "=> Installing Node dependencies"
npm install

if [ ! -f .env ]; then
  echo "=> Creating .env from .env.example"
  cp .env.example .env
fi

if [ ! -f database/database.sqlite ]; then
  echo "=> Creating SQLite database file"
  touch database/database.sqlite
fi

echo "=> Generating application key"
php artisan key:generate --force

echo "=> Running database migrations"
php artisan migrate --force

echo ""
echo "Setup complete. You can now run the dev server with:"
echo "  php artisan serve"
echo "  npm run dev"

