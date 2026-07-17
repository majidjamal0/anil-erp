# Business rules

## Identity and access

1. Every staff user has a UUID, unique email, locale, and explicit active state.
2. Authentication uses Sanctum; authorization uses server-side policies backed by Spatie roles and permissions.
3. Role names and permission names are unique within an authentication guard.
4. Disabling a user prevents new authenticated activity; session/token revocation will be implemented with the authentication workflows.
5. Navigation visibility is never a substitute for backend authorization.

## Organization

1. Branch and warehouse codes are globally unique, stable external identifiers.
2. Every warehouse belongs to exactly one branch.
3. A branch that owns any warehouse cannot be physically deleted.
4. Soft-deleted branches and warehouses are excluded from normal operational queries.

## Settings and audit

1. A setting is uniquely identified by its group and key; only values explicitly marked public may reach unauthenticated clients.
2. Audit events are append-only and must not contain passwords, tokens, payment credentials, or unrelated personal data.
3. Material state-changing application actions must eventually record actor, subject, event, and safe before/after snapshots in the same database transaction.

## Cross-cutting accounting rules

Financial values must be fixed-precision decimals with currency recorded explicitly at domain boundaries. Rounding policy belongs to the finance module and must never depend on binary floating point. Server state and audit timestamps are UTC; Persian calendar/date and number formatting are presentation concerns.

## Identity and authorization rules

1. Only active users may establish or continue an authenticated API session.
2. Authorization is deny-by-default. User operations require the matching `users.*` permission; RBAC operations require the `manage-rbac` gate.
3. Super Admin bypasses application gates and policies. Its role cannot be renamed or deleted.
4. A user cannot delete their own account. Passwords require confirmation and Laravel's configured password defaults.
5. Role changes and user lifecycle operations are audited with actor and request context.
6. Forgot-password responses do not reveal whether an email address exists.
