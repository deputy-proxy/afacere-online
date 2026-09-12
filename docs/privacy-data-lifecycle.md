# Data lifecycle and privacy validation

## Data inventory

### User-controlled data

- account identity and verification state;
- profile information;
- businesses and membership context;
- business profile, preferences and operational context;
- user-submitted documents and evidence where the product exposes them;
- user-created progress, recommendations and activity records.

### Operational or retained data

- audit records required for security/accountability;
- payment and financial records required for reconciliation and legal obligations;
- aggregate analytics that no longer require direct personal identification.

The exact retention period for each category must be confirmed against the final legal/privacy policy and the target jurisdictional requirements.

## Export contract

The export endpoint returns an explicit, serialized set of account, profile, business and data-request information. It deliberately excludes passwords, authentication secrets, recovery codes and internal authorization flags. Stored file contents are not silently represented as JSON metadata; file export behavior must be explicitly defined if promised by the product.

## Deletion lifecycle

1. User submits a deletion request.
2. A pending request is reused when the user repeats the request.
3. Support/operations verifies the request and any retention obligations.
4. Deletion execution removes or anonymizes eligible records and storage objects.
5. Legally or operationally retained records are preserved with a documented reason and retention period.
6. The request is marked completed only after verification.
7. The operation is recorded in the audit trail without retaining unnecessary personal content.

The repository currently implements request creation and idempotency. Actual destructive execution and infrastructure storage deletion require an operational implementation and verification before launch.

## Related records

Deletion review must cover business memberships, profile data, documents, evaluations, action/progress records, community contributions, event registrations, marketplace leads, consultations, notifications, analytics identifiers and audit records. Foreign-key cascade behavior must not be treated as proof that external files or legally retained records were handled correctly.

## Privacy documentation gate

The privacy-facing documentation must match the actual export fields, deletion semantics, retention schedule and third-party processing. Any mismatch is a release blocker rather than a copy-editing problem wearing a legal hat.
