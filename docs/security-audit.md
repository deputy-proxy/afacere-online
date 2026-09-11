# Security, privacy and authorization audit

## Entry-point checklist

| Boundary | Protection | Evidence | Status |
| --- | --- | --- | --- |
| Public pages | Public by design | Route inventory | Reviewed |
| Authenticated application | `auth` + `verified` | `routes/web.php` | Reviewed |
| Business workflows | Current-business/member checks | Livewire/services/tests | Reviewed |
| Internal support | `auth` + `verified` + `admin` | Support routes/tests | Reviewed |
| Data export/deletion | Authenticated user boundary | Feature tests | Reviewed |
| Payment callbacks | Provider-specific validation required | Webhook service/tests | External configuration pending |
| File/document access | Business/document ownership checks | Domain tests | Reviewed |

## Findings and remediation

### Cross-business authorization

Business-facing services must receive the acting `User` when they create or mutate business-owned records. A generic “business has members” check is insufficient because it can authorize an unrelated authenticated user. Marketplace lead creation now checks the acting user's membership before creating a lead.

### Mass assignment

Models use guarded primary keys or explicit fillable attributes. New write paths must continue to validate input before persistence and must not accept authorization-sensitive attributes from user input.

### Sensitive responses

Exports and support responses must use explicit field selection rather than returning unrestricted model arrays. Passwords, tokens, recovery codes, provider secrets and internal credentials must never enter JSON responses or logs.

### Webhooks

The repository contains idempotency persistence for provider event identifiers. Actual signature verification remains provider-specific and cannot be validated without the configured provider integration. A production launch must attach provider-side evidence before marking this boundary operationally complete.

### Session and transport controls

Production must terminate HTTPS at the trusted edge, use secure cookies and retain CSRF protection for state-changing web requests. Any third-party callback intentionally outside CSRF verification requires independent authentication such as a provider signature.

### Abuse protection

Authentication, password recovery, support actions, public forms and payment callbacks are abuse-sensitive. The target deployment must verify framework/provider throttling and edge-level rate limits. Absence of a configured external limit is a launch blocker for public high-risk endpoints.

## Regression requirements

Security-sensitive tests must cover:

- authenticated versus unauthenticated access;
- verified versus unverified access;
- business member versus unrelated user;
- administrator versus ordinary user;
- published versus unpublished content;
- signed versus invalid third-party callbacks;
- repeated/idempotent operations;
- sensitive fields excluded from responses.

## Launch rule

A code review is not a substitute for operational security evidence. Any item marked “external configuration pending” remains a launch blocker until evidence is recorded in the release-readiness report.
