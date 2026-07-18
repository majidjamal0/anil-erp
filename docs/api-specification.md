# API specification

## Conventions

The API is rooted at `/api`, returns JSON, and is served through Nginx. First-party browser authentication will use Sanctum's stateful cookie flow (`/sanctum/csrf-cookie`) with CSRF protection. Protected endpoints will require `auth:sanctum`; authorization policies provide resource-level checks.

Successful resources will use Laravel API Resources. Validation errors use Laravel's standard HTTP `422` JSON error envelope. Authentication and authorization failures use `401` and `403`; missing resources use `404`; conflicts use `409` when applicable. Dates are ISO 8601 UTC strings. UUIDs are serialized as strings and monetary values will be serialized as decimal strings.

## Health endpoint

### `GET /api/health`

Unauthenticated readiness endpoint. It verifies the Laravel request lifecycle, PostgreSQL connection, and Redis connection. It intentionally exposes no credentials or infrastructure addresses.

Response `200`:

```json
{
  "status": "ok",
  "service": "anil-erp-api",
  "timestamp": "2026-07-17T12:00:00+00:00"
}
```

A dependency failure produces HTTP `500` through Laravel's exception handling and causes the Nginx readiness check to fail. Laravel also exposes `GET /up` as a process-level liveness route.

## Planned endpoints

Identity, branches, warehouses, products, inventory, customers, sales, finance, HR, production, and reporting endpoints are intentionally deferred. They must be documented here before implementation, including request/response schemas, permission names, pagination, idempotency, and audit behavior.

## Sprint 2 — Authentication and RBAC

All endpoints use JSON. Browser authentication uses Sanctum's stateful session flow: first request `GET /sanctum/csrf-cookie`, then send credentials with cookies and the XSRF header.

| Method | Endpoint | Access | Purpose |
|---|---|---|---|
| POST | `/api/auth/login` | Guest | Start a session |
| POST | `/api/auth/logout` | Authenticated | End the session |
| GET | `/api/auth/user` | Authenticated, active | Current user, roles and permissions |
| POST | `/api/auth/forgot-password` | Guest | Send reset link |
| POST | `/api/auth/reset-password` | Guest | Reset with token |
| PUT | `/api/auth/password` | Authenticated | Change password |
| REST | `/api/users` | Policy protected | User CRUD |
| POST/DELETE | `/api/users/{user}/roles[/{role}]` | `users.update` | Assign/remove role |
| REST | `/api/roles` | `manage-rbac` gate | Role CRUD |
| REST | `/api/permissions` | `manage-rbac` gate | Permission CRUD |

Validation failures return HTTP 422 with `message` and field-keyed `errors`. Unauthenticated and unauthorized requests return 401 and 403 respectively. Resources are returned under `data`; collections additionally contain pagination links and metadata.

## Sprint 3 organization APIs

Authenticated `/api` endpoints include RESTful CRUD for `/companies`, `/branches`, `/warehouse-types`, `/warehouses`, and `/sales-channels`. They support pagination plus `company_id`, `branch_id`, `type`, `is_active`, and `q` filters where applicable. `/organization/context` returns current user organizational visibility. `/users/{user}/organization-access` reads and updates company, branch, and warehouse assignments transactionally.
