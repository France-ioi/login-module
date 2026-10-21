# LTI and task tokens

The login module is an **LTI 1.0/1.1 tool provider**: a learning management system (Moodle…) can launch
France-ioi content, and the login module creates or finds the matching account.

## Components

| Component | Role |
|---|---|
| `app/LoginModule/LTI/Tool/` | vendored IMS "LTI Tool Provider" library v2.5.00 (LGPL, OAuth 1.0 HMAC-SHA1), patched to accept an explicit request origin and to read `content_id` |
| `App\LoginModule\LTI\LTIPDO` | separate PDO connection to the LTI database (`LTI_DB_*`, see [configuration](configuration.md#lti-tool-provider-database)) |
| `App\LoginModule\LTI\LTIHelper` | runs the launch, maps LTI users to accounts, sends outcomes |
| `App\LoginModule\LTI\LTITokenGenerator` | builds the task token returned to the platform |
| `App\LoginModule\Shared\Jws`, `TokenGenerator`, `TokenParser` | RS512 JWS tokens |

### LTI database

The library stores consumers and launches in its own database (default name `lti-to-bebras`), with the
tables `lti_consumer` (plus a `login_prefix` column added for the login module), `lti_context`,
`lti_user`, `lti_nonce` and `lti_share_key`. They are **not** created by the Laravel migrations. A consumer
(LMS) is a row of `lti_consumer` with its key and secret.

In the main database: `lti_connections` (LTI user ↔ account, per consumer and resource link, with the last
`content_id`), `lti_configs` (login prefix per consumer key, admin "LTI configs") and `lti_results` (grades
waiting to be sent).

## Launch routes

| Route | Controller | Behaviour |
|---|---|---|
| `POST /lti/launch?content_id=…&redirect_url=…` | `Auth\LTILaunchController` | verifies the signed launch, logs the user in, stores `content_id`, redirects (303) to `redirect_url?user_id=…&content_id=…` |
| `POST /lti/entry[?redirectUrl=…]` | `Auth\LTIEntryController` | verifies the launch, logs the user in, redirects to `redirectUrl?loginToken=TOKEN` (or `/`) |
| `POST /platform_api/lti/entry` | `PlatformAPI\LtiInterfaceController` | same verification for a launch the platform received itself (`post_params`, `http_method`, `http_url`), returns the token |
| `GET /lti?sToken=…&sPlatform=…&redirectUrl=…` | `Auth\LTIController` | the platform sends a JWS signed with its key (client found by **name** = `sPlatform`, verified with `oauth_clients.public_key`) carrying `loginData` (`lti_consumer_key`, `lti_user_id`, `firstName`, `lastName`, `email`); the account is found or created (provider `lti`), then redirect with `loginToken` |

`/lti/entry` and `/lti/launch` are excluded from CSRF verification.

Account creation: login generated from the names with the consumer's prefix (`lti_configs`, else
`lti.default_login_prefix` = `lti_`; `GET /lti` uses `lti.prefixes_by_consumer`), primary email with
`login_enabled` false.

Known issue: the first launch of a new resource link fails (`lti_resource_link_id` is only set when the
link is loaded from the LTI database); the next launches work.

## Task tokens

Tokens given to platforms (`loginToken`) are **RS512 JWS** signed with `storage/lm-private.key`:

- header `{"alg":"RS512","typ":"JWS"}`;
- payload: `idUser`, `sLogin`, `lti_connection_id` (LTI), `date` (`d-m-Y`), `platform`, `iat`;
- verified by the platforms with `storage/lm-public.key`.

`TokenParser::decodeJWS()` (for tokens received from platforms) accepts only RS512, checks the signature
and accepts `date` equal to today or tomorrow.

`php artisan lm:keys` generates a 2048-bit RSA key pair (RS512 does not fit in keys shorter than
~1024 bits; the former 512-bit generator could not sign). On Windows set `OPENSSL_CONF`.

`App\LoginModule\Shared\Jws` replaced the abandoned namshi/jose library in 2026 and produces
byte-identical tokens (`tests/Unit/TokensTest.php`).

## Sending grades back

- `platform_api/lti/send_result` (`lti_connection_id`, `score`) and `platform_api/lti_result/send`
  (`user_id`, `content_id`, `score`) call the LMS outcome service (`LTIHelper::sendConnectionResult`;
  scores above 1 are divided by 100).
- Failed sends from `lti/send_result` are queued in `lti_results`.
- `php artisan lti:send` retries them: rows older than `lti.send_result.period` (10 minutes), deleted on
  success or after `lti.send_result.attempts_max` (5) attempts. It is **not** in the Laravel scheduler:
  run it from cron.
