# Administration and maintenance

## Admin interface

`/admin` (prefix `admin`, route names `admin.*`, middleware `auth`). Users with the `admin` role land on
it after login. Each section requires a permission (spatie/laravel-permission, `permission:` middleware,
403 otherwise):

| Section | Permission | Features |
|---|---|---|
| Dashboard `/admin` | logged in | menu |
| Users `/admin/users` | `admin.users.manager` | search (id, login, names, email), details, emails, set password, roles/permissions, delete, password reset link (generated and sent by mail with an editable text) |
| Reset passwords `/admin/reset_passwords` | `admin.users.manager` | random new passwords for a list of logins (one per line), shown in a table |
| Clients `/admin/clients` | `admin.clients.manager` | platforms: name, secret, redirect URIs, required / recommended / hidden / verifiable attributes, attribute filter, sign-in methods order, verification methods and their expiration, badge API, consent skip, API URL, contact email |
| Official domains `/admin/official_domains` | `admin.domains.manager` | school email domains per country |
| Origin instances `/admin/origin_instances` | `admin.lm_instances.manager` | other login-module instances merged in |
| Verifications `/admin/verifications/edit` | `admin.verifications.verify` | review uploaded ID / classroom photos one by one: approve (choose the verified attributes, confidence) or reject, with a message |
| LTI configs, Badge APIs | `admin.misc` | login prefix per LTI consumer key; badge APIs (name, URL, sign-in allowed) |

`admin.verifications.manager` exists but guards no route.

The `admin` role has every permission.

## Console commands

| Command | Purpose |
|---|---|
| `php artisan lm:keys` | generate `storage/lm-private.key` / `lm-public.key` (task and LTI tokens) |
| `php artisan passport:keys` | generate the OAuth signing keys |
| `php artisan lti:send` | retry sending queued LTI results (cron) |
| `php artisan lm:import HOST PORT DATABASE USERNAME PASSWORD` | import accounts from the old (v1) login module |
| `php artisan lm:merge ID HOST PORT DATABASE USERNAME PASSWORD` | merge another login-module (v2) instance, `ID` = its `origin_instances` id |
| `php artisan bebras:import HOST PORT DATABASE USERNAME PASSWORD` | import teachers from a Bebras platform database |

### Importers (`app/LoginModule/Migrators`)

They connect to the source database as `external_source` (`ExternalDatabase`; note: the port argument is
overridden by 3306).

- **lm:import** (v1 login module): users keep their ids; old md5 passwords go to `obsolete_passwords`;
  badges from `user_badges`; PMS / LTI auth connections from `users_auths`; Google / Facebook connections
  from OpenID identities; an approved `imported_data` verification of `role`.
- **lm:merge** (v2 instance): clients matched by redirect URI; users grouped with existing ones sharing a
  login or email (merge groups, see [authentication](authentication.md#merged-accounts)); copies emails,
  obsolete passwords, badges, auth connections, OAuth tokens and codes, official domains. It currently
  doesn't run: it references relations and a column that no longer exist.
- **bebras:import**: Bebras `user` rows (non-admin) matched by `externalID` or emails; adds `email_code`,
  `email_domain` and `imported_data` verifications; writes `externalID` back to the Bebras database.

## Logs and monitoring

- Application log: `storage/logs/laravel.log` (`LOG_CHANNEL`, `LOG_LEVEL`).
- Group admin password changes: table `password_changes`.
- Platform unlinks: table `user_deletions`.
- OAuth usage per user and platform: table `access_token_counters`.
