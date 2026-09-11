# Backup, restore and disaster recovery runbook

## Recovery targets

Production must set explicit targets before launch. Until infrastructure owners provide approved values, the targets are **TBD** and must not be represented as achieved:

- RPO: TBD;
- RTO: TBD.

Targets should be selected separately for database, uploaded documents/storage and application availability where their recovery characteristics differ.

## Backup requirements

- Database backups run automatically at an approved frequency.
- Retention covers the approved operational/legal window.
- Backups are encrypted at rest and access is restricted to recovery operators.
- At least one recent copy is outside the primary failure domain.
- Uploaded files are backed up or otherwise recoverable according to the configured storage provider's durability/recovery contract.
- Backup job failures emit actionable alerts.

## Restore drill

A representative restore must be performed in an isolated environment before launch. Record:

1. backup identifier and timestamp;
2. database/storage versions;
3. application commit/deployment identifier;
4. restore start/end time;
5. data loss relative to the chosen RPO;
6. time to service according to the chosen RTO;
7. migration compatibility;
8. health/readiness result;
9. release smoke result;
10. defects or limitations.

No restore result is claimed by this repository change because no external infrastructure restore was executed.

## Database-loss procedure

1. Declare an incident and preserve evidence.
2. Stop writes if continued writes could worsen corruption.
3. Select the last verified backup consistent with the RPO.
4. Restore into an isolated database first where practical.
5. Verify schema/migration compatibility with the release artifact.
6. Run health/readiness and smoke checks.
7. Cut traffic to the recovered database only after verification.
8. Reconcile payment/webhook state and other external systems whose events may have arrived after the backup.
9. Record data-loss window and customer impact.

## Storage-loss procedure

Restore or rehydrate uploaded documents from the configured storage backup/recovery mechanism. Verify document ownership/access controls after restoration. Do not rebuild permissions from filenames or client-provided metadata.

## Deployment failure

Use the release rollback procedure when application code is the failure source and schema compatibility permits it. Do not blindly roll back destructive migrations. Preserve logs and deployment metadata.

## Credential compromise

1. Revoke/rotate the compromised credential.
2. Disable affected integration access.
3. Preserve relevant audit evidence.
4. Assess whether user/business data was exposed or modified.
5. Rotate dependent credentials where compromise may have propagated.
6. Verify payment, storage, mail and queue integrations after rotation.
7. Record the incident and corrective actions.

## Ownership and evidence

The production owner must attach infrastructure evidence for backup configuration, encryption/access policy, restore drill results, RPO/RTO and alerting. Repository tests alone cannot prove any of those infrastructure properties.
