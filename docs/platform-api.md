# User API, platform API and badges

## User API

`GET /user_api/account`, header `Authorization: Bearer ACCESS_TOKEN`, scope `account`
(`UserAPI\AccountController@show`, guard `api`).

Response: the user as JSON (`User::toArray()`), plus a few computed fields.

| Field(s) | Content |
|---|---|
| user columns | every `users` column except `password`, `regular_password`, `remember_token`, `updated_at`: `id`, `login`, names, `language`, `country_code`, `role`, `graduation_*`, `birthday`, address and phones, `ip`, `last_login`, `creator_client_id`, `merge_group_id`… |
| `picture` | URL of the profile picture or of the default avatar |
| `primary_email`, `secondary_email`, `primary_email_verified`, `secondary_email_verified`, `has_picture` | appended attributes |
| `badges` | `[{id, url, code, data, do_not_possess}]`, badges the user possesses (`url` is the badge API URL) |
| `client_id` | client of the token |
| `platform_group_code` | only once: the `platform_groups` row of (user, client) is **deleted** after it is returned |
| `verification` | `{attribute: state}` for the client's verifiable attributes and the attributes covered by the user's verifications: `NOT_VERIFIED`, `IN_PROCESS`, `ACTION_REQUIRED`, `REFRESH_REQUIRED`, `OBSOLETE`, `VERIFIED`, `REJECTED` |

Dates use `Y-m-d H:i:s`.

## Platform API

Server-to-server calls, prefix `/platform_api/`, all `POST`, middleware `platform_api`.

### Encryption

Request (form fields):

- `client_id`: the client id, in clear;
- `data`: `base64(AES-128-ECB(json, key = client secret))`, i.e. PHP
  `openssl_encrypt(json_encode($params), 'AES-128-ECB', $secret)` (OpenSSL uses the first 16 bytes of the
  secret, PKCS#7 padding).

`App\Http\Middleware\PlatformAPI` decrypts `data`, replaces the request input with it and adds `client`
(model) and `client_id`. The query string is ignored. Missing / unknown `client_id` → `400`.

Response body: `openssl_encrypt(json_encode($result), 'AES-128-ECB', $secret)` (base64), to decrypt with
the same key.

### Endpoints

| Endpoint | Parameters | Result |
|---|---|---|
| `accounts_manager/create` | `prefix` (1–29), `amount` (≥1), `postfix_length` (3–29), `password_length` (6–50); optional `login_fixed`, `language`, `auto_login`, `participation_code` | `{success, data: [{id, login, password, auto_login_token?, participation_code?}]}` or `{success:false, error:"Wrong params" \| "Login generation error"}` |
| `accounts_manager/delete` | `prefix` | deletes the accounts created by this client whose login starts with `prefix` (bulk delete, no model events) |
| `accounts_manager/unlink_client` | `user_id` | revokes the client's tokens for the user and logs a `user_deletions` row |
| `accounts_manager/participation_codes` | `user_ids[]` | `{success, data: {user_id: code}}` |
| `badges_manager/reset_do_not_possess` | `user_id`, `code` | deletes the user's "I don't have a badge" row for the client's badge API; bare `true`/`false` |
| `lti/entry` | `post_params`, `http_method`, `http_url` | verifies an LTI launch received by the platform, returns an LTI task token (see [LTI](lti.md)) |
| `lti/send_result` | `lti_connection_id`, `score` | sends an LTI outcome; queued in `lti_results` on failure |
| `lti_result/send` | `user_id`, `content_id`, `score` | sends the outcome of the user's LTI connection for that content |

Created accounts (`App\LoginModule\AccountsManager`): login `prefix` + random postfix, random password,
`creator_client_id` = the client (these users skip the consent screen for it and don't need an email),
optional auto-login token and participation code (a login-enabled badge).

### Example (PHP)

```php
$data = openssl_encrypt(json_encode([
    'prefix' => 'class42_', 'amount' => 30, 'postfix_length' => 5, 'password_length' => 8,
]), 'AES-128-ECB', $clientSecret);
$response = http_post('https://login.example.org/platform_api/accounts_manager/create',
    ['client_id' => $clientId, 'data' => $data]);
$result = json_decode(openssl_decrypt($response, 'AES-128-ECB', $clientSecret), true);
```

## Badges

A **badge** links an account to an external registration (e.g. a contest participation) identified by a
code and managed by a **badge API** (`badge_apis`: `name`, `url`, `auth_enabled`).

### Badge API protocol (`App\LoginModule\Platform\BadgeRequest`)

`POST {badge_api.url}` (form-encoded, JSON response, http/https only):

| Action | Parameters | Expected response |
|---|---|---|
| `verifyCode` | `code` | user data: `franceioiID`, `sLogin`, `sEmail`, `sFirstName`, `sLastName`, `sStudentId`, `sSex`, `grade`, `data`; or `{error}` |
| `updateInfos` | `code`, `idUser` | `{success: true}`: tells the API which account owns the code |
| `removeByCode` | `code` | `{success: true}`: called when a user is deleted (deletion fails otherwise) |

`grade` is converted to the login module's `graduation_grade`.

### Flows

| Route | Controller | Purpose |
|---|---|---|
| `GET /badge`, `POST /badge/attach`, `POST /badge/do_not_have`, `GET|POST /badge/confirm_difference` | `BadgeController` | badge required by the current platform (`badge_required`): attach a code, or declare not having one; when names differ from the badge, the user confirms which to keep (`override_profile`, `comments`) |
| `GET /badges`, `POST /badges/add` | `BadgesController` | add badges of other APIs |
| `POST /login_with_code` | `Auth\LoginWithCodeController` | sign in with a badge code (see [authentication](authentication.md#codes-and-badges)) |
| `POST /auth_methods/badge_login_ability/{id}/{enabled}` | `AuthMethodsController` | allow signing in with a badge |

`Platform\Badge::valid()` (checked before authorization): true when the platform has no badge API or does
not require a badge, or when the user has a badge row for that API (a "don't have one" row counts).

### Participation codes

Platforms can give pupils **participation codes** (10 characters). `POST /login_with_code` with an
unknown code asks the platform (`POST {client.api_url}`, `module=group_code&action=verify&code=…`,
`{success:true}` expected); the login module then creates an account with a login-enabled badge holding a
new participation code and a `platform_groups` row; the user sees the code on `/participation_code` and the
platform receives the group code once in the user API (`platform_group_code`).
