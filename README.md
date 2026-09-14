# afacere.online

> **Current status:** Phase 5 — Validation, operational readiness and launch gate — **Active**  
> **Product:** afacere.online Business Progression  
> **Stack:** Laravel 13, Filament 5, Livewire 4, PHP 8.4, Blade, Tailwind CSS, Vite, Pest  
> **Launch decision:** **NO-GO** pending production remediation and external/manual evidence.

## Product Definition

afacere.online is a business-progression platform for Romanian entrepreneurs. It helps a founder move from an unvalidated idea through validation, launch and operations toward a healthier, more measurable and more sustainable business.

The product is organized around one progression loop:

```text
Create business → Evaluate → Diagnose → Prioritize → Act
        ↑                                      ↓
        └──────── Monitor ← Progress ← Outcome ┘
```

The fundamental promise is: **help entrepreneurs understand what matters now, decide what to do next, and make measurable progress toward a healthier business and steadier cash flow.**

### Product Definition

afacere.online is:
- a business-context platform centered on a Business aggregate;
- a progression system connecting diagnosis to concrete action;
- a combination of deterministic domain workflows and assisted AI;
- an ecosystem connecting entrepreneurs with Guides, Opportunities, Experts, Marketplace providers, Community and Events;
- a longitudinal system preserving business history and progress.

### afacere.online is not
- a generic business-content portal;
- a business directory disguised as a product;
- a generic consulting agency delivered through software;
- a funding-only platform;
- a social network optimized for engagement;
- an AI chatbot presented as a business operating system;
- a pay-to-win provider marketplace;
- a collection of disconnected tools.

---

## Product Principles

1. **Action over information** — insights should lead to useful next steps.
2. **Business context first** — recommendations use stage, goals and history.
3. **Progress over scores** — evaluation exists to produce diagnosis and action.
4. **Explainability** — users should understand recommendations.
5. **Human judgment where it matters** — AI assists but does not replace professional judgment.
6. **User ownership** — AI suggestions remain suggestions until accepted, edited or rejected.
7. **Continuous improvement** — progress, outcomes and feedback improve future guidance.
8. **Low acquisition friction** — the Evaluator should create immediate value.
9. **Recurring value** — paid products should solve recurring problems, especially through Monitor.
10. **Contextual commerce** — commercial services appear because a business need exists.
11. **Historical integrity** — later rule changes must not silently rewrite history.
12. **Evidence over assertion** — implementation and operational verification are separate claims.

---

## Primary Actors

### Entrepreneur
The primary user who creates businesses, evaluates them, follows priorities and Action Plans, executes Guides, uses Opportunities, Experts and Marketplace services, participates in Community and Events, and monitors progress.

### Expert
A qualified human providing mentoring, consultation or specialist assistance. Only intentionally shared business context should be exposed.

### Marketplace Provider
A business or professional offering relevant services. Visibility must remain governed by relevance and trust, not payment alone.

### Community / Peer Reviewer
An entrepreneur or approved contributor participating in structured peer-review and community workflows.

### Platform Administrator
Internal staff operating the service. Administrative access is authorized and auditable where it affects user data, recommendations, commercial state or trust-sensitive records.

---

## Core Domain Model

The **Business** is the central aggregate.

### Identity & Access
User, Profile, Business Membership, Business Invitation, roles/permissions, authentication, email verification, password recovery, two-factor authentication, passkeys and notifications.

### Business
Business, Business Stage, Business Stage History, Business Profile/Context, Business Goal, Business Metric, Business Metric Value, Business Document and Business Preference.

### Evaluation & Planning
Evaluation, Evaluation Version, Section, Question, Answer, Finding, Recommendation, Priority, Action Plan, Action Plan Revision, Action, Evidence, Outcome and Progress Event.

### Guides & Opportunities
Guide, Guide Revision, Section, Step, Tag, Stage Assignment, Progress, Progress Event, Opportunity, Opportunity Type, Opportunity Application, Match and Opportunity Event.

### Experts & Marketplace
Expert, availability, consultation, consultation sharing, Marketplace Provider, Service, Provider Verification, Provider Review, Lead and transaction workflows.

### Community & Events
Community Post, Peer Review, Comments/Reactions, Moderation Report, Event, Event Type, Registration, Session and Attendance.

### Monitor
Monitor Configuration, Check-in, Threshold, Health Indicator, Alert and Periodic Summary.

### AI
AI Provider, Prompt, Prompt Version, AI Run, AI Recommendation, AI Feedback and AI Usage.

### Commerce
Product, Product Plan, Subscription, Subscription Event, Entitlement, Payment, Invoice, Transaction, Coupon/Promotion and Payment Webhook Event.

### Platform & Analytics
Domain Event, Platform Event, Audit Log, Analytics Event, Conversion, Metric Snapshot, Cohort Snapshot and Data Request/Lifecycle records.

These represent the explicit repository domain structures. Their presence in source code is not, by itself, proof of production verification.

---

## Core Business Lifecycle

The business lifecycle is separate from the development roadmap.

```text
Idea → Validation → Launch → Early Operations → Growth
     → Stable Business → Transformation / Exit
```

The application progression is:

```text
Business → Evaluation → Diagnosis → Priorities → Action Plan
→ Guide / Opportunity / Expert / Marketplace
→ Evidence / Outcome → Monitor → Reassessment
```

### Action lifecycle

```text
Recommended → Accepted → Active
                         ├→ Completed → Outcome
                         ├→ Skipped
                         └→ Blocked → Reassess / replace
```

### Lifecycle Rules

- Important transitions use explicit application/domain workflows.
- Business access is scoped through membership and authorization.
- Authorization is enforced server-side.
- Historical evaluations, plans, recommendations, stage changes and audit-sensitive records remain traceable.
- Actions can carry evidence and outcomes.
- Data requests, deletion, payment callbacks and other sensitive transitions use dedicated workflows where implemented.

---

## Methodology

The methodology is a business-progression model rather than a static scoring exercise:

```text
Current state → Diagnosis → Priority → Action → Outcome → New state
```

Evaluation dimensions include Problem & Customer, Offer & Value Proposition, Market & Competition, Business Model & Economics, Sales & Acquisition, Operations & Delivery, Team & Capability, Financial Health & Cash Flow, Risk & Resilience and Growth Readiness.

Evaluation structures are versioned. Recommendations connect findings and business context to concrete actions, using stage, goals, history, Guide and Opportunity context, Monitor signals and relevant ecosystem capabilities.

Deterministic authorization and eligibility remain server-side. AI may assist interpretation, ranking and explanation.

---

## Public Trust Model

### Authoritative Record

For implemented behavior, the canonical source is the repository and database schema. For code-quality validation, the authoritative source is the configured CI workflow and its actual results. For operational claims, the required manual/infrastructure evidence is authoritative.

### Public Record

Where public records exist, they should expose only intentionally public information and remain distinguishable from private business data. Trust-sensitive records may include stable identifiers, publication/verification state, relevant history and moderation state.

### Public Visibility Rules

- Business data is private by default.
- Experts and providers receive only intentionally shared context.
- Community visibility follows publication and moderation rules.
- UI visibility is never a security boundary.
- Historical records are not silently rewritten because current rules changed.

---

# Development Roadmap

The roadmap is product-oriented. Technical issues are implementation slices and do not replace phase numbering.

## Phase 0 — Product, Domain, Methodology & Architecture
**Status: Complete**

Established product boundaries, actors, business lifecycle, domain model, evaluation methodology, architecture, security principles, AI approach, commerce model and development governance.

## Phase 1 — Application Foundation & Core Domain Implementation
**Status: Complete**

Implemented identity/access, Business lifecycle, goals and metrics, Evaluation, recommendations, priorities, Action Plans, Guides, Opportunities, Monitor, AI foundations, notifications, auditability, analytics foundations and administration.

## Phase 2 — Entrepreneur Experience & Product Integration
**Status: Complete**

Integrated public discovery, onboarding, business dashboard, evaluation/diagnosis, recommendations, Action Plans, Guides, Opportunities, Monitor, notifications, subscriptions and cross-domain entrepreneur workflows.

## Phase 3 — Ecosystem, Monetization & Production-Readiness Foundations
**Status: Complete**

Implemented Experts, Marketplace, Community, Peer Review, Events, controlled document sharing, commerce, trust governance, unified discovery, analytics and production-readiness foundations.

## Phase 4 — Final Hardening & Release Preparation
**Status: Complete**

Completed cross-domain hardening, security/privacy implementation foundations, data lifecycle, observability/performance foundations, readiness services, operational runbooks, launch-checklist foundations and documentation reconciliation.

## Phase 5 — Validation, Operational Readiness & Launch Gate
**Status: Active**

Validates the accumulated implementation against repository, manual and infrastructure evidence. Scope covers ecosystem workflows, deployment, security/privacy, billing, data lifecycle, observability, performance, accessibility, end-to-end smoke testing, backup/restore, operations and final release audit.

Phase 5 issues:

- #103 — Ecosystem reachability and workflow completion
- #104 — Deployment and production validation
- #105 — Security, privacy and authorization audit
- #106 — Billing and payment validation
- #107 — Data lifecycle, privacy rights and retention
- #108 — Observability, monitoring, alerting and incident response
- #109 — Performance, scalability and resource behavior
- #110 — Accessibility, responsive UX and design-system audit
- #111 — End-to-end release and smoke-test coverage
- #112 — Backup, restore and disaster recovery
- #113 — Operations, support and administrative workflows
- #114 — Documentation reconciliation and launch checklist
- #115 — Final release-readiness audit and launch gate

Phase 5 is complete only when all launch blockers are cleared and the final audit records **GO**.

## Phase 6 — Post-Launch Evolution
**Status: Not formally defined**

No Phase 6 product scope is currently approved. It must not be invented or treated as active until the roadmap is formally extended.

---

# Current Reconciliation

| Original phase | Current status | Reconciliation |
| --- | --- | --- |
| Phase 0 | Complete | Product/domain/methodology/architecture foundations established. |
| Phase 1 | Complete | Core application and progression domains implemented. |
| Phase 2 | Complete | Entrepreneur-facing product flows integrated. |
| Phase 3 | Complete | Ecosystem, commerce and readiness foundations implemented. |
| Phase 4 | Complete | Hardening, documentation and release foundations completed. |
| Phase 5 | Active | Repository work is implemented, but external/manual/infrastructure gates remain pending or blocked. |
| Phase 6 | Not defined | No approved post-launch roadmap exists. |

### Reconciliation Rules

- The original product roadmap remains authoritative.
- Technical issue groupings are implementation slices.
- Completed technical work is mapped back to the product phase it serves.
- Partial implementation and partial validation are explicitly distinguished.
- Existing foundations must not be unnecessarily rebuilt.
- Source-code presence does not equal operational completion.

---

# Existing Implementation Milestones

### Foundation
Laravel 13, Filament 5, Livewire 4, PHP 8.4, Blade/Tailwind/Vite and Pest form the current application stack. Fortify provides authentication capabilities including two-factor authentication and passkeys.

### Core progression
The repository contains explicit Business, membership, stage, goals, metrics, Evaluation, Recommendation, Priority, Action Plan, action execution, Guide, Opportunity and Monitor models, migrations, services and tests.

### Ecosystem
Expert consultation, Marketplace, Community/Peer Review, Events, controlled document sharing, commerce and trust-governance domains are represented by dedicated repository structures.

### Operations
The repository includes data lifecycle/deletion services, audit logging, observability, performance monitoring, analytics, readiness/release services and operational documentation.

### Validation
Phase 5 changed release readiness from a source-code claim into an evidence-based gate. The final audit separates repository CI from target-environment and manual/infrastructure verification.

---

# Authoritative Specifications

- [`docs/index.md`](docs/index.md) — central documentation index.
- [`docs/project-status.md`](docs/project-status.md) — current phase and evidence classification.
- [`docs/launch-checklist.md`](docs/launch-checklist.md) — launch gate.
- [`docs/final-release-readiness.md`](docs/final-release-readiness.md) — final audit and GO/NO-GO decision.
- [`docs/privacy-data-lifecycle.md`](docs/privacy-data-lifecycle.md) — privacy/data lifecycle.
- [`docs/data-retention-policy.md`](docs/data-retention-policy.md) — retention.
- [`docs/security-audit.md`](docs/security-audit.md) — security audit.
- [`docs/observability.md`](docs/observability.md) — observability.
- [`docs/performance-scalability.md`](docs/performance-scalability.md) — performance/scalability.
- [`docs/operations/`](docs/operations/) — operational procedures and evidence templates.

When implementation conflicts with an approved specification: identify the conflict, determine the correct authority, document the decision, update the specification when the product decision changes, then implement the approved result.

---

# Technical Architecture

```text
Public / Authenticated UI
        ↓
Blade / Livewire / Filament
        ↓
Actions / Application Services
        ↓
Models / Policies / Contracts
        ↓
Eloquent / Database
```

The repository uses explicit Actions, Services, Contracts, Enums, Events, Policies and UI components. Cross-cutting capabilities such as AI, analytics, notifications, data lifecycle, commerce, observability and trust governance are kept behind dedicated application services where appropriate.

Representative services include BusinessContextService, BusinessMembershipService, BusinessGoalsMetricsService, EvaluationService, RecommendationService, ActionPlanService, GuideService, OpportunityService, OpportunityMatchingService, MonitorService, ExpertConsultationService, TransactionService, SubscriptionService, EntitlementService, PaymentWebhookService, AiService, AiGovernanceService, AnalyticsService, DataLifecycleService, DataDeletionService, DocumentAccessService, ObservabilityService, PerformanceMonitoringService, TrustGovernanceService, UnifiedSearchService and ReleaseReadinessService.

---

# AI Architecture

AI is an assistance layer, not the product itself.

```text
Domain / Application Service
          ↓
       AiService
          ↓
    Provider Contract
          ↓
   External AI Provider
```

The repository models prompts and versions, AI runs, recommendations, feedback and usage. AI output is treated as non-authoritative, should retain context where required, remains distinct from user decisions, and is validated before entering domain workflows. Core domain logic and authorization remain deterministic.

---

# Security & Authorization

Authentication uses Laravel Fortify and supports registration, login, email verification, password reset, two-factor authentication and passkeys.

Business access is scoped through membership and policies. Administrative access is separated from ordinary entrepreneur workflows. Sensitive operations such as data export/deletion, document access, payment callbacks and commercial state transitions are handled through server-side rules.

The repository contains explicit authorization policies and middleware. UI visibility is never treated as the security boundary.

Production edge/rate-limit/provider verification and final operational security sign-off remain Phase 5 requirements.

---

# Data & Historical Integrity

Historical structures include Business Stage History, Evaluation Versions/Findings, Action Plan Revisions, Action Evidence/Outcomes, Guide Revisions/Progress Events, Opportunity Events, Subscription/Payment Events, Audit/Domain Events, Analytics Snapshots and AI Runs.

The repository includes data-request handling, deletion services, deletion processing, retention configuration, lifecycle documentation and document/storage access controls.

Database deletion and object-storage deletion are separate concerns. Repository tests cannot substitute for proof that production objects were actually deleted.

Historical records should be versioned or snapshotted whenever later methodology, pricing, permission or source-data changes could otherwise make old records misleading.

---

# User Interfaces

### Public Website
Home, How It Works, About, Contact, FAQ, Pricing and Legal surfaces provide acquisition and trust.

### Authenticated Application
Onboarding, Business dashboard, Evaluation wizard/diagnosis, Recommendations, Action Plan, Guides/Guide Reader, Opportunities/Opportunity Reader, Monitor, Notifications and account/subscription settings form the entrepreneur experience.

### Administration
Filament is the principal back office. Workflow-sensitive operations use dedicated pages/actions where unrestricted CRUD would violate domain rules or obscure history.

### UX Principles
The current business state matters more than a generic module list. The primary product question is **“What should I do next?”**. Empty/loading/error states are explicit concerns. Browser accessibility and responsive verification remain Phase 5 evidence gates.

---

# Notifications & Background Processing

The repository contains notification infrastructure and Laravel job/command processing. `ProcessDataDeletions` provides operational processing for data deletion work.

Background workflows should be retryable and observable according to Laravel queue configuration and operational runbooks. Real alert delivery remains an infrastructure validation requirement.

---

# Observability & Operations

The repository contains observability, performance monitoring, production-readiness and release-readiness services. Analytics records events, conversions and snapshots; AI usage records consumption; product Monitor provides thresholds and alerts.

Operational runbooks cover production, deployment validation, billing, accessibility, observability, disaster recovery, support and release procedures. Documentation is not evidence that the corresponding production drill has occurred.

---

# Commerce

Products and Plans define offerings; Subscriptions and Entitlements control recurring access; Payments, Invoices, Transactions and Payment Webhook Events represent the transactional layer.

The commercial architecture can support recurring subscriptions, premium content/Guides, Expert consultations, Marketplace/provider revenue and transaction-based offerings. The current business target is **€5,000 MRR**, with recurring subscriptions as the principal scalable layer.

Repository-side billing/idempotency controls exist. Provider signatures, callbacks and reconciliation still require target-environment evidence.

---

# Testing Strategy

The project uses Pest with `tests/Unit` and `tests/Feature`. The test environment uses SQLite in-memory storage.

Testing covers authentication, business management, onboarding, dashboard behavior, evaluations, Action Plans, Guides, Opportunities, Experts, Marketplace, Community, Events, notifications, analytics, commerce, data lifecycle, security and release readiness.

Behavior changes should add or update regression coverage. Tests should verify important success/failure paths, authorization, validation and relevant database state without brittle implementation-detail assertions.

---

# Quality Gates

The repository contract is defined by `.github/workflows/tests.yml`, `composer.json` and tool configuration.

- PHP: `^8.4`
- Laravel: `^13.17`
- Filament: `^5.0`
- Livewire: `^4.1`
- Node.js in CI: `22`
- Pest: `^5.1`
- Larastan: `^3.9`
- Pint: `^1.27`

Required commands:

```text
composer lint:check
composer types:check
php artisan test
composer ci:check
```

CI runs on pushes to `main` and pull requests. It uses Ubuntu, PHP 8.4, Composer 2 and Node 22, runs `composer setup`, then `composer ci:check`. Setup includes dependency installation, environment/key setup, database migration, npm installation and the production asset build.

CI is green only when the configured quality pipeline passes. Unexecuted checks must never be represented as passing.

---

# Development Rules

1. Treat repository configuration and actual CI as authoritative.
2. Read the relevant issue and specification before implementation.
3. Do not invent unresolved business rules.
4. Preserve the business-progression model.
5. Enforce authorization and deterministic eligibility server-side.
6. Keep AI dependencies behind abstractions.
7. Preserve historical and audit-sensitive records.
8. Add/update tests for behavior changes.
9. Follow Laravel, Pint, PHPStan and Pest conventions.
10. Never weaken CI or suppress static-analysis findings to obtain a green result.
11. Keep changes narrowly scoped.
12. Do not use Filament as a second business-logic layer.
13. Review migrations, models, factories and tests together.
14. Review routes, controllers and authorization boundaries together.
15. Distinguish repository evidence from manual/infrastructure evidence.
16. Update documentation when implementation-significant decisions or validation state changes.
17. Never claim a quality or operational check passed without actual evidence.

The complete AI-assisted development contract is [`.github/AI_DEVELOPMENT_RULES.md`](.github/AI_DEVELOPMENT_RULES.md).

---

# Evidence Model

| Evidence class | Meaning | Examples |
| --- | --- | --- |
| **Repository** | Evidence in source/configuration/CI | Code, migrations, tests, GitHub Actions |
| **Manual** | Evidence requiring browser/human/external testing | Accessibility, user journeys, operator rehearsal, load tests |
| **Infrastructure** | Evidence from target environment | Deployment, secrets, queues, storage, provider callbacks, backups, restores, alert delivery |

Source-code presence is not manual or infrastructure evidence.

---

# Current Release & Launch Gate

The final audit in [`docs/final-release-readiness.md`](docs/final-release-readiness.md) reviewed release candidate `744eab6fcf2a9cabce28bce70227fcdbecf30861`. GitHub Actions CI was **GREEN** for that exact commit.

The target Railway deployment was reported **CRASHED**. The first meaningful runtime failure was inability to resolve the configured MySQL host (`mysql.railway.internal`). This is a target-environment service/configuration failure, not a GitHub CI failure.

### Launch blockers

1. Production deployment health and database connectivity.
2. Payment-provider signatures, callbacks and reconciliation.
3. Real object-storage deletion propagation.
4. Real monitoring/alert delivery and operational ownership.
5. Production-like load/performance evidence.
6. Browser accessibility/responsive evidence.
7. Successful isolated backup/restore drill and RPO/RTO approval.
8. Final production privacy/legal approval.

**Launch decision: NO-GO.** Documentation changes must not be used to convert missing operational evidence into a PASS.

---

# MVP Success Criteria

The MVP is successful when a real entrepreneur can create a business, complete an evaluation, receive an understandable diagnosis, identify priorities, accept and execute an Action Plan, use relevant Guides and Opportunities, obtain relevant Expert/Marketplace help, record progress and outcomes, receive useful Monitor signals, understand paid value, subscribe without operational friction, and trust that business data and history are controlled and auditable.

Repository implementation is necessary but not sufficient for launch.

---

# Documentation Index

- [`docs/index.md`](docs/index.md)
- [`docs/project-status.md`](docs/project-status.md)
- [`docs/launch-checklist.md`](docs/launch-checklist.md)
- [`docs/final-release-readiness.md`](docs/final-release-readiness.md)
- [`docs/operations/`](docs/operations/)

---

# Repository Structure

```text
app/
├── Actions/
├── Contracts/
├── Enums/
├── Events/
├── Filament/
├── Http/
├── Livewire/
├── Models/
├── Policies/
├── Providers/
└── Services/

database/
├── factories/
├── migrations/
└── seeders/

docs/
├── operations/
└── *.md

resources/
├── css/
├── js/
└── views/

tests/
├── Feature/
└── Unit/

.github/
├── AI_DEVELOPMENT_RULES.md
└── workflows/tests.yml
```

The repository also contains AI-agent development skills/configuration under `.agents/`, `.claude/` and `.github/skills/`.

---

# Final Status

**Implementation:** Phases 0–4 complete.  
**Validation:** Phase 5 active.  
**Repository CI:** Green for the reviewed release candidate.  
**Production readiness:** Not yet verified.  
**Launch:** **NO-GO** until the documented production, security/privacy, billing, data lifecycle, observability, performance, accessibility, backup/restore and operational gates are cleared.
