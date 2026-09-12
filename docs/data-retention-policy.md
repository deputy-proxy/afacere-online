# Data retention policy

This policy defines the application defaults for data lifecycle operations. It is an engineering control, not legal advice. Jurisdiction-specific legal obligations and counsel-reviewed privacy terms take precedence over these defaults.

| Category | Default retention | Treatment | Reason |
| --- | ---: | --- | --- |
| Active account/profile data | Account lifetime | Delete on approved account deletion | User-controlled service data |
| Business data owned only by the deleted user | Account lifetime | Delete | No remaining business member |
| Shared business data | Business lifetime | Preserve business, remove deleted member and member-owned data | Protect remaining business members |
| Uploaded documents owned by the deleted user | Account/business lifetime | Delete database record and storage object | User-controlled content |
| Completed data-request records | 90 days after completion | Operational purge | Minimal lifecycle evidence |
| Analytics identifiers | 730 days | Remove direct user reference while preserving aggregate event | Product analytics |
| Financial transaction records | Per applicable accounting/provider obligation | Retain and remove direct user reference where possible | Reconciliation and legal obligations |
| Security audit records | Per applicable security/accountability obligation | Retain and minimize personal content | Security and accountability |

## Deletion semantics

A deletion request is idempotent while pending, approved or processing. Support/operations approval is required before execution. Execution moves through processing and only reaches completed after the user record, eligible business records and stored documents have been verified.

A sole-member business is permanently removed. A business with other members is preserved and the deleting member is removed from it. User-owned documents are removed even when the business remains shared.

Financial records and security audit records are not treated as ordinary user content. Their direct user reference is removed when the schema permits it, while the record itself is retained for the applicable obligation.

## Operational controls

- `data:process-deletions` executes approved deletion requests.
- Retention defaults are stored in `config/data-lifecycle.php`.
- Storage deletion is idempotent: an already-missing object does not prevent completion.
- Failed executions remain explicitly failed and are not represented as completed.
