# Phase 1 domain implementation notes

> **Historical status:** Phase 1 is complete. This document records the domain boundaries and implementation decisions that were previously tracked as remaining work. It is not an open implementation backlog.

Phase 1 established the application and domain foundation around deterministic business rules first and AI assistance second.

## Implemented domain boundaries

- **Opportunities:** deterministic criteria evaluation, expiry/freshness, matches and application tracking.
- **Monitor:** recurring configuration, immutable check-ins, thresholds, alerts and summaries.
- **AI:** provider abstraction, versioned prompts, auditable runs, structured validation, usage/cost, feedback and explicit confirmation of AI suggestions.
- **Commerce:** configurable MVP plans, entitlement records, subscription lifecycle, invoices, payments and idempotent subscription events.
- **Events and notifications:** append-oriented persisted domain events and targeted user notifications.
- **Analytics:** product events remain separate from transactional truth; conversions and period snapshots are idempotent.
- **Filament:** operational resources are read-only where mutation would bypass domain workflows, with server-side admin authorization.

## Implementation notes

Structured AI output is validated before it is allowed to create an AI recommendation, and confirmation remains a separate user action. Public service arrays carry explicit PHPStan value types. The AI service uses an explicit dependency property and local array type assertions.

Subsequent changes to these domains are incremental improvements, fixes or extensions governed by the active development and validation issues. They should not be interpreted as reopening Phase 1.
