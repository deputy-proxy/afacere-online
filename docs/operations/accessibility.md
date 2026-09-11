# Accessibility and responsive release checklist

Critical journeys must remain usable with keyboard navigation, without relying on colour alone, and with reduced-motion preferences enabled.

## Release checks

- Every interactive control has an accessible name.
- Focus remains visible and follows the logical interaction order.
- Form errors are associated with their fields and remain understandable without colour.
- Dynamic status messages are exposed to assistive technology where appropriate.
- Public and authenticated layouts work at supported mobile and desktop breakpoints.
- Touch targets remain usable without precise pointer input.
- Loading, empty, unavailable and error states remain actionable.
- `prefers-reduced-motion: reduce` does not remove essential information or controls.

The automated suite validates critical routes. Browser-level accessibility verification remains a release activity and must be repeated when a critical journey or reusable component changes.
