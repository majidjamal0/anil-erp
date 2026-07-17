# Authentication and authorization

## SPA sign-in

The Vue application and API must share a top-level domain configured in `SANCTUM_STATEFUL_DOMAINS`. The browser obtains an XSRF cookie from `/sanctum/csrf-cookie`, then posts credentials to `/api/auth/login` with `credentials: include`. Subsequent protected requests use the encrypted Laravel session cookie; credentials are never stored in local storage.

Inactive accounts receive a validation error. Logout invalidates the server session and rotates the CSRF token. The Pinia auth store restores the current user through `/api/auth/user` before router navigation and redirects guests to login.

## Password lifecycle

`POST /api/auth/forgot-password` always gives a neutral response. Laravel emails a signed reset token. The client submits the token, email, and confirmed new password to `/api/auth/reset-password`. Signed-in users can change their password through `/api/auth/password` after confirming the current password.

## Roles and permissions

Seeded roles are Super Admin, Administrator, Branch Manager, Warehouse Manager, Salesperson, and Accountant. Permissions are granular action strings such as `users.view` and `warehouses.manage`. Policies protect user resources, the `manage-rbac` gate protects roles and permissions, and route middleware enforces authentication, active status, and individual permissions. UI checks improve usability but never replace server authorization.

## Security operations

Use HTTPS, secure/session-only cookies, production stateful-domain settings, and rate limiting. Replace the seed administrator password immediately. Audit entries capture authentication-sensitive and user-management events without recording passwords or tokens.
