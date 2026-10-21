# Configuration reference

All environment variables are read in `config/*.php` only (no `env()` call in `app/`). With
`php artisan config:cache` (run by `update.sh`), `.env` is read once at cache time: re-run it after
changing `.env`.

## Environment variables

### Application

| Variable | Config key | Default | Meaning |
|---|---|---|---|
| `APP_ENV` | `app.env` | `production` | `local` enables nothing special besides debug tooling |
| `APP_DEBUG` | `app.debug` | `false` | detailed error pages; **never true in production** |
| `APP_URL` | `app.url`, `filesystems.disks.public.url` | `http://localhost` | absolute URLs (OAuth callbacks, public file URLs) |
| `APP_KEY` | `app.key` | — | encryption key (sessions, cookies, OAuth codes); generate with `php artisan key:generate` |

`app.name` (`Login module`), `app.timezone` (`UTC`) and `app.locale` (`en`) are not configurable by env.
`app.locales` lists the UI languages (en, fr, de, sl, ru).

### Database

| Variable | Config key | Default | Meaning |
|---|---|---|---|
| `DB_CONNECTION` | `database.default` | `mysql` | `mysql8` adds explicit SQL modes for MySQL 8 |
| `DB_HOST`, `DB_PORT` | `connections.*` | `127.0.0.1`, `3306` | |
| `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | `connections.*` | `forge`, `forge`, empty | |
| `DB_ENGINE` | `connections.mysql(8).engine` | null (server default) | set `InnoDB` when the server default is MyISAM |
| `REDIS_HOST`, `REDIS_PASSWORD`, `REDIS_PORT` | `database.redis.default` | `127.0.0.1`, null, `6379` | only if a Redis driver is used |

Charset/collation are fixed to `utf8` / `utf8_unicode_ci`.

### LTI tool provider database

The IMS LTI library uses its own PDO connection (`app/LoginModule/LTI/LTIPDO.php`), not a Laravel one.

| Variable | Config key | Default |
|---|---|---|
| `LTI_DB_HOST` | `lti.db.host` | `127.0.0.1` |
| `LTI_DB_PORT` | `lti.db.port` | `3306` |
| `LTI_DB_DATABASE` | `lti.db.database` | `lti-to-bebras` |
| `LTI_DB_USERNAME` | `lti.db.user` | `root` |
| `LTI_DB_PASSWORD` | `lti.db.password` | `buddy` |

The defaults are the values that were hard-coded before 2026; set real values in production.

### Mail

| Variable | Config key | Default | Notes |
|---|---|---|---|
| `MAIL_MAILER` | `mail.default` | `MAIL_DRIVER`, then `smtp` | `smtp`, `sendmail`, `log`, `array` |
| `MAIL_DRIVER` | (legacy) | — | still read when `MAIL_MAILER` is unset |
| `MAIL_HOST`, `MAIL_PORT` | `mail.mailers.smtp` | `smtp.mailgun.org`, `587` | |
| `MAIL_ENCRYPTION`, `MAIL_SCHEME` | `mail.mailers.smtp` | `tls`, null | `MAIL_SCHEME=smtps` for implicit TLS |
| `MAIL_USERNAME`, `MAIL_PASSWORD` | `mail.mailers.smtp` | null | |
| `MAIL_SENDMAIL_PATH` | `mail.mailers.sendmail.path` | `/usr/sbin/sendmail -bs` | |
| `MAIL_LOG_CHANNEL` | `mail.mailers.log.channel` | null | |
| `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME` | `mail.from` | `hello@example.com`, `Example` | |

### Logging

| Variable | Config key | Default | Notes |
|---|---|---|---|
| `LOG_CHANNEL` | `logging.default` | `APP_LOG`, then `single` | `single`, `daily`, `syslog`, `errorlog` |
| `LOG_LEVEL` | `logging.channels.*.level` | `APP_LOG_LEVEL`, then `debug` | |
| `LOG_DAILY_DAYS` | `logging.channels.daily.days` | `5` | |
| `LOG_DEPRECATIONS_CHANNEL` | `logging.deprecations.channel` | `null` | e.g. `single` to log PHP/framework deprecations (warning level) |

### Session, cache, queue

| Variable | Default | Notes |
|---|---|---|
| `SESSION_DRIVER` | `file` | lifetime 120 minutes, `expire_on_close` true |
| `SESSION_DOMAIN` | null | |
| `SESSION_SECURE_COOKIE` | `false` | set `true` behind HTTPS |
| `SESSION_SAME_SITE` | null | no `SameSite` attribute (Laravel 5.4 behaviour); `lax` recommended |
| `CACHE_DRIVER` | `file` | `MEMCACHED_*` variables for memcached |
| `QUEUE_DRIVER` | `sync` | no queued jobs in the app |
| `BROADCAST_DRIVER`, `PUSHER_*` | null | broadcasting unused |

### Files

| Variable | Config key | Default | Notes |
|---|---|---|---|
| `FILESYSTEM_DISK` | `filesystems.default` | `public` | disk for profile pictures and verification uploads |
| `AWS_KEY`, `AWS_SECRET`, `AWS_REGION`, `AWS_BUCKET` | `filesystems.disks.s3` | — | S3 disk (visibility public) |

The `public` disk's root is `public/` itself.

### Authentication and passwords

| Variable | Config key | Default | Meaning |
|---|---|---|---|
| `AUTH_MASTER_HASH` | `auth.master_hash` | — | bcrypt hash of the support master password (see [authentication](authentication.md#password-login)) |
| `BCRYPT_ROUNDS` | `hashing.bcrypt.rounds` | `10` | |

### External identity providers

| Variable | Config key | Notes |
|---|---|---|
| `GOOGLE_ID`, `GOOGLE_SECRET` | `oauth_client.google.*` | Google OpenID Connect |
| `GOOGLE_OPENID_REALM` | `oauth_client.google.openid.realm` | legacy OpenID 2 realm, used to map old ids (`uid_old`) |
| `FACEBOOK_ID`, `FACEBOOK_SECRET` | `oauth_client.facebook.*` | |
| `FACEBOOK_GRAPH_API_VERSION` | `oauth_client.facebook.graph_api_version` | default `v2.3` (Facebook upgrades old versions automatically) |
| `PMS_URL`, `PMS_ID`, `PMS_SECRET` | `oauth_client.pms.*` | PMS (Slovenian platform) OAuth server |
| `FRANCE_CONNECT_ID`, `FRANCE_CONNECT_SECRET` | `oauth_client.france_connect.*` | provider currently disabled in `App\OAuthClient\Manager` |

### UI

| Variable | Config key | Default | Meaning |
|---|---|---|---|
| `UI_PROFILE_PICTURE` | `ui.profile_picture.default` | — | default avatar URL (`/images/user.png` in `.env.example`) |
| `UI_FOOTER` | `ui.footer` | empty | HTML footer shown when the client has no footer |

Passport has no environment variables: its keys are `storage/oauth-private.key` and
`storage/oauth-public.key` (`php artisan passport:keys`).

## Application settings (config files)

### `config/auth.php`

| Key | Default | Effect |
|---|---|---|
| `master_hash` | env | see above; checked by `App\LoginModule\UserPassword::checkMasterPassword` |
| `password_recovery_interval` | `86400` (s) | minimum time between two reset-link mails for an account (`users.last_password_recovery_at`); empty disables |
| `reset_password_token_length` | `20` | length of password reset tokens (`App\LoginModule\Passwords\DatabaseTokenRepository`) |
| `password_changes.throttle_interval` | `3600` (s) | window for the group-admin password change limits |
| `password_changes.throttle_user_limit` | `5` | changes per requester in the window |
| `password_changes.throttle_global_limit` | `15` | changes by all requesters in the window |
| `password_changes.forbidden` | `['admin']` | logins whose password can't be changed by group admins |
| `defaults.passwords` | `emails` | password broker on `App\Email` (table `password_resets`, 60 min) |

Guards: `web` (session) and `api` (Passport). User providers: `users` (`login_module_user_provider`,
`App\User`) and `emails` (`login_module_email_provider`, `App\Email`).

### `config/profile.php`

| Key | Default | Effect |
|---|---|---|
| `login_change_available` | `first_interval` `PT1H`, `second_interval` `P1Y` | when a user may change their login (ISO-8601 intervals); `false` = always |
| `login_validator.new` | `/^[a-z0-9-]+$/` | rule `login` for new logins |
| `login_validator.existing` | `/^[a-z0-9-_]+$/` | logins not matching get `login_change_required` (2018 migration) |
| `login_validator.filter` | `/[^a-z0-9-]/` | characters removed by `LoginSuggestion` |
| `login_validator.length.min/max` | `3` / `30` | login length (registration, platform API account creation) |
| `sections` | personal, school, contact, public, settings | grouping of attributes in the profile form |

### `config/verification.php`

| Key | Default | Effect |
|---|---|---|
| `expiration_alert_interval` | 5 days | a verification about to expire is flagged for renewal |
| `email_code_interval` | 60 s | minimum time between two verification codes for an email |

### Others

| File / key | Default | Effect |
|---|---|---|
| `gdpr.attributes` | address, city, zipcode, primary_phone, secondary_phone | personal data attributes: disabled in the profile when empty |
| `graduation_month.default` / `AU` | 7 / 1 | month the school year ends, per country (graduation year computation) |
| `ui.star` | ` (*)` | required field marker |
| `ui.profile_picture.width/height` | 200 / 200 | profile picture resize |
| `ui.profile_picture.max_file_size` | 1 MB | profile picture and verification upload limit |
| `lti.default_login_prefix` | `lti_` | login prefix for accounts created by LTI |
| `lti.prefixes_by_consumer` | `[]` | per-consumer prefixes (also editable in admin "LTI configs") |
| `lti.send_result.period` | `PT600S` | minimum delay between two attempts to send an LTI result |
| `lti.send_result.attempts_max` | `5` | `false` = unlimited |
| `permission.*` | spatie v6 defaults | tables, 24 h cache under `spatie.permission.cache` |
| `hashing.rehash_on_login` | `false` | **must stay false** (master password) |
| `hashing.bcrypt.verify` | `false` | `Hash::check()` returns false on a non-bcrypt hash instead of throwing |
| `image.driver` | `gd` | Intervention Image |
