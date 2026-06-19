<?php
/**
 * Site-wide entry-point modal hub.
 *
 * Included by includes/footer.php on every page EXCEPT the homepage (which ships
 * its own inline copies). Guarantees the audit (#cta-book), strategy-call,
 * buyers-checklist and request modals exist on the current page, so CTAs open in
 * place instead of jumping to the homepage.
 *
 * Every sub-include is guarded, so this never double-renders a modal a page has
 * already added directly (landing pages' book-modal, service/business pages'
 * request-modal, etc.). request-modal.php is included last; it carries the generic
 * .cta-modal scroll-lock / ESC / autofocus behavior that drives ALL the modals on
 * the page (it no-ops if the page already wired its own).
 *
 * Set $home_base before include.
 */
if (defined('VT_CTA_MODALS_RENDERED')) { return; }
define('VT_CTA_MODALS_RENDERED', true);
$home_base = $home_base ?? './';
include __DIR__ . '/book-modal.php';       // #cta-book + HubSpot Meetings loader
include __DIR__ . '/jumpstart-modal.php';  // #cta-strategy-call (reuses the loader)
include __DIR__ . '/checklist-modal.php';  // #cta-buyers-checklist + submit handler
include __DIR__ . '/request-modal.php';    // #cta-request + generic .cta-modal behavior
