# afacere.online

## Application Plan

**Status:** Planned  
**Product:** Product & Subscription platform  
**Language:** Romanian  
**Stack:** Laravel 13, Filament 5, Livewire 4, PHP 8.4  
**Primary domain:** `afacere.online`

---

## 1. Product Direction

afacere.online is a platform for Romanian entrepreneurs that helps them move from an **unvalidated idea to a viable business and then toward steady cash flow**.

The platform is not intended to become a generic business-content website, a directory, or a collection of unrelated consulting services. The application must create a continuous progression:

> **Evaluate → Plan → Act → Measure → Improve → Grow**

The central product principle is:

> **Do not give entrepreneurs more information. Help them decide what to do next and make that action easier.**

The platform combines:

- structured business evaluation;
- actionable plans and guides;
- AI-assisted analysis and recommendations;
- peer review and community interaction;
- mentoring and expert access;
- business opportunities;
- curated providers and marketplace services;
- events and learning experiences;
- recurring monitoring and subscriptions.

The application must therefore behave as a **business progression system**, not merely as a CMS with a login screen attached to it.

---

## 2. Business Objective

The refactored business has one primary commercial objective:

### Target: €5,000 MRR

The product architecture must support recurring revenue while keeping acquisition friction low.

The intended monetization logic is:

1. **Free entry** through the Evaluator.
2. Evaluation produces concrete priorities.
3. Priorities lead naturally to Guides, Opportunities, Marketplace services, Consultation, or other paid actions.
4. Successful users have an ongoing reason to return through Monitor.
5. Recurring subscriptions become the principal scalable revenue layer.
6. Experts and providers create additional transaction and lead-generation revenue without turning the company into a labor-heavy agency.

The application should optimize for **activation, useful outcomes, retention and conversion**, rather than maximizing the number of features.

---

## 3. Core User Model

### Primary user

**Romanian entrepreneur / founder / small-business owner**, particularly:

- first-time entrepreneurs;
- people validating a business idea;
- early-stage founders;
- small business owners trying to improve an existing operation;
- entrepreneurs seeking funding, customers, expertise or operational support.

### Core user lifecycle

```text
Visitor
  ↓
Account
  ↓
Business / Idea
  ↓
Evaluator
  ↓
Priorities
  ↓
Action Plan
  ↓
Guides / Experts / Opportunities / Marketplace
  ↓
Progress & Evidence
  ↓
Monitor
  ↓
Recurring Subscription
```

A user may have multiple businesses or ideas. The business must be the central workspace object, not the user profile itself.

---

## 4. Product Portfolio

The platform should expose a coherent product family rather than isolated applications.

### 4.1 Evaluator Afacere

**Role:** Free entry point and diagnostic engine.

Purpose:

> Transform an unclear business situation into a structured list of problems, priorities and recommended next actions.

Core capabilities:

- evaluate a business idea;
- evaluate an operating business;
- collect structured business information;
- identify strengths, weaknesses, risks and opportunities;
- calculate diagnostic dimensions where useful;
- generate prioritized actions;
- explain why each action matters;
- link recommendations to Guides, Experts, Marketplace providers and Opportunities;
- create or update the user's business profile;
- save evaluation history;
- allow reevaluation after changes.

Principle:

> **We do not evaluate to produce a score. We evaluate to decide what happens next.**

---

### 4.2 Plan de Acțiune

**Role:** Convert diagnosis into execution.

The plan is the bridge between the free diagnostic experience and recurring product usage.

Capabilities:

- prioritized actions;
- objectives;
- milestones;
- tasks;
- dependencies;
- deadlines;
- suggested Guides;
- suggested Experts;
- suggested Opportunities;
- suggested Marketplace providers;
- progress tracking;
- completion evidence;
- notes;
- reassessment triggers.

The system should distinguish between:

- recommended actions;
- accepted actions;
- active actions;
- completed actions;
- skipped actions;
- blocked actions.

---

### 4.3 Ghiduri de Acțiune

**Role:** Self-service execution layer.

Guides must be practical and action-oriented. They are not long-form educational articles disguised as products.

Each Guide should contain:

- problem/context;
- expected outcome;
- prerequisites;
- steps;
- templates/checklists where appropriate;
- estimated effort;
- expected evidence of completion;
- related Evaluator findings;
- related Opportunities;
- related Marketplace providers;
- related Experts;
- next recommended action.

Guide types should support both free and paid content.

---

### 4.4 Monitor Afacere

**Role:** Recurring retention and subscription product.

The Monitor keeps the entrepreneur aware of what changed and what requires attention.

Capabilities should include:

- business health indicators;
- progress against active plans;
- overdue or blocked actions;
- important business metrics;
- recurring check-ins;
- new relevant Opportunities;
- new relevant Guides;
- recommended interventions;
- alerts based on user-defined or system-defined thresholds;
- periodic AI summaries;
- historical trend views.

Monitor is the primary mechanism for turning a one-time evaluator user into a recurring customer.

---

### 4.5 Oportunități

**Role:** Opportunity discovery engine.

Opportunities are a platform capability, not merely a funding directory.

Potential opportunity types:

- grants;
- funding programs;
- investors;
- partnerships;
- competitions;
- tenders;
- accelerators;
- incubators;
- events;
- relevant commercial opportunities.

Each opportunity should have:

- eligibility criteria;
- business-stage relevance;
- sector relevance;
- geographic relevance;
- deadline;
- source;
- required preparation;
- related business actions.

#### Funding

Funding is a vertical within Opportunities.

Principle:

> **Funding is an opportunity for the business, not the purpose of the platform.**

The system should connect funding opportunities to the user's business diagnosis and action plan instead of presenting an undifferentiated list of grants.

---

### 4.6 Marketplace

**Role:** Connect entrepreneurs with vetted external providers.

Marketplace categories may include:

- accounting;
- legal;
- web development;
- branding;
- marketing;
- SEO;
- design;
- business analysis;
- HR;
- procurement;
- technology;
- finance;
- other services validated by demand.

The Marketplace should not initially attempt to become a full transactional marketplace with complex fulfillment infrastructure.

MVP model:

> **Discovery → Match → Lead / Contact → Outcome**

Later versions may add:

- service packages;
- quotes;
- booking;
- payments;
- reviews;
- provider subscriptions;
- transaction fees.

Recommendations must be contextual. A provider should appear because it solves a detected business need, not because the platform needs somewhere to put a banner.

---

### 4.7 Consultanță

**Role:** Human assistance for problems where self-service or AI is insufficient.

Consultation should be structured around specific business outcomes rather than generic hourly consulting.

Capabilities:

- consultation requests;
- matching to expert profiles;
- defined scope;
- objectives;
- preparation materials from the user's business profile;
- session scheduling;
- notes and outcomes;
- recommended follow-up actions;
- conversion into a plan.

The platform should progressively automate intake and preparation so human time is spent on judgment rather than administration.

---

### 4.8 Branding Services

Branding is a specialized Marketplace / service vertical.

It should use the same provider, service, recommendation and lead infrastructure rather than introducing a separate architecture.

---

## 5. Core Domain Model

The application should be organized around the following domain objects.

### Identity

- User
- Profile
- Role
- Permission
- Subscription
- Notification

### Business

- Business
- BusinessMember
- BusinessStage
- BusinessProfile
- BusinessMetric
- BusinessMetricValue
- BusinessGoal
- BusinessDocument

### Evaluation

- Evaluation
- EvaluationSection
- EvaluationAnswer
- EvaluationFinding
- EvaluationScore / DimensionScore where required
- Recommendation
- Priority

### Planning

- ActionPlan
- PlanItem
- Milestone
- Task
- ActionEvidence
- ProgressEvent

### Content

- Guide
- GuideSection
- GuideStep
- Template
- Checklist
- Resource
- Topic
- Tag

### Opportunities

- Opportunity
- OpportunityType
- OpportunityCriteria
- OpportunityApplication / UserOpportunity
- OpportunityMatch

### Marketplace

- Provider
- ProviderProfile
- ServiceCategory
- Service
- ServicePackage
- ProviderVerification
- ProviderReview
- Lead

### Experts / Mentoring

- Expert
- ExpertProfile
- Expertise
- Availability
- Consultation
- MentoringRelationship
- Session
- SessionNote
- ExpertRecommendation

### Community / Peer Review

- CommunityPost
- Comment
- Reaction
- PeerReview
- ReviewRequest
- ReviewResponse
- ModerationReport

### Events

- Event
- EventType
- EventRegistration
- EventSession
- EventAttendance

### AI

- AiProvider
- AiPrompt / PromptVersion
- AiRun
- AiRecommendation
- AiUsage
- AiFeedback

### Commercial

- Product
- ProductPlan
- Subscription
- SubscriptionItem
- Payment
- Invoice
- Coupon / Promotion
- Entitlement

### Analytics

- Event
- FunnelEvent
- Conversion
- CohortSnapshot
- MetricSnapshot

---

## 6. Business as the Central Aggregate

Every meaningful user action should be attributable to a business where applicable.

Examples:

- evaluation belongs to a business;
- action plan belongs to a business;
- guide progress belongs to a business/user context;
- opportunity matches belong to a business;
- consultation requests belong to a business;
- marketplace leads belong to a business;
- monitor data belongs to a business.

This allows the platform to produce a coherent history instead of a pile of disconnected user actions.

---

## 7. Main User Journeys

### Journey A: New entrepreneur

1. Visitor lands on afacere.online.
2. Understands the proposition.
3. Starts Evaluator.
4. Creates account at the appropriate point.
5. Creates business/idea.
6. Completes evaluation.
7. Receives diagnosis.
8. Receives prioritized actions.
9. Starts Action Plan.
10. Executes first action.
11. Uses Guide / Expert / Marketplace / Opportunity.
12. Returns to track progress.
13. Activates Monitor.
14. Converts to subscription when recurring value is established.

### Journey B: Existing business

1. Create/import business.
2. Complete diagnostic.
3. Establish baseline.
4. Identify highest-impact bottlenecks.
5. Build action plan.
6. Track progress.
7. Monitor business health.
8. Receive contextual recommendations.

### Journey C: Funding

1. Evaluation identifies capital requirement.
2. Opportunity engine determines relevant funding types.
3. User sees matched opportunities.
4. User sees eligibility and preparation requirements.
5. Platform creates preparation actions.
6. User accesses relevant Guides / Experts.
7. User tracks opportunity status.

### Journey D: Expert help

1. User receives recommendation that human assistance is useful.
2. Platform identifies appropriate expertise.
3. User reviews expert/service profile.
4. User requests consultation or contact.
5. Business context is shared with the expert according to permissions.
6. Session produces decisions/actions.
7. Actions are added to the Action Plan.

### Journey E: Marketplace

1. Business need is detected.
2. Matching engine recommends category/provider.
3. User reviews providers.
4. User requests contact/quote.
5. Lead is created.
6. Provider follows up.
7. Outcome can later be recorded.

### Journey F: Monitor

1. User has an active business.
2. Baseline and goals exist.
3. User activates Monitor.
4. System periodically evaluates changes.
5. System identifies risks/opportunities/progress.
6. User receives concise recommendations.
7. User acts.
8. Platform records outcome.
9. Recommendations improve over time.

---

## 8. AI Architecture

AI is an assistance layer, not the product itself.

AI should support:

- evaluation interpretation;
- finding explanations;
- recommendation generation;
- action-plan generation;
- guide personalization;
- opportunity matching;
- provider matching;
- consultation preparation;
- periodic business summaries;
- anomaly detection support;
- content generation for internal workflows;
- semantic search.

### AI rules

1. Never present AI output as guaranteed business advice.
2. Preserve the source/context behind recommendations where possible.
3. Store important AI outputs for auditability.
4. Version prompts and relevant model configuration.
5. Separate AI-generated suggestions from user-confirmed decisions.
6. Allow users to reject, edit or accept recommendations.
7. Do not build the core architecture around one AI vendor.
8. Track AI usage and cost.
9. Use deterministic business rules where deterministic rules are sufficient.

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

## 9. Recommendation Engine

The recommendation engine is one of the most important architectural components.

Recommendations should be generated from:

- evaluation findings;
- business stage;
- business profile;
- active goals;
- previous actions;
- completed guides;
- opportunity eligibility;
- user preferences;
- available experts;
- marketplace services;
- historical behavior.

Each recommendation should have:

- reason;
- priority;
- recommended action;
- expected outcome;
- source/context;
- target object;
- confidence where applicable;
- status;
- timestamp.

Recommendations must be explainable enough that users understand **why the platform is telling them to do something**.

---

## 10. Peer Review and Community

Peer review is a core differentiator of the business model.

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

Do not build a social network for the sake of having one. Community exists to improve entrepreneurial decisions and outcomes.

---

## 11. Events

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
- related guides/opportunities;
- paid/free status.

Events should connect to businesses and user goals where possible.

---

## 12. Subscription Architecture

Subscription must be capability-based rather than hard-coded around individual screens.

### Entitlements

Examples:

- number of businesses;
- evaluation history;
- advanced evaluations;
- Monitor access;
- AI usage limits;
- premium Guides;
- opportunity matching depth;
- community capabilities;
- expert access;
- reporting;
- export;
- team members.

This permits pricing experiments without architectural rewrites.

### Commercial strategy

The exact pricing should remain configurable while validating demand.

The initial architecture should support:

- free;
- monthly subscription;
- annual subscription;
- one-time purchase where appropriate;
- paid consultation;
- provider subscriptions;
- Marketplace lead/transaction revenue;
- event revenue.

The first commercial priority remains **recurring revenue**, not maximizing every possible transaction fee.

---

## 13. Filament 5 Administration

Filament is the operational back office.

Admin navigation should be grouped by domain:

### Platform

- Dashboard
- Users
- Roles & permissions
- Subscriptions
- Payments

### Businesses

- Businesses
- Business stages
- Evaluations
- Findings
- Action plans
- Metrics

### Content

- Guides
- Templates
- Topics
- Resources

### Opportunities

- Opportunities
- Opportunity types
- Matching rules

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
- Sessions

### Community

- Posts
- Peer reviews
- Reports
- Moderation

### Events

- Events
- Registrations
- Attendance

### AI

- AI runs
- Prompt versions
- Usage
- Failed runs
- Feedback

### Analytics

- Acquisition
- Activation
- Engagement
- Retention
- Conversion
- Revenue

Admin resources should prioritize operational workflows over exposing every database table. Not every model deserves a CRUD screen. Civilization has suffered enough CRUD already.

---

## 14. Public Application Areas

### Marketing site

- Home
- How it works
- For entrepreneurs
- Product pages
- Guides catalogue
- Opportunities catalogue
- Marketplace
- Experts
- Events
- Pricing
- About
- FAQ
- Legal

### Authenticated application

Primary navigation:

1. **Dashboard**
2. **Afaceri**
3. **Evaluare**
4. **Plan de acțiune**
5. **Oportunități**
6. **Ghiduri**
7. **Experți / Servicii**
8. **Monitor**
9. **Comunitate**
10. **Evenimente**

The navigation should adapt to the user's current state. A brand-new user should not be presented with fifteen equally important destinations.

---

## 15. Dashboard

The authenticated dashboard is the user's operational home.

It should answer four questions immediately:

1. **Where am I?**
2. **What matters now?**
3. **What should I do next?**
4. **What changed since I last visited?**

Core widgets:

- active business selector;
- current stage;
- health/status summary;
- top priorities;
- active plan progress;
- recommended next action;
- new opportunities;
- relevant guides;
- monitor alerts;
- upcoming events;
- recent activity.

Avoid dashboard decoration masquerading as analytics.

---

## 16. Search and Discovery

Global search should eventually cover:

- Guides;
- Opportunities;
- Experts;
- Marketplace providers;
- Services;
- Events;
- community content where appropriate.

Search should be complemented by contextual recommendations. Search answers "what exists"; recommendations answer "what matters to me now".

---

## 17. Notifications

Notification infrastructure should support:

- in-app notifications;
- email;
- future push notifications if justified.

Notification types:

- plan deadlines;
- opportunity deadlines;
- Monitor alerts;
- consultation updates;
- peer-review activity;
- event reminders;
- subscription/payment events;
- important recommendations.

Notifications must be actionable and preference-controlled. The platform should not become another machine for manufacturing unread badges.

---

## 18. Privacy and Permissions

Business data may be commercially sensitive.

Required principles:

- explicit ownership of business data;
- business-level authorization;
- role-based permissions;
- granular sharing for peer review and experts;
- private-by-default business information;
- audit trail for sensitive actions;
- clear AI data usage policy;
- secure document handling;
- no accidental exposure through public URLs or search.

Authorization should be implemented at the domain level, not only through UI hiding.

---

## 19. Analytics and Business Metrics

The application must instrument the complete business funnel.

### Acquisition

- visitors;
- evaluator starts;
- registrations;
- acquisition source;
- landing-page conversion.

### Activation

- first business created;
- evaluation completed;
- first recommendation accepted;
- first action started;
- first action completed.

### Engagement

- weekly active businesses;
- returning users;
- plan activity;
- guide completion;
- opportunity interaction;
- community participation;
- Monitor usage.

### Retention

- subscription activation;
- monthly retention;
- churn;
- reactivation;
- business survival/progress proxies.

### Revenue

- MRR;
- ARR;
- ARPU;
- conversion rate;
- trial/free-to-paid conversion;
- revenue by product;
- provider revenue;
- consultation revenue;
- event revenue;
- lifetime value.

### North-star operating metric

A strong candidate is:

> **Businesses completing meaningful progress actions per month.**

Revenue remains the commercial target, but the product should optimize for the behavior that creates durable value.

---

## 20. Architecture Principles

### 20.1 Laravel-first

Use Laravel conventions and framework capabilities before introducing custom infrastructure.

### 20.2 Filament-first for internal operations

Use Filament resources, pages, actions, widgets and relation managers where they fit naturally.

### 20.3 Livewire-first

Prefer Livewire for application interactivity. Introduce JavaScript only where it provides a material UX benefit.

### 20.4 Modular domain boundaries

Organize application code around clear business domains rather than one enormous collection of generic services.

Suggested conceptual domains:

```text
App/
├── Domain/
│   ├── Businesses/
│   ├── Evaluations/
│   ├── Planning/
│   ├── Guides/
│   ├── Opportunities/
│   ├── Marketplace/
│   ├── Experts/
│   ├── Community/
│   ├── Events/
│   ├── AI/
│   ├── Billing/
│   └── Analytics/
├── Filament/
├── Livewire/
└── Support/
```

This is a target architecture, not permission to create twelve abstraction layers before the first user completes an evaluation.

### 20.5 Services should represent business capabilities

Avoid generic `BusinessService`, `Helper`, or `Manager` classes that eventually become junk drawers.

Prefer explicit capabilities such as:

- `EvaluateBusiness`
- `GenerateActionPlan`
- `MatchOpportunities`
- `RecommendServices`
- `GenerateBusinessSummary`
- `CompletePlanItem`

### 20.6 Events for meaningful domain changes

Use domain/application events where asynchronous or decoupled behavior is valuable.

Examples:

- EvaluationCompleted
- RecommendationAccepted
- PlanItemCompleted
- OpportunityMatched
- ConsultationBooked
- SubscriptionActivated

Do not event-source every button click simply because events sound architectural.

---

## 21. Data Design Principles

- UUIDs/ULIDs where appropriate for public-facing identifiers.
- Foreign keys and database constraints for relational integrity.
- Soft deletes only where business recovery/audit value exists.
- Explicit statuses using enums where state transitions matter.
- Timestamps on important domain records.
- Auditability for sensitive changes.
- Avoid storing derived data when it can be safely calculated, unless performance or historical accuracy requires snapshots.
- Use JSON only for genuinely flexible structures, not as an excuse to avoid designing tables.

---

## 22. Background Processing

Queues should be used for operations that are slow, external, or non-critical to the immediate HTTP response.

Candidates:

- AI generation;
- email delivery;
- opportunity ingestion;
- matching recalculation;
- Monitor calculations;
- analytics aggregation;
- document processing;
- notifications;
- scheduled reports.

Jobs should be idempotent where practical and observable when they fail.

---

## 23. External Integrations

Integrations should be isolated behind application services/adapters.

Likely integrations:

- AI provider;
- email provider;
- payment/subscription provider;
- calendar/scheduling provider;
- analytics;
- storage;
- social authentication where justified.

No external integration should leak provider-specific objects throughout the domain model.

---

## 24. Content Architecture

Content must be structured enough to support personalization and recommendations.

Every important content object should be classifiable by:

- topic;
- business stage;
- problem;
- objective;
- industry where relevant;
- difficulty;
- expected effort;
- outcome;
- product relationship.

This metadata is essential for the recommendation engine.

---

## 25. Opportunity Ingestion

The opportunity engine should eventually support structured ingestion from multiple sources.

Pipeline:

```text
Source
  ↓
Raw opportunity
  ↓
Normalization
  ↓
Validation
  ↓
Classification
  ↓
Eligibility metadata
  ↓
Publication
  ↓
Matching
```

Admin users must be able to review and correct imported opportunities before publication where source reliability requires it.

---

## 26. Provider Quality

Marketplace quality is more important than marketplace volume.

Provider records should support:

- verification status;
- specialties;
- service area;
- price positioning;
- evidence/portfolio;
- reviews;
- response rate where measurable;
- recommendation eligibility;
- active/inactive status.

Provider recommendations should be driven by fit, not simply paid placement.

---

## 27. Security

Minimum requirements:

- Laravel authentication and authorization;
- CSRF protection;
- secure password handling;
- rate limiting for sensitive endpoints;
- validation at boundaries;
- authorization policies;
- secure file uploads;
- signed URLs for private assets where appropriate;
- secrets only in environment configuration;
- audit logging for sensitive business-data access;
- careful handling of AI prompts containing business information;
- privacy-compliant deletion/export mechanisms.

Security tests are required for business-level authorization and sensitive sharing flows.

---

## 28. Testing Strategy

Use Pest and feature tests as the primary safety net.

### Critical feature coverage

- authentication;
- business ownership;
- business member permissions;
- evaluation completion;
- recommendation generation;
- action-plan creation;
- task completion;
- guide access;
- opportunity matching;
- provider matching;
- consultation lifecycle;
- peer-review permissions;
- event registration;
- subscription entitlements;
- payment/webhook handling;
- Monitor alerts;
- notification preferences.

### Testing principles

- test business behavior, not implementation trivia;
- test authorization boundaries;
- test important failure modes;
- use factories;
- keep tests deterministic;
- isolate external services through appropriate fakes/mocks.

Every implementation phase must add or update tests for the behavior introduced.

---

## 29. MVP Scope

The first production milestone must be deliberately narrow.

### MVP must include

1. Authentication.
2. Business creation and management.
3. Evaluator.
4. Diagnostic findings.
5. Prioritized recommendations.
6. Action Plan.
7. Basic Guides.
8. Basic Opportunities.
9. Basic contextual recommendations.
10. Basic subscription/entitlement infrastructure.
11. Basic Monitor foundation.
12. Admin management in Filament.
13. Core analytics.
14. Privacy/authorization.
15. Automated tests.

### MVP should not include initially

- full marketplace transactions;
- sophisticated scheduling marketplace;
- complex social networking;
- mobile applications;
- elaborate gamification;
- dozens of AI agents;
- custom recommendation ML;
- excessive dashboard visualizations;
- broad enterprise functionality.

The MVP is successful when a real entrepreneur can go from **business idea → diagnosis → action → measurable progress** without needing a human to manually operate the system behind the scenes.

---

## 30. Implementation Roadmap

### Phase 0: Foundation

**Goal:** Establish a clean Laravel 13 / Filament 5 foundation.

Deliverables:

- authentication baseline;
- application shell;
- user/business domain foundation;
- roles/permissions;
- testing setup;
- coding standards;
- domain structure;
- basic admin navigation;
- core design system;
- analytics event foundation.

Exit criteria:

- clean application boot;
- tests run;
- admin accessible;
- business can be created;
- authorization works.

---

### Phase 1: Evaluator

**Goal:** Build the free acquisition and diagnostic engine.

Deliverables:

- evaluation schema;
- evaluation flow;
- questions/sections;
- answers;
- findings;
- scoring/dimensions where justified;
- prioritized recommendations;
- evaluation history;
- evaluation completion analytics.

Exit criteria:

> A new entrepreneur can complete a meaningful evaluation and receive actionable priorities.

---

### Phase 2: Action Plan

**Goal:** Turn recommendations into execution.

Deliverables:

- action plans;
- plan items;
- milestones;
- statuses;
- deadlines;
- evidence;
- progress tracking;
- recommendation-to-action links.

Exit criteria:

> A user can take a recommendation and execute it through a measurable workflow.

---

### Phase 3: Guides

**Goal:** Make recommended actions executable without human intervention.

Deliverables:

- Guide model;
- structured guide editor;
- guide catalogue;
- contextual guide recommendations;
- progress/completion;
- templates/checklists;
- free/paid access.

Exit criteria:

> The platform can resolve a meaningful percentage of common recommendations through self-service content.

---

### Phase 4: Opportunities

**Goal:** Connect business needs to relevant external opportunities.

Deliverables:

- opportunity model;
- types/categories;
- eligibility metadata;
- admin curation;
- matching;
- opportunity detail;
- save/follow/status;
- funding vertical.

Exit criteria:

> A business receives opportunities that are meaningfully more relevant than a generic directory search.

---

### Phase 5: AI Assistance

**Goal:** Increase diagnostic and recommendation quality while controlling cost.

Deliverables:

- AI abstraction;
- prompt/version management;
- evaluation interpretation;
- recommendation assistance;
- action-plan assistance;
- guide personalization;
- usage tracking;
- failure handling;
- AI feedback.

Exit criteria:

> AI measurably improves usefulness without becoming an un-auditable black box.

---

### Phase 6: Monitor

**Goal:** Create recurring user value.

Deliverables:

- baseline metrics;
- check-ins;
- business health indicators;
- progress summaries;
- alerts;
- recurring recommendations;
- scheduled jobs;
- subscription entitlements.

Exit criteria:

> A user has a compelling reason to return after completing the initial evaluation.

---

### Phase 7: Marketplace & Experts

**Goal:** Monetize the gap between recommendations and execution.

Deliverables:

- providers;
- services;
- verification;
- matching;
- leads;
- expert profiles;
- consultations;
- outcomes;
- recommendation integration.

Exit criteria:

> A user can move from identified need to qualified external help with minimal friction.

---

### Phase 8: Community & Peer Review

**Goal:** Add differentiated human intelligence and network effects.

Deliverables:

- peer-review requests;
- responses;
- community posts;
- moderation;
- reputation signals;
- privacy controls.

Exit criteria:

> Users can obtain useful peer feedback without compromising business confidentiality.

---

### Phase 9: Events

**Goal:** Build acquisition, trust and additional revenue channels.

Deliverables:

- event catalogue;
- registration;
- attendance;
- speakers;
- event-linked recommendations;
- paid events where validated.

---

### Phase 10: Optimization Toward €5k MRR

**Goal:** Optimize the complete commercial funnel.

Focus:

- evaluator conversion;
- activation;
- first-value time;
- subscription conversion;
- retention;
- Monitor engagement;
- ARPU;
- churn;
- product-led conversion;
- provider economics;
- content ROI;
- AI cost per active business.

No major new product should be introduced unless it improves one of the core business metrics or creates a strategically important capability.

---

## 31. Commercial Funnel

The intended funnel is:

```text
Traffic
  ↓
Evaluator start
  ↓
Account creation
  ↓
Evaluation completion
  ↓
First recommendation accepted
  ↓
First meaningful action
  ↓
Second return session
  ↓
Monitor activation
  ↓
Paid subscription
  ↓
Retention
```

Secondary monetization paths:

```text
Business need
  ├── Guide purchase
  ├── Expert consultation
  ├── Marketplace lead
  ├── Marketplace transaction
  ├── Event
  └── Subscription
```

The platform should always attempt to solve the user's need first and monetize the appropriate next step second.

---

## 32. MRR Strategy

A plausible target composition for €5,000 MRR should be tested rather than assumed.

Example architecture:

- recurring entrepreneur subscriptions as the core;
- provider subscriptions as a secondary recurring layer;
- consultation/marketplace revenue as variable upside;
- events and paid content as additional revenue.

The product database and billing layer must allow pricing experiments without code changes to core business logic.

Key experiments:

- free vs trial;
- monthly vs annual;
- Monitor included vs separate;
- AI limits;
- business-count limits;
- premium Guide bundles;
- provider subscription tiers.

---

## 33. What the Application Must NOT Become

The following are explicit strategic constraints:

1. Not a generic business blog.
2. Not a generic AI chatbot.
3. Not a directory of consultants.
4. Not a grant directory.
5. Not an online course platform.
6. Not a social network.
7. Not a conventional consulting agency disguised as SaaS.
8. Not a feature catalogue without a coherent user journey.

Every feature must answer:

> **Does this help an entrepreneur make or execute a better business decision?**

If not, it probably does not belong in the core product.

---

## 34. Product Principles

### Principle 1: Action over information

Every important piece of information should lead toward a decision or action.

### Principle 2: Context over catalogue

Recommendations must use business context.

### Principle 3: Progress over activity

The platform should measure meaningful progress, not clicks.

### Principle 4: Human expertise where it matters

AI handles scale and preparation. Humans handle judgment, relationships and specialist expertise.

### Principle 5: Recurring value

The product must continuously create value after the first evaluation.

### Principle 6: Trust

Recommendations, providers, opportunities and AI outputs must be transparent enough to earn user confidence.

### Principle 7: Simplicity

The product should become more powerful underneath while becoming simpler for the entrepreneur on the surface.

### Principle 8: Build the smallest system that proves the business

Do not build speculative infrastructure before validating the underlying behavior.

---

## 35. Definition of Done for Features

A feature is not done when its UI exists.

It is done when:

- domain behavior is implemented;
- authorization is implemented;
- validation is implemented;
- relevant states are handled;
- important failure cases are covered;
- tests pass;
- analytics events exist where relevant;
- admin operations exist where operationally required;
- notifications exist where appropriate;
- documentation is updated for durable decisions;
- the feature contributes to a measurable product outcome.

---

## 36. Initial Development Order

The implementation order should be:

```text
Foundation
    ↓
Business
    ↓
Evaluator
    ↓
Findings & Recommendations
    ↓
Action Plan
    ↓
Guides
    ↓
Opportunities
    ↓
AI assistance
    ↓
Monitor
    ↓
Billing / Subscriptions
    ↓
Marketplace / Experts
    ↓
Community / Peer Review
    ↓
Events
    ↓
Optimization
```

Billing infrastructure should be designed early enough that entitlements are not retrofitted later, but payment optimization should not distract from proving the core product journey.

---

## 37. Success Criteria

The application is moving in the right direction when the following become true:

### Product

- entrepreneurs complete the Evaluator;
- recommendations are accepted;
- users execute actions;
- users return;
- Monitor is used repeatedly;
- users perceive the platform as useful without human handholding.

### Business

- free acquisition converts to activated businesses;
- activated businesses convert to paid accounts;
- paid users remain subscribed;
- recurring revenue grows;
- AI cost remains economically sustainable;
- Marketplace/Expert revenue supplements subscriptions.

### Strategic

The strongest signal of product-market fit is not traffic. It is entrepreneurs repeatedly returning because the platform helps them make the **next important business decision**.

---

## 38. Current Technical Baseline

The repository is a fresh Laravel 13 / Filament 5 application.

Current direct PHP dependency constraints include Laravel Framework `^13.17`, Filament `^5.0`, Livewire `^4.1`, Fortify `^1.37.2`, Flux `^2.13.1`, Blaze `^1.0`, Tinker `^3.0`, Chisel `^0.1.0`, and the current development tooling defined in `composer.json`.

The resolved dependency state is tracked by `composer.lock` and must be treated as authoritative for installed PHP package versions.

The project rules in `AGENTS.md` are part of the development contract and must be followed for all implementation work.

---

## 39. Decision Log

### 2026-09-10

- Confirmed repository: `deputy-proxy/afacere-online`.
- Confirmed current application baseline: Laravel 13 / Filament 5.
- Reframed the application around the refactored business model rather than the previous broader product catalogue.
- Established **Evaluator Afacere** as the free entry point.
- Established **Monitor Afacere** as the recurring retention/subscription layer.
- Established **Action Plan** as the bridge from diagnosis to execution.
- Established Guides, Opportunities, Marketplace and Consultation as contextual execution layers.
- Established Funding as a vertical within Opportunities rather than a standalone core product.
- Established the entrepreneur's **Business** as the central domain aggregate.
- Established recurring revenue and the €5,000 MRR target as the primary commercial objective.
- Established action/progress rather than content volume as the primary product philosophy.

---

## 40. Next Implementation Step

Start implementation with **Phase 0: Foundation**, followed by the Business domain and Evaluator.

Do not begin by implementing the entire product catalogue. The first objective is to prove the core loop:

> **Create business → evaluate → receive priorities → take action → record progress → return.**

Everything else should grow around that loop.