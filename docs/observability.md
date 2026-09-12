# Observability and incident response

## Purpose

Production failures must be detectable, diagnosable and recoverable without exposing secrets or unnecessary personal/business data.

## Signals

Track at minimum:

- request availability and error rate;
- response latency, especially critical authenticated journeys;
- database latency and slow-query volume;
- queue depth, failures and retry volume;
- scheduler execution failures;
- payment webhook receipt, duplicate and processing failures;
- subscription lifecycle failures;
- notification delivery failures;
- authentication and password-recovery failures;
- storage/document access failures;
- readiness and health endpoint state.

## Structured telemetry

Application telemetry is emitted through `ObservabilityService` so operational events share a stable shape:

- event name;
- severity;
- environment;
- deployment identifier (`APP_VERSION`, then `GITHUB_SHA`, then `local`);
- request correlation ID from `X-Request-ID`, when available;
- sanitized operational context.

The service recursively redacts keys containing password, secret, token, authorization, credential, card, CVV/CVC, signature, payload or raw-body data. Objects are not serialized into telemetry context.

Slow database logging records connection and duration only. Query bindings must never be logged.

## Canonical events

| Area | Events |
| --- | --- |
| Database | `database.slow_query` |
| Queue | `queue.job_failed` |
| Payments | `payment.webhook_received`, `payment.webhook_duplicate`, `payment.webhook_processed`, `payment.webhook_failed` |
| Subscription | `subscription.changed` |

Additional application-specific failures should use `ObservabilityService` rather than ad-hoc log payloads.

## Health and readiness

- `/up` is the framework/application liveness check.
- `/health/ready` is the traffic-admission/readiness check and returns HTTP 503 when required configuration or dependencies are unavailable.
- Readiness must be used for traffic admission where the hosting platform supports it.
- Optional third-party dependencies must not make readiness fail unless the application contract requires them for normal operation.

## Slow-query threshold

`PERFORMANCE_SLOW_QUERY_MS` controls the warning threshold and defaults to 500 ms. The threshold should be reviewed against production workload rather than treated as a universal performance target.

## Alert catalogue

Alert configuration belongs to the target monitoring platform, while the application defines the signals and recommended response policy below.

| Severity | Trigger | Initial response |
| --- | --- | --- |
| P0 | Authentication compromise, payment/data-integrity corruption, widespread outage or privacy-boundary failure | Page primary owner immediately; preserve evidence; invoke incident response |
| P1 | Sustained critical-journey outage, queue failure/exhaustion, payment processing failure or severe latency | Page primary owner; assess impact; apply reversible mitigation |
| P2 | Degraded non-critical workflow, isolated provider failure or recoverable operational issue | Create operational alert; investigate during normal response window |

The target environment must configure concrete numeric thresholds and evaluation windows for at least:

- HTTP 5xx/error rate;
- readiness failures;
- critical request latency;
- failed/exhausted queue jobs;
- payment/webhook failures;
- critical dependency failures.

## Alert ownership and escalation

Every production alert must have:

1. a primary operational owner;
2. a secondary/escalation owner;
3. severity;
4. response target;
5. documented mitigation;
6. escalation conditions.

Production contact names and paging destinations are environment-specific and must not be committed as secrets.

## Incident procedure

1. Confirm `/up` and `/health/ready`.
2. Identify deployment and first observed timestamp.
3. Determine customer/business impact.
4. Preserve relevant logs and evidence before restarting workers or changing infrastructure.
5. Identify whether the problem is application, database, queue, payment/provider or infrastructure related.
6. Apply the smallest reversible mitigation.
7. Verify critical journeys.
8. Escalate data-integrity, payment, security or privacy incidents immediately.
9. Communicate material customer impact through the approved channel.
10. Record root cause, impact, mitigation and follow-up work.

## Major scenario runbooks

### Application outage

Verify liveness and readiness, compare against the latest deployment, inspect error rate and recent application logs, mitigate or roll back using the deployment runbook, then verify the critical smoke journey.

### Database/dependency failure

Confirm the affected dependency, determine whether readiness is correctly failing, preserve evidence, avoid destructive recovery actions, restore dependency service or fail over according to infrastructure procedures, then verify readiness and critical journeys.

### Queue failure

Inspect failed jobs and retry counts, determine whether retries are safe, identify whether the failure is systemic or job-specific, recover workers/infrastructure as appropriate, and verify that critical asynchronous workflows complete.

### Payment failure

Determine whether the failure is provider, signature/authentication, webhook processing or reconciliation related. Use internal payment/event IDs, never payment credentials. Preserve provider event IDs and application logs, respect idempotency, reconcile state and verify the affected subscription/payment journey.

### Data-integrity or privacy incident

Stop destructive activity, preserve evidence and timestamps, restrict access to affected data, escalate immediately, determine scope, follow the data recovery/privacy incident procedure and document customer impact and corrective actions.

## Incident communication

Material incidents should have an owner, incident start time, impact statement, current status, mitigation, next update target and resolution statement. Customer-facing communication must contain only verified information and must not disclose internal secrets or unnecessary personal/business data.

## Post-incident review

For P0/P1 incidents, record:

- incident and severity;
- timeline and duration;
- customer/business impact;
- detection source;
- root cause and contributing factors;
- mitigation and resolution;
- what worked and what failed;
- corrective actions;
- owner and due date for each action.

## Recovery objectives

The production environment must define and record service-specific RTO/RPO values before launch. Until those values are supplied by hosting and database configuration, they remain unknown.

## External verification

Repository tests can verify telemetry contracts, sanitization, health behavior and monitoring hooks. They cannot prove that a production alert reaches the intended operator. Before launch, the target environment must demonstrate:

- `/up` and `/health/ready` behavior;
- dependency failure detection;
- real alert delivery;
- alert ownership and escalation;
- queue failure visibility;
- payment failure visibility;
- alert recovery/clear behavior.
