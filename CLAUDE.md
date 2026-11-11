# AGENTS.md — Amalfi Theme

Instructions for AI agents working on the **Amalfi** WordPress block theme. Read this before making any change. These rules describe the theme's architecture and the conventions you must follow; they take precedence over your default habits.

> **Amalfi is a general-purpose magazine/blog block theme.** The Amalfi Coast travel content that may exist on the demo site is only sample/demo content. Everything in the theme itself — pattern names, category labels, template content, placeholder text — must stay **generic and reusable** for any publication. Never bake travel-specific (or any other domain-specific) wording into the theme.

---

## 1. Block-first rules (most important)

These are hard rules. Do not violate them even when it seems easier.

1. **Always use WordPress Core blocks for everything.** Build layouts and designs out of core blocks (Group, Columns, Cover, Query Loop, Post Title, Buttons, etc.). Do not reach for third-party blocks or custom blocks unless explicitly asked.
2. **Never use the Custom HTML block (`core/html`) to build layouts or designs.** If you find yourself writing raw markup inside an HTML block to achieve a layout, stop — compose it from core blocks instead.
3. **Style with `theme.json` first, custom CSS last.** Before writing any CSS, express the design through `theme.json` (presets, element styles, per-block styles, `settings`/`styles`). Only write custom SCSS when `theme.json` genuinely cannot express it (e.g. a block style variation like `.is-style-eyebrow`, a reset). Prefer presets (`var:preset|color|…`, `var:preset|spacing|…`, `var:preset|font-size|…`) over hardcoded values everywhere — in patterns, templates, and styles.
   - **Always set borders, colors, typography, spacing (and anything else with a key) through the structured `theme.json` properties — `border`, `color`, `typography`, `spacing`, `elements`, `:hover`/`:focus`, etc. — not CSS.** Reach for the `css` escape hatch (or SCSS) only for what has genuinely no structured key: things like `display`, custom layout, pseudo-elements, or elements the block does not expose as a block/element you can target (e.g. a repeated inline node or a non-link `<span>`).
   - **Structured styles serialize even when a block's `block.json` doesn't advertise the support.** Because `settings.appearanceTools` is on, `styles.blocks.<block>.border` / `.spacing` / `.color` are emitted as CSS regardless of the block's editor `supports`. So prefer them — verify in the rendered page if unsure, rather than assuming you must fall back to CSS.
   - **Prefer `elements.link` on a block to style all its links at once.** A single `styles.blocks.<block>.elements.link` entry (with `color`, `border`, `spacing`, `typography`, `:hover`, `:focus`) styles every `<a>` the block renders in one place — e.g. all Pagination links (Previous/Next/numbers) from `core/query-pagination`. See `theme-json/styles/blocks/core-query-pagination*.js` for a worked example of structured-first with CSS kept to the minimum unreachable bits.
4. **Keep the theme distribution-safe.** No hardcoded IDs, no invented domains, nothing tied to a specific site's data (see §7).

---

## 2. PHP code organization (Packable)

PHP is organized with the [`themezee/packable`](https://packagist.org/packages/themezee/packable) library into **modules** and **services**. Follow the existing structure.

- **No logic in `functions.php`.** It only bootstraps the `Package`, handles exceptions, and registers modules in the `$modules` array inside `initialize()`. Do not add feature code there.
- **All PHP lives in `/src`**, autoloaded via PSR-4: `ThemeZee\Amalfi\` → `src/`. New classes are picked up automatically — **no `composer dump-autoload` needed**.
- **A module** is `src/<Name>/Module.php`. It implements `ServiceModule` + `ExecutableModule`, uses `ModuleClassNameIdTrait`, declares its services in `services()` (factories keyed by class name), and hooks them into WordPress in `run( ContainerInterface $container )`.
- **A service** is an `__invoke`-able class (or a class with action/filter callback methods), e.g. `src/Assets/Frontend.php`, `src/Setup/BlockPatternCategory.php`. Keep each service focused on one responsibility.
- **Registering a new module:** add it to the `$modules` array in `functions.php` `initialize()` (e.g. `new Assets\Module()`, `new Setup\Module()`).

Conventions: `declare(strict_types=1);`, namespace `ThemeZee\Amalfi\…`, `@package ThemeZee\Amalfi` docblocks, WordPress-style array syntax and spacing (matches `phpcs.xml.dist`).

Current modules: `src/Assets/` (enqueues compiled CSS/JS, editor styles) and `src/Setup/` (block pattern categories, block styles, removing default Core patterns).

---

## 3. theme.json is generated — never edit it directly

`theme.json` at the theme root is a **build artifact**. Editing it by hand will be overwritten on the next build.

- **Edit the source fragments under `theme-json/`** instead, then regenerate.
- Fragments are CommonJS `.js` (or `.json`) modules. The **file path maps to a dotted key**, and a leading `core-` in the last path segment becomes `core/`:
  - `theme-json/index.js` → root (`$schema`, `version`, top-level `settings`/`styles`)
  - `theme-json/settings/color/palette.js` → `settings.color.palette`
  - `theme-json/styles/elements/h1.js` → `styles.elements.h1`
  - `theme-json/styles/blocks/core-post-title.js` → `styles.blocks.core/post-title`
- Use preset references in values: `var:preset|color|<slug>`, `var:preset|spacing|<slug>`, `var:preset|font-size|<slug>`.
- **Regenerate + validate with:**
  ```bash
  npm run theme-json
  ```
  This runs `bin/theme-json.js` (assembles fragments → `theme.json`), formats with Prettier, and validates against the **trunk** WordPress schema `https://schemas.wp.org/trunk/theme.json` (`bin/validate-theme-json.js`).
- **Look up valid keys/values in the trunk schema** (`https://schemas.wp.org/trunk/theme.json`) — the latest Core, which this project targets — not from memory or bundled files. The validator above is the gate; a fragment that uses an unknown key will fail it. (The schema defines the key/value surface, but not runtime behaviour — e.g. which blocks accept `:hover`; see block style variations below.)
- Per-block default styling defined here emits as low-specificity `:root :where(.wp-block-…)` rules **without `!important`**, so a user's per-block editor settings still override the defaults. Keep it that way.

### Block style variations — define in theme.json, not SCSS

A custom block style (e.g. the Social Icons **Outline** style, the Button **Outline** style) is **styled in `theme.json`**, the same as any other design — not in custom SCSS. Register the style's *label* in PHP (`src/Setup/BlockStyle.php` via `register_block_style()`), then express its look under `styles.blocks.<block>.variations.<name>`:

- Style the block itself at the variation root (e.g. `theme-json/styles/blocks/core-button.js` → `variations.outline`).
- To style **inner blocks** of a container variation (the class lands on the container, but each child needs styling — e.g. Social Icons, where `is-style-outline` sits on the `<ul>` but the border belongs on each `core/social-link`), nest them: `variations.<name>.blocks.core/<inner>` (and `…elements.<name>` for element styling). WordPress compiles this ("section styles") into a scoped `.is-style-<name>--N` rule at render time. See `theme-json/styles/blocks/core-social-links.js`.
- Prefer real design keys (`border`, `color`, `spacing`, `typography`, with `var:preset|…` values).
- For anything a design key can't express, use the variation's **`css` property** rather than SCSS, so the whole style stays in one `theme.json` fragment. `&` is the node's selector; nest with `& :pseudo` / `& .descendant`, and use real CSS (`var(--wp--preset--color--…)`, not `var:preset|…`). Cases that need it: **pseudo-states** (`:hover`/`:focus`/`:active`/`:focus-within` are valid as design keys only for the `link`/`button` elements and the `core/button` / `core/navigation-link` blocks — `VALID_BLOCK_PSEUDO_SELECTORS`); **`transition` / `transform`**; **sub-elements that are neither a block nor a theme.json element** (e.g. the social-link anchor `.wp-block-social-link-anchor` — design keys only reach the `core/social-link` block, i.e. the `<li>`); and **editor-only selector fixes** (the editor duplicates `wp-block-social-link` onto the inner anchor button, so a `.wp-block-social-link` border is drawn twice — reset the inner one). See `theme-json/styles/blocks/core-social-links.js` for all of these.
- Specificity to know: everything here emits at `:root :where(…)` = `0-1-0` (design keys and `css` alike), and the block-style-variation stylesheet loads after `wp-block-library`, so it beats core block CSS **of equal `0-1-0` specificity** by load order (e.g. core's brand colours, the default anchor padding). Within a node the `css` rules are emitted **after** its design-key rules, so a `css` override (hover fill, border reset) wins the `0-1-0` tie by source order.
- To beat a core rule of **higher** specificity, a `css` declaration needs `!important` (there is no way to raise specificity past `:root :where(…)`). Example: core scales social icons on hover via `.wp-block-social-link:hover` (`0-2-0`), so the variation's `css` suppresses it with `transform: none !important`. Reserve `!important` for exactly this — a single declaration overriding a core default for which **no editor control exists** (so it can't clobber a user setting); never use it where a user block setting should win. This keeps the whole variation in `theme.json`; fall to SCSS only if even `!important` can't do it.

---

## 4. Asset build (SCSS/JS)

CSS/JS is **compiled, not hand-edited** in `assets/`.

- **Source SCSS** lives in `resources/scss/`:
  - Entries: `resources/scss/frontend/index.scss` and `resources/scss/editor/index.scss`.
  - Both `@use "../global"`, so a partial added to `resources/scss/global/` and `@use`d from `global/index.scss` lands in **both** the frontend and editor bundles.
- **Output** goes to `assets/` (`frontend.css`, `editor.css`, their `-rtl` variants, and `*.asset.php`). The `Assets` module enqueues them; `editor.css` is loaded via `add_editor_style()`.
- **Build CSS/JS with:**
  ```bash
  node_modules/.bin/wp-scripts build
  ```
- **Do NOT use `npm run build` for CSS/JS-only changes.** Its `prebuild` hook runs `npm run theme-json`, which regenerates `theme.json` and can reformat/alter it unexpectedly. Use `wp-scripts build` directly to compile only the webpack entries and leave `theme.json` untouched.
- **Block-style CSS** (the SCSS that remains after §3 — the parts of a block style variation `theme.json` can't express): keep it free of `!important` and low-specificity so user block settings and WP preset classes (which use `!important`) still win.

`node_modules` and `vendor` are already installed.

---

## 5. Patterns

- **Location:** `patterns/`, one `.php` file per pattern.
- **File header** (PHP docblock) declares the pattern:
  ```php
  <?php
  /**
   * Title: <Generic Title>
   * Slug: amalfi/<slug>
   * Categories: <category_name>
   *
   * @package Amalfi
   */
  ?>
  ```
- **Slug, filename, and title must stay consistent — the slug *is* the sanitized title, and the filename matches the slug.** A pattern titled `Highlights (Grid)` has slug `amalfi/highlights-grid` and lives in `highlights-grid.php`; `Newsletter (Card)` → `amalfi/newsletter-card` → `newsletter-card.php`. Sanitizing lowercases, drops parentheses/`&`, and turns runs of non-alphanumerics into single hyphens. When you change any one of title/slug/filename, update the other two in the same change (and any `wp:pattern`/`patternName` references in site content). Use `mv`, not `git mv` (see §9).
- **Pattern body is block markup** composed from core blocks, formatted the same way the block editor serializes it (block comments + matching HTML, preset variables in both the attributes and the inline styles). Indentation nesting for readability is fine.
- **Categories** are registered in `src/Setup/BlockPatternCategory.php`. **A category's slug must match its label** — the slug is `amalfi_` + the lowercased label after `Amalfi:` with spaces as underscores (label `Amalfi: Page Sections` → slug `amalfi_page_sections`, `Amalfi: Query Loops` → `amalfi_query_loops`). Reference the registered slug in each pattern's `Categories:` header, and when you rename a category, update every pattern header that uses it.
- **Naming must be generic/structural**, describing the pattern's role, not any demo topic — e.g. "Testimonials", "Contributors", "Highlights", "Browse by Category", "Call to Action". No domain-specific names in the theme.
- After renaming/re-slugging a pattern or editing categories, **flush the pattern cache** so changes show up. On this **multisite** install the theme's pattern file list is a *network* (site) transient, so the `--network` flush is the one that matters; run both to be safe:
  ```bash
  ddev exec wp transient delete --all --network --url=https://themes-website.localhost/amalfi/
  ddev exec wp transient delete --all --url=https://themes-website.localhost/amalfi/
  ```

---

## 6. Images in block patterns (pxhere + readme.txt attribution)

Every image a pattern (or template) uses is a bundled theme asset. Keep the whole pipeline distribution-safe and attributed.

- **Source only from pxhere.com.** All pattern images must come from [pxhere.com](https://pxhere.com/), where every photo is licensed under **CC0 1.0 Universal (public domain)**. Do not use images from any other source (Unsplash, Pexels, other stock sites) unless the user explicitly approves — other licenses differ and would need separate attribution handling.
- **Store the source in `resources/images/`.** That is the source of truth. `wp-scripts build` copies `resources/images/**` → `assets/images/` (see the `CopyWebpackPlugin` config in `webpack.config.js`), and patterns reference the served copy via `get_theme_file_uri( 'assets/images/<file>.jpg' )`. Never hand-edit or drop files directly into `assets/images/` — add/replace them in `resources/images/` and rebuild.
- **Reuse before downloading.** Keep the bundled image set small (~10–15 total) and reuse existing images across patterns rather than downloading a new one each time.
- **Record the exact pxhere source at download time.** Capture the photo page URL (`https://pxhere.com/en/photo/<id>`) when you download — you need it for the readme entry below.

### readme.txt must stay in sync — automatically

Whenever you add, replace, or remove an image in `resources/images/`, update `readme.txt` in the **same change**. Introducing a new image is not finished until its readme entry exists.

- Under `== Resources ==` → `2. Images used in block patterns`, keep one lettered entry per image, in this exact shape:
  ```
  x) <Short description> used in the <Pattern name> block pattern (assets/images/<file>.jpg)
  Source: https://pxhere.com/en/photo/<id>
  License: Distributed under the terms of the CC0 1.0 Universal Public Domain Dedication (https://creativecommons.org/publicdomain/zero/1.0/)
  Copyright: Released into the public domain, no attribution required
  ```
- Because pxhere is CC0, the `License` and `Copyright` lines are **always** the two fixed lines above — only the description, pattern name, filename, and `Source` URL change per image.
- Re-letter the list (a, b, c, …) so it stays sequential, and remove an image's entry when you remove the image from the theme.

---

## 7. Templates & template parts (distribution-safe)

- **Templates:** `templates/*.html` (e.g. `index.html`, `home.html`, `single.html`, `archive.html`, `page.html`, `search.html`, `404.html`, plus custom templates like `page-fullwidth.html`). Custom templates are declared in `theme-json/customTemplates.js`.
- **Template parts:** `parts/*.html` (e.g. `header.html`, `footer.html`, `posts.html`), declared in `theme-json/templateParts.js`.
- Templates and parts are **block markup composed from core blocks**, same formatting rules as patterns.
- **Do NOT hardcode a navigation `ref` (menu ID)** in templates or parts — on a fresh install that menu won't exist. Leave `<!-- wp:navigation /-->` without a `ref`; it falls back gracefully.
- **Placeholder emails/links:** use neutral placeholders like `hello@example.com` — never invented or site-specific domains.
- Keep all visible text and links generic; demo-specific content belongs in the site's content, not in theme files.

---

## 8. Local WordPress access (dev environment)

WordPress runs locally via **ddev** (project `themes-website`, type `php`, root `/Users/thomasw/web/themes-website`).

- Run WP-CLI through the container: `ddev exec wp <command>` (the `ddev wp` shortcut is disabled because the project type is `php`).
- It is a **multisite** network with many subsites. Work **only on the `amalfi` subsite** (URL `https://themes-website.localhost/amalfi/`, `blog_id 44`).
- **Always pass `--url=https://themes-website.localhost/amalfi/`** on every WP-CLI command, so you never touch the main site or another subsite.
- **zsh batch gotcha:** the host shell (zsh) does not word-split an unquoted variable, so `wp post delete $IDS` acts on only the first ID. For multi-ID operations, pipe `--format=ids` through `xargs`, e.g.:
  ```bash
  ddev exec wp post list --post_type=post,page --post_status=any --url=https://themes-website.localhost/amalfi/ --format=ids | tr ' ' '\n' | grep -E '^[0-9]+$' | xargs -I{} ddev exec wp post delete {} --force --url=https://themes-website.localhost/amalfi/
  ```
  Verify afterward with `--format=count`. `--force` deletes permanently (bypasses trash) — use with care.

---

## 9. Version control

- **Never run git operations** (commit, branch, push, merge, rebase, stage, etc.) and don't offer to. The user handles all version control themselves.
- Leave changes in the working tree for the user to review and commit. If a workflow seems to need a commit, describe what changed and let the user decide.
- Rename files with `mv`, not `git mv`.

---

## 10. Quick checklist before finishing a change

- [ ] Built only from **WordPress Core blocks**; no Custom HTML block used for layout.
- [ ] Design expressed via **`theme.json`** (presets); custom CSS only where unavoidable.
- [ ] Edited **`theme-json/` fragments**, not `theme.json`; ran `npm run theme-json` if theme.json should change.
- [ ] Edited **`resources/` SCSS**, not `assets/`; ran `node_modules/.bin/wp-scripts build` for CSS/JS changes.
- [ ] PHP added under **`/src`** as a Packable module/service; nothing added to `functions.php` beyond module registration.
- [ ] Pattern/template/part names and content are **generic and distribution-safe** (no domain-specific wording, no hardcoded nav `ref`, placeholder emails only).
- [ ] Pattern **slug = filename = title**; pattern cache flushed if slugs/categories changed.
- [ ] Any new/changed pattern image is from **pxhere.com (CC0)**, stored in **`resources/images/`**, and **`readme.txt` Resources is updated** with its pxhere source URL.
- [ ] **No git operations** performed.
