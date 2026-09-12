# Launch checklist

Use this as the single release gate. Each row must link to evidence. `Repository` means evidence in source/tests/CI; `Manual` means evidence from a browser or operator; `Infrastructure` means evidence from the target environment.

| Gate | Evidence type | Status | Blocking condition |
| --- | --- | --- | --- |
| Phase 5 issue #103 ecosystem reachability | Repository | Implemented | Critical workflow unreachable |
| #104 deployment validation | Infrastructure | Pending | Fresh staging or production deployment not verified |
| #105 security/privacy authorization | Repository + Infrastructure | Partial | Unverified external security controls |
| #106 billing/payment validation | Repository + Infrastructure | Partial | Provider integration/signature/reconciliation not verified |
| #107 data lifecycle | Repository + Manual | Partial | Destructive deletion/storage propagation not verified |
| #108 observability | Repository + Infrastructure | Partial | Alerts/owners not verified in target environment |
| #109 performance/scalability | Repository + Manual | Partial | Production-like load test not executed |
| #110 accessibility/UX | Manual | Pending | Browser/assistive-tech verification missing |
| #111 release smoke coverage | Repository | Implemented | CI smoke suite fails |
| #112 backup/restore/DR | Infrastructure | Pending | Verified restore drill missing |
| #113 operations/support | Repository + Manual | Partial | Named owners and production operator verification missing |
| #114 documentation reconciliation | Repository | In progress | README/status claims stale |
| #115 final release audit | Repository + Infrastructure | Pending | Any launch blocker unresolved |

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

Known limitations that do not block launch must be explicitly recorded with an owner, mitigation and review date. Silence is not a risk-management strategy, despite humanity's extensive historical experimentation with it.
