composer install
php artisan migrate:refresh --seed
php artisan lm:client "frontend"
npm install
gulp build --production