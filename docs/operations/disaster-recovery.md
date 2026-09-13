# Backup, restore and disaster recovery runbook

## Recovery targets

Production must set explicit targets before launch. Until infrastructure owners provide approved values, the targets are **TBD** and must not be represented as achieved:

- RPO: TBD;
- RTO: TBD.

Targets should be selected separately for database, uploaded documents/storage and application availability where their recovery characteristics differ.

## Backup policy

The production owner must verify and record all of the following against the actual hosting and storage configuration:

| Control | Required evidence |
| --- | --- |
| Database backup frequency | Provider/job configuration showing the schedule and the most recent successful run |
| Database retention | Provider configuration or policy showing the retention window |
| Database encryption | Provider/storage encryption configuration and access policy |
| Off-site/failure-domain copy | Backup location and evidence that it is independent of the primary failure domain |
| Uploaded-file recovery | Storage backup, replication, versioning or provider recovery configuration |
| Backup failure alerting | Alert rule plus a test or recent failure notification |
| Access control | Named recovery operators and the minimum permissions required |

A repository configuration file cannot prove that a provider backup job is enabled, successful, encrypted, retained, or alerting. Those properties require infrastructure evidence.

## Restore drill

A representative restore must be performed in an isolated, non-production environment before launch. The drill must use a real backup and must not write to production.

### Procedure

1. Select a recent successful backup that is representative of the production data set.
2. Record the backup identifier, creation timestamp, source database/storage version and application deployment/commit identifier.
3. Provision an isolated recovery environment with the same supported application and database versions, or document any intentional version difference.
4. Restore the database into the isolated environment.
5. Restore or rehydrate uploaded files using the configured storage recovery mechanism.
6. Apply the application release and run the normal database migration/status checks without silently modifying the backup artifact.
7. Verify database connectivity, schema compatibility and application health/readiness.
8. Verify representative business records and relationships, including authorization-sensitive data.
9. Verify representative uploaded documents and their ownership/access controls where file storage is used.
10. Run the release smoke checks against the restored environment.
11. Record restore start/end times and calculate the observed recovery time.
12. Compare the observed data-loss window with the approved RPO and the recovery time with the approved RTO.
13. Record every defect, workaround and limitation discovered during the drill.
14. Tear down or securely isolate the recovery environment after evidence has been captured.

**Do not claim the restore drill passed until these steps have actually been performed against a real backup outside production.**

## Restore evidence

Record the drill in `docs/operations/disaster-recovery-evidence.md`. The evidence record must contain:

1. backup identifier and timestamp;
2. database/storage versions;
3. application commit/deployment identifier;
4. isolated environment identifier;
5. restore start/end timestamps;
6. observed data-loss window;
7. observed recovery time;
8. approved RPO/RTO values and pass/fail comparison;
9. migration compatibility result;
10. health/readiness result;
11. release smoke result;
12. representative data/file verification result;
13. defects or limitations;
14. evidence locations and operator/owner.

Do not place credentials, connection strings, backup contents, personal data or other secrets in the repository. Store sensitive evidence in the approved restricted operational system and reference it by identifier.

## Database-loss procedure

1. Declare an incident and preserve evidence.
2. Stop writes if continued writes could worsen corruption.
3. Select the last verified backup consistent with the approved RPO.
4. Restore into an isolated database first where practical.
5. Verify schema and migration compatibility with the release artifact.
6. Run health/readiness and smoke checks.
7. Cut traffic to the recovered database only after verification.
8. Reconcile payment/webhook state and other external systems whose events may have arrived after the backup.
9. Record the data-loss window and customer impact.
10. Continue monitoring the recovered service and preserve the final incident record.

## Storage-loss procedure

Restore or rehydrate uploaded documents from the configured storage backup/recovery mechanism. Verify document ownership and access controls after restoration. Do not rebuild permissions from filenames or client-provided metadata.

If storage recovery uses a provider's versioning, replication or object-recovery feature rather than a separate backup, record that mechanism and its documented recovery guarantees as infrastructure evidence.

## Deployment failure

Use the release rollback procedure when application code is the failure source and schema compatibility permits it. Do not blindly roll back destructive migrations. Preserve logs and deployment metadata.

If the failed deployment changed the database schema, first determine whether the previous application version is compatible with the current schema. Prefer a forward fix when a rollback would create an incompatible schema state.

## Credential compromise

1. Revoke or rotate the compromised credential.
2. Disable affected integration access.
3. Preserve relevant audit evidence.
4. Assess whether user or business data was exposed or modified.
5. Rotate dependent credentials where compromise may have propagated.
6. Verify payment, storage, mail and queue integrations after rotation.
7. Record the incident and corrective actions.

## Observability and alerting

Backup operations are not considered operationally complete unless failures are actionable. The production owner must verify:

- successful and failed backup executions are observable;
- failures generate an alert routed to an owned operational channel;
- alerts identify the affected backup job/resource and failure time;
- alert delivery is tested rather than inferred from configuration alone;
- restore-drill failures are recorded as release blockers until resolved.

## Ownership and release gate

The production owner must attach infrastructure evidence for backup configuration, frequency, retention, encryption/access policy, storage recovery, restore drill results, RPO/RTO and alerting.

Repository tests and documentation can validate the procedure and required evidence format, but they cannot prove provider-side backup or restore behavior.

Issue #112 remains open until the required infrastructure evidence includes **at least one successfully executed isolated restore** and the restored application passes the release smoke checks. No repository-only change may mark that external validation as complete.
