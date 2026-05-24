#!/bin/bash
set -e

# Créer les dossiers nécessaires
mkdir -p storage/framework/{cache,sessions,views} bootstrap/cache

# Donner les droits à www-data
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# === TEST PHP ARTISAN ===
echo "=== TEST PHP ARTISAN ==="
php artisan config:clear 2>&1
php artisan config:cache 2>&1
php artisan view:clear 2>&1

# === TEST CONNEXION DB ===
echo "=== TEST CONNEXION DB ==="
php -r "
try {
    \$pdo = new PDO(
        'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_DATABASE'),
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
