#!/bin/bash
# PPOB Enterprise Automatic Deployment Script
set -e

echo "========================================="
echo "        STARTING PPOB DEPLOYMENT         "
echo "========================================="

# Go to the application directory (safety net)
cd "$(dirname "$0")"

# 1. Maintenance Mode On
echo "Enabling Maintenance Mode..."
php artisan down || true

# 2. Pull latest code from GitHub
echo "Pulling latest changes from main branch..."
git pull origin main

# 3. Install PHP Dependencies (No Dev)
echo "Installing Composer dependencies for production..."
composer install --no-dev --optimize-autoloader --no-interaction

# 4. Migrate Database
echo "Running database migrations..."
php artisan migrate --force

# 5. Optimize & Cache Configuration
echo "Caching configurations, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 6. Restart Queue Workers & Horizon
echo "Restarting queue workers and Horizon..."
php artisan queue:restart

if php artisan list | grep -q "horizon:terminate"; then
    echo "Terminating Horizon to trigger supervisor reload..."
    php artisan horizon:terminate || true
fi

# 7. Compile Assets (Vite)
if [ -f "package.json" ]; then
    echo "Installing NPM dependencies and compiling assets..."
    npm install --no-audit --no-fund
    npm run build
fi

# 8. Maintenance Mode Off
echo "Disabling Maintenance Mode..."
php artisan up

echo "========================================="
echo "      DEPLOYMENT SUCCESSFULY COMPLETED   "
echo "========================================="
