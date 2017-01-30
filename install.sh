composer update
php artisan migrate:refresh --seed
php artisan lm:client "frontend"
npm update
gulp build