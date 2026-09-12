# Data lifecycle and privacy validation

## Data inventory

### User-controlled data

- account identity and verification state;
- profile information;
- businesses and membership context;
- business profile, preferences and operational context;
- user-submitted documents and evidence;
- evaluations, findings, recommendations and priorities;
- action plans, actions, evidence, outcomes and progress;
- Guide progress and activity;
- opportunity applications and matching activity;
- consultations, mentoring and session information;
- community contributions and event registrations;
- user notifications;
- AI runs, feedback and recommendations linked to the user/business;
- analytics identifiers and conversion activity.

### Operational or retained data

- security/accountability audit records;
- payment, subscription and financial transaction records required for reconciliation or legal obligations;
- aggregate analytics that no longer require direct personal identification.

The application defaults are documented in `docs/data-retention-policy.md`. Jurisdiction-specific legal obligations and counsel-reviewed privacy terms take precedence over engineering defaults.

## Export contract

The export endpoint returns an explicit schema version containing account identity, profile, business context, membership information and data-request history. It excludes passwords, authentication secrets, recovery codes, internal authorization flags and other credentials. Stored file contents are not silently embedded in JSON; physical file export must be added only if the product explicitly promises it.

## Deletion lifecycle

1. User submits a deletion request.
2. Repeated requests reuse the active pending/approved/processing request.
3. Support/operations reviews the request and retention obligations.
4. An approved request enters `processing`.
5. Eligible records and storage objects are removed; shared businesses are preserved for remaining members.
6. Financial records and security audit records are retained with direct user references removed where supported.
7. Verification confirms the user and eligible business/document records are gone before completion.
8. The request is marked `completed` and a privacy-safe audit event is recorded.
9. Failures remain `failed` and never masquerade as successful completion.

## Shared business semantics

A business with no remaining members after the request is permanently removed. A shared business is retained and the deleting user's membership is removed. User-owned uploaded documents are removed even when the business remains shared.

## Storage

`BusinessDocument` stores the configured storage disk and object path. Deletion removes the physical object first and then the database record. Missing objects are treated as already satisfied so retries remain safe. Storage-path handling is restricted to the configured disk/path pair and never exposes file contents through the export contract.

## Retention

- Completed data-request records: 90 days after completion.
- Direct user references in analytics events/conversions older than 730 days: anonymized while aggregate event data remains.
- Financial records: retained for the applicable accounting/provider obligation, with direct user references removed where the schema permits.
- Security audit records: retained for the applicable security/accountability obligation and must contain only the minimum context required.

The retention periods are configuration-backed and documented in `docs/data-retention-policy.md`. Automated scheduling of retention enforcement remains an operational deployment concern and must use the configured values rather than duplicating them in infrastructure scripts.

## Operational controls

- Approved deletion requests are executable through the dedicated `DataDeletionService` and its `data:process-deletions` command.
- Review endpoints require authenticated administrator access.
- Destructive execution is not exposed as an unrestricted CRUD operation.

## Privacy documentation gate

The privacy-facing documentation must match the actual export fields, deletion semantics, retention schedule and third-party processing. Any mismatch is a release blocker rather than a copy-editing problem wearing a legal hat.
