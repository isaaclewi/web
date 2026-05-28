#!/bin/bash
set -e

# Créer les dossiers nécessaires
mkdir -p storage/framework/{cache,sessions,views} bootstrap/cache
mkdir -p storage/app/public/logos/institutions

# Donner les droits à www-data
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# === LARAVEL SETUP ===
echo "=== LARAVEL SETUP ==="
php artisan config:clear 2>&1

# AJOUTE CES LIGNES dans docker-entrypoint.sh, avant config:cache

echo "=== MIGRATIONS ==="
php artisan migrate --force 2>&1
php artisan config:cache 2>&1
php artisan view:clear 2>&1

# ✅ SYMLINK CRITIQUE : public/storage -> storage/app/public
echo "=== STORAGE LINK ==="
php artisan storage:link --force 2>&1

# === TEST CONNEXION DB ===
echo "=== TEST CONNEXION DB ==="
php -r "
try {
    \$pdo = new PDO(
        'pgsql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_DATABASE'),
        getenv('DB_USERNAME'),
        getenv('DB_PASSWORD')
    );
    echo 'DB OK\n';
} catch(Exception \$e) {
    echo 'DB ERREUR: ' . \$e->getMessage() . '\n';
}
" 2>&1

# Démarrer Apache
apache2-foreground
