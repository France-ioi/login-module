# OAuth 2 server (for platforms)

The login module is the OAuth 2 authorization server of the platforms. It uses Laravel Passport 12
(league/oauth2-server 8.5) with a custom authorization code grant.

## Integrating a platform

1. An admin creates the client in `/admin/clients` (name, secret, redirect URIs, required attributes…).
2. The platform redirects the user to

   ```
   GET /oauth/authorize?client_id=ID&redirect_uri=URI&response_type=code&scope=account&state=STATE[&locale=fr]
   ```

3. The login module signs the user in, makes sure the profile meets the platform's requirements
   (see [below](#what-happens-before-the-code-is-issued)), shows the consent screen (unless skipped), then
   redirects to `redirect_uri?code=CODE&state=STATE`.
4. The platform exchanges the code (server side):

   ```
   POST /oauth/token
   grant_type=authorization_code&client_id=ID&client_secret=SECRET&redirect_uri=URI&code=CODE
   → {"token_type":"Bearer","expires_in":31536000,"access_token":"…","refresh_token":"…"}
   ```

5. The platform reads the account: `GET /user_api/account` with `Authorization: Bearer ACCESS_TOKEN`
   (see [user API](platform-api.md#user-api)).

Refresh: `POST /oauth/token` with `grant_type=refresh_token`. PKCE (`code_challenge`, S256 or plain) is
supported. `prompt=none|login|consent` are supported by Passport's authorization controller.

Errors follow RFC 6749: `{"error":"invalid_request","error_description":"…","hint":"…"}` (before 2026 the
key was `message`).

## Tokens and keys

| Item | Value |
|---|---|
| Authorization code lifetime | 10 minutes (`App\Providers\PassportServiceProvider`) |
| Access / refresh token lifetime | 1 year (Passport defaults, not configured) |
| Scope | `account` |
| Signing keys | `storage/oauth-private.key`, `storage/oauth-public.key` (`php artisan passport:keys`) |

Each access token creation increments `access_token_counters` for (user, client)
(`App\Listeners\AccessTokenCreated`).

## Redirect URIs

`oauth_clients.redirect` holds one or several URIs separated by commas. `App\OAuth\AuthCodeGrant` matches
them as follows:

- an entry starting with `^` is a **regular expression**, evaluated as `preg_match('/' . entry . '/', uri)`:
  - `/` must be escaped (`\/`), an invalid expression never matches;
  - it can't contain a comma (entries are split on commas);
  - write complete patterns, anchored with `$` and with escaped dots, e.g. `^https:\/\/[a-z]+\.algorea\.org\/login\/callback$`;
- any other entry must match exactly.

When exchanging the code, the `redirect_uri` may differ from the one used for the authorization request,
as long as both match the client (league/oauth2-server requires them to be identical).

## What happens before the code is issued

The app overrides Passport's `/oauth/authorize` routes (`routes/passport.php`) with the middleware
`auth.auto_login_token, web, auth, authorization_available` (+ `auto_authorization` on GET):

1. `auth.auto_login_token`: optional one-shot login with `login` + `auto_login_token` parameters;
2. `auth`: guests are sent to `/auth` (the authorization URL is kept as the platform's continue URL);
3. `authorization_available` (`AuthorizationAvailable`):
   - profile incomplete for this platform, merge revalidation pending, `attributes_filter` not met or
     `login_change_required` → `/profile`;
   - verifiable attributes not verified → `/verification`;
   - badge required and missing → `/badge`;
4. `auto_authorization` (`AutoAuthorization`): if the client has `autoapprove_authorization`, or the user
   was created by this client (`creator_client_id`), the request continues to `/oauth/auto_authorize`,
   which approves without the consent screen;
5. Passport's controller: approves directly when the user already has a valid token for this client with
   the same scopes, otherwise shows the consent screen
   (`resources/views/vendor/passport/authorize.blade.php`, with `auth_token`).

## Client settings

`oauth_clients` columns added by the app (edited in `/admin/clients`, model `App\Client`):

| Setting | Effect |
|---|---|
| `user_attributes` | profile attributes the user must fill before authorization; `login` and `primary_email` also drive the registration form |
| `recommended_attributes` | attributes suggested right after login |
| `hidden_attributes` | not shown in the profile form while empty |
| `verifiable_attributes` | attributes that must be verified, with the methods attached to the client (pivot `oauth_client_verification_method`, `expiration` in days) |
| `attributes_filter` | `{attribute: required value}`; users not matching can't authorize (e.g. only teachers) |
| `auth_order` | sign-in methods and order on `/auth` |
| `autoapprove_authorization` | skip the consent screen |
| `badge_api_id`, `badge_required` | badge API of the platform; whether a badge (or "I don't have one") is required |
| `api_url` | platform endpoint for participation codes (`module=group_code&action=verify`) |
| `public_key` | PEM key verifying the `sToken` of `GET /lti` |
| `email` | platform contact shown on the personal data page |
| `footer` | HTML footer of the pages when the user comes from this platform |
| `badge_autologin` | unused |

## Platform context

`App\LoginModule\Platform\PlatformContext` (singleton, state in session key `PLATFORM_CONTEXT`) remembers
which platform the user is dealing with. The `PlatformRequest` middleware updates it on every web request:

- a request with both `client_id` and `redirect_uri` sets the current client;
- on `/oauth/authorize`, the continue URL is the authorization URL itself (`cancelable` false) and `locale`
  sets the language;
- on other URLs, `redirect_uri` becomes the continue URL and the "close" link (`cancelable` true).

`continueUrl()` is where the user goes after login, registration, profile save…; `cancelUrl()` is the close
link of the popup layout. The context survives login/logout session regeneration.
`PlatformHelper` (facade) exposes it to views: platform name, footer, tabs visibility.
