# Operations and support runbook

## Access model

Administrative access is restricted to verified administrators through the `admin` middleware. Support actions must use the existing audited service layer and must not expose more user/business information than required for the case.

## User lookup and diagnostics

Use the internal support lookup to identify the account and inspect only the information necessary to diagnose the issue. Record the reason for privileged access. Do not copy passwords, recovery codes, payment credentials or private business content into tickets.

## Password recovery

1. Confirm the support operator is an authorized administrator.
2. Verify the account identity using the approved support process.
3. Initiate the repository's password-recovery workflow rather than handling credentials manually.
4. Never request or record the user's password.
5. Confirm the action is represented in the audit trail.
6. Escalate suspected account takeover rather than repeatedly resetting credentials.

## Subscription/payment support

For payment disputes, compare the application's subscription/entitlement state with the provider's authoritative state and webhook history. Do not manually grant paid access based on a screenshot or client-side status. Follow the billing reconciliation procedure for refunds, failed payments and duplicate callbacks.

## Data export/deletion support

Verify the requester, inspect the pending data request and follow the data-lifecycle procedure. Do not perform destructive deletion outside the approved process. Confirm retention obligations before removing records.

## Abuse and suspicious activity

Escalate immediately when there is evidence of:

- credential compromise;
- repeated authentication abuse;
- unauthorized business-data access;
- payment manipulation;
- malicious uploads;
- moderation abuse or coordinated spam;
- suspicious administrator activity.

Preserve relevant audit evidence before changing state where doing so is safe.

## Incident ownership

| Incident | Primary owner | Escalation |
| --- | --- | --- |
| Availability/latency | Operations | Engineering |
| Authentication/security | Security/Operations | Engineering + incident lead |
| Payment/entitlement | Billing owner | Engineering + provider |
| Privacy/data request | Privacy owner | Operations + legal as required |
| Abuse/moderation | Trust & Safety | Operations |
| Data integrity | Engineering | Incident lead |

Replace role names with actual named owners before launch.

## Destructive action rule

Any destructive administrative action must be confirmed intentionally, authorized at the correct privilege level and auditable. If the current administrative UI cannot satisfy those properties, the action is a launch blocker.

## Support evidence

Repository tests cover administrator access boundaries and audited support behavior. Production sign-off must additionally verify operator accounts, least-privilege assignments, support ticket procedures, escalation contacts and audit-log retention.
