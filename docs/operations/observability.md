# Operational observability

The canonical observability contract, telemetry events, alert policy, ownership requirements and incident runbooks are maintained in [`docs/observability.md`](../observability.md).

## Production verification gate

Repository implementation must be complemented by target-environment evidence for:

- health and readiness behavior;
- dependency failure detection;
- real alert delivery;
- named alert ownership and escalation;
- queue failure visibility;
- payment/webhook failure visibility;
- alert recovery and clearing.

Source-code presence and automated tests do not substitute for this infrastructure evidence.

## Incident response summary

1. Confirm `/up` and `/health/ready`.
2. Identify deployment and first observed timestamp.
3. Determine impact and preserve evidence.
4. Classify application, database, queue, provider or infrastructure failure.
5. Apply the smallest reversible mitigation.
6. Verify critical smoke journeys.
7. Escalate payment, security, privacy and data-integrity incidents immediately.
8. Record root cause, impact, corrective action and follow-up ownership.
