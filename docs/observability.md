# Observability and incident response

## Signals

Track at minimum:

- request availability and error rate;
- response latency, especially critical authenticated journeys;
- database latency and slow-query volume;
- queue depth, failures and retry volume;
- scheduler execution failures;
- payment webhook receipt and processing failures;
- notification delivery failures;
- authentication and password-recovery failures;
- storage/document access failures;
- readiness and health endpoint state.

## Structured logging

Logs should contain event name, severity, deployment identifier and correlation context where available. Do not log passwords, secrets, payment credentials, authentication tokens, raw document contents or unnecessary personal/business data.

Slow database logging must avoid query bindings and other sensitive values.

## Alert priorities

**P0:** authentication compromise, payment/data-integrity corruption, widespread outage or privacy boundary failure.

**P1:** major critical-journey outage, sustained queue failure, payment processing failure or severe latency affecting many users.

**P2:** degraded non-critical workflow, isolated provider failure or recoverable operational issue.

## Incident procedure

1. Confirm `/up` and `/health/ready`.
2. Identify deployment and first observed timestamp.
3. Determine customer/business impact.
4. Preserve relevant logs and evidence.
5. Identify whether the problem is application, database, queue, provider or infrastructure related.
6. Apply the smallest reversible mitigation.
7. Verify critical journeys.
8. Escalate data-integrity, payment, security or privacy incidents immediately.
9. Record root cause, impact, mitigation and follow-up work.

## Recovery objectives

The production environment must define and record service-specific RTO/RPO values before launch. Until those values are supplied by the hosting and database configuration, they are unknown rather than magically “standard.”

## Health endpoints

- `/up` is the framework health check.
- `/health/ready` verifies application readiness and required Phase 3 tables.

Readiness must be used for traffic admission where the hosting platform supports it.
