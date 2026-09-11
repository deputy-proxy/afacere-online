# afacere.online

## Application Plan

> **Status:** Phase 3 active · Ecosystem, monetization and production-readiness implementation underway
> **Product:** afacere.online Business Progression
> **Stack:** Laravel 13, Filament 5, Livewire 4, PHP 8.4
>
> This document is the high-level application blueprint and implementation roadmap. Important product, domain, architecture and business decisions are recorded in the repository and referenced documents. Those documents are authoritative for their respective subjects.

### Phase status

- **Phase 0 — Product, domain, methodology and architecture:** Complete
- **Phase 1 — Application foundation and domain implementation:** Complete
- **Phase 2 — Entrepreneur-facing experience and product integration:** Complete
- **Phase 3 — Ecosystem, monetization and production readiness:** Active

### Phase 1 completion

Phase 1 has been implemented and merged into `main`. The application foundation and core business domains required for the MVP are now in place, including identity and business access control, business lifecycle, evaluations, recommendations and priorities, action planning, Guides, Opportunities, Monitor, AI infrastructure, commerce and entitlements, notifications and auditability, analytics, and the internal Filament operations interface.

Phase 1 completion is subject to the repository's existing quality gates: lint/format validation, PHPStan/Larastan, automated tests, application setup/migrations, and frontend build validation must remain green for subsequent changes.

### Phase 2 completion

Phase 2 has been implemented and merged into `main`. The entrepreneur-facing progression is now integrated across public discovery, account/onboarding, business management, evaluation, diagnosis, priorities, Action Plans, execution, Guides, Opportunities, Monitor, subscriptions, notifications, analytics and cross-domain workflows.

Phase 2 completion was subject to the repository's quality gates: lint/format validation, PHPStan/Larastan, automated tests, application setup/migrations, frontend build validation, and a GREEN repository CI check.

### Phase 3

Phase 3 extends the completed entrepreneur progression into the broader afacere.online ecosystem. It focuses on Experts, Marketplace providers, Community and Peer Review, Events, secure business documents and evidence, transactional commerce, unified discovery, trust and moderation, product analytics, and final production-readiness hardening.

The Phase 3 implementation roadmap is tracked through repository issues **#80–#89**, following the naming convention `Phase 3.x — ...`.

Every Phase 3 issue is subject to the same mandatory quality gates: lint/format validation, PHPStan/Larastan, automated tests, frontend build validation, and a **GREEN repository CI check**. No Phase 3 issue is considered complete while any required CI check is red.

### Authoritative specifications

- [`README.md`](README.md) — high-level product blueprint, application structure and implementation roadmap.
- Repository issues — authoritative record of unresolved product, business and architecture decisions and the answers that govern implementation.

### Decision-recording rule

Important decisions made during development must be reflected in the relevant repository documentation or decision issue. The README remains the high-level project record; detailed decisions belong in the appropriate issue or specification. Documentation must be updated when an architectural, product, workflow, monetization, AI, security or other implementation-significant decision changes.

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

The platform exists to improve entrepreneurial decisions and execution. Information, AI, community, experts, opportunities and providers are means to that end.

---

## 2. Product Principles

The application must encode these principles in both UX and data architecture.

1. **Action over information** — every important insight should lead to a useful next step.
2. **Business context first** — recommendations should be grounded in the user's business, stage, goals and history.
3. **Progress over scores** — evaluation is valuable because it produces priorities and action, not because it produces a number.
4. **Explainability** — users should understand why a recommendation, opportunity, guide, expert or provider is being suggested.
5. **Human judgment where it matters** — AI assists analysis and execution but does not replace professional judgment when stakes or ambiguity require it.
6. **User ownership** — AI suggestions remain suggestions until the user accepts, edits or rejects them.
7. **Continuous improvement** — the system should learn from business progress, outcomes and feedback.
8. **Low acquisition friction** — the free Evaluator should create enough immediate value to establish trust before asking for payment.
9. **Recurring value** — paid recurring products must solve problems that return over time, especially through Monitor.
10. **Contextual commerce** — paid services and providers should appear because a business need exists, not because the platform needs inventory exposure.

---

## 3. Primary Actors

### 3.1 Entrepreneur

The primary user: a Romanian entrepreneur, founder or small-business owner.

Capabilities include creating businesses, completing evaluations, accepting and executing action plans, using Guides, tracking Opportunities, requesting Expert or Marketplace assistance, participating in Community, registering for Events, monitoring business progress and managing subscriptions.

### 3.2 Expert

A qualified human who provides mentoring, consultation or specialist assistance.

Expert participation should be structured around business needs and outcomes rather than generic profile browsing. Experts receive only the business context the user explicitly shares and only the access permitted by the relevant workflow.

### 3.3 Marketplace Provider

A business or professional offering services relevant to entrepreneurial needs.

Providers have profiles, services, verification information where applicable and lead or transaction capabilities. Provider visibility must not override relevance or recommendation quality.

### 3.4 Community / Peer Reviewer

An entrepreneur or approved contributor who participates in structured peer review.

Peer review exists to improve entrepreneurial decisions, not to create an unrestricted social network. Visibility and participation are controlled according to the sensitivity of the business information.

### 3.5 Platform Administrator

Internal staff operating the service. Administrators manage users, businesses, evaluations, content, opportunities, experts, providers, moderation, subscriptions, payments, events, AI configuration, recommendation rules and audit data.

Administrative capabilities must respect explicit workflow boundaries and must remain auditable where they affect user data, recommendations, commercial state or trust-sensitive records.

---

## 4. Core Domain Model

The application is built around the following bounded concepts. The Business is the central aggregate and other domains attach to it where meaningful.

### Identity & Access

- User
- Profile
- Organization / Team where applicable
- Membership
- Role
- Permission
- Notification

### Business

- Business
- Business Member
- Business Stage
- Business Profile
- Business Metric
- Business Metric Value
- Business Goal
- Business Document
- Business Preference

### Evaluation

- Evaluation
- Evaluation Version
- Evaluation Section
- Evaluation Answer
- Evaluation Finding
- Evaluation Score / Dimension Score where required
- Recommendation
- Priority

### Planning & Progress

- Action Plan
- Plan Item
- Milestone
- Task
- Action Evidence
- Progress Event
- Outcome

### Guides & Content

- Guide
- Guide Section
- Guide Step
- Template
- Checklist
- Resource
- Topic
- Tag

### Opportunities

- Opportunity
- Opportunity Type
- Opportunity Criteria
- Opportunity Application / User Opportunity
- Opportunity Match

Funding is one Opportunity type rather than a separate platform architecture.

### Marketplace

- Provider
- Provider Profile
- Service Category
- Service
- Service Package
- Provider Verification
- Provider Review
- Lead
- Transaction where applicable

### Experts & Consultation

- Expert
- Expert Profile
- Expertise
- Availability
- Consultation
- Mentoring Relationship
- Session
- Session Note
- Expert Recommendation

### Community & Peer Review

- Community Post
- Comment
- Reaction
- Peer Review
- Review Request
- Review Response
- Moderation Report

### Events

- Event
- Event Type
- Event Registration
- Event Session
- Event Attendance

### Monitor & Recurring Value

- Monitor Subscription / Configuration
- Check-in
- Health Indicator
- Threshold
- Alert
- Periodic Summary
- Trend Snapshot

### AI

- AI Provider
- Prompt / Prompt Version
- AI Run
- AI Recommendation
- AI Usage
- AI Feedback

### Commerce

- Product
- Product Plan
- Subscription
- Subscription Item
- Payment
- Invoice
- Coupon / Promotion
- Entitlement

### Analytics

- Event
- Funnel Event
- Conversion
- Cohort Snapshot
- Metric Snapshot

---

## 5. Core Lifecycle Rules

The workflow is explicit and state-driven. Important transitions require an authorized actor, validation rules, timestamps, audit entries and appropriate notifications.

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

A business may move between lifecycle stages without losing its history. Historical evaluations, plans, outcomes and important recommendations remain traceable.

### Business lifecycle

```text
Idea
  ↓
Validation
  ↓
Launch
  ↓
Early Operations
  ↓
Growth
  ↓
Stable Business
  ↓
Transformation / Exit
```

Stage changes should be based on explicit business signals and/or user confirmation rather than arbitrary calendar rules.

### Action lifecycle

```text
Recommended
  ↓
Accepted
  ↓
Active
  ├── Completed → Outcome recorded
  ├── Skipped
  └── Blocked → Reassess / replace
```

The platform should preserve why an action was skipped, blocked or replaced whenever that information is useful for future recommendations.

---

## 6. Evaluation & Recommendation Methodology

afacere.online uses a structured business evaluation to understand the current situation and determine what should happen next.

The evaluator is not primarily a scoring product. Scores and dimensions may be used internally where they improve diagnosis, but the user-facing outcome is a prioritized set of business problems, opportunities and actions.

Core diagnostic areas may include:

1. Problem & Customer
2. Offer & Value Proposition
3. Market & Competition
4. Business Model & Economics
5. Sales & Acquisition
6. Operations & Delivery
7. Team & Capability
8. Financial Health & Cash Flow
9. Risk & Resilience
10. Growth Readiness

The exact evaluation framework should remain versioned. In-progress evaluations must retain the version under which they were completed.

### Recommendation model

Recommendations should connect diagnosis to action.

Each recommendation should contain, where applicable:

- reason;
- priority;
- recommended action;
- expected outcome;
- source/context;
- target object;
- confidence where useful;
- status;
- created timestamp;
- accepted/rejected state.

The system should distinguish between:

- system-generated recommendations;
- user-confirmed decisions;
- actions completed and supported by evidence.

---

## 7. Public Website & Discovery

Core public pages:

- Home
- How It Works
- Evaluator
- For Entrepreneurs
- Guides
- Opportunities
- Funding
- Experts
- Marketplace
- Community
- Events
- Pricing
- About
- FAQ
- Contact
- Legal pages

Public discovery should be useful without requiring an account wherever practical. Account creation should happen when persistence, personalization or business-specific output requires it.

The public website is an acquisition and trust layer. The authenticated application is the progression layer.

---

## 8. Entrepreneur Application

The entrepreneur-facing application is a separate experience from the internal operations interface, even if both are implemented in the same Laravel application.

The dashboard centers on the user's current business state rather than on a generic list of modules.

Core areas include:

- business overview;
- evaluation and latest diagnosis;
- current priorities;
- active Action Plan;
- recommended Guides;
- matched Opportunities;
- relevant Experts and Marketplace providers;
- Community / Peer Review activity;
- Events;
- Monitor;
- subscription and account management.

The primary home-screen question should always be:

> **What should I do next?**

---

## 9. Internal Filament Application

Filament is the primary operations back office.

### Platform

- Dashboard
- Users
- Roles / Permissions
- Subscriptions
- Payments
- Notifications

### Businesses

- Businesses
- Business Stages
- Evaluations
- Findings
- Action Plans
- Metrics
- Goals

### Content

- Guides
- Guide Sections / Steps
- Templates
- Topics
- Resources

### Opportunities

- Opportunities
- Opportunity Types
- Criteria
- Matching Rules
- Applications / User Opportunities

### Marketplace

- Providers
- Services
- Categories
- Leads
- Reviews
- Verification

### Experts

- Experts
- Expertise
- Availability
- Consultations
- Mentoring Relationships
- Sessions

### Community

- Posts
- Peer Reviews
- Reports
- Moderation

### Events

- Events
- Sessions
- Registrations
- Attendance

### AI & Recommendations

- AI Providers
- Prompts / Versions
- AI Runs
- Recommendations
- Usage / Cost
- Feedback

### System

- Settings
- Audit Log
- Analytics

Not every model needs a generic CRUD resource. Workflow-specific pages should be used where uncontrolled editing would violate domain rules or obscure the user's business history.

---

## 10. AI Architecture

AI is an assistance layer, not the product itself.

AI should support:

- evaluation interpretation;
- finding explanations;
- recommendation generation;
- Action Plan generation;
- Guide personalization;
- opportunity matching;
- provider matching;
- consultation preparation;
- periodic business summaries;
- anomaly detection support;
- content generation for internal workflows;
- semantic search.

### AI rules

1. Never present AI output as guaranteed business advice.
2. Preserve the source/context behind important recommendations where possible.
3. Store important AI outputs for auditability and later analysis.
4. Version prompts and relevant model configuration.
5. Separate AI-generated suggestions from user-confirmed decisions.
6. Allow users to reject, edit or accept recommendations.
7. Do not build core domain logic around one AI vendor.
8. Track AI usage and cost.
9. Use deterministic business rules where deterministic rules are sufficient.
10. Prefer structured outputs that can be validated before entering the domain workflow.

### AI abstraction

Create an application-level AI service interface so model providers can be changed without rewriting domain logic.

Conceptually:

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

## 11. Recommendation, Matching & Personalization

The recommendation engine is one of the most important architectural components.

Recommendations should be generated from:

- evaluation findings;
- business stage;
- business profile;
- active goals;
- previous actions;
- completed Guides;
- opportunity eligibility;
- user preferences;
- available Experts;
- Marketplace services;
- historical behavior;
- Monitor signals and outcomes.

Matching should cover at least:

- Guide relevance;
- Opportunity eligibility;
- Expert relevance;
- Provider relevance;
- Event relevance.

The engine should support deterministic filters first and AI-assisted ranking where appropriate. Business eligibility rules should not be delegated to an unconstrained language model.

Recommendations must be explainable enough that users understand **why the platform is telling them to do something**.

---

## 12. Peer Review, Community & Events

### Peer review

Peer review is a core differentiator of the platform.

MVP capabilities:

- publish an idea/question for review;
- structured review request;
- receive responses;
- mark useful responses;
- report abuse;
- moderation;
- optionally keep sensitive business information private.

The system should support controlled visibility:

- private;
- selected reviewers;
- community;
- anonymized.

### Events

Events support acquisition, community and monetization.

Capabilities:

- event catalogue;
- online/offline location;
- date/time;
- capacity;
- registration;
- waitlist;
- attendance;
- speakers/experts;
- related business topics;
- related Guides/Opportunities;
- paid/free status.

Community and events should connect back to businesses and user goals rather than becoming disconnected content channels.

---

## 13. Authorization & Security

Authorization must be policy-driven and scoped to the business context.

Business data belongs to the user and/or team explicitly associated with that business. Access must follow least-privilege principles.

Experts and Providers access only the business information that the user has intentionally shared through the relevant workflow.

Community content requires separate visibility and moderation controls.

Administrators have broader operational access, but sensitive transitions remain explicit and auditable.

Do not rely on hidden UI controls as the security boundary. Authorization must be enforced server-side.

Business documents and user-provided content must be treated as private by default unless intentionally published or shared.

---

## 14. Commerce & Subscription

The business model is designed around a low-friction free entry point and recurring value.

The commercial architecture must support:

- free access;
- monthly subscription;
- annual subscription;
- one-time purchases where appropriate;
- paid consultation;
- provider subscriptions;
- Marketplace lead/transaction revenue;
- event revenue;
- premium Guides.

### Entitlements

Subscription capabilities should be represented through configurable entitlements rather than hard-coded screens or roles.

Examples:

- number of businesses;
- evaluation depth/history;
- Monitor access;
- AI usage limits;
- premium Guides;
- opportunity matching depth;
- community capabilities;
- Expert access;
- reporting;
- exports;
- team members.

The primary commercial target is **€5,000 MRR**. Recurring subscriptions are the principal scalable revenue layer; services, providers and events are supporting monetization channels.

The exact pricing remains configurable until validated through actual demand.

---

## 15. Implementation Roadmap

### Phase 0 — Product & Architecture

**Status: Complete.**

Completed:

- product definition;
- target users and actors;
- business progression methodology;
- lifecycle model;
- domain model;
- architecture decisions;
- security and authorization principles;
- AI architecture principles;
- commerce model;
- analytics principles;
- repository decision-recording process.

### Phase 1 — Application Foundation & Core Domains

**Status: Complete.**

Phase 1 delivered the application foundation and the core domain infrastructure required to operate the MVP:

1. application foundation;
2. identity, profiles and business access control;
3. business core domain and lifecycle;
4. goals and metrics;
5. versioned evaluations;
6. recommendations and priorities;
7. action plans and execution history;
8. Guides and content foundation;
9. Opportunities and matching/application tracking;
10. Monitor and recurring progress infrastructure;
11. AI abstraction, auditable runs, recommendations, feedback and usage tracking;
12. subscriptions, plans, entitlements, payments and invoice lifecycle foundation;
13. domain events, notifications and auditability;
14. analytics events, conversions and snapshots;
15. Filament operations/admin foundation;
16. testing, static analysis and CI quality gates.

Phase 1 is closed. Changes to completed Phase 1 domains should be treated as incremental improvements, bug fixes or extensions and should not reopen the phase unless a material architectural decision requires it.

### Phase 3 — Ecosystem, Monetization & Production Readiness

**Status: Active.**

Phase 3 extends the entrepreneur progression into the broader platform ecosystem and commercial layer.

Expected focus areas include:

- Expert discovery, matching and consultation workflows;
- Marketplace provider onboarding, verification and service workflows;
- Community and structured Peer Review;
- Events, registration and participation;
- business documents, evidence and secure sharing;
- transactional commerce for consultations, services and paid events;
- advanced search and unified discovery;
- trust, moderation and operational governance;
- product analytics, funnel intelligence and outcome measurement;
- end-to-end integration, UX hardening and production readiness.

Phase 3 implementation is tracked through issues **#80–#89**. Every issue must satisfy the repository's lint/format, PHPStan/Larastan, automated test, application setup/migration, frontend build and **GREEN CI** requirements before it is considered complete.

### Phase 2 — Entrepreneur Experience & Product Integration

**Status: Planned.**

Phase 2 will turn the completed domain foundation into the coherent entrepreneur-facing product experience.

Expected focus areas include:

- authenticated entrepreneur dashboard;
- business onboarding;
- evaluation UX and result presentation;
- priority and Action Plan experience;
- Guide consumption;
- Opportunity discovery and application flows;
- Monitor experience and recurring engagement;
- subscription and entitlement UX;
- notification experience;
- AI-assisted user workflows with explicit confirmation boundaries;
- product-level navigation and information architecture;
- public-to-authenticated acquisition funnel integration;
- end-to-end product journeys and integration tests.

Phase 2 should preserve the Phase 1 principles: business context first, deterministic domain rules, server-side authorization, auditability, user ownership of AI decisions, and measurable progress over feature volume.

---

## 16. Technical Quality Standards

Every meaningful implementation change must preserve the project's quality gates.

Required checks include:

- Laravel/PHP coding standards through Pint;
- PHPStan/Larastan static analysis;
- Pest automated tests;
- database migration/setup validation;
- frontend dependency/build validation where applicable;
- authorization and domain-rule tests for workflow-sensitive changes.

CI must be green before a change is considered complete.

Business rules must live in appropriate domain/application services and policies rather than being duplicated across controllers, Livewire components or Filament resources.

Historical and audit-sensitive records must remain traceable. Destructive or state-changing operations should be explicit, authorized and tested.

---

## 17. Development Rules

1. Read the relevant issue/specification before implementing a domain change.
2. Treat answered repository issues as authoritative business decisions.
3. Do not silently invent business rules when an important decision is unresolved.
4. Keep deterministic eligibility and authorization rules deterministic.
5. Keep AI provider dependencies behind application abstractions.
6. Persist important AI runs and distinguish suggestions from confirmed decisions.
7. Keep user/business data private by default.
8. Enforce authorization server-side.
9. Preserve history for evaluations, action plans, recommendations, commercial state and audit-sensitive records.
10. Add or update tests with domain behavior changes.
11. Run and satisfy Pint, PHPStan/Larastan and Pest before declaring work complete.
12. Do not use Filament as a second business-logic layer.
13. Prefer small, reviewable changes that preserve the integrity of completed domains.
14. Update documentation when implementation-significant decisions change.

---

## 18. MVP Success Criteria

The MVP is successful when a real entrepreneur can:

1. create a business;
2. complete an evaluation;
3. receive an understandable diagnosis;
4. see what matters most now;
5. accept a prioritized action plan;
6. execute actions and record evidence/outcomes;
7. use relevant Guides and Opportunities;
8. return later and see measurable progress;
9. receive useful recurring Monitor signals;
10. understand when and why paid functionality is valuable;
11. subscribe without operational friction;
12. trust that their data and decisions are controlled and auditable.

The primary commercial objective remains **€5,000 MRR**, but product validation should prioritize genuine user progress and recurring value rather than optimizing prematurely for revenue alone.
