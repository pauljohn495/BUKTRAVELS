# BUKTRAVELS developer handoff

## Current state

The repository now contains the preserved static prototype plus a complete Laravel/React/PostgreSQL application foundation. Start with `README.md` for exact commands and `docs/AUDIT_AND_MIGRATION_PLAN.md` for the completed migration record.

The static checkpoint is `dist/index.html`; do not modify or remove it. `dist/CHECKPOINT.sha256` records its hash.

## Product behavior

Visitor functionality:

- Browse 11 migrated destination records.
- Search by destination, municipality, category, summary, or description.
- Filter by municipality/category and show local favorites only.
- Open accessible destination dialogs, source links, Google Maps searches, and deep links.
- Use a verified-only map; unverified coordinates never render.
- Keep favorites before login and optionally merge/sync them after registration.
- Build private account-owned itineraries with dates, notes, ordered stops, and verified-coordinate route links.
- Browse suggested trip collections, which are clearly distinguished from timed itineraries.

Staff functionality:

- Content editors manage drafts and unverified media.
- Reviewers control fact/photo verification, coordinates, and publication.
- Administrators also manage staff roles and destructive content operations.
- Photo uploads store alt text, attribution, source URL, license, verification state, and ordering.
- Content and staff changes create immutable audit events.
- The last administrator cannot be demoted.

## Truth-in-content rule

The project owner will manually verify every destination fact and photograph. Until then, retain `unverified`, null coordinates, and null operational facts. Never infer or invent fees, hours, access conditions, coordinates, licenses, or destination-specific images.

The verified-only map and route gates are deliberate. Do not weaken them to make empty states disappear.

## Architecture notes

- `backend/`: Laravel 13 API, Sanctum bearer tokens, policies, PostgreSQL migrations, seeders, feature tests.
- `frontend/`: React 19, TypeScript, Vite, React Router, React Leaflet, Vitest, Playwright, Axe.
- `compose.yaml`: local PostgreSQL 18.
- `compose.production.example.yaml`: production-shaped web/API/database deployment.
- `.github/workflows/ci.yml`: PostgreSQL tests, frontend checks/E2E, and container builds.
- `.github/workflows/release-images.yml`: tagged GHCR image publication.
- `scripts/`: explicit PostgreSQL backup and restore commands.

Traveler and admin authentication intentionally use separate token keys. Anonymous favorites always remain in `localStorage`; account login performs a union merge rather than replacing local choices.

## Verification baseline

As of 2026-10-28:

- PHP: 13 tests / 61 assertions pass using SQLite; Pint passes.
- Frontend: 4 unit/component tests pass; lint and production build pass.
- Playwright covers desktop and Pixel 7 projects, critical visitor behavior, account itinerary loading, and Axe accessibility.
- Production Compose configuration validates.
- Docker/PostgreSQL runtime jobs are configured for CI but were not executed locally because the Docker engine was unavailable.

When changing features, rerun all commands under `README.md` and update its counts only from observed output.

## Owner/environment launch work

Application code for the requested scope is implemented. Remaining launch work requires owner judgment or credentials: manual editorial/photo verification, deployment secrets and TLS, privacy/contact copy, production map-tile terms, monitoring/backup destinations, and a real database restore drill.
