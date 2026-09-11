# Deployment validation runbook

## Validation boundary

Repository CI validates application code, dependency installation, migrations and the frontend build. It does not prove that the target infrastructure, DNS, queues, mail provider, payment provider, object storage or backup service is correctly provisioned.

Record infrastructure evidence separately for each staging and production environment.

## Required environment variables

Provision secrets through the hosting platform rather than source control. At minimum verify the values required by the committed `.env.example`, including application URL/key, database, cache/queue, mail and any enabled third-party integrations.

Before release, compare the target environment against `.env.example` and the actual configuration read by the application. Missing optional integrations must be deliberately disabled, not silently misconfigured.

## Fresh staging procedure

1. Provision a new application environment and database.
2. Configure secrets and non-production integration credentials.
3. Deploy the exact release candidate.
4. Run the application's migration command.
5. Build and publish frontend assets.
6. Start queue workers and the scheduler using the platform's process manager.
7. Verify `/up` and `/health/ready`.
8. Run the release smoke journeys.
9. Record versions, deployment identifier, migration result and evidence.

## Production deployment order

1. Confirm the release candidate has GREEN GitHub CI.
2. Confirm a recent verified backup exists.
3. Confirm required secrets and external dependencies are healthy.
4. Deploy the exact application artifact.
5. Run additive migrations before traffic depends on the new schema.
6. Restart workers and scheduler processes.
7. Verify health/readiness endpoints.
8. Execute critical smoke journeys.
9. Remove maintenance mode only after the smoke gate succeeds.
10. Monitor errors, queues, latency, payments and critical workflows.

## Migration failure handling

A failed migration is a release blocker. Preserve the deployment identifier and logs, stop dependent traffic when necessary, and do not improvise destructive schema changes. Determine whether the migration can be safely corrected forward or whether restoration of the known-good database is required.

## Rollback

Application rollback is appropriate when the failure is isolated to the application artifact and the previous artifact is compatible with the current schema. Schema rollback is not assumed to be safe. For destructive or data-integrity incidents, use the verified backup/restore procedure.

A representative rollback drill must be performed in staging and recorded with:

- release candidate identifier;
- previous known-good identifier;
- migration state;
- rollback steps;
- verification results;
- recovery time;
- unresolved limitations.

## Infrastructure evidence register

| Area | Repository evidence | Infrastructure evidence required |
| --- | --- | --- |
| Application | CI setup/build succeeds | Target deployment succeeds |
| Database | Migrations are committed | Provisioning, backups and restore |
| Queue | Jobs are committed/tested | Worker process is running |
| Scheduler | Scheduling code is committed | Scheduler is running |
| Cache | Application configuration exists | Cache service is reachable |
| Storage | Storage calls are covered by code | Bucket/disk policy and access verified |
| Mail | Notification/mail code is tested | Provider delivery verified |
| Payments | Webhook/idempotency code is tested | Provider credentials/webhooks verified |

Infrastructure-only claims must not be marked complete until evidence is attached to the release record.
