/**
 * Ambient module declarations for non-code imports.
 *
 * SCSS is imported for its build side effect only (webpack extracts it into
 * `assets/admin.css`); it has no TypeScript surface.
 */
declare module '*.scss';
