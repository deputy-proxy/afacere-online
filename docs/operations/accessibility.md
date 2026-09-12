# Accessibility and responsive UX checklist

## Validation status

This checklist records static code-review requirements. Browser/assistive-technology verification is still required in a real browser because source inspection cannot prove keyboard focus order, screen-reader announcements or visual contrast. No such browser test is claimed here.

## Semantic and keyboard requirements

- Every interactive control is reachable with the keyboard.
- Interactive elements use native buttons/links where possible.
- Focus remains visible and is not removed by custom styles.
- Navigation targets have meaningful accessible names.
- Headings form a logical hierarchy.
- Lists and repeated records use semantic list structures where appropriate.
- State changes are announced or otherwise perceivable without relying on color.
- Destructive actions require an intentional confirmation where the operation cannot be undone.

## Forms

- Every input has a programmatic label.
- Required fields are identified in text, not color alone.
- Validation messages identify the field and explain how to correct it.
- Server-side validation remains authoritative.
- Error summaries do not steal focus unexpectedly, but important errors are reachable and perceivable.
- Loading/submission states prevent accidental duplicate actions where necessary.

## Critical journeys

Review keyboard-only and screen-reader behavior for:

1. authentication and password recovery;
2. business onboarding;
3. evaluation wizard and diagnosis;
4. action-plan execution;
5. guide reading and completion;
6. opportunity discovery/application;
7. subscription and billing;
8. ecosystem discovery, including search and empty states;
9. data export/deletion requests.

## Visual and responsive requirements

- Text remains readable at mobile widths without horizontal scrolling except where the content itself requires it.
- Layouts are checked at mobile, tablet and desktop breakpoints.
- Information is not communicated by color alone.
- Text/background contrast meets the chosen WCAG target.
- Focus, hover and disabled states remain distinguishable.
- Touch targets are sufficiently large and separated to avoid accidental activation.
- Tables and dense operational views have an accessible narrow-screen strategy.

## Error, loading and empty states

Every critical async action needs a clear state for:

- initial loading;
- successful completion;
- validation failure;
- authorization failure;
- unavailable dependency;
- empty result;
- retryable failure.

The ecosystem page implements intentional empty states for experts, providers, community and events and provides a labelled search input with a no-results state.

## Design-system consistency

Reuse the application's existing Tailwind/layout patterns rather than introducing one-off component styles. Keep spacing, typography, borders, controls and interaction states consistent across public and authenticated screens.

## Evidence required before launch

Attach browser evidence for keyboard traversal, focus behavior, screen-reader smoke testing, responsive widths and contrast. Automated repository CI is necessary but cannot establish these human-interface properties by itself.
