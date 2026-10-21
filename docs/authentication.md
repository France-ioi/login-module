# Authentication and accounts

## The sign-in page

`GET /auth` (`Auth\IndexController`, guests only) shows the sign-in methods of the current platform.

The list and its order come from the client's `auth_order` (JSON array) through
`App\LoginModule\AuthList`:

| Entry | Method |
|---|---|
| `login_email_code` | one field accepting a login, an email or a code (badge / participation code) |
| `login_email` | login or email, then password |
| `code` | badge or participation code only |
| `facebook`, `google`, `pms` | external providers |
| `_HIDDEN` | the following methods go into a collapsed "other methods" section |
| `_DISABLED` | the following methods are not shown |

`AuthList::normalize()` drops unknown entries and appends the missing methods in the default order
(`login_email_code, facebook, google, pms, _HIDDEN, _DISABLED, login_email, code`). Without a platform
the default order is used.

## Password login

`GET|POST /login` (`Auth\LoginController`, laravel/ui `AuthenticatesUsers`).

- Field `login` accepts a login or an email; `remember` enables the remember-me cookie.
- Throttling: 5 failed attempts per minute per login + IP (Laravel default).
- `App\LoginModule\AuthProviders\UserProvider` resolves the user:
  - without `@`: by `users.login`;
  - with `@`: by `emails.email` where `login_enabled = 1` (the email's user), then as a login;
  - accounts merged from other login-module instances are retried with each `origin_instance_id`.
- `validateCredentials()` calls `App\LoginModule\UserPassword::check()`:
  1. the support **master password** (`auth.master_hash`, `UserPassword::checkMasterPassword()`);
  2. bcrypt `users.password` when `regular_password` is set;
  3. obsolete passwords (see below).

  Then `Group::reduceByPassword()` merges the other accounts of the user's merge group that have the same
  password (see [account merging](#merged-accounts)).
- After login (`AuthLogin` listener): `users.ip` and `last_login` are updated, and the recommended
  attributes of the platform are checked once.
- Redirect: users with the `admin` role go to `/admin`; others to the platform
  (`PlatformContext::continueUrl()`, default `/profile`). The platform context survives the session
  regeneration.

### Obsolete passwords

`obsolete_passwords` holds hashes imported from older systems (`md5` with salt, `sha512`).
`UserPassword::checkObsoletePassword()` would rehash a matching password into `users.password` and delete
the obsolete rows, but it reads `$user->obsolete_passwords`, which is not a relation (the relation is
`obsoletePasswords()`), so **obsolete passwords are never checked** (true before and after the 2026 port).
Fixing it would enable logins that currently fail: a product decision.

## Codes and badges

`POST /login_with_code` (`Auth\LoginWithCodeController`): `identity`, `try_code`, `try_password`, `remember`.

With `try_code`, `identity` is tried as:

1. **a badge code** already stored (`badges.code`): its data is refreshed from the badge API; if
   `badges.login_enabled`, the badge's user is logged in;
2. **a badge from the platform's badge API** (`BadgeApi.auth_enabled`): the badge data (user id, names,
   email…) is stored in session; if the badge user is unknown, an account is created (login prefixed
   `badge_`, see [platform integration](platform-api.md#badges)) or, when the email/login is taken, the
   user is sent to the registration form with the badge attached;
3. **a participation code**: verified by the platform (`POST {api_url}` `module=group_code&action=verify`);
   a `user-XXXXXXXX` account is generated with a login-enabled badge holding a new participation code, a
   `platform_groups` row is saved and the user sees the code on `/participation_code`.

With `try_password`, a value that is an email or an existing login redirects to `/login` with the field
filled in.

Generated logins, passwords, codes and tokens come from `App\LoginModule\UserDataGenerator`
(alphabet without ambiguous characters).

## External providers

| Provider | Library | Notes |
|---|---|---|
| Google | google/apiclient (`Google_Client`) | OpenID Connect, scopes openid/profile/email, `prompt=select_account`; `uid` = `sub`, legacy OpenID ids (`uid_old`) migrated |
| Facebook | league/oauth2-facebook | fields id, name, email; Graph API version `FACEBOOK_GRAPH_API_VERSION` |
| PMS | league/oauth2-client `GenericProvider` subclass | `{PMS_URL}/wa/OAuth2/{authorize,token,userData}`; user data overwrites the profile, fixed fields (email, birthday, names, language) are read-only in the profile |
| FranceConnect | league/oauth2-client | implemented but disabled in `App\OAuthClient\Manager` |

Routes (`Auth\OAuthClientController`):

| Route | Purpose |
|---|---|
| `GET /oauth_client/redirect/{provider}` | start a sign-in |
| `GET /oauth_client/add/{provider}` | link a provider to the current account (back to `/auth_methods`) |
| `GET /oauth_client/preferences/{provider}` | PMS preferences page |
| `GET /oauth_client/callback/{provider}` | provider callback |
| `POST /oauth_client/remove/{provider}` | unlink (facebook, google) |
| `GET /oauth_client/email_exists` | "this email already has an account" page |
| `GET /session_expired` | shown when the provider callback fails |

Callback logic (`App\LoginModule\AuthConnector`):

- known connection (`auth_connections` by provider + uid) → log in that user (PMS also updates the profile);
- unknown connection, user logged in → link it to the current account;
- unknown connection, provider email matches a login-enabled email → `email_exists` page (sign in with the
  existing account and link it from the auth methods page);
- otherwise → create a user with the provider data and a primary email.

PMS sign-ins also create badges from the PMS data (teacher, school, competitions, grade).

## Logout

| Route | Behaviour |
|---|---|
| `GET /logout` | guests: back to `redirect_uri` or `/auth`. Users with active Facebook/Google connections get a page to also log out from them (choice remembered in `users.logout_config`); others are logged out at once |
| `POST /logout_start` | saves the provider choice, starts the loop |
| `GET /logout_loop` | redirects to each provider's logout URL in turn, coming back with `?provider=` |
| `GET|POST /logout_finish`, `POST /logout` | logs out, keeps the platform context, redirects to the saved `redirect_uri` or `/auth` |

## Registration

`GET|POST /register` (`Auth\RegisterController`, laravel/ui `RegistersUsers`).

- Fields: `login` and/or `primary_email` (those the platform requires, `login` by default), password
  (min 6, confirmed). Rules come from `SchemaConfig`.
- If the login is not normalized, the form comes back with a suggestion (`App\LoginModule\LoginSuggestion`:
  lower case, allowed characters only, random digits appended while taken or historical).
- The email gets a verification code when the platform requires an email.
- Badge data kept in session (badge sign-in with a taken email/login) is attached to the new account.

## Passwords

### Reset

1. `GET /password/reset`: field `login_or_email`.
2. `POST /password/email`: an email sends the code to that address; a login redirects to
   `GET /password/emails/{login}`, which lists the account's emails (masked by `EmailMasker`) to choose one
   (`email_id`).
3. The mail (`ResetPasswordNotification`) contains a **20-character code** (`auth.reset_password_token_length`)
   and the login; `GET /password/reset/new` lets the user paste it.
4. `POST /password/reset` (laravel/ui `ResetsPasswords`, minimum 6 characters) sets the password,
   stamps `last_password_recovery_at`, deletes obsolete passwords and logs the user in.

Specifics (`App\LoginModule\Passwords`):

- the broker works on **emails** (`App\Email` implements `CanResetPassword`, provider `emails`);
- a new reset is refused (`passwords.reset_refused`) within `auth.password_recovery_interval`
  (24 h) of the last successful recovery;
- codes are valid `auth.passwords.emails.expire` minutes (60). The custom `PasswordBrokerManager`
  converts minutes to seconds for Laravel 7+.

### Change

`GET|POST /password` (`PasswordController`, behind `reauthentication`): new password (min 6, confirmed),
obsolete passwords deleted. The form is on the auth methods page.

### Reauthentication

`CheckReauthentication` (alias `reauthentication`) protects `/auth_methods` and `/password`: users who
have a password and logged in more than 10 minutes ago (`Reauthentication::INTERVAL`) must type it again
at `/reauthentication`.

### Group admin password tool

`GET|POST /group_admin/password` (`GroupAdmin\PasswordController`, logged-in users) lets a platform's group
administrator (e.g. a teacher) set the password of an account of their group, without the platform
handling passwords.

- The platform links to it with `client_id`, `token`, `return_url` and `alg`. The token is a JSON
  `{requester_id, target_id, exp}` encrypted with the client secret: `AES-256-GCM` (hex: 12-byte IV +
  ciphertext + 16-byte tag, key = first 32 bytes of the secret).
- `requester_id` must be the logged-in user, `exp` in the future.
- Limits (`config/auth.php` `password_changes`): 5 changes per requester and 15 overall per hour; logins
  in `forbidden` (`admin`) can't be changed. Every attempt is logged in `password_changes`
  (done / throttled / rejected).

## Auto-login tokens

Platforms can create accounts with an **auto-login token** (platform API `accounts_manager/create` with
`auto_login`). Sending the user to `/oauth/authorize?…&login=…&auto_login_token=…` logs them in once
(`AutoLoginToken` middleware, Passport routes only); the token is then deleted.

## Auth methods page

`GET /auth_methods` (behind `reauthentication`) lists the linked providers, the badges and the password
form. `POST /auth_methods/badge_login_ability/{id}/{enabled}` enables or disables signing in with one of
the user's badges. A warning is shown when the account has no password and no login-enabled badge.

## Logins

`GET|POST /profile/username` changes the login: see [profile](profile-and-verification.md#login-changes).

## Merged accounts

Accounts imported from other login-module instances (`origin_instances`) or from platforms can belong to
a **merge group** (`users.merge_group_id`): accounts that are probably the same person.

- `merging_accounts` middleware: when a merge is pending, the user is sent to `/merging_accounts`, which
  shows the other account (instance, shared login/emails).
- Accept (`POST /merging_accounts/accept`): the user is logged out and asked to sign in with the other
  account; accepting again merges them (`Group::mergeUsers`: badges copied, the other user deleted).
- Decline: shared values must be revalidated (`login_revalidate_required`, `email_revalidate_required`),
  shown as an alert in the profile.
- Signing in with a password shared by accounts of the group merges them automatically
  (`Group::reduceByPassword`).

## Language

`App\LoginModule\Locale::get()`: user's `language`, then session, then browser `Accept-Language`, among
`config('app.locales')` (en, fr, de, sl, ru). `GET /set_locale/{locale}` and the `locale` parameter of
`/oauth/authorize` change it (saved on the user when logged in).
