/usr/bin/composer.phar install
php artisan migrate --seed
php artisan passport:keys
php artisan lm:keys
cd public/
ln -s ../storage/app/ storage
cd ..
