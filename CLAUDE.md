# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A single static HTML marketing page for **Virtual Teammate** — a staffing agency for medical, dental, and business virtual assistants. The entire site is one self-contained file: [index.html](index.html). No build step, no package manager, no JavaScript, no external CSS.

## Serving / previewing

This project lives inside a WAMP webroot (`c:\wamp64\www\vtnew`), so it is served by the local Apache instance at:

```
http://localhost/vtnew/
```

To preview a change, save [index.html](index.html) and refresh that URL. There is no dev server, hot reload, lint, or test command — the only "build" is the browser parsing the file.

External dependencies are pulled from CDNs at runtime:
- Google Fonts: Manrope (all weights — body + display)
- Font Awesome 6.5.2 (icons, monochromatic — see `i.fa { color: var(--gold); }`)
- Unsplash images for hero collage, specialty cards, and VA profile photos

## Architecture you need to know before editing

### Responsive layout (fluid up to 1980px, with tablet + mobile breakpoints)

`body` uses `max-width:1980px; width:100%; margin:0 auto` so the canvas is fluid up to its 1980px ceiling — it doesn't render at a hard-coded 1980px on smaller screens. The viewport meta is `width=device-width, initial-scale=1`.

Two breakpoints live at the bottom of the `<style>` block:
- `@media (max-width:1280px)` — tablet / small-laptop: collapses the hero into a single column, stacks the ROI calculator panels, drops side padding from 80px → 32px, and reduces heading sizes.
- `@media (max-width:768px)` — mobile: hides the nav links and phone, single-column grids everywhere, mobile hero photo collage stacks vertically, floating hero chips and orbs scale down.

When adding new styles, mirror this pattern: write the desktop rule first, then add a `@media (max-width:1280px)` and/or `@media (max-width:768px)` override in the matching responsive block. Don't introduce a third breakpoint without a reason.

### Design tokens in `:root`

Colors and glass-morphism effects are centralized as CSS custom properties at the top of the `<style>` block (`--gold`, `--violet`, `--violet-dk`, `--glass-bg`, `--glass-blur`, etc.). When introducing a new element, reuse these tokens rather than hardcoding hex values or `rgba()`/`blur()` literals — the whole page leans on a consistent "glass over violet→gold gradient" aesthetic and ad-hoc colors will visually drift.

### Section pattern

Each content block follows the same skeleton:

```html
<div class="sec">
  <div class="sec-lbl">EYEBROW</div>
  <div class="sec-h2">Heading</div>
  <div class="sec-sub">Subhead paragraph.</div>
  <!-- section-specific grid -->
</div>
<div class="divider"></div>
```

Sections are separated by `.divider`. When adding a new section, follow this scaffold so spacing/typography stay consistent.

### Page order (top → bottom)

`topbar → nav → hero (+ stats) → client marquee → global network (world map) → news/press marquee → specialties (healthcare + dental + HIPAA strip + business services) → ROI calculator → process → testimonials → how we work → VA profiles → FAQ → CTA form → footer`

Use this map to locate the right block — search for the section comment (e.g. `<!-- PROCESS -->`) rather than scrolling.

### Naming conventions in the CSS

Class prefixes scope rules to a section: `hero-*`, `spec-*` (specialties), `biz-*` (business services), `roi-*`, `pstep-*`/`proc-*` (process), `test-*` (testimonials), `hw-*` (how we work), `prof-*` (profiles), `faq-*`, `cf-*` (CTA form), `ft-*` (footer). Stay inside the existing prefix when editing a section so styles don't leak.

### Sibling projects in `c:\wamp64\www\`

`vt`, `vtsite`, `vt_saas`, `vtadmin`, `staging_virtualteammate` are separate apps under the same webroot. They are **not** part of this project — don't read or edit them when working on `vtnew` unless the user asks.
