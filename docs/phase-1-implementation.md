# Phase 1 implementation

Phase 1 establishes the application and domain foundation for afacere.online.

## Quality gate

Every Phase 1 change is validated with the repository CI pipeline: Pint, PHPStan/Larastan, and Pest.

## Domain boundaries

- Business is the central aggregate for business-scoped capabilities.
- Historical records are versioned or append-oriented where later changes must not rewrite history.
- Lifecycle changes are performed through explicit application/domain actions.
- AI providers are accessed through an application-level abstraction.
- Analytics events are separate from transactional domain truth.
- Audit records are append-oriented.
- Entitlements are evaluated server-side.
