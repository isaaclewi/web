#!/bin/bash
set -e

mkdir -p storage/framework/{cache,sessions,views} bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Afficher l'erreur exacte dans les logs Render
echo "=== TEST PHP ARTISAN ==="
php artisan config:clear 2>&1
php artisan config:cache 2>&1
php artisan view:clear 2>&1

echo "=== TEST CONNEXION DB ==="
php -r "
try {
    \$pdo = new PDO(
        'mysql:host=sql209.infinityfree.com;dbname=if0_40074398_syntriforg',
        'if0_40074398',
        'gzbPADUGRP'
    );
    echo 'DB OK\n';
} catch(Exception \$e) {
    echo 'DB ERREUR: ' . \$e->getMessage() . '\n';
}
" 2>&1

apache2-foreground
