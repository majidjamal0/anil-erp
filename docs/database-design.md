# Database design

## Platform conventions

PostgreSQL 17 is the production system of record. All business entities use application-generated UUID primary keys so records can be safely created across branches and imported without sequence collisions. Framework infrastructure tables such as queued jobs may retain Laravel's internal numeric identifiers.

All timestamps are stored in UTC using timezone-aware columns. Money and quantities must use explicit fixed-precision `decimal` columns; IEEE floating-point values are prohibited for financial calculations. JSON is reserved for schemaless setting values and audit snapshots, not normalized business relationships. Laravel's portable `json` schema type maps to PostgreSQL `jsonb` while remaining compatible with SQLite tests.

## Sprint 1 entities

- `users`: authenticated staff identity, Persian locale preference, active status, and credentials.
- `roles`, `permissions`: UUID-backed Spatie Permission records. All three pivot tables also use UUID foreign/morph keys.
- `branches`: a physical or organizational sales branch with a unique code.
- `warehouses`: inventory location owned by a branch. A branch with warehouses cannot be physically deleted.
- `settings`: namespaced typed JSON values, uniquely addressed by `(group, key)`.
- `audit_logs`: append-only actor, event, polymorphic subject, before/after values, and request metadata.
- `sessions`, `cache`, `jobs`: standard Laravel operational tables for supported fallback drivers; local Compose uses Redis for all three runtime concerns except jobs, which use Redis queues.

## Integrity and lifecycle

Branch and warehouse records are soft-deleted. Warehouse-to-branch deletion uses `RESTRICT` to prevent orphaned inventory locations. Audit actors use `SET NULL` so staff deletion does not destroy history. Role/permission assignments cascade when their owning authorization record is deleted.

Future migrations must be reversible, add indexes based on measured access patterns, and be exercised against both PostgreSQL and the SQLite test profile. Production migrations require a backup and rollback plan.

## Sprint 2 identity and access data

`users` uses UUID primary keys and stores unique email, hashed password, locale, activation status, verification and session metadata. Sanctum's `personal_access_tokens` exists for trusted API clients, while the SPA uses server sessions. Password reset tokens are short-lived and keyed by email.

RBAC uses Spatie Permission tables: `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, and `role_has_permissions`. Role and permission identifiers are UUIDs. `audit_logs` records actor, event, polymorphic subject, before/after JSON, IP address, user agent, and creation time. Audit records intentionally survive actor deletion with a nullable user foreign key.

## Sprint 3 organizational schema

The organization schema adds `companies`, `branches`, `warehouse_types`, `warehouses`, `sales_channels`, `company_user`, `branch_user`, and `user_warehouse`. New organizational entities use UUID primary keys, UTC timestamp columns, database foreign keys, indexes, company-scoped unique codes, and soft deletes where appropriate.
