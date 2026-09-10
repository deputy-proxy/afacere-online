# afacere.online

## Application Plan

> **Status:** Phase 1 in progress · Application foundation established
> **Product:** afacere.online Business Progression
> **Stack:** Laravel 13, Filament 5, Livewire 4, PHP 8.4
>
> This document is the high-level application blueprint and implementation roadmap. Important product, domain, architecture and business decisions are recorded in the repository and referenced documents. Those documents are authoritative for their respective subjects.

### Phase status

- **Phase 0 — Product, domain, methodology and architecture:** Complete
- **Phase 1 — Application foundation and domain implementation:** In progress

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

- Product refactoring around the business progression loop
- Core promise and product principles
- Primary actor definitions
- Business as the central aggregate
- Business lifecycle definition
- Evaluator → Priorities → Action Plan progression
- Guide / Opportunity / Expert / Marketplace relationship to business needs
- Monitor as the recurring-value layer
- Opportunity engine with funding as a vertical
- Recommendation and matching architecture
- AI assistance boundaries and provider abstraction
- Peer review and community purpose
- Event role in acquisition, community and monetization
- Subscription and entitlement strategy
- €5,000 MRR commercial objective
- Domain model and bounded concepts
- Authorization and privacy boundaries
- Repository decision-recording workflow

Authoritative outputs:

- `README.md`
- repository decision issues

### Phase 1 — Application Foundation & Domain Implementation

**Status: In progress.**

Current focus:

- application foundation and configuration
- domain models and enums
- Business and Business Profile persistence
- business stage and lifecycle primitives
- evaluation persistence
- recommendation and priority persistence
- Action Plan foundations
- policy-driven authorization
- auditability of important state changes
- subscription and entitlement foundations
- AI abstraction boundaries
- CI, linting, PHPStan and automated tests

Remaining Phase 1 work:

- reconcile implementation with the approved domain model
- complete remaining lifecycle entities and relationships
- harden domain invariants against all mutation paths
- complete evaluation and recommendation workflow foundations
- expand authorization, business lifecycle and subscription test coverage
- keep CI green across lint, PHPStan and test suites

### Phase 2 — Evaluator & Action Plan

- entrepreneur business onboarding
- evaluator flow
- evaluation sections and answers
- diagnostic findings
- priorities
- recommendation generation
- Action Plan creation
- plan item lifecycle
- progress and evidence
- evaluation history
- reassessment workflow

### Phase 3 — Guides, Opportunities & Matching

- Guide catalogue and authoring
- Guide execution flow
- templates/checklists/resources
- Opportunity catalogue
- funding vertical
- eligibility and matching rules
- contextual recommendations
- Opportunity preparation actions
- recommendation ranking and explanation

### Phase 4 — Experts, Marketplace & Community

- Expert onboarding and profiles
- expertise and availability
- consultation workflow
- mentoring relationships
- Marketplace providers and services
- provider verification
- contextual provider matching
- lead workflow
- peer review
- community moderation
- controlled visibility

### Phase 5 — Monitor, Subscription & Public Product

- Monitor configuration
- business health indicators
- recurring check-ins
- alerts and thresholds
- periodic summaries
- trend/history views
- subscription plans and entitlements
- billing/payment flows
- public website
- Events and registrations
- launch readiness

### Phase 6 — Optimization & Scale

- recommendation quality improvement
- AI cost and quality optimization
- cohort and funnel analytics
- retention optimization
- provider and Expert network growth
- marketplace monetization
- event monetization
- pricing experimentation
- operational automation
- performance and infrastructure hardening
- progression toward and beyond €5,000 MRR

---

## 16. Development Rules

1. Keep business rules in explicit domain services, policies and workflows rather than relying on UI behavior.
2. Treat the Business as the central aggregate and preserve meaningful historical context.
3. Record important state changes, administrative actions and materially relevant AI decisions in auditable data where appropriate.
4. Never allow commercial incentives to override recommendation relevance or business need.
5. Keep AI-generated suggestions distinguishable from user-confirmed decisions.
6. Use deterministic rules for deterministic eligibility and policy decisions.
7. Update the relevant documentation or decision issue whenever an important product, architecture, business, AI, workflow or security decision changes.
8. Prefer explicit domain workflows over generic CRUD when direct editing could violate an invariant.
9. Build for the current phase and target MRR rather than prematurely implementing the entire product portfolio.
10. Keep acquisition, activation, retention and conversion measurable from the beginning.


## Static Analysis

Phase 1 development uses Laravel Pint, PHPStan and automated tests as CI quality gates. Domain models and services should provide concrete types for relationships, factories, enums, dates, collections and workflow results so static analysis can reason about business logic without broad suppressions.

PHPStan failures, lint failures and test failures are treated as implementation defects rather than normal development noise. Temporary diagnostic or repair changes may be used during development, but they must not remain as permanent quality compromises.
