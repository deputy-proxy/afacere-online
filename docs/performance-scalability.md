# Performance and scalability validation

## Critical journey budgets

These are engineering budgets, not measured results. They define what a production-like load test must validate.

| Journey | Target budget |
| --- | ---: |
| Public page first response | < 500 ms p95 |
| Authenticated dashboard | < 800 ms p95 |
| Evaluation step transition | < 800 ms p95 |
| Action-plan update | < 800 ms p95 |
| Guides listing | < 700 ms p95 |
| Opportunities listing | < 700 ms p95 |
| Monitor check-in | < 800 ms p95 |
| Search | < 800 ms p95 |
| Health/readiness | < 300 ms p95 excluding unavailable external dependencies |

No measured performance claim is made by this document because no load test was executed in the available environment.

## Query review

Critical listing paths must use explicit limits or pagination. Phase 5 ecosystem discovery uses bounded collections and eager loading for expert users and provider services. Published resource search is bounded by the existing service limits.

When a collection becomes user-sized rather than catalog-sized, replace fixed limits with pagination or cursor-based iteration. Do not render an unbounded Eloquent collection in a Livewire component.

## N+1 review checklist

For each Livewire/HTTP journey inspect:

1. relationships referenced inside loops;
2. computed properties evaluated more than once per request;
3. repeated authorization queries;
4. counts that can be aggregated in SQL;
5. nested collections without eager loading;
6. polymorphic relationships requiring explicit loading.

The ecosystem page eager-loads expert users and published provider services. New listing code must follow the same rule.

## Index review

Existing Phase 3 migrations include indexes for common business/status and expert/provider lookup patterns. Before adding an index, confirm the actual query shape and migration ordering. Indexes should support a demonstrated filter/order combination rather than being decorative database jewelry.

## Asynchronous work

Payment callbacks, notifications, analytics recording and other slow third-party or batch operations should not block the critical request when the existing domain contract permits queueing. Queue failures must remain observable through the operational monitoring defined in `docs/observability.md`.

## Storage and uploads

Uploaded files must have bounded size/type validation, use configured storage disks, avoid loading large files into PHP memory, and keep access checks at the business/document boundary. External object storage behavior must be tested in staging before launch.

## Resource exhaustion and degradation

Known degradation points include database connection exhaustion, queue backlog, slow external providers, oversized uploads and large catalog queries. The system should fail closed for authorization-sensitive operations and degrade gracefully for optional discovery/analytics functionality.

## Load-test protocol

A production-like load test should record:

- environment and instance size;
- database engine and size;
- application build identifier;
- concurrency and arrival rate;
- journey mix;
- duration and warm-up period;
- p50/p95/p99 latency;
- error rate;
- CPU/memory/database saturation;
- queue depth;
- slow queries;
- observed breaking point;
- recovery behavior.

Until that test is executed, scalability limits remain documented assumptions rather than results.
