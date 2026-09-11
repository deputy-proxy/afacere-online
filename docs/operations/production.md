# Production deployment and operations

## Environment boundaries

- `local` is for development and disposable data.
- `staging` mirrors production configuration and integrations with non-production credentials.
- `production` uses dedicated secrets, persistent storage, managed database infrastructure and monitored queues.

Never commit `.env` files or production credentials. Provision secrets through the deployment platform's secret store.

## Deployment order

1. Build the application and frontend assets.
2. Provision or update infrastructure and environment variables.
3. Put the application in maintenance mode when the migration requires exclusive access.
4. Run `php artisan migrate --force`.
5. Restart queue workers and long-running processes.
6. Warm application caches where the deployment platform supports it.
7. Verify `/up` and the application readiness checks.
8. Run the release smoke checklist before removing maintenance mode.

## Rollback

Application code must be deployable independently from database changes. Prefer additive migrations first. Do not deploy destructive schema changes in the same release as code that still depends on the old schema.

If a release fails, stop new traffic, preserve logs and deployment metadata, restore the previous application artifact, and follow the database rollback procedure documented by the hosting provider. Database restoration is preferred to ad-hoc destructive rollback when data integrity is at risk.

## Backups

Production database backups must be automated by the hosting provider or database service. At least one recent backup must be retained outside the primary failure domain. Restore drills must be performed before launch and after material infrastructure changes.

## Release verification

The repository CI workflow is the authoritative code-quality gate. A deployment is not considered releasable until CI is green and the post-deployment smoke checks succeed.
