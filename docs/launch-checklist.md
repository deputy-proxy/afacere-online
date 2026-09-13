# Launch checklist

Use this as the single release gate. Each row identifies the evidence class, current status, responsible owner role and the condition that blocks launch.

`Repository` means evidence in source, tests or CI. `Manual` means evidence from a browser, human workflow or external test. `Infrastructure` means evidence from the target environment.

| Gate | Evidence type | Status | Owner | Evidence required | Blocking condition |
| --- | --- | --- | --- | --- | --- |
| #103 ecosystem reachability | Repository + Manual | Implemented / external verification pending | Engineering + Product | Route/component tests plus browser journey evidence | Critical workflow unreachable |
| #104 deployment validation | Infrastructure | Pending | Engineering / Release | Fresh target deployment, health checks and smoke evidence | Target deployment not verified |
| #105 security/privacy authorization | Repository + Infrastructure | Partial | Engineering + Security | Authorization tests, audit findings and production edge/rate-limit/provider verification | Critical security/privacy control unverified |
| #106 billing/payment validation | Repository + Infrastructure | Partial | Engineering + Finance/Billing | Idempotency tests plus provider signatures, callbacks and reconciliation evidence | Payment integrity unverified |
| #107 data lifecycle | Repository + Manual + Infrastructure | Partial | Engineering + Privacy | Export/deletion/retention tests plus storage propagation and legal/privacy review | Data deletion or retention behavior unverified |
| #108 observability | Repository + Infrastructure | Partial | Engineering / Operations | Instrumentation/runbook plus real alert delivery, dependency checks and named owners | Critical alerting or ownership unverified |
| #109 performance/scalability | Repository + Manual | Partial | Engineering | Query/resource review and production-like load-test evidence | Critical performance/resource risk unverified |
| #110 accessibility/UX | Manual | Pending | Product / UX | Keyboard, screen-reader, responsive and contrast verification | Critical accessibility or UX defect |
| #111 release smoke coverage | Repository | Implemented | Engineering | Release smoke suite and authorization checks with green CI | CI smoke suite fails |
| #112 backup/restore/DR | Infrastructure | Pending | Engineering / Operations | Backup configuration plus successful isolated restore drill and approved RPO/RTO | Recovery evidence missing |
| #113 operations/support | Repository + Manual | Partial | Operations / Support | Runbooks, access tests, named production operators and procedure verification | Production support ownership unverified |
| #114 documentation reconciliation | Repository | In progress | Engineering / Release | README, documentation index, status register and launch checklist aligned | Documentation claims stale or contradictory |
| #115 final release audit | Repository + Infrastructure | Pending | Release owner | Exact release-candidate evidence review and final GO/NO-GO record | Any launch blocker unresolved |

## Code and CI

- [ ] Exact release candidate identified.
- [ ] GitHub CI is GREEN for that exact commit.
- [ ] Pint/format checks are green.
- [ ] PHPStan/Larastan is green.
- [ ] Automated tests are green.
- [ ] Database migrations succeed from a clean environment.
- [ ] Frontend build succeeds.
- [ ] No dependency or configuration change is unreviewed.

## Product and UX

- [ ] Public discovery works.
- [ ] Registration/verification/onboarding works.
- [ ] Evaluation/diagnosis/priorities/action plan work together.
- [ ] Action execution and Monitor work together.
- [ ] Guides and Opportunities work.
- [ ] Ecosystem discovery works.
- [ ] Subscription/entitlement behavior is verified.
- [ ] Notifications work.
- [ ] Accessibility and responsive UX are manually verified.

## Security and privacy

- [ ] Authentication and authorization boundaries verified.
- [ ] Admin/support least privilege verified.
- [ ] Sensitive logs/responses reviewed.
- [ ] Payment callback authentication verified.
- [ ] Data export verified.
- [ ] Data deletion execution and retention rules verified.
- [ ] Privacy/legal documentation matches behavior.

## Operations

- [ ] Health/readiness checks verified.
- [ ] Monitoring and alerts verified with named owners.
- [ ] Queue/job failure visibility verified.
- [ ] Backups configured and retention documented.
- [ ] Restore drill completed outside production.
- [ ] RPO/RTO approved.
- [ ] Deployment and rollback procedures rehearsed.
- [ ] Support procedures and escalation contacts confirmed.

## Launch blockers

The release is **NO-GO** if any of the following remain unresolved:

- red GitHub CI on the exact release candidate;
- unresolved critical security/privacy issue;
- unverified payment integrity;
- unverified data recovery for a critical failure;
- unresolved data-loss risk;
- critical user journey broken;
- authentication/authorization bypass;
- missing backup/restore evidence;
- missing target-environment deployment verification.

Known limitations that do not block launch must be recorded with an owner, status, mitigation and review date. Evidence gaps must remain explicit until the required evidence is attached or the gate is formally accepted by the responsible owner.
