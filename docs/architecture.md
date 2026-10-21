# Architecture

## What the login module is

The login module is the central identity provider of the France-ioi platforms
(France-ioi, Algorea, Bebras, Castor…). It:

- stores user accounts and their profile attributes (names, school, role, graduation…);
- lets users sign in with several methods (password, external OAuth providers, badges / participation codes, LTI);
- is an **OAuth 2 authorization server** for the platforms: a platform redirects the user here, gets an
  authorization code, exchanges it for an access token and reads the account through the user API;
- makes sure that the profile requested by a platform is complete and, when needed, **verified**
  (teacher status, school email…) before handing the user back;
- offers server-to-server APIs (encrypted "platform API") and an LTI 1.0 tool provider.

## Stack

| Component | Version | Notes |
|---|---|---|
| PHP | 8.4.27+ / 8.5.12+ | `composer.json` requires `^8.4`, `config.platform.php` is `8.4.0` |
| Laravel | 12 | classic structure kept (`app/Http/Kernel.php`, `app/Providers/*`, `config/app.php` providers) |
| Laravel Passport | 12 | OAuth 2 server (league/oauth2-server 8) |
| spatie/laravel-permission | 6 | roles and permissions of the admin interface |
| laravel/ui | 4 | `Auth::routes()` and the auth controller traits |
| laravel/helpers | 1.x | legacy `array_*` / `str_*` helpers still used in the code |
| laravelcollective/html 6.4.1, watson/bootstrap-form 1.1.15 | vendored in `packages/` | form builders (`Form::`, `BootForm::`), abandoned upstream |
| intervention/image | 2.7 | profile pictures (GD) |
| google/apiclient | 2.x | Google sign-in (only the `Oauth2` service is kept, see `composer.json` `extra`) |
| league/oauth2-client, league/oauth2-facebook | 2.x | PMS, FranceConnect and Facebook sign-in |
| MySQL | 5.7+ / 8.x, InnoDB | MariaDB 10.3+ also works |
| Front end | Bootstrap 3, jQuery, Vue 2, Laravel Mix 0.10 | see [development](development.md#front-end-assets) |

## Directory layout

```
app/
  *.php                    Eloquent models (User, Email, Client, Badge, Verification…), namespace App
  Console/Commands/        artisan commands (lm:keys, lm:import, lm:merge, bebras:import, lti:send)
  Exceptions/Handler.php   CSRF mismatch → redirect back with a message; guests → /auth
  Helpers/                 EmailMasker
  Http/Controllers/        web controllers; Admin/, Auth/, GroupAdmin/, PlatformAPI/, UserAPI/, Verification/
  Http/Middleware/         app middleware (platform context, locale, platform API decryption…)
  Listeners/               Login and AccessTokenCreated event listeners
  LoginModule/             domain code (profile schema, verification, platform context, LTI, migrators…)
  Notifications/           localized mail notifications
  OAuth/AuthCodeGrant.php  custom authorization code grant (regexp redirect URIs)
  OAuthClient/             external identity providers (Google, Facebook, PMS, FranceConnect)
  Providers/               service providers
config/                    configuration (see configuration.md)
database/migrations/       schema history since 2016
database/seeders/          development data and roles/permissions
packages/                  vendored abandoned libraries (path repositories in composer.json)
resources/views/           Blade views
resources/lang/            translations (en, fr, de, sl, ru)
resources/assets/          Sass and JS sources of the front end
routes/                    web.php, passport.php, user_api.php, platform_api.php, console.php
storage/*.key              OAuth (Passport) and LTI/task token key pairs (not versioned)
tests/                     PHPUnit (Unit/ and Feature/)
```

## Request lifecycle

### Middleware

Global middleware (`app/Http/Kernel.php`): maintenance mode, post size, `TrimStrings` (passwords excluded),
`ConvertEmptyStringsToNull`.

The `web` group, in order:

1. `EncryptCookies`, `AddQueuedCookiesToResponse`, `StartSession`
2. **`PlatformRequest`**: feeds the request to the `PlatformContext` singleton, which remembers in the
   session which platform (OAuth client) the user came from, see [OAuth server](oauth-server.md#platform-context)
3. `ShareErrorsFromSession`, `VerifyCsrfToken` (except `lti/entry`, `lti/launch`), `SubstituteBindings`
4. **`AppLocale`**: sets the locale from the session / browser (`App\LoginModule\Locale`)

Route middleware aliases defined by the app:

| Alias | Class | Role |
|---|---|---|
| `auth.auto_login_token` | `AutoLoginToken` | logs in with `?login=…&auto_login_token=…` (one-shot token) |
| `guest` | `RedirectIfAuthenticated` | authenticated users go to `/profile` |
| `reauthentication` | `CheckReauthentication` | asks for the password again before sensitive pages |
| `authorization_available` | `AuthorizationAvailable` | blocks `/oauth/authorize` until the profile is complete, verified and the badge valid |
| `auto_authorization` | `AutoAuthorization` | auto-approves some authorization requests |
| `merging_accounts` | `MergingAccounts` | forces the merge screen when the account has pending merges |
| `platform_api` | `PlatformAPI` | decrypts platform API payloads (AES with the client secret) |
| `role`, `permission` | `RoleMiddleware`, `PermissionMiddleware` | admin interface access (spatie roles/permissions) |
| `scopes`, `scope` | Passport | OAuth scope checks (`account`) |

### Routes

| File | Prefix / middleware | Content |
|---|---|---|
| `routes/web.php` | `web` | pages: auth, profile, verification, badges, admin… |
| `routes/passport.php` | `auth.auto_login_token, web, auth, authorization_available` | overrides of Passport's `/oauth/authorize` routes, `oauth/auto_authorize` |
| Passport defaults | `oauth/` | `/oauth/token`, tokens, clients, scopes… (registered by Passport) |
| `routes/user_api.php` | `user_api/`, `auth:api` | `GET user_api/account` |
| `routes/platform_api.php` | `platform_api/`, `platform_api` | encrypted server-to-server API |

`App\Providers\PassportServiceProvider` (which replaces Passport's provider, excluded from package
discovery) is registered **before** `RouteServiceProvider` in `config/app.php`, so that the app's
`/oauth/authorize` routes, loaded later, override Passport's ones.

### Service providers

| Provider | Role |
|---|---|
| `AppServiceProvider` | custom validators: `login`, `not_historical_username`, `value_different` |
| `AuthServiceProvider` | Passport scope `account`; user providers `login_module_user_provider` and `login_module_email_provider` |
| `EventServiceProvider` | `Login` → `AuthLogin` listener (IP, last login); `AccessTokenCreated` → counter |
| `PassportServiceProvider` | Passport with the custom `App\OAuth\AuthCodeGrant` |
| `RouteServiceProvider` | maps the route files above (controller namespace `App\Http\Controllers`) |
| `Passwords\PasswordResetServiceProvider` | custom password broker (reset by email, refusal interval) |
| `Platform\PlatformContextServiceProvider` | `PlatformContext` singleton |
| `Profile\UserProfileServiceProvider` | `UserProfile` singleton |

## Design notes

- **Users vs emails.** Authentication uses two Eloquent user providers: `users` (`App\User`, login by
  login or email) and `emails` (`App\Email`, used by the password broker, since password reset works
  per email address). See [authentication](authentication.md).
- **Platform-driven behaviour.** Most screens adapt to the platform the user came from: required
  profile attributes, authentication methods order, verification requirements, badges, footer…
  These come from columns of `oauth_clients` (`App\Client`), see [OAuth server](oauth-server.md#client-settings).
- **Laravel 5.4 compatibility layer.** The app was ported from Laravel 5.4 in 2026. Several behaviours
  were deliberately preserved (request `has()` semantics, date serialization in JSON, no password rehash
  on login…). They are listed in [development](development.md#laravel-54-behaviours-kept-on-purpose).
