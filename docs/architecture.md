# Architecture

Anil ERP is a modular monolith with an API boundary. Laravel owns business rules and persistence; Vue is an independently deployable SPA. PostgreSQL is the system of record, Redis backs ephemeral cache, sessions, and queues, and Nginx is the sole public ingress.

## Domain boundaries

`Users`, `Authorization`, `Branches`, `Warehouses`, `Products`, `Inventory`, `Customers`, `Sales`, `Finance`, `HR`, `Production`, and `Reports` are explicit modules. Sprint 1 supplies identity, organization, settings, and audit persistence only. Later modules should expose application actions rather than reaching into another module's tables.

## Data and security

Business records use UUID keys, currency uses fixed precision decimal columns (never floats), and timestamps are UTC `timestamptz`. The presentation layer localizes dates and numbers. Sanctum authenticates first-party SPA/API requests; Spatie Permission supplies RBAC. Authorization remains enforced in policies, not only navigation. Secrets enter through runtime environment variables.

Audit records are append-only. Personally identifiable data must be minimized, exports authorized, and sensitive values excluded from logs. Database backups, TLS termination, queue workers, observability, and secret rotation are deployment responsibilities.

## Localization

Persian (`fa`) is the default locale, English is the fallback, and the root document uses `dir="rtl"`. UI components must support both logical CSS directions and localized content.

## Health

Laravel exposes `/up` for process liveness and `/api/health` for API/database readiness. Orchestrators should use the appropriate endpoint and keep readiness checks unauthenticated but information-poor.
