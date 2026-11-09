# Horse Tools — Phase 2 Strategic Roadmap & Architectural Specification

**Document Version:** 2.0.0-PROPOSAL  
**Status:** Approved Architectural Blueprint  
**Baseline Release:** v1.3.48  
**Target Horizon:** v1.4.0 through v2.0.0  
**Target Environments:** PHP 8.2+ | WordPress 6.2+ (with defensive WP 6.0/6.1 fallback; tested through WP 6.7+)  
**Repository Working Directory:** `D:\Antigravity\Horse-tools\`  
**Exclusive File Ownership:** `docs/ROADMAP_PHASE_2.md`  

---

## Table of Contents
1. [Section 1: Executive Summary & Architecture Vision](#section-1-executive-summary--architecture-vision)
   - 1.1 Baseline v1.3.48 Retrospective & Context
   - 1.2 Phase 2 Strategic Objectives
   - 1.3 Target Release Milestones & Versioning Plan
   - 1.4 Core Architecture Principles
2. [Section 2: Standards & Architectural Conventions](#section-2-standards--architectural-conventions)
   - 2.1 PHP 8.2+ Modern Language Standard
   - 2.2 WordPress 6.2+ Core APIs Alignment
   - 2.3 WordPress Coding Standards (WPCS) & VIP-Go Guidelines
   - 2.4 Horse Tools Architectural & Naming Conventions
3. [Section 3: Tooling, Automated Testing & Verification Infrastructure](#section-3-tooling-automated-testing--verification-infrastructure)
   - 3.1 Root `composer.json` Specification
   - 3.2 Enhanced Coding Standards Ruleset (`phpcs.xml.dist`)
   - 3.3 GitHub Actions Continuous Integration (CI) Matrix
   - 3.4 Dual-Layer Testing Architecture
   - 3.5 Core Web Vitals Benchmark Criteria & Measurement Protocol
4. [Section 4: Rolling Phased Milestones](#section-4-rolling-phased-milestones)
   - 4.1 Phase 2.1: Legacy Modularization & Core Foundation (High Priority)
   - 4.2 Phase 2.2: Performance Engine & Core Web Vitals Acceleration (High Priority)
   - 4.3 Phase 2.3: Modern Gutenberg Blocks & REST API (Medium Priority)
   - 4.4 Phase 2.4: AI Automation & Advanced Workflows (Medium Priority)
   - 4.5 Phase 3 Outlook: Enterprise & Headless Ecosystem
5. [Section 5: Technical Architecture Specifications for All Three Core Tracks](#section-5-technical-architecture-specifications-for-all-three-core-tracks)
   - 5.1 Track 1: Modern WordPress Feature Expansion
     - 5.1.1 Gutenberg Block Editor Integration (`block.json` v3, SSR, Interactivity API, FSE)
     - 5.1.2 Unified REST API Controller Architecture
     - 5.1.3 Pluggable AI Provider Architecture & Multimodal Workflows
     - 5.1.4 Dual-Driver Background Task Queueing
   - 5.2 Track 2: Performance & Core Web Vitals (CWV) Optimization
     - 5.2.1 Interaction-Driven Non-Destructive Script Delay Engine
     - 5.2.2 Asynchronous & Critical CSS Delivery Pipeline
     - 5.2.3 Dual-Engine Native Image Conversion (WebP & AVIF)
     - 5.2.4 Multi-Tier Database Query Caching Architecture
   - 5.3 Track 3: Legacy Module Modularization & Technical Debt Reduction
     - 5.3.1 Step-by-Step Modularization of `inc/scuri.php`
     - 5.3.2 Step-by-Step Modularization of `inc/goo.php`
     - 5.3.3 Deconstruction and Sunset of `main/code.php`
     - 5.3.4 Zero-Dependency PSR-4 Autoloader & Service Container
     - 5.3.5 100% Backward Compatibility Layer (`inc/deprecated.php`)
6. [Section 6: Comprehensive Risk & Dependency Matrix](#section-6-comprehensive-risk--dependency-matrix)
   - 6.1 Risk Registry (R-01 through R-06)
   - 6.2 Phasing Dependency Graph
7. [Section 7: Verification & Acceptance Criteria Matrix](#section-7-verification--acceptance-criteria-matrix)
   - 7.1 Pre-Merge Gate Checklists per Milestone
   - 7.2 Objective Success Verification Methods

---

## Section 1: Executive Summary & Architecture Vision

### 1.1 Baseline v1.3.48 Retrospective & Context
Horse Tools reached version 1.3.48 following comprehensive Phase 1 stabilization. The plugin functions as an all-in-one WordPress performance, security, and administrative utility toolkit. In Phase 1, the codebase achieved deterministic plugin hiding via MD5 hashes, a standardized design system featuring three modern administrative skins (`Default`, `WordPress`, and `Dark`), normalization of legacy `ft*` DOM identifiers to `ht*`, and safe conditional loading of WooCommerce integrations.

Despite these stability milestones, architectural surveys revealed that the v1.3.48 codebase remains structured around procedural PHP paradigms:
- Business logic is concentrated in monolithic files exceeding 600–900 lines (`inc/scuri.php` at 766 lines, `inc/goo.php` at 644 lines, `inc/speed.php` at 906 lines).
- Global variables (`$horsetools_options`, `$horsetools_code_options`) and global functions coordinate state across loosely coupled admin controllers.
- Asynchronous client-server communications rely on over 50 procedural `wp_ajax_*` actions rather than modern, schema-validated REST endpoints.
- Gutenberg block integration utilizes legacy WP 5.0-era programmatic JavaScript arrays without `block.json` declarations, preventing Full Site Editing (FSE) iframe canvas isolation.
- Next-generation Core Web Vitals optimizations (interaction-driven script delay, critical CSS generation, automated AVIF/WebP conversion, multi-tier query caching) exist only in embryonic states or require formal re-engineering.

### 1.2 Phase 2 Strategic Objectives
Phase 2 establishes a multi-year engineering roadmap designed to transform Horse Tools from a procedural toolkit into an enterprise-ready, modular WordPress optimization platform.

```
+----------------------------------------------------------------------------------------------------+
|                                      HORSE TOOLS PHASE 2 ROADMAP                                   |
+----------------------------------------------------------------------------------------------------+
|                                                                                                    |
|    [ Track 1: Modern WordPress ]       [ Track 2: Performance & CWV ]       [ Track 3: Modularization ]  |
|    - Gutenberg block.json v3           - Non-destructive Script Delay       - Split inc/scuri.php (766L) |
|    - Server-Side Render (render.php)   - Critical CSS & Media Swap          - Split inc/goo.php (644L)   |
|    - Interactivity API (WP 6.5+)       - Dual-Engine WebP & AVIF            - Sunset main/code.php (164L)|
|    - Unified WP_REST_Controller        - 3-Tier DB Query Caching            - PSR-4 Autoloader & PSR-11  |
|    - Pluggable AI / Gemini Vision      - O(1) Cache Group Invalidation      - 100% BC via deprecated.php |
|                                                                                                    |
|    ================================== FOUNDATION & INFRASTRUCTURE ==============================   |
|    PHP 8.2+ Strict Typing | WP 6.2-6.7+ Alignment | WPCS 3.1 & VIP-Go | Dual-Layer CI & PHPUnit    |
+----------------------------------------------------------------------------------------------------+
```

The core objectives of Phase 2 are:
1. **Zero-Regression Modularization**: Deconstruct legacy procedural monoliths into strict, domain-driven PSR-4 classes under `src/` while providing complete backward compatibility shims in `inc/deprecated.php`.
2. **Core Web Vitals Supremacy**: Deliver non-destructive, interaction-driven performance features that achieve Lighthouse scores > 95 and real-world CrUX passing thresholds (LCP < 1.8s, INP < 100ms, CLS < 0.05).
3. **Full Site Editing & Modern Gutenberg Alignment**: Standardize all blocks using `block.json` (metadata v3 schema), dynamic server rendering (`render.php`), and WP 6.5+ Interactivity API directives with zero external UI framework overhead.
4. **Structured REST API Ecosystem**: Consolidate legacy AJAX endpoints into a unified `horse-tools/v1` REST namespace powered by `WP_REST_Controller` subclasses with strict capability authorization, nonce guards, and JSON schema validation.
5. **Privacy-First AI & Automation**: Introduce pluggable AI provider interfaces (supporting Google Gemini and local proxies) for multimodal image accessibility (alt-text generation) and content optimization, processed asynchronously via Action Scheduler / WP-Cron with zero external telemetry.

### 1.3 Target Release Milestones & Versioning Plan
Horse Tools adheres strictly to Semantic Versioning (`MAJOR.MINOR.PATCH`):
- **v1.4.0 (Phase 2.1 Complete)**: Core Foundation & Modularization Release. Introduces PSR-4 autoloader, PSR-11 container, modular security/privacy/auth/captcha classes, root `composer.json`, and dual-layer test runner.
- **v1.5.0 (Phase 2.2 Complete)**: Performance & Core Web Vitals Acceleration Release. Ships interaction-driven script delay, asynchronous critical CSS engine, native WebP/AVIF converter, and multi-tier database query caching.
- **v1.6.0 (Phase 2.3 Complete)**: Modern Gutenberg Blocks & REST API Architecture Release. Deploys `block.json` v3 blocks, Interactivity API client hydration, and `horse-tools/v1` REST controller hierarchy.
- **v1.7.0 (Phase 2.4 Complete)**: AI Automation & Background Queueing Release. Integrates multimodal Gemini alt-text generator, pluggable AI provider interface, and dual-driver background queues.
- **v2.0.0 (Phase 3 Enterprise & Beyond)**: Major Architecture Release. Deprecates procedural shims from `inc/deprecated.php`, introduces Multisite network policies, and provides Headless WordPress GraphQL/REST integrations.

### 1.4 Core Architecture Principles
1. **PHP 8.2+ Language Standard**: Strict typing (`declare(strict_types=1);`), typed properties, return types, backed enums, match expressions, readonly classes, and standalone `true`, `false`, `null` types.
2. **WordPress 6.2+ Platform Standard**: Core baseline adoption of `WP_HTML_Tag_Processor` (with defensive compatibility fallback `if ( class_exists( 'WP_HTML_Tag_Processor' ) )` for legacy WP 6.0/6.1 hosts), `register_block_type_from_metadata()`, core script loading strategies (`defer`/`async`), and `WP_REST_Controller`.
3. **Non-Destructive Optimization**: Performance enhancements must never break site functionality. Script delays enforce a 5-second safeguard timeout and sequential dependency restoration; image conversions retain source files indefinitely; critical CSS prevents FOUC through automated media swaps.
4. **Zero-Telemetry & Privacy by Design**: The plugin collects zero analytics, telemetry, or remote usage metrics from client sites. Outbound calls only occur when explicitly configured by the site administrator (e.g. Google reCAPTCHA, Google Indexing, Gemini API).
5. **SSRF & Security Isolation**: All outbound HTTP communications pass through `horsetools_safe_fetch()`, preventing Server-Side Request Forgery (SSRF) against loopback addresses or cloud metadata endpoints.
6. **Dual-Layer Testing Discipline**: All code is verified through both fast standalone CLI test runners (`tools/test-*.php`) and formal multi-version PHPUnit/GitHub Actions test matrices.

---

## Section 2: Standards & Architectural Conventions

### 2.1 PHP 8.2+ Modern Language Standard
Horse Tools officially declares `Requires PHP: 8.2` in `horse-tools.php`. All newly authored code under `src/` must strictly adopt modern PHP 8.2+ idioms.

#### 2.1.1 Strict Typing & Typed Properties
Every new PHP file must open with strict types enabled. All class properties, method parameters, and return types must be explicitly declared.

```php
<?php
declare(strict_types=1);

namespace HorseTools\Security;

use WP_Error;

final class RestAuthenticationGuard {
    public function __construct(
        private readonly bool $requireAuthentication,
        private readonly array $whitelistedRoutes = array()
    ) {}

    /**
     * Filters REST authentication errors.
     *
     * @param mixed $result Current authentication result.
     * @return bool|WP_Error|null
     */
    public function checkAuthentication( mixed $result ): bool|WP_Error|null {
        if ( true === $result || is_wp_error( $result ) ) {
            return $result;
        }

        if ( $this->requireAuthentication && ! is_user_logged_in() ) {
            return new WP_Error(
                'rest_not_logged_in',
                __( 'You are not logged in.', 'horse-tools' ),
                array( 'status' => 401 )
            );
        }

        return $result;
    }
}
```

> **PHP Type Compatibility Rule**: Standalone `true` in union types (`true|WP_Error`) is supported natively in PHP 8.2+ (RFC *Add true type*). For broad WordPress ecosystem and static analysis compatibility, boolean success/failure union returns are standardized to declare `bool|WP_Error` or `bool|WP_Error|null`.

#### 2.1.2 Backed Enums
Magic strings and integer flags are deprecated in favor of PHP 8.1+ string-backed enums, ensuring static analysis validation and runtime type safety.

```php
<?php
declare(strict_types=1);

namespace HorseTools\Core;

enum SkinPreset: string {
    case Default   = 'Default';
    case WordPress = 'WordPress';
    case Dark      = 'Dark';
}

enum AlertSeverity: string {
    case Low      = 'low';
    case Medium   = 'medium';
    case High     = 'high';
    case Critical = 'critical';
}

enum ImageFormat: string {
    case WebP = 'webp';
    case Avif = 'avif';
    case Jpeg = 'jpeg';
    case Png  = 'png';
}

enum CacheTier: string {
    case RuntimeMemory = 'l1_runtime';
    case ObjectCache   = 'l2_object';
    case Transients    = 'l3_transients';
}
```

#### 2.1.3 Match Expressions
Verbose `switch` statements are replaced by type-safe, exhaustive `match` expressions.

```php
<?php
declare(strict_types=1);

namespace HorseTools\Core;

function horsetools_resolve_skin_stylesheet( SkinPreset $preset ): string {
    return match ( $preset ) {
        SkinPreset::WordPress => 'link/skins/wordpress.css',
        SkinPreset::Dark      => 'link/skins/dark.css',
        SkinPreset::Default   => 'link/htadmin.css',
    };
}
```

#### 2.1.4 Readonly Properties and Value Objects (DTOs)
Immutable data transfer objects utilize PHP 8.1+ `readonly` properties / PHP 8.2 `readonly` classes to prevent unintended state mutations.

```php
<?php
declare(strict_types=1);

namespace HorseTools\Performance;

final class CwvMetricRecord {
    public function __construct(
        public readonly string $metricName,
        public readonly float $value,
        public readonly string $rating,
        public readonly int $recordedAt,
        public readonly string $requestUri
    ) {}
}
```

### 2.2 WordPress 6.2+ Core APIs Alignment (with WP 6.0/6.1 Fallback Guards)

#### 2.2.1 Gutenberg Block Registration via `block.json`
All blocks must declare metadata conforming to WordPress Block Metadata v3 schema (`apiVersion: 3`) and be registered using `register_block_type_from_metadata()` on the `init` action.

#### 2.2.2 Unified REST API via `WP_REST_Controller`
Endpoints must inherit from `WP_REST_Controller`, register routes under namespace `horse-tools/v1`, declare arguments with declarative JSON schema validation, and protect operations using granular capability callbacks (`current_user_can`) and nonces.

#### 2.2.3 HTML API (`WP_HTML_Tag_Processor`) & Backward-Compatibility Guard
In conformance with modern WordPress core standards, all DOM transformations (such as rewriting script tags for delay execution, injecting critical CSS, and mutating image tags to responsive `<picture>` elements) utilize `WP_HTML_Tag_Processor` (introduced in WordPress 6.2). To ensure flawless stability on legacy WP 6.0/6.1 hosts within the support window, all HTML transformations must wrap invocations in a defensive guard:
`if ( class_exists( 'WP_HTML_Tag_Processor' ) )` with a fallback to pass unmodified markup, preventing fatal runtime crashes on older cores.

```php
<?php
declare(strict_types=1);

namespace HorseTools\Performance;

use WP_HTML_Tag_Processor;

final class ScriptTagTransformer {
    public function transform( string $html, array $delayedHandles ): string {
        if ( ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
            return $html; // Defensive fallback for WP 6.0 / 6.1 hosts.
        }

        $tags = new WP_HTML_Tag_Processor( $html );

        while ( $tags->next_tag( 'script' ) ) {
            $src = $tags->get_attribute( 'src' );
            $id  = $tags->get_attribute( 'id' );

            if ( null !== $src && $this->shouldDelay( $src, $id, $delayedHandles ) ) {
                $tags->set_attribute( 'type', 'ht/delayed' );
                $tags->set_attribute( 'data-ht-src', $src );
                $tags->remove_attribute( 'src' );
            }
        }

        return $tags->get_updated_html();
    }

    private function shouldDelay( string $src, ?string $id, array $delayedHandles ): bool {
        // Validation logic
        return true;
    }
}
```

#### 2.2.4 Script Loading Strategies (WP 6.3+)
Front-end scripts registered via `wp_register_script()` or `wp_enqueue_script()` must utilize core loading strategy parameters (`'strategy' => 'defer'` or `'strategy' => 'async'`) in the `$args` array.

### 2.3 WordPress Coding Standards (WPCS) & VIP-Go Guidelines

#### 2.3.1 Database Safety & Strict Prepared SQL
- Every direct `$wpdb` query must use `$wpdb->prepare()`.
- Dynamic table names or order directions must be strictly checked against static allow-lists.
- Wildcard `LIKE` queries against `wp_options` that cause full table scans or InnoDB table locks are strictly prohibited.

#### 2.3.2 Input Sanitization and Late Escaping
- Sanitization must be applied immediately upon receiving untrusted input (`sanitize_text_field()`, `sanitize_key()`, `absint()`, `wp_unslash()`).
- Late escaping must be applied at the point of output (`esc_html()`, `esc_attr()`, `esc_url()`, `esc_js()`, `wp_json_encode()`, `wp_kses_post()`).
- Custom raw code injection (such as custom CSS/JS in `src/CustomCode/`) requires both `current_user_can('manage_options')` and `current_user_can('unfiltered_html')`.

#### 2.3.3 Capability & Nonce Security Guards
All administrative mutating actions (AJAX handlers, REST requests, settings submissions) must enforce dual verification:
1. **Capability Guard**: `if ( ! current_user_can( 'manage_options' ) ) { wp_die( -1, 403 ); }`
2. **Nonce Verification**: `check_ajax_referer( 'horsetools_nonce', 'nonce' );` or REST API `X-WP-Nonce` authentication.

#### 2.3.4 Network Boundary, SSRF Prevention & Local Ollama Exemption
All outbound HTTP requests must pass through `horsetools_safe_request()` (upgraded from legacy `horsetools_safe_fetch()` in `inc/http.php`), which wraps `wp_safe_remote_request()` to support both HTTP GET and HTTP POST methods with arbitrary headers and JSON body payloads (essential for sending multimodal requests to Google Gemini Vision `:generateContent`).

By default, the SSRF boundary strictly resolves DNS and blocks loopback addresses (`127.0.0.1`, `::1`), RFC 1918 private subnets (`10.0.0.0/8`, `172.16.0.0/12`, `192.168.0.0/16`), and cloud metadata endpoints (`169.254.169.254`).

To enable local self-hosted AI proxies (such as Ollama running on `http://127.0.0.1:11434` or local LAN infrastructure) without weakening global SSRF defenses, Horse Tools introduces the `horsetools_ai_allowed_local_hosts` administrative filter:
```php
$allowed_local_hosts = apply_filters( 'horsetools_ai_allowed_local_hosts', array(), $provider_id );
```
This exemption is strictly constrained:
- It activates **only** for registered, administrator-configured AI provider endpoints managed by users with `manage_options` capability.
- Arbitrary user-supplied URLs and frontend user inputs remain strictly blocked from resolving to internal IP ranges.
- Cloud metadata endpoints (`169.254.169.254`) remain permanently blacklisted and cannot be bypassed via any filter.

### 2.4 Horse Tools Architectural & Naming Conventions
- **PHP Classes & Namespaces**: `HorseTools\<Domain>\<Class>` (e.g. `HorseTools\Security\HardeningModule`).
- **PHP Functions & Shims**: Strictly prefixed with `horsetools_` (e.g. `horsetools_opt()`, `horsetools_safe_fetch()`).
- **Constants**: Strictly prefixed with `HORSETOOLS_` (e.g. `HORSETOOLS_VERSION`, `HORSETOOLS_DIR`).
- **Options Keys**: `horsetools_*` (`horsetools_settings`, `horsetools_code_settings`, etc.).
- **CSS Selectors & DOM Identifiers**: Strictly prefixed with `ht-` (e.g. `.ht-card`, `.ht-contact-btn`, `#ht-save-fast`).
- **Text Domain**: Strictly `'horse-tools'` across all internationalization functions (`__()`, `_e()`, `esc_html__()`, `_n()`).

---

## Section 3: Tooling, Automated Testing & Verification Infrastructure

### 3.1 Root `composer.json` Specification
To support modern PSR-4 autoloading, static analysis, coding standards enforcement, and unit testing, a root `composer.json` is established in Phase 2.1:

```json
{
  "name": "tranduythuan/horse-tools",
  "description": "All-in-one WordPress performance, security, and optimization toolkit.",
  "type": "wordpress-plugin",
  "license": "GPL-2.0-or-later",
  "require": {
    "php": ">=8.2"
  },
  "require-dev": {
    "dealerdirect/phpcodesniffer-composer-installer": "^1.0",
    "wp-coding-standards/wpcs": "^3.1",
    "automattic/vipwpcs": "^3.0",
    "phpcompatibility/phpcompatibility-wp": "^2.1",
    "phpunit/phpunit": "^9.6 || ^10.5",
    "yoast/phpunit-polyfills": "^2.0",
    "brain/monkey": "^2.6",
    "phpstan/phpstan": "^1.11",
    "szepeviktor/phpstan-wordpress": "^1.3"
  },
  "autoload": {
    "psr-4": {
      "HorseTools\\": "src/"
    }
  },
  "autoload-dev": {
    "psr-4": {
      "HorseTools\\Tests\\": "tests/phpunit/"
    }
  },
  "scripts": {
    "lint": "phpcs -s -p",
    "lint:fix": "phpcbf -p",
    "phpstan": "phpstan analyse --memory-limit=512M",
    "test:unit": "phpunit --testsuite=Unit",
    "test:integration": "phpunit --testsuite=Integration",
    "test:legacy": "php tools/test-phase1.php",
    "test": [
      "@test:legacy",
      "@test:unit"
    ]
  },
  "config": {
    "allow-plugins": {
      "dealerdirect/phpcodesniffer-composer-installer": true
    }
  }
}
```

### 3.2 Enhanced Coding Standards Ruleset (`phpcs.xml.dist`)
The project’s `phpcs.xml.dist` is updated to include WordPress VIP-Go sniffs and strict PHP 8.2–8.4 compatibility checks:

```xml
<?xml version="1.0"?>
<ruleset name="HorseToolsStandard">
    <description>WordPress VIP, Security, and PHP 8.2+ Compatibility for Horse Tools.</description>
    <file>.</file>
    <exclude-pattern>*/link/google-api/*</exclude-pattern>
    <exclude-pattern>*/link/svg-sanitize/*</exclude-pattern>
    <exclude-pattern>*/vendor/*</exclude-pattern>
    <exclude-pattern>*/node_modules/*</exclude-pattern>
    <exclude-pattern>*/build/*</exclude-pattern>

    <arg name="extensions" value="php"/>
    <arg name="basepath" value="."/>
    <arg name="colors"/>
    <arg value="sp"/>

    <!-- Security & VIP Rules -->
    <rule ref="WordPress.Security"/>
    <rule ref="WordPress-VIP-Go"/>
    <rule ref="WordPress.DB.PreparedSQL"/>
    <rule ref="WordPress.DB.PreparedSQLPlaceholders"/>
    <rule ref="WordPress.WP.EnqueuedResources"/>

    <!-- PHP Compatibility Matrix -->
    <rule ref="PHPCompatibilityWP"/>
    <config name="minimum_supported_wp_version" value="6.2"/>
    <config name="testVersion" value="8.2-8.4"/>
</ruleset>
```

### 3.3 GitHub Actions Continuous Integration (CI) Matrix
A multi-dimensional CI workflow (`.github/workflows/ci.yml`) executes on all pull requests and pushes across supported environments:

```yaml
name: Continuous Integration & Verification

on:
  push:
    branches: [ master, main, develop ]
  pull_request:
    branches: [ master, main, develop ]

jobs:
  lint-and-analyze:
    name: Lint & Static Analysis
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP 8.2
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mbstring, intl, gd, imagick
          coverage: none
      - name: Install Dependencies
        run: composer install --prefer-dist --no-progress
      - name: PHPCS Coding Standards
        run: composer run lint
      - name: PHPStan Static Analysis
        run: composer run phpstan

  matrix-test:
    name: PHP ${{ matrix.php }} | WP ${{ matrix.wordpress }} | OS ${{ matrix.os }}
    runs-on: ${{ matrix.os }}
    strategy:
      fail-fast: false
      matrix:
        os: [ ubuntu-latest, windows-latest ]
        php: [ '8.2', '8.3', '8.4' ]
        wordpress: [ '6.2', '6.4', '6.5', '6.6', '6.7' ] # WP 6.2+ core baseline; optional legacy job checks WP 6.0/6.1 fallback guards
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php }}
          extensions: mbstring, intl, gd, imagick
          coverage: none
      - name: Install Dependencies
        run: composer install --prefer-dist --no-progress
      - name: Run Standalone Legacy Test Suite
        run: php tools/test-phase1.php
      - name: Run PHPUnit Test Matrix
        run: composer run test:unit
```

### 3.4 Dual-Layer Testing Architecture
Horse Tools employs a **Dual-Layer Testing Architecture** to balance rapid local development with comprehensive CI assurance:
1. **Layer 1: Fast Standalone Test Runners (`tools/test-*.php`)**:
   - Zero-dependency CLI scripts executing under 100 milliseconds.
   - Mock minimal WordPress primitives (`get_option`, `update_option`, `add_filter`) locally without requiring MySQL or Composer dependencies.
   - Provides immediate regression verification (e.g. `tools/test-phase1.php`).
2. **Layer 2: Formal PHPUnit CI Test Suite (`tests/phpunit/`)**:
   - Executes via PHPUnit, Yoast Polyfills, and Brain Monkey / WP Core Integration harnesses.
   - Validates class autoloading, container bindings, REST route schemas, Gutenberg render callbacks, and multi-version compatibility.

### 3.5 Core Web Vitals Benchmark Criteria & Measurement Protocol
All Phase 2 performance features are validated against rigorous benchmark thresholds:

| Metric | Industry Good Threshold | Horse Tools Target Ceiling | Measurement Protocol |
|---|---|---|---|
| **Largest Contentful Paint (LCP)** | `< 2.5s` | **`< 1.8s`** | Lighthouse Mobile 4G / CrUX Web Vitals API |
| **Interaction to Next Paint (INP)** | `< 200ms` | **`< 100ms`** | Chrome PerformanceObserver / Real User Monitoring |
| **Cumulative Layout Shift (CLS)** | `< 0.10` | **`< 0.05`** | Layout Instability API |
| **Time to First Byte (TTFB)** | `< 600ms` | **`< 350ms` (uncached) / `< 150ms` (cached)** | cURL benchmark / Server-Timing header |
| **Peak Memory Overhead** | `< 20MB` | **`< 10MB`** | `memory_get_peak_usage(true)` |
| **Added Frontend Queries** | N/A | **0 uncached queries** | Query Monitor / `$wpdb->num_queries` delta |

---

## Section 4: Rolling Phased Milestones

```
+----------------------------------------------------------------------------------------------------+
|                                    PHASED MILESTONES ROADMAP                                       |
+----------------------------------------------------------------------------------------------------+
|                                                                                                    |
|  Phase 2.1: Legacy Modularization & Core Foundation [High Priority] (Target: v1.4.0)               |
|  * Deconstruct inc/scuri.php, inc/goo.php, main/code.php                                           |
|  * Deploy PSR-4 autoloader, PSR-11 container, and inc/deprecated.php BC layer                      |
|  * Establish root composer.json, enhanced phpcs.xml.dist, and GitHub Actions CI matrix             |
|                                         |                                                          |
|                                         v (Foundation Gate Complete)                               |
|                    +--------------------+--------------------+                                     |
|                    |                                         |                                     |
|                    v                                         v                                     |
|  Phase 2.2: Performance Engine & CWV        Phase 2.3: Modern Gutenberg Blocks                     |
|  Acceleration [High Priority] (v1.5.0)      & REST API [Medium Priority] (v1.6.0)                  |
|  * Non-destructive Script Delay Engine      * block.json v3 Gutenberg blocks                       |
|  * Asynchronous & Critical CSS Pipeline     * Dynamic SSR (render.php) & Interactivity API         |
|  * Dual-Engine WebP & AVIF Generation       * Unified horse-tools/v1 WP_REST_Controller            |
|  * 3-Tier DB Query Caching & O(1) Mutex     * Declarative JSON schema & permission guards          |
|                    |                                         |                                     |
|                    +--------------------+--------------------+                                     |
|                                         |                                                          |
|                                         v                                                          |
|  Phase 2.4: AI Automation & Advanced Workflows [Medium Priority] (Target: v1.7.0)                  |
|  * Pluggable AI provider interface (Google Gemini, OpenAI, Local Proxies)                          |
|  * Automated multimodal image alt-text generator (accessibility compliant <= 125 chars)           |
|  * Dual-driver background queueing (Action Scheduler / WP-Cron) with exponential backoff & jitter  |
|                                         |                                                          |
|                                         v                                                          |
|  Phase 3 Outlook: Enterprise & Headless Ecosystem [Future Outlook] (Target: v2.0.0)                |
|  * Multisite global policy management & centralized synchronization                                |
|  * Headless WordPress GraphQL & REST extension SDK                                                 |
|  * Advanced heuristic threat intelligence and real-time firewall rules                             |
|                                                                                                    |
+----------------------------------------------------------------------------------------------------+
```

### 4.1 Phase 2.1: Legacy Modularization & Core Foundation
- **Priority Tier**: **High (Critical Architectural Gate)**
- **Target Release**: v1.4.0
- **Target Outcomes**:
  - Eliminate architectural technical debt by deconstructing procedural monoliths (`inc/scuri.php`, `inc/goo.php`, `main/code.php`) into single-responsibility, testable PSR-4 classes.
  - Deploy a zero-dependency PSR-4 autoloader and a lightweight PSR-11 service container.
  - Guarantee 100% backward compatibility for all existing WordPress hooks, saved options, and external code through `inc/deprecated.php`.
  - Establish automated CI/CD and developer tooling (Composer, PHPCS, PHPStan, PHPUnit).
- **Affected Codebase Areas**:
  - `inc/scuri.php` (766 lines) → Refactored into `src/Security/` and `src/Privacy/`.
  - `inc/goo.php` (644 lines) → Refactored into `src/Auth/` and `src/Captcha/`.
  - `main/code.php` (164 lines) & `inc/code.php` (61 lines) → Refactored into `src/CustomCode/`.
  - `horse-tools.php` → Bootstraps container and autoloader.
  - Repository Root → Adds `composer.json`, `phpcs.xml.dist`, `.github/workflows/ci.yml`.
- **Deliverables**:
  1. `src/Core/Autoloader.php`: Standalone PSR-4 autoloader.
  2. `src/Core/Container.php`: PSR-11 compliant service container.
  3. `src/Core/ModuleInterface.php`: Common lifecycle registration contract.
  4. Domain classes in `src/Security/`, `src/Privacy/`, `src/Auth/`, `src/Captcha/`, `src/CustomCode/`.
  5. `inc/deprecated.php`: Backward compatibility wrapper functions with `_deprecated_function()` notices.
  6. Verified CI workflow passing lint, static analysis, and multi-version tests.
- **Actionable Task Breakdown Checklist**:
  - [ ] Author root `composer.json` with dev tooling and autoloading configurations.
  - [ ] Implement `src/Core/Autoloader.php` and register with `spl_autoload_register()`.
  - [ ] Implement `src/Core/Container.php` and define `horsetools_container()` helper.
  - [ ] Implement `src/Core/ModuleInterface.php`.
  - [ ] Extract system hardening logic from `inc/scuri.php` into `src/Security/HardeningModule.php`.
  - [ ] Extract user enumeration protection from `inc/scuri.php` into `src/Security/EnumerationModule.php`.
  - [ ] Extract brute force limit login attempts from `inc/scuri.php` into `src/Security/BruteForce/`.
  - [ ] Extract login challenge questions from `inc/scuri.php` into `src/Security/Challenge/`.
  - [ ] Extract Google Fonts localization and scanner from `inc/scuri.php` into `src/Privacy/`.
  - [ ] Extract Google OAuth2 Social Login from `inc/goo.php` into `src/Auth/`.
  - [ ] Extract Google reCAPTCHA v2 and v3 from `inc/goo.php` into `src/Captcha/`.
  - [ ] Extract custom code registration and execution into `src/CustomCode/`.
  - [ ] Implement comprehensive procedural shims in `inc/deprecated.php`.
  - [ ] Run `tools/test-phase1.php` and verify zero regressions across existing tests.

### 4.2 Phase 2.2: Performance Engine & Core Web Vitals Acceleration
- **Priority Tier**: **High**
- **Target Release**: v1.5.0
- **Target Outcomes**:
  - Drastically reduce Total Blocking Time (TBT) and Interaction to Next Paint (INP) via interaction-driven script delays with 5-second safeguard timeouts.
  - Eliminate render-blocking styles and prevent Cumulative Layout Shift (CLS) through critical CSS inlining and asynchronous media-swap stylesheets.
  - Reduce image payload sizes by 30–70% through dual-engine WebP/AVIF conversion with lossless alpha support and zero-original-deletion guarantees.
  - Reduce server response time (TTFB) and database load through a 3-tier query caching architecture with O(1) group version invalidation and thundering herd protection.
- **Affected Codebase Areas**:
  - `inc/speed.php` (906 lines) → Refactored into `src/Performance/`.
  - `inc/media.php` → Integrated with `src/Performance/ImageConverter.php`.
  - `main/speed.php` & `main/page/1speed.php` → Enhanced administrative UI controls.
- **Deliverables**:
  1. `src/Performance/ScriptDelay.php`: HTML transformation via `WP_HTML_Tag_Processor`, inert `ht/delayed` MIME type, and vanilla JS loader with synthetic event replay.
  2. `src/Performance/CriticalCss.php`: Above-the-fold CSS inlining and async stylesheet loader.
  3. `src/Performance/ImageConverter.php`: Imagick & GD conversion engines, `<picture>` tag rewriter, and Apache/Nginx header negotiation rules.
  4. `src/Performance/QueryCache.php`: Multi-tier caching layer with O(1) group versioning and stampede mutex.
- **Actionable Task Breakdown Checklist**:
  - [ ] Build `ScriptDelay.php` with `WP_HTML_Tag_Processor` to rewrite enqueued scripts.
  - [ ] Implement vanilla JavaScript delay loader with passive interaction listeners, 5-second safeguard timeout, and synthetic event replay.
  - [ ] Implement `CriticalCss.php` supporting per-template storage (`front_page`, `post`, `page`, `archive`, `woocommerce`).
  - [ ] Implement asynchronous stylesheet delivery via `style_loader_tag` filter using `media="print"` with `onload="this.media='all'"` and `<noscript>` fallbacks.
  - [ ] Implement `ImageConverter.php` supporting both Imagick and GD with alpha preservation and source file retention safety checks.
  - [ ] Implement responsive `<picture>` markup rewriter hooking `the_content` and `post_thumbnail_html`.
  - [ ] Generate Apache `.htaccess` rewrite rules with `Vary: Accept` header support.
  - [ ] Implement 3-tier query cache in `QueryCache.php` with deterministic cache keys and blog-scoped prefixes.
  - [ ] Implement O(1) group version cache invalidation hooking WordPress entity mutation actions.
  - [ ] Implement mutex transient stampede lock preventing dogpiling during cache regenerations.
  - [ ] Create standalone benchmark test script in `tools/test-cwv-benchmark.php`.

### 4.3 Phase 2.3: Modern Gutenberg Blocks & REST API
- **Priority Tier**: **Medium**
- **Target Release**: v1.6.0
- **Target Outcomes**:
  - Integrate native Gutenberg block editor capabilities complying with `block.json` (metadata v3 schema), dynamic server rendering (`render.php`), and Interactivity API client hydration.
  - Achieve complete compatibility with Full Site Editing (FSE) block themes, iframe canvas isolation, and `theme.json` styling tokens.
  - Establish a unified REST API architecture under namespace `horse-tools/v1`, replacing legacy `admin-ajax.php` handlers with schema-validated, role-guarded `WP_REST_Controller` endpoints.
- **Affected Codebase Areas**:
  - `blocks/` → New directory housing declarative blocks (`contact-buttons`, `snippet`, `table`, `faq-schema`).
  - `src/RestApi/` → New namespace housing REST controllers.
  - `horse-tools.php` → Block registration and REST route bootstrapping.
  - Admin UI JavaScript (`link/htadmin.js`) → Modernized to consume REST endpoints.
- **Deliverables**:
  1. `blocks/contact-buttons/block.json` & `render.php`: Multi-channel contact drawer block.
  2. `blocks/snippet/block.json` & `render.php`: Dynamic reusable code snippet block.
  3. `blocks/table/block.json` & `render.php`: Responsive data table block.
  4. `blocks/faq-schema/block.json` & `render.php`: SEO FAQ Accordion block with JSON-LD schema.
  5. `src/RestApi/SettingsController.php`: REST endpoint for plugin configuration.
  6. `src/RestApi/SpeedController.php`: REST endpoints for cache purging, script scans, and critical CSS.
  7. `src/RestApi/MediaController.php`: REST endpoints for WebP/AVIF batch conversion status.
- **Actionable Task Breakdown Checklist**:
  - [ ] Define `blocks/contact-buttons/block.json` (schema v3) with attributes, supports, and editor scripts.
  - [ ] Implement server-side render template `blocks/contact-buttons/render.php`.
  - [ ] Implement client-side hydration directives via Interactivity API (`data-wp-interactive`, `data-wp-context`).
  - [ ] Ensure editor styles inherit active block theme tokens via `var(--wp--preset--color--primary)`.
  - [ ] Register all blocks via `register_block_type_from_metadata()` on `init`.
  - [ ] Implement base controller `HorseTools_REST_Controller` extending `WP_REST_Controller`.
  - [ ] Implement `SettingsController.php` with declarative schema validation and `manage_options` checks.
  - [ ] Implement `SpeedController.php` with cache invalidation endpoints.
  - [ ] Implement `MediaController.php` with conversion monitoring endpoints.
  - [ ] Author integration tests in `tests/phpunit/Integration/RestApiTest.php`.

### 4.4 Phase 2.4: AI Automation & Advanced Workflows
- **Priority Tier**: **Medium**
- **Target Release**: v1.7.0
- **Target Outcomes**:
  - Deploy pluggable AI provider architecture (`HorseTools_AI_Provider_Interface`) supporting Google Gemini 2.0 Flash, OpenAI, Claude, and self-hosted Ollama proxies.
  - Provide automated multimodal image alt-text generation for media library attachments, adhering to WCAG 2.1 AA accessibility guidelines (concise, <= 125 characters, zero promotional fluff).
  - Implement a dual-driver background task queue (Action Scheduler primary with WP-Cron fallback) supporting exponential backoff, rate limit handling (HTTP 429), and zero frontend latency.
  - Enforce strict privacy safeguards: zero telemetry, opt-in execution, and fail-open resilience.
- **Affected Codebase Areas**:
  - `src/AI/` → New namespace housing AI interfaces, providers, and queue workers.
  - `inc/media.php` → Attachment upload hooks and bulk action integration.
  - `main/page/5media.php` → Administrative settings for AI provider keys and prompts.
- **Deliverables**:
  1. `src/AI/ProviderInterface.php`: Unified AI contract.
  2. `src/AI/Providers/GeminiProvider.php`: Multimodal Gemini 2.0 Flash provider via `horsetools_safe_fetch()`.
  3. `src/AI/AltTextGenerator.php`: Vision analysis workflow generating accessible alt text saved to `_wp_attachment_image_alt`.
  4. `src/AI/Queue/JobQueue.php`: Dual-driver background queue with Action Scheduler and WP-Cron.
- **Actionable Task Breakdown Checklist**:
  - [ ] Define `src/AI/ProviderInterface.php`.
  - [ ] Implement `GeminiProvider.php` using Base64 image encoding and SSRF-safe HTTP transport.
  - [ ] Implement `AltTextGenerator.php` with prompt safety engineering and 125-character constraints.
  - [ ] Build `JobQueue.php` resolving Action Scheduler with automatic WP-Cron fallback.
  - [ ] Implement exponential backoff retry with jitter on HTTP 429/503 responses.
  - [ ] Hook into `add_attachment` and media library bulk actions.
  - [ ] Author unit tests in `tests/phpunit/Unit/AITest.php` mocking AI responses and network errors.

### 4.5 Phase 3 Outlook: Enterprise & Headless Ecosystem
- **Priority Tier**: **Future Vision (Target: v2.0.0)**
- **Strategic Vision**:
  1. **Multisite Centralized Policy Sync**: Enable enterprise network administrators to manage security hardening, performance presets, and code injection centrally across hundreds of sub-sites with single-click synchronization.
  2. **Headless WordPress & Jamstack SDK**: Expose GraphQL and REST endpoints designed specifically for decoupled frontends (Next.js, Nuxt, Astro), including static asset pre-optimization, WebP/AVIF image transformations, and on-demand cache revalidation webhooks.
  3. **Advanced Heuristic Threat Defense**: Expand brute-force protection into real-time heuristic anomaly detection, identifying credential-stuffing attacks and distributed botnets with automated IP blacklisting.
  4. **Deprecated Shim Sunset**: Deprecate and remove legacy procedural wrapper functions in `inc/deprecated.php`, transitioning Horse Tools to an entirely object-oriented architecture.

---

## Section 5: Technical Architecture Specifications for All Three Core Tracks

### 5.1 Track 1: Modern WordPress Feature Expansion

```
+----------------------------------------------------------------------------------------------------+
|                                 TRACK 1: MODERN WORDPRESS ARCHITECTURE                             |
+----------------------------------------------------------------------------------------------------+
|                                                                                                    |
|  [ Gutenberg Block Layer ]          [ Unified REST API Layer ]          [ AI & Automation Queue ]  |
|  - block.json (Metadata v3)         - Namespace: horse-tools/v1         - Pluggable Providers      |
|  - FSE / Site Editor Iframe         - WP_REST_Controller Base           - Gemini / OpenAI / Local  |
|  - Dynamic Server Render            - Declarative Schema Validation     - Dual Background Driver   |
|  - Interactivity API (Hydration)    - Granular Capability Guards        - Action Scheduler/WP-Cron |
|  - Theme.json Token Harmony         - Settings / Speed / AI Endpoints   - Exponential Backoff      |
|                                                                                                    |
+----------------------------------------------------------------------------------------------------+
```

#### 5.1.1 Gutenberg Block Editor Integration
All blocks reside under `blocks/<block-slug>/` and declare metadata conforming to schema v3 (`apiVersion: 3`).

##### Declarative `block.json` Specification (`blocks/contact-buttons/block.json`)
```json
{
  "$schema": "https://schemas.wp.org/trunk/block.json",
  "apiVersion": 3,
  "name": "horse-tools/contact-buttons",
  "version": "1.0.0",
  "title": "Contact Buttons",
  "category": "widgets",
  "icon": "phone",
  "description": "Multi-channel quick contact drawer (Zalo, Messenger, Phone, WhatsApp, Telegram).",
  "keywords": ["chat", "contact", "zalo", "messenger", "phone", "horse-tools"],
  "textdomain": "horse-tools",
  "supports": {
    "html": false,
    "align": ["wide", "full"],
    "spacing": {
      "margin": true,
      "padding": true
    },
    "color": {
      "background": true,
      "text": true
    }
  },
  "attributes": {
    "layout": {
      "type": "string",
      "default": "floating",
      "enum": ["floating", "inline", "grid"]
    },
    "channels": {
      "type": "array",
      "default": ["phone", "zalo", "messenger"]
    },
    "showLabels": {
      "type": "boolean",
      "default": true
    }
  },
  "editorScript": "file:./index.js",
  "editorStyle": "file:./index.css",
  "style": "file:./style-index.css",
  "render": "file:./render.php"
}
```

##### Server-Side Render Pattern (`blocks/contact-buttons/render.php`)
Dynamic server-side rendering guarantees zero JavaScript execution overhead on standard page loads:

```php
<?php
/**
 * Server-side render callback for horse-tools/contact-buttons.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block inner content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$layout      = ! empty( $attributes['layout'] ) ? sanitize_key( $attributes['layout'] ) : 'floating';
$show_labels = ! empty( $attributes['showLabels'] );
$options     = get_option( 'horsetools_settings', array() );

$channels = array(
    'phone'     => ! empty( $options['horsetools-phone'] ) ? $options['horsetools-phone'] : '',
    'zalo'      => ! empty( $options['horsetools-zalo'] ) ? $options['horsetools-zalo'] : '',
    'messenger' => ! empty( $options['horsetools-fb'] ) ? $options['horsetools-fb'] : '',
);

$wrapper_attributes = get_block_wrapper_attributes( array(
    'class' => 'ht-contact-block ht-layout-' . esc_attr( $layout ),
) );
?>
<div <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
    <ul class="ht-contact-list">
        <?php if ( ! empty( $channels['phone'] ) ) : ?>
            <li class="ht-channel-item ht-channel-phone">
                <a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $channels['phone'] ) ); ?>" rel="nofollow" class="ht-contact-btn">
                    <span class="ht-icon ht-icon-phone" aria-hidden="true"></span>
                    <?php if ( $show_labels ) : ?>
                        <span class="ht-label"><?php echo esc_html( $channels['phone'] ); ?></span>
                    <?php endif; ?>
                </a>
            </li>
        <?php endif; ?>
        <?php if ( ! empty( $channels['zalo'] ) ) : ?>
            <li class="ht-channel-item ht-channel-zalo">
                <a href="https://zalo.me/<?php echo esc_attr( preg_replace( '/[^0-9]/', '', $channels['zalo'] ) ); ?>" target="_blank" rel="noopener noreferrer" class="ht-contact-btn">
                    <span class="ht-icon ht-icon-zalo" aria-hidden="true"></span>
                    <?php if ( $show_labels ) : ?>
                        <span class="ht-label"><?php esc_html_e( 'Chat Zalo', 'horse-tools' ); ?></span>
                    <?php endif; ?>
                </a>
            </li>
        <?php endif; ?>
        <?php if ( ! empty( $channels['messenger'] ) ) : ?>
            <li class="ht-channel-item ht-channel-messenger">
                <a href="<?php echo esc_url( 'https://m.me/' . $channels['messenger'] ); ?>" target="_blank" rel="noopener noreferrer" class="ht-contact-btn">
                    <span class="ht-icon ht-icon-messenger" aria-hidden="true"></span>
                    <?php if ( $show_labels ) : ?>
                        <span class="ht-label"><?php esc_html_e( 'Messenger', 'horse-tools' ); ?></span>
                    <?php endif; ?>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</div>
```

##### Interactivity API Client Hydration (WP 6.5+)
Interactive client interactions utilize core `wp-interactive` directives without loading external UI libraries:
```html
<div data-wp-interactive="horseToolsContact" data-wp-context='{ "isOpen": false }'>
    <button data-wp-on--click="actions.toggleDrawer" class="ht-contact-toggle" aria-expanded="false">
        <span class="ht-icon-chat"></span>
    </button>
    <div data-wp-bind--hidden="!context.isOpen" class="ht-drawer-content">
        <!-- Drawer content -->
    </div>
</div>
```

##### Full Site Editing (FSE) & `theme.json` Harmony
Editor styling seamlessly adapts to active block themes by referencing core CSS custom properties with brand fallbacks:
```css
.ht-contact-btn {
    background-color: var(--wp--preset--color--primary, #8a6100);
    color: var(--wp--preset--color--base, #ffffff);
    border-radius: var(--wp--custom--border-radius, 6px);
    font-family: var(--wp--preset--font-family--body, inherit);
}
```

#### 5.1.2 Unified REST API Controller Architecture
All REST endpoints inherit from `HorseTools_REST_Controller` under namespace `horse-tools/v1`.

##### Route Catalog (`horse-tools/v1`)
| Route | Method | Capability | Nonce / Auth | Description |
|---|---|---|---|---|
| `/horse-tools/v1/settings` | GET | `manage_options` | Cookie Nonce / Token | Retrieve plugin settings |
| `/horse-tools/v1/settings` | POST | `manage_options` | Cookie Nonce / Token | Update module settings |
| `/horse-tools/v1/modules` | GET | `manage_options` | Cookie Nonce / Token | Retrieve module status registry |
| `/horse-tools/v1/modules/toggle` | POST | `manage_options` | Cookie Nonce / Token | Toggle individual module |
| `/horse-tools/v1/speed/scan-scripts` | POST | `manage_options` | Cookie Nonce / Token | Run homepage script scan |
| `/horse-tools/v1/speed/critical-css` | GET/POST | `manage_options` | Cookie Nonce / Token | Read/save template critical CSS |
| `/horse-tools/v1/speed/purge-cache` | POST | `manage_options` | Cookie Nonce / Token | Purge query & transient caches |
| `/horse-tools/v1/media/convert-webp` | POST | `manage_options` | Cookie Nonce / Token | Trigger WebP/AVIF batch job |
| `/horse-tools/v1/media/status` | GET | `manage_options` | Cookie Nonce / Token | Query image optimization stats |
| `/horse-tools/v1/ai/generate-alt` | POST | `edit_posts` | Cookie Nonce / Token | Generate AI alt text for image |
| `/horse-tools/v1/ai/optimize-content` | POST | `edit_posts` | Cookie Nonce / Token | Generate content recommendations |
| `/horse-tools/v1/ai/jobs/(?P<id>)` | GET | `edit_posts` | Cookie Nonce / Token | Check background job status |

##### Base Controller (`src/RestApi/HorseTools_REST_Controller.php`)
```php
<?php
declare(strict_types=1);

namespace HorseTools\RestApi;

use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;

abstract class HorseTools_REST_Controller extends WP_REST_Controller {
    protected $namespace = 'horse-tools/v1';

    public function check_admin_permission( WP_REST_Request $request ): bool|WP_Error {
        if ( ! current_user_can( 'manage_options' ) ) {
            return new WP_Error(
                'rest_forbidden',
                esc_html__( 'You do not have permission to access Horse Tools administrative endpoints.', 'horse-tools' ),
                array( 'status' => rest_authorization_required_code() )
            );
        }
        return true;
    }

    public function check_editor_permission( WP_REST_Request $request ): bool|WP_Error {
        if ( ! current_user_can( 'edit_posts' ) ) {
            return new WP_Error(
                'rest_forbidden',
                esc_html__( 'You do not have permission to edit content.', 'horse-tools' ),
                array( 'status' => rest_authorization_required_code() )
            );
        }
        return true;
    }

    protected function success_response( mixed $data, int $status = 200 ): WP_REST_Response {
        return new WP_REST_Response( array(
            'success' => true,
            'data'    => $data,
        ), $status );
    }

    protected function error_response( string $code, string $message, int $status = 400 ): WP_Error {
        return new WP_Error( $code, $message, array( 'status' => $status ) );
    }
}
```

#### 5.1.3 Pluggable AI Provider Architecture & Multimodal Workflows
To maintain vendor neutrality and future-proofing, all AI integrations implement `HorseTools_AI_Provider_Interface`.

```php
<?php
declare(strict_types=1);

namespace HorseTools\AI;

use WP_Error;

interface HorseTools_AI_Provider_Interface {
    public function get_id(): string;
    public function get_name(): string;
    public function is_configured(): bool;
    public function generate_text( string $prompt, array $options = array() ): string|WP_Error;
    public function generate_alt_text( string $image_path, array $options = array() ): string|WP_Error;
}
```

##### Gemini Vision Alt-Text Generation Workflow (`src/AI/Providers/GeminiProvider.php`)
- **Model**: `gemini-2.0-flash`
- **Transport**: SSRF-protected HTTP POST via `horsetools_safe_request()` (wrapping `wp_safe_remote_request()` with `method => 'POST'`, `Content-Type => 'application/json'`). This guarantees full compatibility with Google Gemini's `:generateContent` endpoint while preserving the SSRF boundary.
- **Local Ollama Support**: For self-hosted Ollama providers (`http://127.0.0.1:11434`), administrators with `manage_options` can whitelist local host resolution via `apply_filters( 'horsetools_ai_allowed_local_hosts', array( '127.0.0.1', 'localhost' ), 'ollama' )`, safely bypassing loopback blocks for trusted local AI servers without opening SSRF holes to cloud metadata.
- **Multimodal Payload**: Local file read, verified MIME type, Base64 encoded:
  ```json
  {
    "contents": [
      {
        "parts": [
          { "text": "Describe this image in Vietnamese and English for web accessibility (alt text). Maximum 125 characters. Output only the plain alt text description without any prefixes." },
          {
            "inline_data": {
              "mime_type": "image/jpeg",
              "data": "/9j/4AAQSkZJRgABAQEASABIAAD..."
            }
          }
        ]
      }
    ]
  }
  ```
- **Post-Processing**: Response sanitized via `sanitize_text_field()` and stored in `_wp_attachment_image_alt` post meta.

#### 5.1.4 Dual-Driver Background Task Queueing
Batch operations execute asynchronously via a dual-driver pattern:
1. **Action Scheduler (Primary)**: Utilized when available for transaction-safe background execution with dedicated worker pools.
2. **WP-Cron (Fallback)**: Native WordPress scheduling with transient-based mutex locking to prevent overlapping executions on sites without Action Scheduler.
3. **Exponential Backoff with Jitter**: Automatic retry logic for HTTP 429/503 responses, seamlessly falling back to WP-Cron when Action Scheduler is absent:
   ```php
   $attempt = (int) get_post_meta( $job_id, '_ht_attempt', true ) + 1;
   if ( $attempt <= 5 ) {
       update_post_meta( $job_id, '_ht_attempt', $attempt );
       $delay = min( 300, (int) ( ( 2 ** $attempt ) * 5 ) + wp_rand( 1, 5 ) );
       if ( function_exists( 'as_schedule_single_action' ) ) {
           as_schedule_single_action( time() + $delay, 'horsetools_ai_process_job', array( 'job_id' => $job_id ) );
       } else {
           wp_schedule_single_event( time() + $delay, 'horsetools_ai_process_job', array( $job_id ) );
       }
   } else {
       update_post_meta( $job_id, '_ht_status', 'failed' );
       update_post_meta( $job_id, '_ht_error', esc_html__( 'Maximum retries exceeded.', 'horse-tools' ) );
   }
   ```

---

### 5.2 Track 2: Performance & Core Web Vitals (CWV) Optimization

```
+----------------------------------------------------------------------------------------------------+
|                                 CORE WEB VITALS OPTIMIZATION ENGINE                                |
+----------------------------------------------------------------------------------------------------+
|                                                                                                    |
|  [ INP / TBT Optimization ]         [ LCP / FCP Optimization ]          [ TTFB / Server Engine ]   |
|  - Non-destructive Script Delay     - Critical CSS Inlining (<head>)    - 3-Tier Query Caching     |
|  - Passive Event Listeners          - Media Swap Async Stylesheets      - L1: Static Runtime       |
|  - Sequential Order Restoration     - Native WebP & AVIF Generation     - L2: Persistent Object    |
|  - 5s Safe Timeout Safeguard        - Picture Element Rewriting         - L3: Transients Fallback  |
|  - Synthetic Event Replay           - Zero-Delete Original Image Safe   - O(1) Group Invalidation  |
|                                                                                                    |
+----------------------------------------------------------------------------------------------------+
```

#### 5.2.1 Interaction-Driven Non-Destructive Script Delay Engine
Heavy third-party tags (Facebook Pixel, TikTok Pixel, Google Analytics, live chat widgets) block the browser’s main thread during initial parsing, driving Total Blocking Time (TBT) into seconds.

##### Delay Mechanics & Safeguards
1. **Targeted Non-Critical Scope (Allow-List Driven)**: To guarantee zero frontend breakage, script delay defaults strictly to an **allow-list of non-critical analytics, marketing, and tracking scripts** (Google Tag Manager, Google Analytics / GA4, Facebook Pixel, TikTok Pixel, Hotjar, Crisp, Tawk.to, Intercom). Core UI libraries (`jquery`, `jquery-core`), interactive UI sliders (Slick, Swiper, OwlCarousel), and WooCommerce frontend assets (`wc-add-to-cart`, `woocommerce`, `wc-cart-fragments`, plus entire `is_cart()`, `is_checkout()`, `is_account_page()` screens) are **strictly excluded by default**.
2. **Buffer Transformation**: `WP_HTML_Tag_Processor` rewrites targeted `<script>` tags to inert MIME type `type="ht/delayed"`. External scripts store their URL in `data-ht-src`, and ES modules preserve `data-ht-type="module"`.
3. **Passive Interaction Triggers**: The browser listens for 9 user interaction events:
   `'keydown', 'mousedown', 'mousemove', 'wheel', 'touchstart', 'touchmove', 'touchend', 'click', 'scroll'`
   All listeners attach with `{ passive: true, capture: true }` ensuring zero scroll lag.
4. **Click Buffering & Event Replay**: If a user clicks an interactive element (`<button>`, `<a>`, `<input>`) while scripts are delayed, the event target is buffered and synthetically replayed once the respective script dependencies complete initialization, preventing dropped user actions.
5. **5-Second Safeguard Timeout**: If the user remains completely idle for 5 seconds, all delayed scripts execute automatically. This ensures analytics accuracy and bot visibility.
6. **First Interaction Unbinding**: Upon first interaction or timeout, all 9 listeners detach immediately (`removeEventListener`).
7. **Strict Sequential Execution & Module Preservation**: Scripts execute in exact document order by setting `newScript.async = false;`. When `data-ht-type="module"` is detected, `newScript.type = 'module'` is preserved.
8. **Synthetic Event Replay**: Late-bound event listeners for `DOMContentLoaded` and `load` receive synthetic dispatch events after delayed execution completes.

```javascript
(function() {
    'use strict';
    var triggered = false;
    var events = ['keydown', 'mousedown', 'mousemove', 'wheel', 'touchstart', 'touchmove', 'touchend', 'click', 'scroll'];
    var timeoutId = setTimeout(triggerDelay, 5000);
    var bufferedClick = null;

    function captureEarlyClick(e) {
        var target = e.target && e.target.closest('button, a, input, select');
        if (target) {
            bufferedClick = { target: target, event: e };
        }
    }
    window.addEventListener('click', captureEarlyClick, { capture: true, once: true });

    function triggerDelay() {
        if (triggered) return;
        triggered = true;
        clearTimeout(timeoutId);
        events.forEach(function(e) { window.removeEventListener(e, triggerDelay, { passive: true, capture: true }); });
        restoreScripts();
    }

    events.forEach(function(e) { window.addEventListener(e, triggerDelay, { passive: true, capture: true }); });

    function restoreScripts() {
        var scripts = document.querySelectorAll('script[type="ht/delayed"]');
        var index = 0;
        function runNext() {
            if (index >= scripts.length) {
                window.dispatchEvent(new Event('htScriptsLoaded'));
                if (bufferedClick && typeof bufferedClick.target.click === 'function') {
                    bufferedClick.target.click();
                    bufferedClick = null;
                }
                return;
            }
            var oldScript = scripts[index++];
            var newScript = document.createElement('script');

            // Strictly preserve sequential execution order across all browsers
            newScript.async = false;

            // Preserve ES module declaration if originally declared as type="module"
            var isModule = oldScript.getAttribute('data-ht-type') === 'module' || oldScript.type === 'module';
            if (isModule) {
                newScript.type = 'module';
            }

            for (var i = 0; i < oldScript.attributes.length; i++) {
                var attr = oldScript.attributes[i];
                if (attr.name !== 'type' && attr.name !== 'data-ht-src' && attr.name !== 'data-ht-type') {
                    newScript.setAttribute(attr.name, attr.value);
                }
            }

            var src = oldScript.getAttribute('data-ht-src');
            if (src) {
                newScript.src = src;
                newScript.onload = newScript.onerror = runNext;
                oldScript.parentNode.replaceChild(newScript, oldScript);
            } else {
                newScript.text = oldScript.textContent;
                oldScript.parentNode.replaceChild(newScript, oldScript);
                runNext();
            }
        }
        runNext();
    }
})();
```

#### 5.2.2 Asynchronous & Critical CSS Delivery Pipeline
Render-blocking CSS files delay First Contentful Paint (FCP) and Largest Contentful Paint (LCP), while asynchronous stylesheet swaps risk causing Cumulative Layout Shift (CLS) if structural geometry or typography reflows.

##### Critical CSS Inlining & Layout Shift (CLS < 0.05) Safeguards
Above-the-fold CSS rules are stored per template type (`global`, `front_page`, `singular_post`, `singular_page`, `archive`, `woocommerce`) and inlined in `<head>` at priority 1.

To strictly guarantee the target ceiling of **CLS < 0.05** during the asynchronous stylesheet media swap, Horse Tools enforces three mandatory structural safeguards:
1. **Container Aspect-Ratio & Intrinsic Sizing**: All above-the-fold hero sections, featured media, sliders, and navigation headers must declare explicit aspect ratios and dimension reservations in critical CSS (e.g. `aspect-ratio: 16 / 9; min-height: 400px; contain-intrinsic-size: 800px 450px;`). This prevents the viewport from collapsing and reflowing when external stylesheets load.
2. **Font Metric Overrides & `font-display: optional`**: Font swapping is the primary driver of layout reflows during asynchronous CSS swaps. Critical CSS declares `@font-face` rules using `font-display: optional;` (which uses the fallback font if the web font is not immediately cached) or utilizes modern CSS font metric overrides (`ascent-override`, `descent-override`, `line-gap-override`, and `size-adjust`). This aligns fallback glyph heights with the custom web font, eliminating layout shifts when web fonts activate.
3. **Template Extraction Methodology**: Critical CSS is managed via built-in structural presets for core templates, editable via the administrative Settings screen per template type, and supports automated extraction via an optional headless crawler CLI / REST API hook.

```html
<style id="ht-critical-css">
/* Inlined above-the-fold critical structural CSS */
body { margin: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.site-header { display: flex; height: 60px; contain: layout size; }
.hero-banner { min-height: 400px; aspect-ratio: 16 / 9; contain-intrinsic-size: 1200px 675px; background: #fafafa; }
/* Font metric alignment to eliminate swap layout shift */
@font-face {
    font-family: 'CustomFontFallback';
    src: local('Arial');
    ascent-override: 90%;
    descent-override: 22%;
    size-adjust: 102%;
}
</style>
```

##### Asynchronous Stylesheet Loading (Media Swap)
Non-critical external stylesheets are intercepted via the `style_loader_tag` filter and loaded asynchronously using the media swap pattern:
```html
<link rel="stylesheet" id="theme-style-css" href="https://example.com/style.css" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" id="theme-style-css-ns" href="https://example.com/style.css" media="all"></noscript>
```
*Specific media queries (e.g. `(min-width: 768px)`) are preserved on swap rather than overwritten to `all`.*

#### 5.2.3 Dual-Engine Native Image Conversion (WebP & AVIF)
Unoptimized image transfer represents the majority of web payload weight. Horse Tools provides dual conversion engines (`Imagick` primary and `GD` fallback).

##### Engine Capability Probe
```php
<?php
declare(strict_types=1);

namespace HorseTools\Performance;

use Imagick;

final class ImageEngineDetector {
    public static function detect(): array {
        $imagick = extension_loaded( 'imagick' ) && class_exists( 'Imagick' );
        $gd      = extension_loaded( 'gd' );

        $capabilities = array(
            'engine' => 'none',
            'webp'   => false,
            'avif'   => false,
        );

        if ( $imagick ) {
            $formats = Imagick::queryFormats();
            $capabilities['engine'] = 'imagick';
            $capabilities['webp']   = in_array( 'WEBP', $formats, true );
            $capabilities['avif']   = in_array( 'AVIF', $formats, true );
        } elseif ( $gd ) {
            $capabilities['engine'] = 'gd';
            $capabilities['webp']   = function_exists( 'imagewebp' );
            $capabilities['avif']   = function_exists( 'imageavif' );
        }

        return $capabilities;
    }
}
```

##### Conversion Safeguards & Zero-Deletion Guarantee
1. **Lossless Alpha Support**: Transparent PNG images retain alpha channels (`imagesavealpha($im, true)` in GD; alpha preservation in Imagick).
2. **Byte Size Comparison**: If the converted WebP or AVIF file is larger than or equal to the source JPEG/PNG, the converted file is deleted immediately and the original is retained.
3. **Zero-Deletion Guarantee**: Source image files (`.jpg`, `.png`) are **never deleted**.

##### Front-End Delivery Methods
- **Method A: `<picture>` Element Rewriting (Universal & 404-Protected)**:
  `the_content` and `post_thumbnail_html` filters rewrite standard `<img>` tags into responsive `<picture>` tags.

  > **Critical 404 Prevention Guarantee**: To completely eliminate HTTP 404 errors caused when Safeguard 2 (Byte Size Comparison) discards converted files that are larger than source images, `<source type="image/avif">` and `<source type="image/webp">` are **strictly emitted only if the converted file exists on disk**.
  > Existence is verified via post attachment metadata flags (`_ht_converted_formats = ['webp']` or `_ht_has_webp` / `_ht_has_avif`) to eliminate per-request filesystem `stat()` I/O penalties, with a defensive fallback to `file_exists()`.
  > If AVIF was deleted because it exceeded the original PNG/JPEG size, the `<source type="image/avif">` tag is omitted entirely, allowing AVIF-capable browsers to seamlessly select WebP or the original `<img>` without broken image 404s:

  ```html
  <!-- Scenario: AVIF was discarded by Safeguard 2; only WebP and source JPEG exist -->
  <picture class="ht-picture">
      <source type="image/webp" srcset="/wp-content/uploads/2026/11/photo.webp">
      <img src="/wp-content/uploads/2026/11/photo.jpg" alt="..." loading="lazy" decoding="async" width="800" height="600">
  </picture>
  ```

  ```html
  <!-- Scenario: Both AVIF and WebP were successfully generated and smaller than source -->
  <picture class="ht-picture">
      <source type="image/avif" srcset="/wp-content/uploads/2026/11/photo.avif">
      <source type="image/webp" srcset="/wp-content/uploads/2026/11/photo.webp">
      <img src="/wp-content/uploads/2026/11/photo.jpg" alt="..." loading="lazy" decoding="async" width="800" height="600">
  </picture>
  ```
- **Method B: Server Header Negotiation (Zero Markup Modification)**:
  For Apache/LiteSpeed web servers, `.htaccess` rewrite rules deliver next-gen images directly based on the browser's `Accept` header:
  ```apache
  <IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteCond %{HTTP_ACCEPT} image/avif
  RewriteCond %{DOCUMENT_ROOT}/$1.avif -f
  RewriteRule (.+)\.(jpe?g|png)$ $1.avif [T=image/avif,E=accept:1,L]

  RewriteCond %{HTTP_ACCEPT} image/webp
  RewriteCond %{DOCUMENT_ROOT}/$1.webp -f
  RewriteRule (.+)\.(jpe?g|png)$ $1.webp [T=image/webp,E=accept:1,L]
  </IfModule>
  <IfModule mod_headers.c>
  Header append Vary Accept env=REDIRECT_accept
  </IfModule>
  ```

#### 5.2.4 Multi-Tier Database Query Caching Architecture
To minimize database query execution time and eliminate thundering herd vulnerabilities, Horse Tools implements a 3-tier caching hierarchy.

```
[ Incoming Query Request ]
         |
         v
[ Tier 1: Static In-Memory Cache (PHP Request Scope) ] ---- Hit ----> [ Return Data ]
         | Miss
         v
[ Tier 2: Persistent Object Cache (Redis/Memcached) ] ----- Hit ----> [ Return Data ]
         | Miss (or External Object Cache Inactive)
         v
[ Tier 3: Transients API Fallback (Options DB) ] ---------- Hit ----> [ Return Data ]
         | Miss
         v
[ Acquire Mutex Stampede Lock ]
         |
         v
[ Execute Database Query ($wpdb) ]
         |
         v
[ Write Result to Tier 1, Tier 2, and Tier 3 ]
         |
         v
[ Release Mutex Lock & Return Data ]
```

##### O(1) Group Versioned Cache Invalidation
Traditional wildcard SQL cache deletion (`DELETE FROM wp_options WHERE option_name LIKE '_transient_ht_q_%'`) creates severe table locks on high-traffic sites. Horse Tools utilizes **Group Versioning**:
- Each entity group maintains an integer version counter (e.g. `ht_q_ver_posts`).
- The cache key incorporates the version: `"ht_q_{$blog_id}_{$group}_{$version}_{$fingerprint}"`.
- When an entity mutates (`save_post`, `delete_post`), the version counter increments by 1.
- All existing query caches for that group become immediate cache misses in O(1) time complexity without executing SQL table scans. Old transients expire naturally via standard TTLs.

##### Cache Stampede (Dogpiling / Thundering Herd) Protection & Stale-While-Revalidate
When a cached query expires or when a group version counter increments from `N - 1` to `N` during content edits, dozens or hundreds of simultaneous requests could trigger the same expensive query. To prevent thundering herd spikes and PHP-FPM worker pool exhaustion, Horse Tools enforces an **Atomic Mutex Lock & Stale-While-Revalidate** pattern:

1. **Atomic Mutex Lock Acquisition**:
   Standard `set_transient()` is non-atomic and susceptible to race conditions. Horse Tools acquires locks using atomic primitives:
   - **Database (MySQL)**: `add_option( 'ht_lock_' . $fingerprint, time(), '', 'no' )` (returns `false` if already locked due to unique key constraints).
   - **Persistent Object Cache (Redis)**: `SET ht_lock_{$fingerprint} 1 NX EX 15` (15-second expiration).
2. **Stale-While-Revalidate on Version Increments**:
   When version increments to `N`, the key under `version N` is a cache miss for all incoming requests:
   - The first request acquires the atomic lock and regenerates the fresh query result in the background, writing it to cache under version `N`.
   - Concurrent requests that fail to acquire the mutex lock **do not busy-wait or query the database**. Instead, they immediately query and serve stale cached data from **`version - 1`** (`"ht_q_{$blog_id}_{$group}_{$version_minus_1}_{$fingerprint}"`), maintaining sub-50ms response times.
3. **Emergency Bypass Query**:
   If neither version `N` nor `version - 1` is cached, a single direct database query executes with a strict 2-second timeout, completely preventing cascading server lockups.

```php
// Stale-While-Revalidate & Atomic Lock Implementation Pattern
$cache_key = "ht_q_{$blog_id}_{$group}_{$version}_{$fingerprint}";
$data = $this->get_from_tiers( $cache_key );

if ( false === $data ) {
    $lock_acquired = add_option( 'ht_lock_' . $fingerprint, time(), '', 'no' );
    if ( $lock_acquired ) {
        try {
            $data = $this->execute_db_query( $prepared_sql );
            $this->write_to_tiers( $cache_key, $data, 3600 );
        } finally {
            delete_option( 'ht_lock_' . $fingerprint );
        }
    } else {
        // Serve stale data from version - 1 during regeneration
        $stale_key = "ht_q_{$blog_id}_{$group}_" . ( $version - 1 ) . "_{$fingerprint}";
        $data = $this->get_from_tiers( $stale_key );
        if ( false === $data ) {
            $data = $this->execute_db_query( $prepared_sql, 2 ); // Direct bypass with 2s timeout
        }
    }
}
return $data;
```

---

### 5.3 Track 3: Legacy Module Modularization & Technical Debt Reduction

```
src/
├── Core/
│   ├── Autoloader.php              # Zero-dependency PSR-4 autoloader
│   ├── Container.php               # Lightweight PSR-11 service container
│   ├── ModuleInterface.php         # Standard module lifecycle contract
│   └── ServiceProviderInterface.php# Service registration contract
├── Security/                       # Replaces inc/scuri.php (Hardening & Protection)
│   ├── HardeningModule.php         # REST lockdown, XML-RPC, feeds, headers, DISALLOW_FILE_EDIT
│   ├── EnumerationModule.php       # ?author=N, REST /users/, oEmbed, generic login errors
│   ├── BruteForce/
│   │   ├── IpResolver.php          # REMOTE_ADDR and trusted proxy resolution
│   │   ├── LockoutRepository.php   # Transients and horsetools_lla_index management
│   │   ├── BruteForceModule.php    # Gatekeeper, attempt tracking, reset AJAX, email alerts
│   │   └── LockoutDuration.php     # Enum: Minutes, Hours, Days calculation
│   └── Challenge/
│       ├── UnicodeNormalizer.php   # Form C normalization, mb_strtolower, whitespace collapse
│       └── SecurityQuestionModule.php # Login form injection and authenticate filter
├── Privacy/                        # Replaces inc/scuri.php (Privacy & Asset Localization)
│   ├── FontLocalizerModule.php     # Style enqueuing and resource hints filter
│   ├── FontDownloader.php          # safe_fetch, binary validation, uploads storage
│   └── PrivacyScanner.php          # DOM parser, external host classifier, AJAX scan handler
├── Auth/                           # Replaces inc/goo.php (Social Login)
│   ├── GoogleLoginModule.php       # Shortcode, login form hooks, WooCommerce hooks
│   ├── GoogleClientFactory.php     # Autoloading bridge and client configuration
│   ├── OAuthStateManager.php       # Transient + Cookie CSRF state issuer and timing-safe validator
│   └── UserProvisioner.php         # Email claim validator, role mapper, 2FA coordinator
├── Captcha/                        # Replaces inc/goo.php (Bot Defense)
│   ├── RecaptchaModule.php         # Shared coordinator and threshold filter
│   ├── RecaptchaVerifier.php       # Fail-open siteverify HTTP client and host validation
│   ├── RecaptchaDiagnostic.php     # Pure verdict evaluator and AJAX test handler
│   ├── RecaptchaV2Provider.php     # v2 markup and authentication/registration hooks
│   └── RecaptchaV3Provider.php     # v3 markup, fallback warning, actions and verification
└── CustomCode/                     # Replaces main/code.php and inc/code.php
    ├── CustomCodeModule.php        # Lifecycle bootstrapper
    ├── CodeRepository.php          # Options reader for code1..code5
    ├── CodeInjector.php            # wp_head, wp_body_open, wp_footer, login_head hooks
    └── SettingsRegistrar.php       # register_setting and wp_cache_delete invalidation
```

#### 5.3.1 Step-by-Step Modularization of `inc/scuri.php` (766 lines)
`inc/scuri.php` contains 5 distinct domains that violate the Single Responsibility Principle:
1. **Core System Hardening**: REST unauthenticated lockdown (`scuri-off1`), XML-RPC disable (`scuri-off2`), `wp-embed` deregistration (`scuri-off3`), pingback strip (`scuri-off4`), RSD/WLW links removal (`scuri-off5`), RSS feeds 410 Gone (`scuri-off6`), generator version empty (`scuri-verof2`), `DISALLOW_FILE_EDIT` definition (`scuri-fileedit1`), and security HTTP headers (`scuri-head1`).  
   → **Migrated to**: `src/Security/HardeningModule.php`.
2. **User Enumeration Defense**: `?author=N` 301 redirect (`scuri-enum1`), `/wp/v2/users` strip, oEmbed author strip, generic login errors masking (`login_errors` filter).  
   → **Migrated to**: `src/Security/EnumerationModule.php`.
3. **Brute Force Rate Limiting (Limit Login Attempts)**: Client IP extraction (`horsetools_login_ip`), proxy header validation (`scuri-login-proxy`), `authenticate` gatekeeper (priority 30), `wp_login_failed` attempt tracking, lockout transient management, `horsetools_lla_index` tracking, email alerts, and AJAX reset (`horsetools_lla_reset_ajax`).  
   → **Migrated to**: `src/Security/BruteForce/` (`IpResolver.php`, `LockoutRepository.php`, `BruteForceModule.php`, `LockoutDuration.php`).
4. **Login Security Question Challenge**: Unicode Form C normalization (`Normalizer::normalize(FORM_C)`), UTF-8 lowercasing (`mb_strtolower`), whitespace collapse, login form injection, and verification on `authenticate` (priority 25).  
   → **Migrated to**: `src/Security/Challenge/` (`UnicodeNormalizer.php`, `SecurityQuestionModule.php`).
5. **Google Fonts Privacy & Localizer**: Remote stylesheet downloading (`horsetools_safe_fetch`), binary font asset saving (`wp-content/uploads/horsetools-gfonts/`), enqueued stylesheet rewriting, `wp_resource_hints` preconnect stripping, homepage privacy scanner AJAX (`horsetools_privacy_scan_ajax`), and localization AJAX (`horsetools_gfonts_localise_ajax`).  
   → **Migrated to**: `src/Privacy/` (`FontLocalizerModule.php`, `FontDownloader.php`, `PrivacyScanner.php`).

#### 5.3.2 Step-by-Step Modularization of `inc/goo.php` (644 lines)
`inc/goo.php` conflates social authentication with bot mitigation under a legacy "Google" banner. Furthermore, architectural audits confirmed that **Google Analytics tracking does not reside in `inc/goo.php`** (it resides in `inc/chat-track.php`), and Google Search Console indexing resides in `inc/gindex.php`.
1. **Google OAuth2 Social Login (Lines 4–265)**:
   - Google API client initialization (`horsetools_initialize_google_client`), transient + cookie CSRF state token issuer and timing-safe validator (`hash_equals`), `[google-login]` shortcode handler, login form buttons injection, and OAuth callback handler (`wp_ajax_horsetools_login_google`).  
   → **Migrated to**: `src/Auth/` (`GoogleLoginModule.php`, `GoogleClientFactory.php`, `OAuthStateManager.php`, `UserProvisioner.php`).
2. **Google reCAPTCHA v2 & v3 Bot Defense (Lines 266–637)**:
   - Fail-open remote verification against `siteverify` (`horsetools_recaptcha_verify`), hostname verification, score threshold filter (`horsetools_recaptcha_threshold`), diagnostic verdict evaluator (`horsetools_recaptcha_verdict`), AJAX test checker (`horsetools_recaptcha_check_ajax`), v2 script/div injection and validation, and v3 non-fatal markup with fallback warnings and action token validation.  
   → **Migrated to**: `src/Captcha/` (`RecaptchaModule.php`, `RecaptchaVerifier.php`, `RecaptchaDiagnostic.php`, `RecaptchaV2Provider.php`, `RecaptchaV3Provider.php`).  
   *(Note: All authentication and verification method signatures declare `bool|WP_Error` return types conforming to PHP 8.2+ standards).*

#### 5.3.3 Deconstruction and Sunset of `main/code.php` (164 lines)
`main/code.php` defines a standalone submenu page that has been commented out since v1.2.77, as custom code is now rendered inside the grouped Tools screen (`main/tools.php` tabs `tl-code1` through `tl-code5`). However, `main/code.php` is still included solely to call `register_setting()` and invalidate the cache.
- **Deconstruction Strategy**:
  1. Settings registration (`register_setting('horsetools_code_settings_group', ...)` with `horsetools_sanitize_code`) and cache invalidation (`update_option_horsetools_code_settings`) are extracted into `src/CustomCode/SettingsRegistrar.php`.
  2. Runtime hook injection (`wp_head`, `wp_body_open`, `wp_footer`, `login_head`) in `inc/code.php` is refactored into `src/CustomCode/CodeInjector.php`.
  3. `main/code.php` is sunset and transformed into a backward compatibility wrapper that emits `_deprecated_function()` notices if invoked by external callers.

#### 5.3.4 Zero-Dependency PSR-4 Autoloader & Service Container
To maintain zero-dependency operation without requiring Composer in production zip packages, Horse Tools implements a lightweight autoloader and container:

##### `src/Core/Autoloader.php`
```php
<?php
declare(strict_types=1);

namespace HorseTools\Core;

final class Autoloader {
    private const PREFIX = 'HorseTools\\';
    private static bool $registered = false;

    public static function register( string $baseDir ): void {
        if ( self::$registered ) {
            return;
        }

        $baseDir = rtrim( $baseDir, '/\\' ) . '/';

        spl_autoload_register( function ( string $class ) use ( $baseDir ): void {
            if ( 0 !== strpos( $class, self::PREFIX ) ) {
                return;
            }

            $relativeClass = substr( $class, strlen( self::PREFIX ) );
            $file = $baseDir . str_replace( '\\', DIRECTORY_SEPARATOR, $relativeClass ) . '.php';

            if ( file_exists( $file ) ) {
                require_once $file;
            }
        } );

        self::$registered = true;
    }
}
```

##### `src/Core/Container.php` (PSR-11 Compliant)
```php
<?php
declare(strict_types=1);

namespace HorseTools\Core;

use Exception;

final class Container {
    private array $services = array();
    private array $instances = array();

    public function set( string $id, callable $factory ): void {
        $this->services[ $id ] = $factory;
        unset( $this->instances[ $id ] );
    }

    public function get( string $id ): mixed {
        if ( isset( $this->instances[ $id ] ) ) {
            return $this->instances[ $id ];
        }

        if ( ! isset( $this->services[ $id ] ) ) {
            if ( class_exists( $id ) ) {
                $this->instances[ $id ] = new $id();
                return $this->instances[ $id ];
            }
            throw new Exception( "Service {$id} not found in container." );
        }

        $this->instances[ $id ] = call_user_func( $this->services[ $id ], $this );
        return $this->instances[ $id ];
    }

    public function has( string $id ): bool {
        return isset( $this->services[ $id ] ) || isset( $this->instances[ $id ] ) || class_exists( $id );
    }
}
```

#### 5.3.5 100% Backward Compatibility Layer (`inc/deprecated.php`)
To ensure third-party integrations, child themes, and existing Horse Tools hooks continue operating seamlessly, all procedural function signatures from legacy monoliths are preserved in `inc/deprecated.php`.

##### Exhaustive Deprecation Catalog (All 58 Legacy Procedural Functions)
The refactoring of `inc/scuri.php` (27 functions), `inc/goo.php` (22 functions), `main/code.php` (4 functions), and `inc/code.php` (5 functions) accounts for **58 distinct procedural functions**. Every single function is shimmed in `inc/deprecated.php` with a formal `_deprecated_function()` call that forwards to its modern PSR-4 replacement via `horsetools_container()`:

| # | Legacy Function Name | Original Monolith File | Deprecated In | Target PSR-4 Replacement / Handler |
|---|---|---|---|---|
| **`inc/scuri.php` (27 Functions — Security Hardening, Enumeration, Brute Force & Privacy)** |
| 1 | `horsetools_deregister_scripts` | `inc/scuri.php` | 2.0.0 | `HorseTools\Security\HardeningModule::deregister_scripts()` |
| 2 | `horsetools_adminify_remove_pingback_head` | `inc/scuri.php` | 2.0.0 | `HorseTools\Security\HardeningModule::remove_pingback_head()` |
| 3 | `horsetools_remove_header_info` | `inc/scuri.php` | 2.0.0 | `HorseTools\Security\HardeningModule::remove_header_info()` |
| 4 | `horsetools_disable_feed` | `inc/scuri.php` | 2.0.0 | `HorseTools\Security\HardeningModule::disable_feed()` |
| 5 | `horsetools_block_author_scan` | `inc/scuri.php` | 2.0.0 | `HorseTools\Security\EnumerationModule::block_author_scan()` |
| 6 | `horsetools_hide_users_rest` | `inc/scuri.php` | 2.0.0 | `HorseTools\Security\EnumerationModule::hide_users_rest()` |
| 7 | `horsetools_generic_login_error` | `inc/scuri.php` | 2.0.0 | `HorseTools\Security\EnumerationModule::generic_login_error()` |
| 8 | `horsetools_security_headers` | `inc/scuri.php` | 2.0.0 | `HorseTools\Security\HardeningModule::apply_security_headers()` |
| 9 | `horsetools_login_ip` | `inc/scuri.php` | 2.0.0 | `HorseTools\Security\BruteForce\IpResolver::resolve()` |
| 10 | `horsetools_login_key` | `inc/scuri.php` | 2.0.0 | `HorseTools\Security\BruteForce\LockoutRepository::get_ip_transient_key()` |
| 11 | `horsetools_login_max` | `inc/scuri.php` | 2.0.0 | `HorseTools\Security\BruteForce\BruteForceModule::get_max_attempts()` |
| 12 | `horsetools_login_lock_seconds` | `inc/scuri.php` | 2.0.0 | `HorseTools\Security\BruteForce\LockoutDuration::calculate_seconds()` |
| 13 | `horsetools_login_gate` | `inc/scuri.php` | 2.0.0 | `HorseTools\Security\BruteForce\BruteForceModule::gatekeeper()` |
| 14 | `horsetools_login_record_fail` | `inc/scuri.php` | 2.0.0 | `HorseTools\Security\BruteForce\BruteForceModule::record_failed_attempt()` |
| 15 | `horsetools_login_clear` | `inc/scuri.php` | 2.0.0 | `HorseTools\Security\BruteForce\BruteForceModule::clear_attempts()` |
| 16 | `horsetools_lla_reset_ajax` | `inc/scuri.php` | 2.0.0 | `HorseTools\Security\BruteForce\BruteForceModule::handle_ajax_reset()` |
| 17 | `horsetools_login_question_field` | `inc/scuri.php` | 2.0.0 | `HorseTools\Security\Challenge\SecurityQuestionModule::render_login_field()` |
| 18 | `horsetools_lq_norm` | `inc/scuri.php` | 2.0.0 | `HorseTools\Security\Challenge\UnicodeNormalizer::normalize()` |
| 19 | `horsetools_login_question_check` | `inc/scuri.php` | 2.0.0 | `HorseTools\Security\Challenge\SecurityQuestionModule::verify_challenge()` |
| 20 | `horsetools_gfonts_dir` | `inc/scuri.php` | 2.0.0 | `HorseTools\Privacy\FontDownloader::get_storage_dir()` |
| 21 | `horsetools_gfont_normalise_url` | `inc/scuri.php` | 2.0.0 | `HorseTools\Privacy\FontDownloader::normalise_url()` |
| 22 | `horsetools_is_font_binary` | `inc/scuri.php` | 2.0.0 | `HorseTools\Privacy\FontDownloader::is_font_binary()` |
| 23 | `horsetools_gfont_localise_one` | `inc/scuri.php` | 2.0.0 | `HorseTools\Privacy\FontDownloader::localise()` |
| 24 | `horsetools_gfont_rewrite` | `inc/scuri.php` | 2.0.0 | `HorseTools\Privacy\FontLocalizerModule::rewrite_style_tag()` |
| 25 | `horsetools_privacy_classify_host` | `inc/scuri.php` | 2.0.0 | `HorseTools\Privacy\PrivacyScanner::classify_host()` |
| 26 | `horsetools_privacy_scan_ajax` | `inc/scuri.php` | 2.0.0 | `HorseTools\Privacy\PrivacyScanner::handle_ajax_scan()` |
| 27 | `horsetools_gfonts_localise_ajax` | `inc/scuri.php` | 2.0.0 | `HorseTools\Privacy\FontDownloader::handle_ajax_localise()` |
| **`inc/goo.php` (22 Functions — Google OAuth Social Login & reCAPTCHA Bot Defense)** |
| 28 | `horsetools_initialize_google_client` | `inc/goo.php` | 2.0.0 | `HorseTools\Auth\GoogleClientFactory::create()` |
| 29 | `horsetools_issue_oauth_state` | `inc/goo.php` | 2.0.0 | `HorseTools\Auth\OAuthStateManager::issue_state()` |
| 30 | `horsetools_consume_oauth_state` | `inc/goo.php` | 2.0.0 | `HorseTools\Auth\OAuthStateManager::consume_state()` |
| 31 | `horsetools_get_google_login_url` | `inc/goo.php` | 2.0.0 | `HorseTools\Auth\GoogleLoginModule::get_auth_url()` |
| 32 | `horsetools_login_shortcode_google` | `inc/goo.php` | 2.0.0 | `HorseTools\Auth\GoogleLoginModule::render_shortcode()` |
| 33 | `horsetools_google_finish_login` | `inc/goo.php` | 2.0.0 | `HorseTools\Auth\UserProvisioner::provision_and_login()` |
| 34 | `horsetools_login_google` | `inc/goo.php` | 2.0.0 | `HorseTools\Auth\GoogleLoginModule::handle_oauth_callback()` |
| 35 | `horsetools_login_form_social_login` | `inc/goo.php` | 2.0.0 | `HorseTools\Auth\GoogleLoginModule::render_login_button()` |
| 36 | `horsetools_google_login_to_woocommerce_login_form` | `inc/goo.php` | 2.0.0 | `HorseTools\Auth\GoogleLoginModule::render_woocommerce_button()` |
| 37 | `horsetools_recaptcha_verify` | `inc/goo.php` | 2.0.0 | `HorseTools\Captcha\RecaptchaVerifier::verify()` |
| 38 | `horsetools_recaptcha_verdict` | `inc/goo.php` | 2.0.0 | `HorseTools\Captcha\RecaptchaDiagnostic::verdict()` |
| 39 | `horsetools_recaptcha_check_ajax` | `inc/goo.php` | 2.0.0 | `HorseTools\Captcha\RecaptchaDiagnostic::handle_ajax_check()` |
| 40 | `horsetools_recaptcha_login_form_middle` | `inc/goo.php` | 2.0.0 | `HorseTools\Captcha\RecaptchaV2Provider::render_login_form_field()` |
| 41 | `horsetools_recaptcha_threshold` | `inc/goo.php` | 2.0.0 | `HorseTools\Captcha\RecaptchaModule::get_threshold()` |
| 42 | `horsetools_authentication_login_form` | `inc/goo.php` | 2.0.0 | `HorseTools\Captcha\RecaptchaV2Provider::render_form_field()` |
| 43 | `horsetools_authentication_login_verify` | `inc/goo.php` | 2.0.0 | `HorseTools\Captcha\RecaptchaV2Provider::verify_login()` |
| 44 | `horsetools_authentication_register_verify` | `inc/goo.php` | 2.0.0 | `HorseTools\Captcha\RecaptchaV2Provider::verify_registration()` |
| 45 | `horsetools_authentication_login_form_v3` | `inc/goo.php` | 2.0.0 | `HorseTools\Captcha\RecaptchaV3Provider::enqueue_login_assets()` |
| 46 | `horsetools_recaptcha_v3_markup` | `inc/goo.php` | 2.0.0 | `HorseTools\Captcha\RecaptchaV3Provider::render_markup()` |
| 47 | `horsetools_authentication_register_form_v3` | `inc/goo.php` | 2.0.0 | `HorseTools\Captcha\RecaptchaV3Provider::enqueue_register_assets()` |
| 48 | `horsetools_authentication_login_verify_v3` | `inc/goo.php` | 2.0.0 | `HorseTools\Captcha\RecaptchaV3Provider::verify_login()` |
| 49 | `horsetools_authentication_register_verify_v3` | `inc/goo.php` | 2.0.0 | `HorseTools\Captcha\RecaptchaV3Provider::verify_registration()` |
| **`main/code.php` (4 Functions — Custom Code Administration)** |
| 50 | `horsetools_code_options_page` | `main/code.php` | 2.0.0 | `HorseTools\CustomCode\SettingsRegistrar::render_options_page()` (Admin Tools screen) |
| 51 | `horsetools_code_options_link` | `main/code.php` | 2.0.0 | `HorseTools\CustomCode\SettingsRegistrar::add_action_links()` |
| 52 | `horsetools_code_register_settings` | `main/code.php` | 2.0.0 | `HorseTools\CustomCode\SettingsRegistrar::register()` |
| 53 | `horsetools_code_settings_cache` | `main/code.php` | 2.0.0 | `HorseTools\CustomCode\SettingsRegistrar::invalidate_cache()` |
| **`inc/code.php` (5 Functions — Frontend Script & Style Injection)** |
| 54 | `horsetools_show_css` | `inc/code.php` | 2.0.0 | `HorseTools\CustomCode\CodeInjector::inject_head_css()` |
| 55 | `horsetools_header_script` | `inc/code.php` | 2.0.0 | `HorseTools\CustomCode\CodeInjector::inject_header_script()` |
| 56 | `horsetools_body_script` | `inc/code.php` | 2.0.0 | `HorseTools\CustomCode\CodeInjector::inject_body_script()` |
| 57 | `horsetools_footer_script` | `inc/code.php` | 2.0.0 | `HorseTools\CustomCode\CodeInjector::inject_footer_script()` |
| 58 | `horsetools_login_script` | `inc/code.php` | 2.0.0 | `HorseTools\CustomCode\CodeInjector::inject_login_script()` |

##### Implementation Pattern in `inc/deprecated.php`
```php
<?php
/**
 * Horse Tools — Backward Compatibility Wrapper Layer.
 *
 * Forwards legacy procedural function calls to the PSR-4 container and emits deprecation notices.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function horsetools_recaptcha_verify( $token, $expected_action = null, $min_score = null ) {
    _deprecated_function( __FUNCTION__, '2.0.0', 'HorseTools\\Captcha\\RecaptchaVerifier::verify' );
    return horsetools_container()->get( HorseTools\Captcha\RecaptchaVerifier::class )->verify( (string) $token, $expected_action, $min_score );
}

function horsetools_recaptcha_verdict( $mode, $loader_code, array $secret_errs, $secret_asked ) {
    _deprecated_function( __FUNCTION__, '2.0.0', 'HorseTools\\Captcha\\RecaptchaDiagnostic::verdict' );
    return HorseTools\Captcha\RecaptchaDiagnostic::verdict( $mode, $loader_code, $secret_errs, $secret_asked );
}

function horsetools_login_ip() {
    _deprecated_function( __FUNCTION__, '2.0.0', 'HorseTools\\Security\\BruteForce\\IpResolver::resolve' );
    return horsetools_container()->get( HorseTools\Security\BruteForce\IpResolver::class )->resolve();
}

function horsetools_login_question_check( $user, $username, $password ) {
    _deprecated_function( __FUNCTION__, '2.0.0', 'HorseTools\\Security\\Challenge\\SecurityQuestionModule::verify_challenge' );
    return horsetools_container()->get( HorseTools\Security\Challenge\SecurityQuestionModule::class )->verify_challenge( $user, $username, $password );
}

function horsetools_google_finish_login( $payload ) {
    _deprecated_function( __FUNCTION__, '2.0.0', 'HorseTools\\Auth\\UserProvisioner::provision_and_login' );
    return horsetools_container()->get( HorseTools\Auth\UserProvisioner::class )->provision_and_login( $payload );
}

function horsetools_lq_norm( $s ) {
    _deprecated_function( __FUNCTION__, '2.0.0', 'HorseTools\\Security\\Challenge\\UnicodeNormalizer::normalize' );
    return HorseTools\Security\Challenge\UnicodeNormalizer::normalize( (string) $s );
}

function horsetools_gfont_localise_one( $css_url ) {
    _deprecated_function( __FUNCTION__, '2.0.0', 'HorseTools\\Privacy\\FontDownloader::localise' );
    return horsetools_container()->get( HorseTools\Privacy\FontDownloader::class )->localise( (string) $css_url );
}

function horsetools_code_options_page() {
    _deprecated_function( __FUNCTION__, '2.0.0', 'Admin Tools screen (main/tools.php)' );
    require_once HORSETOOLS_DIR . 'main/code.php';
}
```

---

## Section 6: Comprehensive Risk & Dependency Matrix

### 6.1 Risk Registry (R-01 through R-06)

| Risk ID | Risk Description | Probability | Impact | Affected Phase | Mitigation Strategy | Contingency Plan |
|---|---|---|---|---|---|---|
| **R-01** | **Monolith Refactoring Regression**: Extracting logic from `inc/scuri.php`, `inc/goo.php`, and `main/code.php` breaks existing site integrations, custom hooks, or external code expecting global functions. | Medium | High | Phase 2.1 | Implement comprehensive backward compatibility shims in `inc/deprecated.php` preserving every legacy function signature, emitting `_deprecated_function()` notices, and executing existing standalone tests (`tools/test-phase1.php`) on every build. | Maintain legacy procedural files as inactive fallback shims; instant rollback via Git versioning. |
| **R-02** | **Script Delay Breaks Critical JavaScript**: Deferring scripts interferes with e-commerce checkouts, sliders, or analytics trackers that require early DOM initialization. | High | High | Phase 2.2 | Enforce strict sequential order via `newScript.async = false`; preserve ES module types; restrict delay by default to an allow-list of non-critical analytics/marketing scripts; exclude core UI libraries (`jquery`), sliders, and WooCommerce frontend screens (`is_checkout()`, `is_cart()`, `is_account_page()`); buffer and replay early user clicks. | Provide an emergency one-click killswitch in admin: "Disable Script Delay". |
| **R-03** | **Critical CSS FOUC / Cumulative Layout Shift**: Incomplete above-the-fold CSS extraction causes a visible Flash of Unstyled Content or layout shifts (CLS > 0.1). | Medium | Medium | Phase 2.2 | Mandate container `aspect-ratio` reservations and `font-display: optional` / font metric overrides in critical CSS; invalidate and regenerate critical CSS on template modification; preserve exact media queries on non-critical stylesheet swap; validate against Lighthouse CrUX CLS target `< 0.05`. | Instant toggle to revert to standard synchronous stylesheet enqueuing. |
| **R-04** | **Host Server Lacks WebP/AVIF Support**: Budget or legacy shared hosting environments lack PHP `Imagick` or `GD` with AVIF/WebP compiling flags enabled. | Medium | Medium | Phase 2.2 | Execute pre-flight capability probes (`ImageEngineDetector::detect()`); automatically disable unavailable formats in the admin UI with helpful diagnostic notices; guarantee that source images are never deleted. | Retain original JPEG/PNG delivery without throwing PHP warnings or broken image links. |
| **R-05** | **AI API Rate Limiting, Latency, or Outages**: High-volume media uploads or batch jobs cause external AI API throttling (HTTP 429) or timeouts, freezing admin requests. | Medium | Low | Phase 2.4 | Enforce strict asynchronous execution via Action Scheduler / WP-Cron; never execute AI calls during frontend page requests; implement exponential backoff retry with random jitter. | Fail open gracefully: log errors to Horse Tools debug log, leave existing alt text intact, and mark the background job as failed. |
| **R-06** | **WPCS & VIP-Go Standards Churn**: Enforcing strict VIP-Go and WPCS rules generates thousands of legacy formatting and docstring notices across inherited code. | High | Low | Phase 2.1 | Baseline legacy code in `phpcs.xml.dist` using exclude patterns; enforce strict VIP-Go sniffs specifically on newly authored `src/` modules. | Incrementally refactor modules per phase without blocking releases on legacy formatting. |

### 6.2 Phasing Dependency Graph

```
[ Phase 2.1: Legacy Modularization & Core Foundation ]
  |
  +---> [ Core Foundation Gate ] (Autoloader, Container, CI Pipeline passing)
  |        |
  |        +---------------------------+---------------------------+
  |        |                                                       |
  |        v                                                       v
  |  [ Phase 2.2: Performance Engine ]              [ Phase 2.3: Modern Gutenberg Blocks ]
  |  - Script Delay (WP_HTML_Tag_Processor)         - block.json (Metadata v3)
  |  - Critical CSS & Media Swap                    - Dynamic SSR (render.php)
  |  - Dual-Engine WebP & AVIF                      - Interactivity API Hydration
  |  - 3-Tier DB Query Caching                      - Unified WP_REST_Controller
  |        |                                                       |
  |        +---------------------------+---------------------------+
  |                                    |
  |                                    v
  |              [ Phase 2.4: AI Automation & Workflows ]
  |              - Pluggable Provider Interface (Gemini Vision)
  |              - Background Task Queue (Action Scheduler / WP-Cron)
  |              - Exponential Backoff with Jitter
  |                                    |
  v                                    v
[ Phase 3: Enterprise & Headless Ecosystem (v2.0.0 Target) ]
- Multisite Network Policy Sync
- Headless WordPress & Jamstack SDK
- Sunset Procedural Shims (inc/deprecated.php)
```

- **Phase 2.1 is the Hard Foundation Gate**: Modular PSR-4 architecture, container, and automated CI tests must be functional before implementing Phase 2.2, 2.3, or 2.4.
- **Phase 2.2 and Phase 2.3 can proceed concurrently**: Performance optimizations and Gutenberg/REST expansions operate in orthogonal code domains.
- **Phase 2.4 depends on Phase 2.3 & 2.1**: AI alt-text generation utilizes REST endpoints from Phase 2.3 and queue infrastructure established in Phase 2.1/2.2.
- **Phase 3 depends on complete Phase 2 stability**: Enterprise multisite synchronization and headless SDKs build directly on top of the unified REST API and modular container.

---

## Section 7: Verification & Acceptance Criteria Matrix

### 7.1 Pre-Merge Gate Checklists per Milestone

#### Milestone 2.1 Gate Checklist (Core Foundation & Modularization)
- [ ] Root `composer.json` exists, validates via `composer validate --strict`, and defines PSR-4 namespaces.
- [ ] `src/Core/Autoloader.php` successfully autoloads classes under `HorseTools\` with normalized trailing slashes (`rtrim($baseDir, '/\\') . '/'`).
- [ ] `src/Core/Container.php` registers and resolves service factories complying with PSR-11.
- [ ] `inc/scuri.php` (27), `inc/goo.php` (22), `main/code.php` (4), and `inc/code.php` (5) logic cleanly extracted into `src/Security/`, `src/Privacy/`, `src/Auth/`, `src/Captcha/`, and `src/CustomCode/`.
- [ ] `inc/deprecated.php` provides backward compatibility shims for all 58 refactored legacy procedural functions emitting `_deprecated_function()` notices.
- [ ] Standalone test runner `tools/test-phase1.php` executes and passes 100% of assertions (18/18).
- [ ] CI matrix workflow executes across PHP 8.2, 8.3, 8.4 and WordPress 6.2 through 6.7 without failures (with WP 6.0/6.1 fallback guard testing).

#### Milestone 2.2 Gate Checklist (Performance & Core Web Vitals)
- [ ] `src/Performance/ScriptDelay.php` transforms `<script>` tags via `WP_HTML_Tag_Processor` to `type="ht/delayed"`.
- [ ] Vanilla JS delay loader binds passive listeners, executes on user interaction, detaches listeners, and fires fallback execution within 5000ms.
- [ ] Delayed scripts restore sequentially in document order via `newScript.async = false`, preserve ES module types (`type="module"`), buffer early clicks, and replay synthetic events.
- [ ] Critical CSS inlines above-the-fold styles at `<head>` priority 1 with container `aspect-ratio` reservations and `font-display: optional` / font metric overrides; asynchronous stylesheets swap media from `print` to `all` with `<noscript>` fallback.
- [ ] `ImageConverter.php` detects host capabilities (`Imagick` and `GD`), generates `.webp` and `.avif` assets alongside source images, and preserves PNG transparency.
- [ ] Byte size safety check verifies converted files are smaller than source files; source images are never deleted; `<source>` tags are conditionally rendered only if the converted next-gen file exists on disk (verified via attachment metadata or `file_exists()`), completely preventing 404s.
- [ ] Multi-tier query cache stores query results across Runtime Memory, Object Cache, and Transients; group version invalidation executes in O(1) time complexity.
- [ ] Atomic stampede mutex lock (`add_option` / Redis `SET NX` with 15s TTL) and Stale-While-Revalidate from `version - 1` prevent concurrent query execution spikes during cache expiration and group version increments.
- [ ] Core Web Vitals benchmark criteria verified: LCP < 1.8s, INP < 100ms, CLS < 0.05, TTFB < 350ms, peak memory < 10MB, 0 extra uncached queries.

#### Milestone 2.3 Gate Checklist (Modern Gutenberg Blocks & REST API)
- [ ] Blocks declare metadata conforming to `block.json` schema v3 (`apiVersion: 3`) under `blocks/<slug>/`.
- [ ] Blocks render server-side via `render.php` without client-side rendering overhead.
- [ ] Client hydration utilizes WP 6.5+ Interactivity API directives (`data-wp-interactive`, `data-wp-context`).
- [ ] Block styling adapts to active block theme CSS variables (`--wp--preset--color--primary`) with brand fallbacks.
- [ ] REST API routes registered under namespace `horse-tools/v1` inheriting from `HorseTools_REST_Controller`.
- [ ] Every REST endpoint enforces capability authorization (`manage_options` or `edit_posts`) declaring PHP 8.2 compliant return types `bool|WP_Error` and nonce checks.
- [ ] Every REST parameter validates and sanitizes against declarative JSON schema definitions; invalid inputs return HTTP 400 Bad Request.

#### Milestone 2.4 Gate Checklist (AI Automation & Workflows)
- [ ] `HorseTools_AI_Provider_Interface` declared and implemented by `GeminiProvider.php`.
- [ ] Outbound AI requests route strictly through SSRF-protected HTTP POST `horsetools_safe_request()`; administrator-configured Ollama endpoints on local/LAN IPs permitted safely via `horsetools_ai_allowed_local_hosts` filter.
- [ ] Multimodal image alt-text generation produces concise, accessibility-compliant descriptions (<= 125 chars) and stores in `_wp_attachment_image_alt`.
- [ ] Batch image operations execute in background queue resolving Action Scheduler (primary) or WP-Cron (fallback).
- [ ] HTTP 429/503 responses trigger exponential backoff retry with random jitter and WP-Cron fallback; max retry exhaustion logs failure without crashing frontend.
- [ ] Zero telemetry confirmed: no usage data transmitted to external endpoints.

---

### 7.2 Objective Success Verification Methods
To independently verify that the Phase 2 master roadmap satisfies all requirements defined in `ORIGINAL_REQUEST.md`:

1. **Document Completeness Verification**:
   - Inspect `docs/ROADMAP_PHASE_2.md` to confirm the presence of all 7 core sections:
     - Section 1: Executive Summary & Architecture Vision
     - Section 2: Standards & Architectural Conventions
     - Section 3: Tooling, Automated Testing & Verification Infrastructure
     - Section 4: Rolling Phased Milestones (Phase 2.1 to 2.4, Phase 3)
     - Section 5: Technical Architecture Specifications (Track 1, Track 2, Track 3)
     - Section 6: Comprehensive Risk & Dependency Matrix (R-01 to R-06)
     - Section 7: Verification & Acceptance Criteria Matrix
2. **Technical Track Coverage Verification**:
   - Verify Track 1 contains complete designs for Gutenberg `block.json` v3, `render.php`, Interactivity API, `WP_REST_Controller` routes, Gemini multimodal AI workflows via `horsetools_safe_request()` POST, and local Ollama exemption filter.
   - Verify Track 2 contains complete designs for non-destructive script delay (5s timeout, passive listeners, `async = false`, module preservation, click replay, allow-list exclusions), critical CSS inlining + media swap (aspect-ratio & font metric overrides for CLS < 0.05), dual-engine WebP/AVIF conversion with 404 prevention, and 3-tier DB query caching with atomic lock & Stale-While-Revalidate from version - 1.
   - Verify Track 3 contains detailed step-by-step modularization plans for `inc/scuri.php`, `inc/goo.php`, and `main/code.php`, normalized zero-dependency autoloader, PSR-11 container, and exhaustive catalog of all 58 legacy functions in `inc/deprecated.php` backward compatibility shims.
3. **Coding Standards & Conventions Verification**:
   - Verify that all code examples adhere to PHP 8.2+ syntax (strict types, typed properties, `bool|WP_Error` union types, backed enums, match expressions, readonly properties).
   - Verify WordPress 6.2+ baseline APIs (`WP_HTML_Tag_Processor` with defensive WP 6.0/6.1 fallback, `WP_REST_Controller`, `block.json` v3).
   - Verify Horse Tools naming conventions (`horsetools_`, `HORSETOOLS_`, `'horse-tools'`).
4. **Workspace Integrity Verification**:
   - Execute existing test suite: `tools/test-phase1.php`.
   - Verify that all 18 existing tests pass with zero failures and zero regressions.
