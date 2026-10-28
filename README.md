# BUKTRAVELS

BUKTRAVELS is a tourism information and travel-planning system for Bukidnon Province, Philippines. It provides a public destination guide, optional traveler accounts, itinerary planning, and a protected content-management workspace.

The system preserves the forest-green and cream identity of the original prototype while replacing its hardcoded content with a maintainable Laravel, React, and PostgreSQL architecture.

## System architecture

BUKTRAVELS consists of three primary layers:

- **Frontend:** React and TypeScript single-page application for visitors, travelers, and staff.
- **Backend:** Laravel REST API responsible for authentication, validation, authorization, content workflows, favorites, itineraries, and audit history.
- **Database:** PostgreSQL storage for tourism content, users, favorites, itineraries, media metadata, and audit events.

The original static prototype remains preserved in `dist/index.html` as a migration checkpoint.

## Visitor experience

Visitors can:

- Browse published Bukidnon destinations.
- Search by destination, municipality, category, summary, or description.
- Filter listings by municipality and category.
- Open destination details, highlights, visitor notices, sources, and Google Maps searches.
- Save favorites locally without creating an account.
- Browse suggested trip collections.
- View verified destinations on an interactive map.
- Open destination-specific deep links.
- Use the application on desktop and mobile screen sizes.

Unverified destinations and photographs are visibly identified throughout the interface.

## Traveler accounts and planning

Traveler accounts are optional. Anonymous favorites continue to work in the browser without authentication.

After registration or login, travelers can:

- Merge browser favorites with their account.
- Synchronize favorites across authenticated sessions.
- Create private itineraries.
- Add and arrange destination stops.
- Store travel dates and private notes.
- Open a Google Maps route when every stop has verified coordinates.

Itineraries are private to their owner and protected by server-side authorization.

## Destination map and routing

The map uses a verification gate. A destination appears only when:

- The destination is marked as verified.
- Both latitude and longitude are present.
- The coordinates were approved through the reviewer workflow.

Itinerary routing follows the same rule. This prevents incomplete or guessed coordinates from being presented as reliable travel information.

## Content-management workspace

The protected staff workspace manages destinations, photographs, highlights, publication states, visitor notices, verification, and staff access.

The system defines three staff roles:

- **Content editor:** Creates destinations and edits draft content and unverified media.
- **Reviewer:** Verifies information, photographs, and coordinates and controls publication.
- **Administrator:** Has reviewer permissions and can also manage staff roles and destructive content operations.

The final administrator cannot be demoted, preventing the system from losing administrative access.

## Publication and verification workflow

Destination publication and factual verification are separate states.

A destination may move between:

- `draft`
- `published`
- `archived`

Content and photographs may be:

- `unverified`
- `verified`

Editors prepare drafts, while reviewers or administrators approve verification and publication. These permissions are enforced by the Laravel API rather than relying only on hidden frontend controls.

## Photograph management

Staff can upload, reorder, and remove destination photographs. Each photograph record supports:

- Alternative text
- Attribution
- Source URL
- License or permission information
- Primary-image ordering
- Verification status

Uploaded files are stored separately from their database metadata, allowing the storage provider to be changed without redesigning the destination records.

## Tourism content model

The principal system records are:

- Municipalities
- Categories
- Destinations
- Destination photographs
- Destination highlights
- Destination updates
- Users and staff roles
- Favorites
- Itineraries and itinerary stops
- Audit events

Destinations contain publication and verification states, optional coordinates, source information, access notes, and relationships to their municipality, category, photographs, highlights, and updates.

## Security and accountability

The backend protects application writes with:

- Laravel Sanctum authentication
- Role-based middleware
- Model authorization policies
- Server-side request validation
- Login rate limiting
- Token expiration
- Ownership checks for traveler itineraries
- Immutable audit events for content and staff changes

Public API responses expose published destinations only. Draft and archived content remain available solely to authorized staff.

## Content-integrity policy

The migrated destination records originate from the prototype and are not treated as independently verified tourism facts.

The system does not assume or invent:

- Entrance fees
- Opening hours
- Coordinates
- Access restrictions
- Safety guidance
- Destination-specific photographs

Facts, coordinates, sources, and photographs remain unverified until they are manually reviewed. Generic scenic images must not be represented as confirmed photographs of a named destination.

## Preserved prototype

The original standalone version is retained at `dist/index.html`. Its checksum is stored in `dist/CHECKPOINT.sha256`, providing a stable reference while the Laravel and React application evolves.
