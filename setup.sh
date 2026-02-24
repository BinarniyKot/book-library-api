#!/bin/bash
# Automatic setup script for Book Library API
# Installs all dependencies (Composer, npm), builds assets, runs migrations and seeds

set -e

echo "Setting up Book Library API..."

# Install PHP dependencies
echo "Installing Composer dependencies..."
composer install

# Install Node.js dependencies and build frontend assets (Vite + SCSS)
if command -v npm &>/dev/null; then
    echo "Installing npm packages and building assets..."
    npm install
    npm run build
else
    echo "Warning: npm not found. Skip frontend build. Install Node.js to build SCSS/CSS assets."
fi

# Create .env if not exists
if [ ! -f .env ]; then
    echo "Creating .env from .env.example..."
    cp .env.example .env
    php artisan key:generate
fi

# Create SQLite database if using SQLite
if grep -q "DB_CONNECTION=sqlite" .env 2>/dev/null; then
    if [ ! -f database/database.sqlite ]; then
        echo "Creating SQLite database..."
        touch database/database.sqlite
    fi
fi

# Run migrations and seed
echo "Running migrations and seeding fixtures..."
php artisan migrate:fresh --seed --force

echo "Setup complete! Run 'php artisan serve' to start the server."
