# Requirements

- PHP 8.5.12+ (or 8.4.27+) with the openssl, pdo_mysql, mbstring, gd, sodium, curl, fileinfo and bcmath extensions.
  Older 8.4/8.5 releases are affected by the critical remote code execution vulnerabilities
  CVE-2026-109618 and CVE-2026-109619 (fixed on 2026-10-21).
- MySQL 5.7+ / MariaDB 10.3+
- Composer 2

The application runs on Laravel 12 and Passport 12. `laravelcollective/html` and
`watson/bootstrap-form` are abandoned upstream: they are vendored in `packages/`.

# Installation

- Create config file **.env** using **.env.example**

| Key(s) | Value |
| --- | --- |
| APP_URL | App base URL |
| DB_ | MySQL connection credentials |
| DB2_ | Old login module MySQL connection credentials |
| MAIL_ | Mail driver config |
| FACEBOOK_ | FB oAuth credentials |
| GOOGLE_ | Google oAuth credentials |
| PMS_ | PMS oAuth credentials |
| AUTH_MASTER_HASH | admin master password hash, use bcrypt function to create new one |

- Run **install.sh**

# Maintenance

- Import accounts from old login module database
``` php artisan lm:import ```

- Merge two login-module databases
``` php artisan lm:merge HOST PORT DATABASE USERNAME PASSWORD ```

- Import accounts from bebras database
``` php artisan bebras:import HOST PORT DATABASE USERNAME PASSWORD ```

# Tests

Feature tests need a MySQL database named `login_module_test` (see `phpunit.xml`), which they wipe.

``` php vendor/bin/phpunit ```

# Upgrading a Laravel 5.4 / PHP 7 installation

- Install PHP 8.5.12+ (or 8.4.27+) and run `composer install --no-dev`
  (`config.platform.php` is 8.4.0 in `composer.json`, so the lock file installs on both)
- `php artisan migrate`: only two migrations run on an existing database,
  `2026_10_21_000001_upgrade_passport_tables` (adds `oauth_clients.provider`, makes `secret` nullable)
  and `2026_10_21_000002_upgrade_permission_tables` (spatie/laravel-permission v1 → v6 tables, data is copied).
  Both can be rolled back.
- The LTI database settings moved from `config/lti.php` to `LTI_DB_*` variables
  (the previous values are the defaults).
- `MAIL_DRIVER` / `APP_LOG` / `APP_LOG_LEVEL` are still read; `MAIL_MAILER` / `LOG_CHANNEL` / `LOG_LEVEL` take precedence.
- Users are logged out once (session cookies of the old version cannot be decrypted), and authorization
  codes issued just before the switch are lost (10 minutes lifetime). Access and refresh tokens stay valid.
- OAuth error responses now contain `error_description` (RFC 6749) and no longer always contain `message`.
- `php artisan lm:keys` now generates 2048-bit keys (512-bit keys cannot produce RS512 signatures).
