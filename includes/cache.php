<?php
/**
 * Public-site asset cache control.
 *
 * The super-admin portal (Site Cache card on the dashboard) writes
 * `data/cache-state.php` with an on/off flag and a version token. These helpers
 * read it cheaply (a single opcache-friendly include, memoised per request) and
 * decide the `?v=` query string on the marketing site's CSS/JS tags.
 *
 *   caching ENABLED  → ?v={filemtime}[-{version}]   (stable; deploys + manual
 *                       flushes both change it, so browsers cache until then)
 *   caching DISABLED → ?v={unique-per-load}          (URL never repeats, so the
 *                       browser always re-fetches — effectively no caching)
 *
 * Safe defaults: if the state file is missing (fresh install, or a deploy
 * environment without the portal), caching is treated as ENABLED — i.e. the
 * exact filemtime-busting behaviour the site had before this feature existed.
 */

if (!function_exists('vt_cache_state')) {
    function vt_cache_state(): array
    {
        static $state = null;
        if ($state !== null) {
            return $state;
        }
        $state = ['enabled' => true, 'version' => ''];
        $file  = __DIR__ . '/../data/cache-state.php';
        if (is_file($file)) {
            $loaded = @include $file;
            if (is_array($loaded)) {
                $state['enabled'] = !empty($loaded['enabled']);
                $state['version'] = (string) ($loaded['version'] ?? '');
            }
        }
        return $state;
    }
}

if (!function_exists('vt_asset_ver')) {
    function vt_asset_ver(string $absPath): string
    {
        $state = vt_cache_state();
        if (!$state['enabled']) {
            // Unique each request → the asset URL never repeats → no browser reuse.
            return (string) (function_exists('hrtime') ? hrtime(true) : (time() . mt_rand(1000, 9999)));
        }
        $mtime = @filemtime($absPath) ?: time();
        return $state['version'] !== '' ? $mtime . '-' . $state['version'] : (string) $mtime;
    }
}
