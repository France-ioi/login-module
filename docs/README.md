# Login module: developer documentation

The login module is the identity provider and OAuth 2 server of the France-ioi platforms
(France-ioi, Algorea, Bebras…). It runs on Laravel 12 / PHP 8.5.

| Document | Content |
|---|---|
| [Architecture](architecture.md) | stack, directory layout, request lifecycle, middleware, providers, design notes |
| [Development](development.md) | local setup, front-end build, tests, conventions, behaviours kept from Laravel 5.4 |
| [Authentication and accounts](authentication.md) | sign-in page, password / code / badge / Google / Facebook / PMS sign-in, registration, password reset and change, reauthentication, logout, merged accounts, language |
| [Profile, verification and personal data](profile-and-verification.md) | attribute schema, profile page, emails and notification mails, verification methods, official domains, GDPR pages |
| [OAuth server](oauth-server.md) | integrating a platform, tokens, redirect URI rules, checks before authorization, client settings, platform context |
| [User API, platform API and badges](platform-api.md) | `user_api/account`, encrypted server-to-server API, badge APIs, participation codes |
| [LTI and task tokens](lti.md) | LTI tool provider, task tokens (RS512 JWS), sending grades back |
| [Administration](admin.md) | admin interface and permissions, console commands, importers |
| [Configuration](configuration.md) | every environment variable and application setting |
| [Data model](data-model.md) | tables, models, relations, seeders |
| [Deployment](deployment.md) | installation, updates, upgrade from the Laravel 5.4 version, Docker |

## Glossary

| Term | Meaning |
|---|---|
| Platform, client | an application using the login module (OAuth client, `oauth_clients` row) |
| Platform context | the platform the current user came from, kept in session |
| Attribute | a profile field (`SchemaConfig`) |
| Verification | proof that an attribute is true (email code, school domain, ID upload…) |
| Badge | an external registration (contest participation…) identified by a code and managed by a badge API |
| Participation code | code given to pupils by a platform, signs them in and links them to a group |
| Origin instance | another login-module installation whose accounts were merged in |
| Merge group | accounts that are probably the same person (from merged instances) |
| Master password | support password (`AUTH_MASTER_HASH`) |
| Task token / login token | RS512 JWS given to a platform after an LTI or token login |
