# Billing and payment operations

Every payment-provider webhook must be identified by the provider name and the provider's immutable event identifier. The event identifier is persisted before domain-side processing so retries cannot create duplicate financial records.

## Required workflow

1. Verify the provider signature before accepting the callback.
2. Claim the provider event through the payment webhook idempotency service.
3. Ignore an already-claimed event without replaying financial side effects.
4. Apply the domain state transition in a transaction.
5. Mark the event processed only after the transaction succeeds.
6. Keep failed events visible for reconciliation and support.

Payment and entitlement state must be reconciled from the provider when callbacks are delayed, duplicated or delivered out of order. Never grant durable access solely because an unverified client-side payment state says that payment succeeded.
