#!/bin/bash

# Install dependencies
composer install --no-dev --optimize-autoloader

# Set up environment
cp .env.example .env

# Generate application key
php artisan key:generate --force

# Create database directory
mkdir -p /tmp
touch /tmp/database.sqlite

# Run migrations
php artisan migrate --force

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
