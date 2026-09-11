# Observability and incident response

## Health signals

- `/up` is the framework-level health endpoint.
- `/health/ready` validates the application configuration and required Phase 3 domain tables.
- Slow database queries are logged with connection and duration metadata without logging query bindings.

## Incident response

1. Confirm the health endpoint and recent deployment status.
2. Identify whether the incident affects availability, latency, payments, notifications, search or data integrity.
3. Preserve relevant logs and timestamps before restarting workers or changing infrastructure.
4. Apply the smallest reversible mitigation available.
5. Verify the critical smoke journey after mitigation.
6. Record root cause, customer impact, corrective action and follow-up work.

Operational logs must not contain passwords, secrets, payment credentials or unnecessary personal/business data.
