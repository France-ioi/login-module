# Development guide

## Local setup

### Requirements

- PHP 8.4.27+ or 8.5.12+ with the `openssl`, `pdo_mysql`, `mbstring`, `gd`, `sodium`, `curl`, `fileinfo`,
  `intl`, `zip` and `bcmath` extensions
- Composer 2
- MySQL 5.7+/8.x (or MariaDB 10.3+) with **InnoDB** tables
- Node 14 for the front-end build only (Laravel Mix 0.10 / node-sass 4 do not run on newer Node)

### Steps

```bash
composer install
cp .env.example .env            # then edit it, see below
php artisan key:generate
php artisan migrate --seed      # creates the schema, roles, test users and example clients
php artisan passport:keys       # storage/oauth-*.key
php artisan lm:keys             # storage/lm-*.key (LTI / task tokens)
php artisan serve               # http://127.0.0.1:8000
```

Minimum `.env` changes for a local install:

| Variable | Local value | Why |
|---|---|---|
| `APP_ENV`, `APP_DEBUG` | `local`, `true` | error pages with stack traces |
| `APP_URL` | `http://127.0.0.1:8000` | absolute URLs, OAuth callback URLs |
| `DB_*` | your server | `DB_CONNECTION=mysql8` on MySQL 8 |
| `DB_ENGINE` | `InnoDB` | needed when the server's default engine is MyISAM (WAMP): MyISAM's 1000-byte key limit breaks the migrations, and tests need transactions |
| `MAIL_DRIVER` (or `MAIL_MAILER`) | `log` | mails are written to `storage/logs/laravel.log` |
| `FILESYSTEM_DISK` | `local` | profile pictures and uploads on disk instead of S3 |
| `LOG_DEPRECATIONS_CHANNEL`, `APP_LOG_LEVEL` | `single`, `debug` | log PHP / framework deprecations |

The seeders create the users `admin` (role admin), `test1`, `test2` (obsolete password) and a user with
emails only; their password is in `database/seeders/UsersSeeder.php`. They also create 8 example
OAuth clients (`database/seeders/ClientsSeeder.php`).

On Windows, `openssl_pkey_new()` (used by `lm:keys` and the token tests) needs the `OPENSSL_CONF`
environment variable pointing to PHP's `extras/ssl/openssl.cnf`.

### Front-end assets

`public/js/app.js`, `public/css/app.css` and `public/css/bootstrap-datepicker3.css` are built by Laravel
Mix from `resources/assets/` (`webpack.mix.js`). `public/css/app.css` is **not versioned**
(`.gitignore`), so a fresh checkout has no stylesheet until it is built.

The `npm run dev/production` scripts use Windows `set NODE_ENV=…` syntax and all build in development
mode (unminified), which is how the committed `public/js/app.js` was built. With Node 14 (Linux / WSL):

```bash
npm ci --unsafe-perm        # --unsafe-perm only when running as root (e.g. WSL)
NODE_ENV=development node_modules/.bin/webpack --hide-modules --config=node_modules/laravel-mix/setup/webpack.config.js
```

The build also rewrites the versioned `public/js/app.js`, `public/css/bootstrap-datepicker3.css` and
`public/mix-manifest.json`: commit them only when the JavaScript sources changed.

The toolchain (Laravel Mix 0.10, webpack 2, node-sass 4, Node 14) is end-of-life; replacing it with Vite
is a follow-up.

## Tests

```bash
php vendor/bin/phpunit
```

- Unit tests (`tests/Unit`) need no database: task tokens (`Jws`, `TokenGenerator`, `TokenParser`),
  redirect URI matching of `App\OAuth\AuthCodeGrant`.
- Feature tests (`tests/Feature`) need a MySQL database named `login_module_test` (set in
  `phpunit.xml`), which is wiped: `RefreshDatabase` runs all migrations once, then the
  `DatabaseSeeder`, and wraps each test in a transaction.
- `FeatureTestCase::user($login)` returns a seeded user; `TestCase::TEST_PASSWORD` is the seeded password.
- `LoginTest::testObsoletePasswordIsUpgradedOnLogin` is skipped on purpose: obsolete passwords are never
  checked (see [authentication](authentication.md#obsolete-passwords)).

## Conventions

- Models live directly in `app/` (namespace `App`), domain services in `app/LoginModule`.
- Every model uses the `App\LoginModule\Shared\SerializesLegacyDates` trait (see below). Add it to new models.
- Controllers get their services through constructor injection; declare the properties (PHP 8.2+
  deprecates dynamic properties).
- Request input: use `filled()` to test a value (the 5.4 meaning of `has()`), and prefer `input()` /
  `query()` to `get()` (deprecated by Symfony 7.4; the existing 135 `get()` calls are a known follow-up).
- Forms in views use `BootForm::` (watson/bootstrap-form, Bootstrap 3 markup) and `Form::`.
- Translations: `resources/lang/{en,fr,de,sl,ru}`; notification mails in `resources/lang/*/notifications/`.

## Laravel 5.4 behaviours kept on purpose

The 2026 port to Laravel 12 kept these behaviours, which platforms or users rely on:

| Behaviour | Where | Why |
|---|---|---|
| Dates serialized as `Y-m-d H:i:s` in JSON (not ISO-8601) | `SerializesLegacyDates` trait on all models | the user API / data export format is unchanged |
| `$request->filled()` used where 5.4 code called `has()` | controllers, middleware | 5.5 changed `has()` to "present" instead of "present and not empty" |
| `UserProfile::update()` sets missing fillable attributes to `null` | `app/LoginModule/Profile/UserProfile.php` | 5.4 `only()` returned missing keys as null (unchecked checkboxes) |
| No password rehash on login, bcrypt 10 rounds, no exception on non-bcrypt hashes | `config/hashing.php` | the master password must never be stored as the user's password |
| No `SameSite` attribute on the session cookie by default | `config/session.php` (`SESSION_SAME_SITE`) | 5.4 behaviour; Laravel 7+ defaults to `lax` |
| `App\Providers\PassportServiceProvider` boots before `RouteServiceProvider` | `config/app.php` | the app overrides Passport's `/oauth/authorize` routes |
| `-- ` signature separator escaped in mails | `App\LoginModule\LocalizedNotification` | CommonMark turned the line above it into a heading |
| Legacy env names `MAIL_DRIVER`, `APP_LOG`, `APP_LOG_LEVEL` still read | `config/mail.php`, `config/logging.php` | existing deployments |

## Vendored packages

`packages/laravelcollective-html` (6.4.1) and `packages/bootstrap-form` (watson 1.1.15, the version used
before the upgrade, so the form HTML is unchanged) are Composer path repositories, mirrored into `vendor/`
(`symlink: false`). After editing them, run `composer reinstall france-ioi/laravelcollective-html
france-ioi/bootstrap-form` and `composer update --lock`.

## Useful commands

| Command | Purpose |
|---|---|
| `php artisan route:list` | all routes with their middleware |
| `php artisan migrate:status` | migrations state |
| `php artisan tinker` | REPL with the app booted |
| `php artisan lm:keys` / `passport:keys` | generate key pairs |
| `php artisan lti:send` | send pending LTI results (cron) |
| `composer audit` | known vulnerabilities in PHP dependencies |
