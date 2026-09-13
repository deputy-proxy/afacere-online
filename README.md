# afacere.online

## Application Plan

> **Status:** Phase 5 active · Validation, operational readiness and launch gate
> **Product:** afacere.online Business Progression
> **Stack:** Laravel 13, Filament 5, Livewire 4, PHP 8.4
>
> This document is the high-level product blueprint and implementation roadmap. It describes repository implementation state separately from production and operational evidence. Detailed operational procedures and validation evidence are maintained under [`docs/`](docs/).

### Current phase status

- **Phase 0 — Product, domain, methodology and architecture:** Complete
- **Phase 1 — Application foundation and domain implementation:** Complete
- **Phase 2 — Entrepreneur-facing experience and product integration:** Complete
- **Phase 3 — Ecosystem, monetization and production readiness:** Complete
- **Phase 4 — Final hardening and release preparation:** Complete
- **Phase 5 — Validation, operational readiness and launch gate:** Active

Phases 0–4 describe implemented and integrated product capabilities. Phase 5 is a validation gate. A source-code implementation is not, by itself, evidence that a production deployment, external provider, backup/restore drill, accessibility review, load test or other infrastructure/manual control has been operationally verified.

### Authoritative documentation

- [`README.md`](README.md) — product definition, architecture, roadmap and repository-level status.
- [`docs/index.md`](docs/index.md) — central documentation index and operational runbooks.
- [`docs/project-status.md`](docs/project-status.md) — current phase status and evidence classification.
- [`docs/launch-checklist.md`](docs/launch-checklist.md) — single launch gate and launch-blocking criteria.
- [`docs/final-release-readiness.md`](docs/final-release-readiness.md) — final release audit and GO/NO-GO decision.
- Repository issues — authoritative record of unresolved implementation decisions and validation work.

Important implementation-significant decisions must be reflected in the relevant documentation or decision issue. Operational claims must identify the evidence class required to support them.

---

## 1. Product Definition

afacere.online is a business progression platform for Romanian entrepreneurs that helps them move from an **unvalidated idea to a viable business and then toward steady cash flow**.

A user creates a business workspace, evaluates its current situation, receives priorities and recommended actions, executes those actions with the help of practical Guides, Opportunities, Experts and Marketplace providers, and returns to measure progress through Monitor.

The fundamental product loop is:

```text
Create business
  ↓
Evaluate
  ↓
Prioritize
  ↓
Act
  ↓
Record progress
  ↓
Return
```

### The fundamental promise

**Help entrepreneurs understand what matters now, decide what to do next, and make measurable progress toward a healthier business and steadier cash flow.**

### What afacere.online is not

- Not a generic business-content portal.
- Not a business directory disguised as a product.
- Not a generic consulting agency delivered through software.
- Not a funding-only platform.
- Not a social network built for engagement metrics rather than useful outcomes.
- Not an AI chatbot pretending to be a business operating system.
- Not a marketplace that recommends providers simply because they paid for visibility.
- Not a collection of unrelated tools without a coherent business progression.

Information, AI, community, experts, opportunities and providers are means to improve entrepreneurial decisions and execution.

---

## 2. Product Principles

1. **Action over information** — important insights should lead to useful next steps.
2. **Business context first** — recommendations are grounded in business stage, goals and history.
3. **Progress over scores** — evaluation exists to produce diagnosis, priorities and action.
4. **Explainability** — users should understand why something is recommended.
5. **Human judgment where it matters** — AI assists analysis and execution but does not replace professional judgment.
6. **User ownership** — AI suggestions remain suggestions until accepted, edited or rejected.
7. **Continuous improvement** — the system should learn from progress, outcomes and feedback.
8. **Low acquisition friction** — the free Evaluator should create immediate value before payment is requested.
9. **Recurring value** — paid recurring products should solve problems that return over time, especially through Monitor.
10. **Contextual commerce** — paid services and providers should appear because a business need exists.

---

## 3. Primary Actors

### Entrepreneur

The primary user: a Romanian entrepreneur, founder or small-business owner. Capabilities include business management, evaluation, action planning and execution, Guides, Opportunities, Experts, Marketplace, Community, Events, Monitor and subscriptions.

### Expert

A qualified human providing mentoring, consultation or specialist assistance. Experts receive only the business context intentionally shared through the relevant workflow.

### Marketplace Provider

A business or professional offering services relevant to entrepreneurial needs. Provider visibility must not override relevance or recommendation quality.

### Community / Peer Reviewer

An entrepreneur or approved contributor participating in structured peer review. Participation and visibility are controlled according to business-information sensitivity.

### Platform Administrator

Internal staff operating the service. Administrative capabilities remain bounded, authorized and auditable where they affect user data, recommendations, commercial state or trust-sensitive records.

---

## 4. Core Domain Model

The Business is the central aggregate. Other capabilities attach to it where meaningful.

### Identity & Access

User, Profile, Organization/Team, Membership, Role, Permission, Notification.

### Business

Business, Business Member, Business Stage, Business Profile, Business Metric, Business Metric Value, Business Goal, Business Document, Business Preference.

### Evaluation & Planning

Evaluation, Evaluation Version, Evaluation Section, Evaluation Answer, Evaluation Finding, Evaluation Score/Dimension Score, Recommendation, Priority, Action Plan, Plan Item, Milestone, Task, Action Evidence, Progress Event, Outcome.

### Guides & Opportunities

Guide, Guide Section, Guide Step, Template, Checklist, Resource, Topic, Tag, Opportunity, Opportunity Type, Opportunity Criteria, Opportunity Application/User Opportunity, Opportunity Match.

### Marketplace & Experts

Provider, Provider Profile, Service Category, Service, Service Package, Provider Verification, Provider Review, Lead, Transaction; Expert, Expert Profile, Expertise, Availability, Consultation, Mentoring Relationship, Session, Session Note, Expert Recommendation.

### Community & Events

Community Post, Comment, Reaction, Peer Review, Review Request, Review Response, Moderation Report; Event, Event Type, Event Registration, Event Session, Event Attendance.

### Monitor, AI, Commerce & Analytics

Monitor Subscription/Configuration, Check-in, Health Indicator, Threshold, Alert, Periodic Summary, Trend Snapshot; AI Provider, Prompt/Prompt Version, AI Run, AI Recommendation, AI Usage, AI Feedback; Product, Product Plan, Subscription, Subscription Item, Payment, Invoice, Coupon/Promotion, Entitlement; Event, Funnel Event, Conversion, Cohort Snapshot, Metric Snapshot.

---

## 5. Core Lifecycle Rules

The workflow is explicit and state-driven. Important transitions require an authorized actor, validation, timestamps, audit entries and appropriate notifications.

```text
Business / Idea
  ↓
Evaluator
  ↓
Diagnosis
  ↓
Priorities
  ↓
Action Plan
  ↓
Action / Guide / Expert / Opportunity / Marketplace
  ↓
Progress & Evidence
  ↓
Monitor
  ↓
New signals / reassessment
  ↺
```

A business may move between lifecycle stages without losing history. Historical evaluations, plans, outcomes and important recommendations remain traceable.

### Business lifecycle

```text
Idea → Validation → Launch → Early Operations → Growth → Stable Business → Transformation / Exit
```

Stage changes are based on explicit business signals and/or user confirmation rather than arbitrary calendar rules.

### Action lifecycle

```text
Recommended → Accepted → Active
                         ├── Completed → Outcome recorded
                         ├── Skipped
                         └── Blocked → Reassess / replace
```

---

## 6. Evaluation & Recommendation

The evaluator is not primarily a scoring product. It produces an understandable diagnosis and a prioritized set of business problems, opportunities and actions.

Core diagnostic areas include Problem & Customer, Offer & Value Proposition, Market & Competition, Business Model & Economics, Sales & Acquisition, Operations & Delivery, Team & Capability, Financial Health & Cash Flow, Risk & Resilience, and Growth Readiness.

Evaluation frameworks are versioned. Recommendations connect diagnosis to action and distinguish system-generated suggestions, user-confirmed decisions and completed actions supported by evidence.

The recommendation engine uses business context such as findings, stage, profile, goals, previous actions, Guide history, opportunity eligibility, preferences, Experts, Marketplace services, behavior, Monitor signals and outcomes. Deterministic eligibility and authorization remain server-side; AI may assist ranking and explanation.

---

## 7. Public Website & Discovery

Core public areas include Home, How It Works, Evaluator, For Entrepreneurs, Guides, Opportunities, Funding, Experts, Marketplace, Community, Events, Pricing, About, FAQ, Contact and Legal pages.

Public discovery is an acquisition and trust layer. The authenticated application is the progression layer. Account creation is required when persistence, personalization or business-specific output requires it.

---

## 8. Entrepreneur Application

The entrepreneur-facing experience centers on the current business state rather than a generic module list.

Core areas include business overview, evaluation and diagnosis, priorities, Action Plan, Guides, Opportunities, Experts and Marketplace, Community/Peer Review, Events, Monitor, subscription and account management.

The primary home-screen question is:

> **What should I do next?**

---

## 9. Internal Operations

Filament is the primary operations back office. It covers platform administration, businesses, evaluations, action plans, content, opportunities, Marketplace, Experts, Community, Events, AI/recommendations, subscriptions/payments, analytics, settings and audit data.

Not every model is exposed as unrestricted CRUD. Workflow-specific pages are used where uncontrolled editing could violate domain rules or obscure business history. Server-side authorization is the security boundary.

---

## 10. AI Architecture

AI is an assistance layer, not the product itself. It supports evaluation interpretation, finding explanations, recommendations, Action Plans, Guide personalization, matching, consultation preparation, periodic summaries, anomaly-detection support, internal content workflows and semantic search.

Important AI rules:

1. AI output is not guaranteed business advice.
2. Important recommendations retain source/context where practical.
3. Important AI outputs are stored for auditability.
4. Prompts and relevant configuration are versioned.
5. AI suggestions remain separate from user-confirmed decisions.
6. Users can reject, edit or accept recommendations.
7. Core domain logic is not coupled to one AI vendor.
8. AI usage and cost are tracked.
9. Deterministic business rules remain deterministic.
10. Structured AI output is validated before entering domain workflows.

The application-level abstraction is conceptually:

```text
Domain service
    ↓
AI application service
    ↓
Provider abstraction
    ↓
OpenAI / other provider
```

---

## 11. Security, Privacy & Authorization

Authorization is policy-driven and scoped to business context. Business data is private by default. Experts and Providers receive only information intentionally shared through the relevant workflow. Community content has separate visibility and moderation controls. Administrative access is broader but remains authorized and auditable.

Sensitive operations such as data export, deletion, payment callbacks and commercial state transitions require explicit server-side rules. Operational readiness of these controls is validated separately from source-code implementation during Phase 5.

---

## 12. Commerce & Subscription

The commercial architecture supports free access, monthly and annual subscriptions, appropriate one-time purchases, paid consultations, provider subscriptions, Marketplace lead/transaction revenue, event revenue and premium Guides.

Entitlements are configurable and enforced server-side rather than being inferred from UI visibility. The primary commercial target remains **€5,000 MRR**, with recurring subscriptions as the principal scalable revenue layer.

Pricing remains configurable until validated through actual demand.

---

## 13. Implementation Roadmap & Completion State

### Phase 0 — Product & Architecture

**Complete.** Product definition, target actors, progression methodology, lifecycle model, domain model, architecture, security principles, AI architecture, commerce model, analytics principles and decision-recording process were established.

### Phase 1 — Application Foundation & Core Domains

**Complete.** The application foundation and core domains are implemented and integrated, including identity and access control, business lifecycle, goals and metrics, versioned evaluations, recommendations and priorities, Action Plans, Guides, Opportunities, Monitor, AI infrastructure, commerce and entitlements, notifications, auditability, analytics and the internal operations interface.

The former `phase1-remaining-domains.md` document is retained as historical Phase 1 design context. It does not represent outstanding implementation work.

### Phase 2 — Entrepreneur Experience & Product Integration

**Complete.** The entrepreneur-facing progression is integrated across public discovery, account/onboarding, business management, evaluation, diagnosis, priorities, Action Plans, execution, Guides, Opportunities, Monitor, subscriptions, notifications, analytics and cross-domain workflows.

### Phase 3 — Ecosystem, Monetization & Production Readiness

**Complete.** The broader ecosystem and commercial layer has been implemented through the Phase 3 issue series, including Experts, Marketplace providers, Community and Peer Review, Events, secure business documents/evidence, transactional commerce, unified discovery, trust/moderation and production-readiness foundations.

### Phase 4 — Final Hardening & Release Preparation

**Complete.** Final hardening, integration, release preparation and operational documentation foundations have been implemented. Phase 4 is closed; subsequent findings are handled as validation or release work rather than reopening the completed phase by default.

### Phase 5 — Validation, Operational Readiness & Launch Gate

**Active.** Phase 5 validates the implementation accumulated through Phases 0–4 against repository, manual and infrastructure evidence requirements.

The Phase 5 validation series is **#103–#115**:

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

Every executable-code or CI change remains subject to the repository's quality gates. Documentation-only changes are not represented as executable validation. Phase 5 may only declare operational capabilities complete when the evidence required by their risk has been recorded.

---

## 14. Evidence Model

Phase 5 uses three evidence classes:

| Evidence class | Meaning | Examples |
| --- | --- | --- |
| **Repository** | Evidence visible in the repository and CI | Source, migrations, tests, configuration, GitHub Actions |
| **Manual** | Evidence requiring a browser, human workflow or external test | Accessibility, real user journeys, operator procedures, load tests |
| **Infrastructure** | Evidence from the target operational environment | Deployment, secrets, queues, storage, provider callbacks, backups, restore drills, alert delivery |

Source-code presence must not be presented as infrastructure or manual evidence.

---

## 15. Launch Gate

The single launch gate is [`docs/launch-checklist.md`](docs/launch-checklist.md). The current release-readiness assessment is [`docs/final-release-readiness.md`](docs/final-release-readiness.md).

The release is **NO-GO** while any critical blocker remains unresolved, including red CI on the exact release candidate, unresolved critical security/privacy issues, unverified payment integrity, unresolved data-loss risk, broken critical user journeys, authentication/authorization bypasses, missing backup/restore evidence or missing target-environment deployment verification.

Known limitations that do not block launch must be recorded with an owner, status, mitigation and review date. A pending evidence item is not silently promoted to complete merely because the corresponding code exists.

---

## 16. Technical Quality Standards

Every meaningful implementation change must preserve:

- Laravel/PHP coding standards through Pint;
- PHPStan/Larastan static analysis;
- Pest automated tests;
- database migration/setup validation;
- frontend dependency/build validation where applicable;
- authorization and domain-rule tests for workflow-sensitive changes.

CI must be green before an executable or CI change is considered complete. Business rules belong in appropriate domain/application services and policies rather than being duplicated across controllers, Livewire components or Filament resources. Historical and audit-sensitive records remain traceable.

---

## 17. Development Rules

1. Read the relevant issue/specification before implementing a domain change.
2. Treat answered repository issues as authoritative business decisions.
3. Do not silently invent unresolved business rules.
4. Keep deterministic eligibility and authorization rules deterministic.
5. Keep AI provider dependencies behind application abstractions.
6. Persist important AI runs and distinguish suggestions from confirmed decisions.
7. Keep user/business data private by default.
8. Enforce authorization server-side.
9. Preserve history for evaluations, action plans, recommendations, commercial state and audit-sensitive records.
10. Add or update tests with domain behavior changes.
11. Run and satisfy the required repository quality gates before declaring executable work complete.
12. Do not use Filament as a second business-logic layer.
13. Prefer small, reviewable changes that preserve completed domains.
14. Update documentation when implementation-significant decisions or validation status changes.

---

## 18. MVP Success Criteria

The MVP is successful when a real entrepreneur can create a business, complete an evaluation, receive an understandable diagnosis, identify priorities, accept and execute an Action Plan, use relevant Guides and Opportunities, record progress, receive useful Monitor signals, understand paid value, subscribe without operational friction and trust that their data and decisions are controlled and auditable.

The primary commercial objective remains **€5,000 MRR**, but product validation should prioritize genuine user progress and recurring value rather than premature revenue optimization.
