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
