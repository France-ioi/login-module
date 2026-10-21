# Data model

`increments` = `INT UNSIGNED` auto-increment primary key. User ids start at 100000000.

## Users and credentials

### `users` → `App\User`

Traits: `Notifiable`, `HasApiTokens` (Passport), `VerifiableUser`, `HasRoles` (spatie), `SerializesLegacyDates`.

| Group | Columns |
|---|---|
| Login | `login` (nullable), `login_fixed`, `login_updated_at`, `login_revalidate_required`, `login_change_required`, `password` (nullable), `regular_password` (false = only obsolete passwords) |
| Identity | `first_name`, `last_name`, `real_name_visible`, `gender` (m/f/o), `birthday`, `birthday_year`, `nationality`, `language`, `timezone` |
| Contact | `country_code`, `address`, `city`, `zipcode`, `primary_phone`, `secondary_phone` |
| School | `role` (student/teacher/other), `school_grade`, `student_id`, `ministry_of_education`, `ministry_of_education_fr`, `graduation_grade`, `graduation_year`, `graduation_grade_expire_at` |
| Public profile | `presentation`, `website`, `picture` |
| Technical | `ip`, `last_login`, `remember_token`, `logout_config` (JSON), `last_password_recovery_at`, `merge_group_id`, `origin_instance_id`, `creator_client_id`, `subscription_results`, `subscription_news`, timestamps |

Unique `(login, origin_instance_id)`. Hooks (`User::boot`):

- `saving`: sets `regular_password` when a password is set; normalizes `graduation_grade` and recomputes
  `graduation_year` / `graduation_grade_expire_at` (`App\LoginModule\Graduation`); stamps `login_updated_at`
  and clears `login_change_required` on a valid login change; keeps `birthday` and `birthday_year` in sync.
- `deleting`: removes the user's badges from the badge APIs (`BadgeRequest::remove`); throws if a removal fails.

Relations: `authConnections`, `emails`, `badges`, `obsoletePasswords`, `platformGroups`, `tokens`,
`verifications`, `accessTokenCounters`, `ltiConnections`, `usernameChanges` (has many), `autoLoginToken`
(has one), `originInstance` (belongs to).

### Related tables

| Table | Model | Columns / notes |
|---|---|---|
| `emails` | `App\Email` | `user_id`, `role` (primary/secondary), `email`, `login_enabled`, `email_revalidate_required`, `origin_instance_id`, `code`, `last_code_at`; unique `(email, login_enabled, origin_instance_id)`; implements `CanResetPassword` (password reset is per email) |
| `password_resets` | — | `email`, `token` (hashed), `created_at` |
| `obsolete_passwords` | `App\ObsoletePassword` | `user_id`, `password`, `salt`, `type` (md5/sha512): hashes imported from older systems |
| `auth_connections` | `App\AuthConnection` | `user_id`, `provider`, `uid`, `active`, `access_token`, `refresh_token`: links to Google/Facebook/PMS accounts |
| `auto_login_tokens` | `App\AutoLoginToken` | `user_id` (unique), `token` (unique): one-shot login links |
| `username_changes` | `App\UsernameChange` | `user_id`, `old_login`: former logins can't be reused |
| `password_changes` | `App\PasswordChange` | `requester_user_id`, `target_user_id`, `status` (done/throttled/rejected): group admin password changes |
| `user_deletions` | `App\UserDeletion` | `user_id`, `client_id` |
| `origin_instances` | `App\OriginInstance` | `name`: other login-module instances whose accounts were merged in |
| `official_domains` | `App\OfficialDomain` | `country_code`, `domain`: school email domains (teacher verification) |

## OAuth

| Table | Model | Notes |
|---|---|---|
| `oauth_clients` | `App\Client` (extends Passport's) | platforms; base Passport columns + the extensions below |
| `oauth_auth_codes`, `oauth_access_tokens`, `oauth_refresh_tokens`, `oauth_personal_access_clients` | Passport | created by the `2016_06_01_*` migrations (Passport 4 schema, copied into the repo) |
| `access_token_counters` | `App\AccessTokenCounter` | `(user_id, client_id)` primary key, `total`, `last_created_at`: incremented on each access token |
| `platform_groups` | `App\PlatformGroup` | `user_id`, `client_id`, `group_code`, `participation_code`: group code returned once by the user API |
| `oauth_client_verification_method` | pivot | `client_id`, `verification_method_id`, `expiration` |

`oauth_clients` extensions (JSON arrays default to `[]`):

| Column | Type | Meaning |
|---|---|---|
| `user_attributes` | JSON array | profile attributes the platform requires |
| `recommended_attributes` | JSON array | attributes suggested after login |
| `verifiable_attributes` | JSON array | attributes that must be verified |
| `hidden_attributes` | JSON array | attributes hidden from the profile form for this platform |
| `attributes_filter` | JSON object | conditions on attribute values the user must meet |
| `auth_order` | JSON array | authentication methods shown on `/auth`, in order (`_HIDDEN`, `_DISABLED` separators) |
| `public_key` | text | key verifying the platform's `/lti` tokens |
| `badge_api_id`, `badge_required`, `badge_autologin` | | badge integration |
| `api_url` | string | platform endpoint |
| `autoapprove_authorization` | bool | skip the consent screen |
| `email`, `footer` | string | platform contact email, footer HTML |
| `provider` | string | Passport 9+ column (unused) |

See [OAuth server](oauth-server.md#client-settings) for how they are used.

## Badges

| Table | Model | Notes |
|---|---|---|
| `badge_apis` | `App\BadgeApi` | `name`, `url`, `auth_enabled` |
| `badges` | `App\Badge` | `user_id`, `badge_api_id`, `code`, `data` (JSON), `do_not_possess`, `login_enabled`, `override_profile`, `comments`, `origin_client_id`, `url` (legacy); unique `(url, code, badge_api_id)` |
| `pms_admin_badges` | `App\PmsAdminBadge` | `pms_id`, `badge` |

## Verification

| Table | Model | Notes |
|---|---|---|
| `verification_methods` | `App\VerificationMethod` | `name` (email_code, email_domain, id_upload, classroom_upload, peer, imported_data, user_helper), `user_attributes` (JSON: attributes it can verify), `public` |
| `verifications` | `App\Verification` | `user_id`, `method_id`, `user_attributes`, `status` (pending/approved/rejected), `approved_at`, `confidence`, `message`, `file`, `code`, `rejected_attributes` |

## LTI

| Table | Model | Notes |
|---|---|---|
| `lti_connections` | `App\LtiConnection` | `user_id`, `lti_consumer_key`, `lti_context_id`, `lti_user_id`, `content_id`; unique `(lti_user_id, lti_context_id, lti_consumer_key)` |
| `lti_results` | `App\LtiResult` | `lti_connection_id`, `score`, `attempts`, `last_attempt`: queue of grades to send back |
| `lti_configs` | `App\LtiConfig` | `lti_consumer_key` (unique), `prefix`: login prefix per LMS |

The IMS LTI library also needs `lti_consumer` (with an extra `login_prefix` column), `lti_context`,
`lti_user`, `lti_nonce` and `lti_share_key` in the separate LTI database (`LTI_DB_*`). These tables are
**not** created by the migrations.

## Roles and permissions (spatie/laravel-permission v6)

`roles`, `permissions` (`name`, `guard_name`), `model_has_roles`, `model_has_permissions`
(`model_type` = `App\User`), `role_has_permissions`.

Permissions: `admin.users.manager`, `admin.verifications.manager`, `admin.verifications.verify`,
`admin.clients.manager`, `admin.domains.manager`, `admin.lm_instances.manager`, `admin.misc`. The `admin`
role has all of them.

## Migrations with side effects

- `2017_04_10_*` regenerates codes and **sends a verification mail** to every unverified address. Keep that in mind when replaying migrations on an old dump.
- `2018_03_19_221945_convert_admins` and `2018_03_20_215239_*` run seeders through `Artisan::call`.
- `2018_10_25_185041_*` creates the `admin.misc` permission and grants it to the `admin` role.
- `2026_10_21_000001_upgrade_passport_tables`, `2026_10_21_000002_upgrade_permission_tables`: see [deployment](deployment.md#upgrading-from-the-laravel-54--php-7-version).

## Seeders

| Seeder | Creates |
|---|---|
| `DatabaseSeeder` | runs the five seeders below |
| `RolesAndPermissionsSeeder` | permissions, role `admin` (idempotent), assigns it to the `admin` user |
| `UsersSeeder` | `admin`, an email-only user, `test1`, `test2` (obsolete md5 passwords): **test data** |
| `ClientsSeeder` | 8 example clients (Example client, Algorea, France-ioi, Bebras × dev/demo), all with the same secret: **test data** |
| `OfficialDomainsSeeder`, `OriginInstancesSeeder` | sample domains and two origin instances |
| `VerificationMethodsSeeder` | the 7 verification methods (run by a migration) |
