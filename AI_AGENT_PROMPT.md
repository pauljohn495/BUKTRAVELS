# Paste this prompt into your coding AI agent

You are continuing the existing **BUKTRAVELS** tourism web app for Bukidnon Province, Philippines. I have attached a ZIP containing the complete existing working project.

Read `README.md`, `AGENT_HANDOFF.md`, and the source at `dist/index.html` before making changes. Inspect the current implementation and preserve the look and all working features.

**My goal:** Evolve this prototype into a complete, maintainable tourism information and travel planning web app using **Laravel (backend API), React + TypeScript (frontend), and PostgreSQL (database)**.

**First task:**

1. Audit the existing prototype and list its current features.
2. Propose a clear folder structure and phased migration plan.
3. Create the Laravel API and PostgreSQL database schema for destinations, municipalities, categories, photos, highlights and admin users.
4. Build the React frontend by porting the existing interface and feature behavior.
5. Seed the database with the 11 existing places, but clearly mark data and scenic images as needing verification.
6. Add a protected admin panel for managing attractions and publishing updates.
7. Implement tests and provide exact setup commands in a README.

**Critical requirements:**

- Keep BUKTRAVELS' forest-green and cream design identity and responsive behavior.
- Do not invent tourist attraction facts, real photos, entrance fees, or coordinates. Use null/"unverified" states until real sources are checked.
- Make admin access secure and ensure content writes are protected server-side.
- Keep favorites usable before login, and only add cloud sync when user accounts are implemented.
- Do not break the existing search, category/location filters, detail dialogs, Google Maps links, and trip ideas.
- Preserve the current static version or migration checkpoint until the new version passes tests.
- Tell me what you changed, how to run it, which tests pass, and which features remain pending.

Begin by explaining the existing code and giving me an incremental implementation plan. Then proceed with Phase 1, creating and testing the foundational Laravel, React and PostgreSQL structure.
