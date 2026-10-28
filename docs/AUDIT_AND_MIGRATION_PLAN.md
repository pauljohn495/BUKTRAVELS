# BUKTRAVELS audit and migration record

## Preserved prototype audit

The migration checkpoint is `dist/index.html`: one dependency-free HTML file containing markup, CSS, hardcoded data, and browser JavaScript. It remains unchanged and is protected by `dist/CHECKPOINT.sha256`.

Prototype visitor behavior preserved in React:

- 11 destination cards with generic illustrative scenery.
- Combined search over destination name, municipality, category, and teaser.
- Municipality select, category chips, filter reset, and saved-only view.
- Detail dialogs with overview, highlights, practical notes, source, and Google Maps search.
- Browser-local favorites and `#spot=<slug>` deep links.
- Three suggested collections, responsive navigation/layouts, keyboard focus, and Escape-to-close dialogs.

Prototype risks addressed by the migration:

- Hardcoded content now has API/database ownership and a staff workflow.
- Every legacy fact and image retains an explicit unverified state.
- Anonymous favorites remain local, while optional accounts enable cloud sync.
- Writes are protected by server-side authentication, policies, roles, and validation.
- Original mojibake in the static checkpoint was not silently rewritten.

## Repository layout

```text
BUKTRAVELS/
|-- backend/                    Laravel API
|   |-- app/Http/               controllers, requests, middleware, resources
|   |-- app/Models/             content, account, audit, and itinerary models
|   |-- app/Policies/           destination and itinerary authorization
|   |-- database/migrations/    PostgreSQL-compatible schema
|   |-- database/seeders/       unverified legacy data and optional admin
|   `-- tests/Feature/          API/auth/workflow/ownership tests
|-- frontend/                   React + TypeScript + Vite SPA
|   |-- src/api/                typed HTTP client
|   |-- src/components/         visitor UI and map
|   |-- src/context/            traveler authentication
|   |-- src/hooks/              local/cloud favorites
|   |-- src/pages/              visitor, account, planner, and staff screens
|   `-- tests/e2e/              Playwright desktop/mobile accessibility flows
|-- dist/index.html             untouched static checkpoint
|-- scripts/                    PostgreSQL backup and restore
|-- .github/workflows/          PostgreSQL CI and release images
|-- compose.yaml                local PostgreSQL
`-- compose.production.example.yaml
```

## Completed phases

### Phase 0 - Preservation and audit

Complete. The original is retained; features, data risks, target layout, and setup are documented.

### Phase 1 - Foundational vertical slice

Complete. Laravel/Sanctum, PostgreSQL-ready migrations, 11 idempotent unverified seeds, public filters/pagination, the React port, protected admin writes, and unit/feature tests are implemented.

### Phase 2 - Content operations

Complete for the requested scope. The staff workspace supports content editor/reviewer/admin separation, draft and publication gates, manual verification, uploaded-photo metadata and ordering, visitor updates, audit events, and administrator-managed roles.

### Phase 3 - Verified geographic experience

Complete in code and intentionally data-gated. React Leaflet renders only reviewer-verified destinations with complete coordinates. The tile endpoint is environment configurable. External navigation and itinerary directions appear only from verified data.

### Phase 4 - Traveler accounts and planning

Complete for first-party planning. Registration/login, cloud favorite merge, private account-owned itineraries, dates, notes, stop ordering, and verified-coordinate directions are implemented. Anonymous favorites still require no login.

### Phase 5 - Operational hardening

Complete at repository level. Permission tests, audit history, desktop/mobile Playwright flows, Axe checks, PostgreSQL CI, container builds/releases, and backup/restore scripts are present.

## Manual launch gate

The owner will perform destination fact and photograph verification. Production launch also requires owner-supplied secrets and infrastructure, a suitable map tile contract, monitoring/backup destinations, a privacy policy, and an actual restore drill. These are environment or editorial decisions rather than missing application code.
