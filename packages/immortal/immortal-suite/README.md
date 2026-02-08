# Immortal Suite (SeAT v5)

Immortal Suite is a single-package recruiting and risk plugin for SeAT v5. It provides an end-to-end Discord-initiated applicant flow, dossier views, a rule-based risk engine, and bidirectional Discord integration.

## Installation

1. Place the package under `packages/immortal/immortal-suite`.
2. Add the package path to your root composer.json repositories (path) if needed.
3. Run composer install/update to load the package.
4. Enable the provider in your SeAT config or rely on package discovery.
5. Publish config and migrations:

```bash
php artisan vendor:publish --tag=immortal-suite-config
php artisan migrate
```

6. Configure settings in the Immortal Suite Settings page (Discord secrets, feature flags, risk weights).

## Docker (Windows 10 PowerShell) Install

1. Edit your SeAT Docker `.env` (usually in `/opt/seat-docker`) and set:

```
SEAT_PLUGINS=immortal/immortal-suite
```

2. Rebuild containers:

```
docker-compose up -d
```

3. Run maintenance + migrations inside the container:

```
docker exec -it <seat_container_name> php artisan down
docker exec -it <seat_container_name> composer require immortal/immortal-suite
docker exec -it <seat_container_name> php artisan vendor:publish --force --all
docker exec -it <seat_container_name> php artisan migrate
docker exec -it <seat_container_name> php artisan route:cache
docker exec -it <seat_container_name> php artisan config:cache
docker exec -it <seat_container_name> php artisan seat:cache:clear
docker exec -it <seat_container_name> php artisan db:seed --class=Seat\\\\Services\\\\Database\\\\Seeders\\\\PluginDatabaseSeeder
docker exec -it <seat_container_name> php artisan up
```

> Note: The plugin settings are stored in the database (no extra .env keys required).

## Docker (Local/Private Package) Install

If `SEAT_PLUGINS=immortal/immortal-suite` fails with “Could not find a matching version”, the package is not published to Packagist.
In that case, use a **local path repository** inside the container:

1. Mount this plugin into every SeAT service container (example for `front`, `worker`, and `scheduler`):

```
services:
  front:
    volumes:
      - "./packages:/var/www/seat/packages"
      - "./packages/immortal/immortal-suite:/var/www/seat/packages/immortal/immortal-suite"
  worker:
    volumes:
      - "./packages:/var/www/seat/packages"
      - "./packages/immortal/immortal-suite:/var/www/seat/packages/immortal/immortal-suite"
  scheduler:
    volumes:
      - "./packages:/var/www/seat/packages"
      - "./packages/immortal/immortal-suite:/var/www/seat/packages/immortal/immortal-suite"
```

> Note: On Windows, the left side of the volume mount should be an absolute path (for example,
> `C:/seat-docker/Plugins/SeAT-Recruit-Master/packages/immortal/immortal-suite`).

2. Add a path repository to the SeAT `composer.json` (inside the container or on the host volume):

```json
"repositories": [
  { "type": "path", "url": "packages/immortal/immortal-suite", "options": { "symlink": true } }
]
```

3. Require the package and run migrations (inside the container):

```
composer require immortal/immortal-suite
php artisan vendor:publish --tag=immortal-suite-config
php artisan migrate
```

## Dummies Guide (Quick Start)

1. **Install & migrate**
   - Run the migrations and ensure the provider is loaded.
2. **Open Immortal Suite → Settings**
   - Set your Discord shared secret.
   - Add the API token for bot-to-SeAT calls.
   - Define application questions in JSON.
3. **Create a Discord apply link**
   - Use `/immortal/apply/start` with HMAC signature:
     `hash_hmac('sha256', "{ticket}|{discord}|{guild}", shared_secret)`
   - Use `/api/v1/immortal/intel/record` with `X-Immortal-Admin-Token` to push intel events.
4. **Intel auto-derivation**
   - Ensure SeAT contact tables are present (configurable via Settings → Intel Configuration).
5. **Applicant completes the checklist**
   - They confirm alts, answer required questions, and click **Done**.
6. **Recruiters review**
   - Applications appear under **Applications**, and Dossiers show risk findings.

## Feature Overview

- **Applications**: Ticket → Apply → Done flow with checklist.
- **Dossier**: Applicant intel view with explainable risk signals.
- **Risk Engine**: Rule-based scoring with configurable weights.
- **Discord Integration**: Webhooks and signed API endpoints (application completed/status changed/intel alerts).
- **Audit Log**: Track status changes and internal notes.
- **Intel Feed**: Recent intel events exposed via API.

## Webhook Secrets

- Set the shared secret in Settings.
- Signed requests use HMAC SHA-256.

## Application Flow

Discord bot can link applicants to:

```
/immortal/apply/start?ticket={ticketId}&discord={discordUserId}&guild={guildId}&sig={hmac}
```

The signature is calculated as:

```
hash_hmac('sha256', "{ticket}|{discord}|{guild}", shared_secret)
```

## API

All API routes are under `/api/v1/immortal`. Provide `X-Immortal-Token` header or `?token=` param matching Settings.

Requests must be signed with headers:

```
X-Immortal-Timestamp: <unix timestamp>
X-Immortal-Signature: hash_hmac('sha256', "{timestamp}|{method}|{path}|{body}", shared_secret)
```

Write endpoints (claim/status/note/intel record) require `X-Immortal-Admin-Token`.

## Intel Configuration

The Intel module checks configured contact table names to auto-classify standings and detect home-space sources.
You can adjust the table list in Settings → Intel Configuration.

## Permissions

- immortal.view_applications
- immortal.manage_applications
- immortal.view_dossier
- immortal.manage_risk
- immortal.manage_settings
- immortal.view_audit
- immortal.view_intel
- immortal.manage_discord
