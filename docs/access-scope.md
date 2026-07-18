# Access Scope

Backend APIs enforce organizational visibility with roles, permissions, and user assignments in `company_user`, `branch_user`, and `user_warehouse`. Access levels are `view`, `operate`, `approve`, and `manage`.

Super Admin users have global access. Other users see only assigned companies, branches, warehouses, and channels from assigned companies. Cross-company changes are rejected and warehouse company changes are not allowed.

`GET /api/organization/context` returns accessible companies, branches, warehouses, sales channels, and default selections for the current user.
