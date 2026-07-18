# Sprint 3: Organization, Warehouses, Channels

Implemented foundation:

- Companies with locale, currency, timezone, status, settings, and soft deletes.
- Branches and organizational units with hierarchy, managers, operational/external flags, and company-scoped codes.
- Configurable warehouse types with capability flags.
- Warehouses scoped to companies and optionally branches.
- Sales channels scoped to companies and optionally branches.
- Organization access assignment APIs and context endpoint.
- Persian RTL organization administration pages.
- Audit events for organizational CRUD and access assignment.

## Migration and rollback

Run `php artisan migrate --seed` from `backend/`. Roll back with `php artisan migrate:rollback`; tables are ordered so sales channels and access pivots drop before dependent organization records.
