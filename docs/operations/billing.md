# Billing, payments and entitlement validation

## State model

The application must represent the subscription lifecycle deterministically. At minimum the operational mapping must distinguish pending/active, cancelled, expired and payment-failed states. A provider callback may update state only after authentication and idempotency checks.

## Webhook contract

1. Identify the configured provider.
2. Verify its signature using the provider's documented signing algorithm.
3. Validate the immutable provider event identifier.
4. Claim the event before applying side effects.
5. Ignore a duplicate event without repeating financial effects.
6. Apply the state transition transactionally.
7. Mark the event processed only after the domain transaction succeeds.
8. Preserve failed events for retry and reconciliation.

The repository implements persistence for webhook event idempotency. A real provider signature cannot be verified in repository CI without a configured external provider and its credentials, so production sign-off must include provider evidence.

## Required transition checks

- New subscription activates the correct entitlement only after trusted payment confirmation.
- Renewal preserves the entitlement while the provider confirms continued payment.
- Cancellation records the correct end-of-access semantics.
- Expiry removes or limits entitlements according to the plan contract.
- Payment failure does not silently retain an entitlement forever.
- Refunds and chargebacks are reconciled when the provider supports them.
- Replayed, delayed and out-of-order callbacks do not corrupt the final state.

## Customer-visible state

Billing UI must display the application's trusted state, not an optimistic browser-side payment result. Error and pending states must explain what the user can do next without exposing provider secrets or internal identifiers.

## Reconciliation procedure

For a discrepancy:

1. Identify the local subscription/account and provider customer reference.
2. Inspect the stored webhook event history.
3. Retrieve the provider's authoritative state through the configured integration.
4. Compare the provider state with the local entitlement state.
5. Apply the smallest deterministic correction.
6. Record the correction in the audit trail.
7. Re-check entitlement access.

## Launch evidence

Repository evidence includes idempotency persistence and service-level tests. Infrastructure/provider evidence still required before launch includes valid webhook signing configuration, delivery from the provider, retry behavior, customer/subscription state synchronization, refund/chargeback handling where applicable, and reconciliation of a representative failure case.
