#!/usr/bin/env bash

set -euo pipefail

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
VITE_LOG="$PROJECT_ROOT/storage/logs/vite-dev.log"

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

mkdir -p "$(dirname "$VITE_LOG")"
echo ""
echo "Setup complete."
echo "=> Starting Vite dev server (npm run dev) in the background"
nohup npm run dev > "$VITE_LOG" 2>&1 &
VITE_PID=$!
echo "   PID: $VITE_PID"
echo "   Logs: $VITE_LOG"
echo ""
echo "Next steps:"
echo "  - Tail Vite: tail -f $VITE_LOG"
echo "  - In a new terminal to start the Laravel server Run \n ** php artisan serve ** "

