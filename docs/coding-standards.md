# Coding standards

## General

- Keep domain logic independent of HTTP and framework presentation concerns.
- Use English identifiers and Persian/English translation resources for user-facing copy.
- Store UTC timestamps; localize only at system edges. Never use floating point for money.
- Never commit credentials, customer data, generated dependencies, or runtime artifacts.

## Backend

Follow PSR-12 and Laravel conventions; enforce formatting with Pint. Enable strict typing in new domain classes. Prefer typed DTOs, form requests, API resources, policies, and transactional application services. Every schema change needs reversible migrations and tests. UUIDs are mandatory for business entities.

## Frontend

Use Vue Composition API with `<script setup lang="ts">`, strict TypeScript, accessible semantic HTML, and logical CSS properties for RTL/LTR. Keep Pinia stores focused on client state; server access belongs in typed API services. Components and stores require Vitest coverage.

## Git and review

Use Conventional Commits. Keep changes focused, require passing CI, document migrations and operational impact, and obtain review before merging to `main`.
