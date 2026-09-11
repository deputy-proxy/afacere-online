# Phase 1 remaining domain boundaries

The remaining Phase 1 implementation is organized around deterministic business rules first and AI assistance second.

- Opportunities: deterministic criteria evaluation, expiry/freshness, matches and application tracking.
- Monitor: recurring configuration, immutable check-ins, thresholds, alerts and summaries.
- AI: provider abstraction, versioned prompts, auditable runs, structured validation, usage/cost, feedback and explicit confirmation of AI suggestions.
- Commerce: configurable MVP plans, entitlement records, subscription lifecycle, invoices, payments and idempotent subscription events.
- Events and notifications: append-oriented persisted domain events and targeted user notifications.
- Analytics: product events remain separate from transactional truth; conversions and period snapshots are idempotent.
- Filament: operational resources are read-only where mutation would bypass domain workflows, with server-side admin authorization.
