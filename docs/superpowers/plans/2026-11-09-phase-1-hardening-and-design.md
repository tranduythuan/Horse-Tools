# Phase 1: Hardening Concealment Features, Design System Modernization & Conditional Loading

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Eliminate hazardous legacy concealment mechanisms (replace fragile ordinal plugin hiding with deterministic basename hashing), modernize the UI skin system to 3 clean presets, standardize legacy `ft*` symbols to `ht*`, enforce conditional WooCommerce loading, verify zero security regressions, and release version 1.3.48.

**Architecture:** 
Refactor plugin hiding in `inc/horsetools.php` and `main/page/ht-setting.php` from numeric index array lookups to deterministic MD5 hashes of plugin basenames (`horsetools-hp-<hash>`) with backward-compatible migration. Streamline `main/style.php` to 3 well-defined CSS token palettes (Default Gold & Charcoal, WordPress Native, Dark Mode) while deprecating old legacy color schemes. Apply defensive conditional loading in `inc/woo.php` and `modal/modal.php` to eliminate fatal error risks and reduce memory overhead when WooCommerce is absent. Standardize remaining `ft*` frontend identifiers in `link/htadmin.js` and `main/page/5media.php`.

**Tech Stack:** PHP 8.1+, WordPress 6.0+, Vanilla JS, CSS3 Custom Properties, Gettext i18n (`.po`/`.mo`).

**Spec:** Phase 1 Development Roadmap for Horse Tools (`CLAUDE.md`, Security Audit Run-2 recommendations).

## Global Constraints

- Prefix all PHP functions with `horsetools_`, constants with `HORSETOOLS_`, option names with `horsetools_`, and CSS/JS tokens with `ht-`.
- Text domain MUST be strictly `'horse-tools'` for all translatable strings.
- Every user-facing string MUST have an exact Vietnamese translation in `tools/sync-translations.php` and pass `tools/check-translations.php` with 0 missing strings.
- Any security-sensitive action MUST enforce `current_user_can('manage_options')` and `check_ajax_referer` / nonces.
- Backward compatibility: existing saved user options MUST NOT cause fatal errors or unexpected broken states on update.

## Review Focus

1. **Plugin hiding collision or missing plugin:** Plugin filenames with special characters or varying paths (e.g. `foo/foo.php` vs single-file `hello.php`) must resolve deterministically via `md5($plugin_path)` without collisions.
2. **WooCommerce inactive or missing:** Sites without WooCommerce installed must not trigger fatal errors when `apply_filters('woocommerce_get_price_html')` is evaluated or when `inc/woo.php` is parsed.
3. **Legacy skin settings:** Existing installs with legacy skin settings ('Bright', 'Girly', 'Coffe', 'Rocket', 'Blue') must seamlessly fall back to 'Default' without PHP warnings or broken CSS variables.
4. **JavaScript tab selection state:** `htSelectedRank()` must properly restore active tab from `localStorage` while retaining `window.ftSelectedRank` fallback for any cached or external calls.
5. **Admin user query alteration:** User hiding in `inc/horsetools.php` must strictly validate user ID (`intval > 0`) and never hide the current logged-in user from their own session.

---

### Task 1: Replace Ordinal Plugin Hiding with Deterministic Basename Hashing

**Files:**
- Modify: `inc/horsetools.php:45-67`
- Modify: `main/page/ht-setting.php:54-71`
- Test: `tools/test-phase1.php`

**Interfaces:**
- Consumes: `get_plugins()`, `global $horsetools_options`
- Produces: `horsetools_hide_plugins(array $plugins): array` using deterministic hash keys `horsetools-hp-<md5>`

- [ ] **Step 1: Write test case in `tools/test-phase1.php` for plugin hiding**

Verify that:
1. `horsetools_hide_plugins()` correctly hides a plugin when keyed by `horsetools-hp-` . md5($plugin_path).
2. Installing/reordering other plugins does NOT shift or hide unselected plugins.
3. Legacy `horsetools-pu1` keys are either migrated or safely ignored.

- [ ] **Step 2: Run test to verify it fails**

Run: `D:\xampp\php\php.exe tools/test-phase1.php`
Expected: FAIL (test file or function assertion not matching new keying).

- [ ] **Step 3: Implement deterministic plugin hiding in `inc/horsetools.php` and `main/page/ht-setting.php`**

In `inc/horsetools.php`:
```php
function horsetools_hide_plugins($plugins) {
    global $horsetools_options;
    if ( ! is_array( $horsetools_options ) ) {
        return $plugins;
    }
    foreach ( $plugins as $plugin_path => $plugin_data ) {
        $hash_key = 'horsetools-hp-' . md5( $plugin_path );
        if ( ! empty( $horsetools_options[ $hash_key ] ) ) {
            unset( $plugins[ $plugin_path ] );
        }
    }
    return $plugins;
}
add_filter('all_plugins', 'horsetools_hide_plugins');
```

In `main/page/ht-setting.php`:
Loop through `$all_plugins as $plugin_path => $plugin_info`, generate `$key = 'horsetools-hp-' . md5( $plugin_path );`, and render the switch toggle.

In `horse-tools.php` (`horsetools_housekeeping`):
Add one-time migration for any legacy `horsetools-pu<N>` settings in `horsetools_settings` to `horsetools-hp-<hash>`.

- [ ] **Step 4: Run test to verify it passes**

Run: `D:\xampp\php\php.exe tools/test-phase1.php`
Expected: PASS for Task 1 assertions.

- [ ] **Step 5: Verify syntax**

Run: `D:\xampp\php\php.exe -l inc/horsetools.php && D:\xampp\php\php.exe -l main/page/ht-setting.php`
Expected: No syntax errors detected.

---

### Task 2: Standardize Design System Skins to 3 Modern Presets

**Files:**
- Modify: `main/style.php:1-25`
- Modify: `main/page/ht-setting.php:85-102`
- Test: `tools/test-phase1.php`

**Interfaces:**
- Consumes: `$horsetools_options['horsetools5']` (theme preset name)
- Produces: CSS `:root` variable definitions for `Default` (Gold/Charcoal), `WordPress` (Native Blue/Grey), and `Dark` (Pure Dark Mode, aliased from `Black`).

- [ ] **Step 1: Write test case in `tools/test-phase1.php` for skin output**

Verify that:
1. Setting skin to `'WordPress'` outputs core WordPress colors.
2. Setting skin to `'Dark'` or legacy `'Black'` outputs high-contrast dark mode colors.
3. Setting skin to `'Default'`, invalid values, or deprecated skins (`'Bright'`, `'Girly'`, etc.) outputs default Gold brand palette (or empty style block inheriting `link/htadmin.css`).

- [ ] **Step 2: Run test to verify it fails**

Run: `D:\xampp\php\php.exe tools/test-phase1.php`
Expected: FAIL for skin assertions.

- [ ] **Step 3: Update `main/style.php` and `main/page/ht-setting.php`**

In `main/style.php`:
Define clean styles for `WordPress` and `Dark` (and alias `Black` to `Dark`). Deprecate the remaining unmaintained skins (`Bright`, `Girly`, `Coffe`, `Rocket`, `Blue`), letting them fall back to the default Gold palette.

In `main/page/ht-setting.php`:
Present the 3 distinct thumbnails:
- `1.jpg`: Default (Gold & Warm Charcoal)
- `2.jpg`: WordPress (Native Core Admin)
- `5.jpg`: Dark Mode (Pure Dark)

Add clean user-facing labels under the selector.

- [ ] **Step 4: Run test to verify it passes**

Run: `D:\xampp\php\php.exe tools/test-phase1.php`
Expected: PASS for Task 2 assertions.

- [ ] **Step 5: Verify syntax**

Run: `D:\xampp\php\php.exe -l main/style.php && D:\xampp\php\php.exe -l main/page/ht-setting.php`
Expected: No syntax errors detected.

---

### Task 3: Clean Legacy `ft*` Identifiers in Admin JS and Media UI

**Files:**
- Modify: `link/htadmin.js:53-64`
- Modify: `main/page/5media.php:252-257`
- Test: `tools/test-phase1.php`

**Interfaces:**
- Produces: `htSelectedRank()` function in global JS scope, with `window.ftSelectedRank = htSelectedRank` alias.
- Produces: `htwBoxes` in `main/page/5media.php`.

- [ ] **Step 1: Write verification test for JS identifier consistency**

Scan `link/htadmin.js` and `main/page/5media.php` for unresolved `ftSelectedRank` or `ftwBoxes`.

- [ ] **Step 2: Update `link/htadmin.js` and `main/page/5media.php`**

Rename `ftSelectedRank()` -> `htSelectedRank()` in `link/htadmin.js`, and attach backwards-compatible bridge:
```javascript
function htSelectedRank() {
  var htranksel = localStorage.getItem('htranksel');
  if (htranksel) {
    var sotab = document.querySelector('[onclick="httab(event, \'' + htranksel + '\')"]');
    if (sotab) {
      sotab.click();
    }
  }
}
window.ftSelectedRank = htSelectedRank;
document.addEventListener("DOMContentLoaded", function() {
  htSelectedRank();
});
```
In `main/page/5media.php`:
Rename `ftwBoxes` -> `htwBoxes`.

- [ ] **Step 3: Run test to verify it passes**

Run: `D:\xampp\php\php.exe tools/test-phase1.php`
Expected: PASS.

---

### Task 4: Conditional WooCommerce Module Loading and Inactive Warning

**Files:**
- Modify: `inc/woo.php:1-5`
- Modify: `modal/modal.php:150-165`
- Modify: `main/page/8woo.php:1-12`
- Test: `tools/test-phase1.php`

**Interfaces:**
- Consumes: `class_exists( 'WooCommerce' )`
- Produces: Guarded execution in `inc/woo.php`, conditional skip in `modal/modal.php`, and informative admin warning in `main/page/8woo.php` when WooCommerce is not installed or inactive.

- [ ] **Step 1: Write test case in `tools/test-phase1.php` for WooCommerce guard**

Verify that:
1. When `class_exists('WooCommerce')` is false, `inc/woo.php` returns early without registering filters.
2. `modal/modal.php` skips including `inc/woo.php` if WooCommerce is inactive, saving memory.

- [ ] **Step 2: Run test to verify it fails**

Run: `D:\xampp\php\php.exe tools/test-phase1.php`
Expected: FAIL.

- [ ] **Step 3: Implement guards and notice**

In `inc/woo.php` line 2:
```php
if ( ! defined( 'ABSPATH' ) ) { exit; }
if ( ! class_exists( 'WooCommerce' ) ) { return; }
```

In `modal/modal.php`:
When iterating `$horsetools_option_files`:
```php
if ( 'woo' === $option_key && ! class_exists( 'WooCommerce' ) ) {
    continue;
}
```

In `main/page/8woo.php`:
```php
<?php if ( ! class_exists( 'WooCommerce' ) ) : ?>
<div class="notice notice-warning inline" style="margin: 12px 0; padding: 10px 14px;">
    <p><i class="ti ti-alert-triangle"></i> <?php esc_html_e( 'WooCommerce is not active. These settings will take effect once WooCommerce is activated.', 'horse-tools' ); ?></p>
</div>
<?php endif; ?>
```

- [ ] **Step 4: Run test to verify it passes**

Run: `D:\xampp\php\php.exe tools/test-phase1.php`
Expected: PASS.

- [ ] **Step 5: Verify syntax**

Run: `D:\xampp\php\php.exe -l inc/woo.php && D:\xampp\php\php.exe -l modal/modal.php && D:\xampp\php\php.exe -l main/page/8woo.php`
Expected: No syntax errors detected.

---

### Task 5: Translation Synchronization and Verification

**Files:**
- Modify: `tools/sync-translations.php`
- Modify: `lang/horse-tools-vi.po`, `lang/horse-tools-vi.mo`
- Test: `tools/check-translations.php`

**Interfaces:**
- Consumes: All `__()`, `_e()`, `esc_html__()` strings in PHP files
- Produces: 100% translated Vietnamese catalog with 0 missing strings

- [ ] **Step 1: Add new strings to `tools/sync-translations.php`**

Ensure all newly added strings (e.g. `'WooCommerce is not active. These settings will take effect once WooCommerce is activated.'`, `'Gold & Warm Charcoal'`, `'WordPress Native'`, `'Dark Mode'`, etc.) are registered in `$known_translations`.

- [ ] **Step 2: Run sync and compile translations**

Run: `D:\xampp\php\php.exe tools/sync-translations.php`
Expected: Updates `lang/horse-tools-vi.po` and compiles `lang/horse-tools-vi.mo`.

- [ ] **Step 3: Verify translation coverage**

Run: `D:\xampp\php\php.exe tools/check-translations.php`
Expected: `✓ All <N> unique translatable strings have a Vietnamese translation.` with 0 missing.

---

### Task 6: Version Bump, Housekeeping Migration & Release

**Files:**
- Modify: `horse-tools.php:8,24,320-348`
- Modify: `readme.txt:5,7, changelog`
- Test: Full test suite (`tools/test-*.php`)

**Interfaces:**
- Produces: Version `1.3.48`, automated release tag `v1.3.48`

- [ ] **Step 1: Update version in `horse-tools.php` and `readme.txt`**

Bump version from `1.3.47` to `1.3.48`.
Add changelog entry in `readme.txt` detailing:
- Hardening plugin hiding with deterministic basename hashing and automatic migration.
- Modernizing design presets to 3 clean styles (Default Gold, WordPress Native, Dark Mode).
- Standardizing legacy JavaScript identifiers.
- Conditional loading and safeguards for WooCommerce integration.

- [ ] **Step 2: Add housekeeping migration in `horse-tools.php`**

In `horsetools_housekeeping()`, add migration logic to migrate legacy `horsetools-pu<N>` keys to `horsetools-hp-<hash>`.

- [ ] **Step 3: Run comprehensive verification**

Run PHP lint on all PHP files in repo.
Run `tools/check-translations.php`.
Run all test runners in `tools/test-*.php`.

- [ ] **Step 4: Commit, Tag, and Push**

Commit changes: `refactor: harden plugin hiding, streamline skins, and optimize woocommerce loading (v1.3.48)`
Push commit to `origin main`.
Create tag `v1.3.48` and push to `origin v1.3.48`.
Monitor GitHub Actions workflow for successful package build and release.
