# Release and launch procedure

## Pre-release gates

1. Review the complete accumulated diff.
2. Confirm every release-blocking issue has an implementation and regression coverage.
3. Run the repository CI workflow through GitHub Actions.
4. Require a green result for Pint, PHPStan/Larastan, automated tests, application setup/migrations and frontend build.
5. Verify production secrets are configured outside source control.
6. Verify backups and restore procedures have been exercised.
7. Verify payment and notification integrations in a production-like environment.
8. Complete the browser smoke and accessibility checklist.

## Deployment

- Deploy the exact artifact represented by the reviewed commit.
- Run additive database migrations before enabling traffic that depends on them.
- Restart workers and schedulers after deployment.
- Check `/up` and `/health/ready`.
- Execute the critical public, authenticated and commercial smoke journeys.
- Keep the deployment identifier and timestamps with the release record.

## Rollback decision

Rollback application code when the failure is isolated to the new artifact and the previous artifact is known good. Stop and investigate instead of blindly rolling back when the incident involves irreversible data writes or schema changes. Restore from a verified backup when data integrity is at risk.

## Launch-day monitoring

Watch availability, error rate, slow queries, queue failures, payment webhook failures, notification failures and critical user journeys. Escalate immediately when business data integrity, payment correctness, authentication or privacy boundaries are affected.

## Post-launch stabilization

For the first stabilization window, record incidents and customer-impacting defects against the release. Prioritize data integrity, security, payments, availability and broken critical journeys before cosmetic or low-impact improvements.
