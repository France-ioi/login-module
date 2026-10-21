# Deployment

## Server requirements

- PHP **8.5.12+** (or 8.4.27+): earlier 8.4/8.5 releases are affected by the critical remote code execution
  vulnerabilities CVE-2026-109618 and CVE-2026-109619 (fixed 2026-10-21). PHP-FPM recommended over mod_php.
- Extensions: openssl, pdo_mysql, mbstring, gd, sodium, curl, fileinfo, intl, zip, bcmath.
- MySQL 5.7+/8.x with InnoDB (set `DB_ENGINE=InnoDB` if the server default is MyISAM).
- A web server pointing to `public/` (nginx config example in `docker/nginx/nginx.conf`).
- Writable `storage/` and `bootstrap/cache/`.
- Cron for `php artisan lti:send` if LTI results are used (it is not registered in the scheduler).

## First installation

`install.sh` does:

```bash
composer install                       # use --no-dev in production
php artisan migrate --seed
php artisan passport:keys              # storage/oauth-private.key, oauth-public.key
php artisan lm:keys                    # storage/lm-private.key, lm-public.key (2048-bit RSA)
cd public && ln -s ../storage/app/ storage
npm install                            # then build the assets, see development.md
```

In production, do not keep the seeded test users and example clients (`--seed` runs
`DatabaseSeeder`, which creates them): seed only `RolesAndPermissionsSeeder`, then create the admin
account and the real clients.

## Updates

`update.sh`:

```bash
composer install
php artisan down
php artisan config:cache
php artisan migrate
php artisan up
npm install
```

The front-end assets must be rebuilt when `resources/assets` changes (`public/css/app.css` is not
versioned).

## Upgrading from the Laravel 5.4 / PHP 7 version

1. Install PHP 8.5.12+ (or 8.4.27+) and run `composer install --no-dev`.
2. Update `.env` (old names keep working):
   - `MAIL_DRIVER` → `MAIL_MAILER`, `APP_LOG` → `LOG_CHANNEL`, `APP_LOG_LEVEL` → `LOG_LEVEL` (optional);
   - add `LTI_DB_HOST`, `LTI_DB_PORT`, `LTI_DB_DATABASE`, `LTI_DB_USERNAME`, `LTI_DB_PASSWORD`
     (they were hard-coded in `config/lti.php`; the old values are the defaults);
   - optionally `FACEBOOK_GRAPH_API_VERSION`, `SESSION_SAME_SITE`, `DB_ENGINE`.
3. `php artisan migrate` runs only two migrations on an existing database:
   - `2026_10_21_000001_upgrade_passport_tables`: adds `oauth_clients.provider`, makes `secret` nullable;
   - `2026_10_21_000002_upgrade_permission_tables`: spatie/laravel-permission v1 → v6
     (`user_has_roles` / `user_has_permissions` → `model_has_roles` / `model_has_permissions`, data copied).

   Both can be rolled back. The Passport 4 migrations (`2016_06_01_*`) are now in `database/migrations`
   with their original names, so they are already recorded as run.
4. Effects of the switch:
   - users are logged out once (old session cookies cannot be decrypted);
   - authorization codes issued just before the switch are lost (10 minutes lifetime);
   - access and refresh tokens issued by the old version stay valid (tested);
   - OAuth error responses contain `error_description` (RFC 6749) and no longer always `message`.
5. Optional: `php artisan lm:keys` again if the existing LTI keys are 512-bit (they cannot produce RS512
   signatures). Platforms verifying task tokens then need the new public key.

## Docker (development)

`docker-compose.yml` runs nginx, PHP-FPM (`docker/php-fpm/Dockerfile`, `php:8.5.12-fpm` with Xdebug 3,
Redis, GD, GMP, bcmath, Composer, Node 14), MySQL 8.0 and Mailhog. Use `.env.example.docker`.
The Docker setup was updated for PHP 8 but has not been tested.
