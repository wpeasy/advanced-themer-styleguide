# Code Standards

Project-wide coding standards for consistency and maintainability across all code.

---

## Related Documentation

- **CLAUDE.md** - Project properties, namespaces, prefixes
- **SVELTE5_IMPLEMENTATION.md** - Svelte 5 patterns and runes (required reading)
- **WORDPRESS.md** - Plugin header template
- **lib/wpea/claude.md** - UI framework and components

---

## General Principles

1. **Consistency over preference** - Match existing patterns in the codebase
2. **Explicit over implicit** - Clear, readable code over clever shortcuts
3. **DRY but not premature** - Extract only after 3+ repetitions
4. **Comments explain "why"** - Code should explain "what"
5. **Fail fast, fail loudly** - Validate early, throw meaningful errors

---

## File Organization

```
plugin-name/
├── plugin-name.php              # Main plugin file (header from WORDPRESS.md)
├── uninstall.php                # Cleanup on uninstall
├── composer.json                # PSR-4 autoloader
├── package.json                 # Vite + Svelte 5
├── vite.config.js
├── CLAUDE.md                    # Project properties (source of truth)
│
├── src/                         # PHP classes (namespace from CLAUDE.md)
│   ├── Plugin.php               # Bootstrap class
│   ├── Admin/                   # Admin-only functionality
│   ├── REST/                    # REST API controllers
│   ├── Models/                  # Database models
│   ├── Services/                # Business logic
│   └── Traits/                  # Shared traits
│
├── src-svelte/                  # Svelte 5 source
│   ├── admin-main.ts            # Admin entry point
│   ├── stores/                  # State management
│   ├── components/              # Reusable components
│   └── lib/                     # Utilities
│
├── lib/                         # Shared libraries
│   └── wpea/                    # UI framework
│
├── assets/
│   └── dist/                    # Vite build output (gitignored)
│
├── templates/                   # PHP templates
│   ├── admin/
│   └── public/
│
└── languages/                   # Translation files
```

---

## Naming Conventions

### Files

| Type | Convention | Example |
|------|------------|---------|
| PHP Classes | PascalCase | `RulesController.php` |
| PHP Traits | PascalCase + Trait suffix | `SingletonTrait.php` |
| Svelte Components | PascalCase | `RuleEditor.svelte` |
| TypeScript | camelCase | `apiClient.ts` |
| CSS/SCSS | kebab-case | `admin-styles.css` |
| Config files | lowercase | `vite.config.js` |

### Code

| Language | Variables | Functions/Methods | Classes | Constants |
|----------|-----------|-------------------|---------|-----------|
| PHP | `$snake_case` | `snake_case()` | `PascalCase` | `UPPER_SNAKE` |
| TypeScript | `camelCase` | `camelCase()` | `PascalCase` | `UPPER_SNAKE` |
| CSS | `--kebab-case` | N/A | `.kebab-case` | N/A |

### Prefixes (from CLAUDE.md)

All project-specific identifiers must use the prefix defined in CLAUDE.md:

- **PHP Constants**: `{PREFIX}_PLUGIN_PATH`, `{PREFIX}_VERSION`
- **Database Tables**: `{wp_prefix}_{prefix}_tablename`
- **REST Routes**: `/{namespace}/v1/endpoint`
- **Options**: `{prefix}_settings`
- **Transients**: `{prefix}_cache_key`
- **Hooks**: `{prefix}_action_name`, `{prefix}_filter_name`

---

## PHP Standards

### WordPress Coding Standards

Follow [WordPress PHP Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/) with these specifics:

```php
<?php
/**
 * Class description.
 *
 * @package AB\PluginName
 * @since   1.0.0
 */

declare(strict_types=1);

namespace AB\PluginName;

defined('ABSPATH') || exit;

/**
 * Brief class description.
 *
 * Longer description if needed.
 */
final class ExampleClass {

    /**
     * Property description.
     *
     * @var string
     */
    private string $property;

    /**
     * Method description.
     *
     * @param string $param Parameter description.
     * @return bool Return value description.
     */
    public function example_method(string $param): bool {
        return true;
    }
}
```

### Requirements

1. **ABSPATH Check**: Every PHP file must start with `defined('ABSPATH') || exit;`
2. **Strict Types**: Use `declare(strict_types=1);` in all files
3. **Type Hints**: Use parameter and return type hints everywhere
4. **PSR-4 Autoloading**: All classes via Composer autoloader
5. **Final Classes**: WordPress hook integration classes should be `final`

### Class Structure Pattern

Use static methods for WordPress hook integration classes. This pattern is simpler, stateless, and appropriate for most WordPress plugin classes.

```php
final class ServiceClass {

    /**
     * Initialize hooks.
     */
    public static function init(): void {
        add_action('init', [self::class, 'on_init']);
        add_action('admin_menu', [self::class, 'register_menu']);
    }

    /**
     * Handle init action.
     */
    public static function on_init(): void {
        // Initialization logic
    }

    /**
     * Register admin menu.
     */
    public static function register_menu(): void {
        // Menu registration logic
    }
}
```

**When to use static methods:**
- Hook registration/bootstrap classes
- Controllers that wrap WordPress APIs
- Stateless utility classes

**When to consider alternatives:**
- Classes needing expensive cached computations (use static properties)
- Classes requiring unit test mocking (consider dependency injection)

### DocBlocks

Required for:
- All classes (with `@package` and `@since`)
- All public/protected methods
- Complex private methods
- Properties with non-obvious types

Not required for:
- Simple getters/setters with clear names
- Overridden methods (use `@inheritdoc`)

---

## JavaScript / TypeScript Standards

### General Rules

1. **TypeScript Required**: All new JavaScript must be TypeScript
2. **Svelte 5 Only**: Use runes, not Svelte 4 patterns (see SVELTE5_IMPLEMENTATION.md)
3. **ES Modules**: Use ESM format (requires WordPress 6.5+)
4. **No jQuery**: Never use jQuery unless required for legacy integration
5. **Strict Mode**: Enable strict TypeScript checks

### TypeScript Configuration

```json
{
  "compilerOptions": {
    "strict": true,
    "noImplicitAny": true,
    "noImplicitReturns": true,
    "noUnusedLocals": true,
    "noUnusedParameters": true
  }
}
```

### Svelte Components

See **SVELTE5_IMPLEMENTATION.md** for detailed patterns. Key rules:

1. **Reuse Components**: Check libraries first (WPEA → project → external)
2. **Props Interface**: Define TypeScript interfaces for complex props
3. **State Exposure**: Expose app state on `window.{ConstantsPrefix}` for debugging

```svelte
<script lang="ts">
  import type { Rule } from '../types';

  interface Props {
    rule: Rule;
    onSave: (rule: Rule) => void;
  }

  let { rule, onSave }: Props = $props();
  let editing = $state(false);
</script>
```

---

## CSS Standards

### WPEasy Admin Framework

All admin UI must use the WPEasy Admin Framework:

1. **Root Container**: Apply `.wpea` class to root elements
2. **CSS Variables**: Use `--wpea-*` variables exclusively
3. **Components**: Use framework components before custom CSS

```css
/* Use framework variables */
.my-component {
  padding: var(--wpea-space-4);
  background: var(--wpea-surface-1);
  border-radius: var(--wpea-radius-m);
  color: var(--wpea-text-1);
}

/* Never hardcode colors - breaks dark mode */
.my-component {
  background: #ffffff;  /* WRONG */
}
```

### Spacing with Flex and Gap (Required)

**NEVER use margins on headings or child elements for spacing.** Instead, use `flex-direction: column` with `gap` on parent containers.

```css
/* CORRECT: Use flex column with gap */
.my-section {
  display: flex;
  flex-direction: column;
  gap: var(--wpea-space--md);
}

.my-section h3 {
  font-size: var(--wpea-text--lg);
  font-weight: 600;
  /* NO margin - spacing comes from parent gap */
}

/* WRONG: Using margins for spacing */
.my-section h3 {
  margin: 0 0 var(--wpea-space--md) 0;  /* WRONG */
}
```

### BEM Naming (Required)

All CSS must use BEM (Block Element Modifier) naming convention:

```css
/* Block */
.rule-editor { }

/* Element (double underscore) */
.rule-editor__header { }
.rule-editor__body { }
.rule-editor__footer { }

/* Modifier (double dash) */
.rule-editor--expanded { }
.rule-editor__header--sticky { }
```

### Theme Support

Use CSS `light-dark()` function or framework variables:

```css
.element {
  /* Automatic light/dark switching */
  background: light-dark(var(--wpea-gray-100), var(--wpea-gray-900));

  /* Or use semantic variables that auto-switch */
  background: var(--wpea-surface-1);
}
```

---

## Security Standards

### Input Validation

```php
// Sanitize all input
$title = sanitize_text_field($_POST['title'] ?? '');
$html = wp_kses_post($_POST['content'] ?? '');
$email = sanitize_email($_POST['email'] ?? '');
$url = esc_url_raw($_POST['url'] ?? '');
$int = absint($_POST['count'] ?? 0);

// Validate after sanitizing
if (empty($title)) {
    return new WP_Error('invalid_title', 'Title is required');
}
```

### Output Escaping

```php
// Always escape output
echo esc_html($user_input);
echo esc_attr($attribute_value);
echo esc_url($url);
echo wp_kses_post($html_content);
```

### Nonce Verification

```php
// REST API - verify nonce header
public function create_item($request) {
    // WordPress REST API verifies X-WP-Nonce automatically
    // when registered with 'permission_callback'
}

// Admin forms
if (!wp_verify_nonce($_POST['_wpnonce'], 'my_action')) {
    wp_die('Security check failed');
}
```

### Capability Checks

```php
// Check capabilities before operations
if (!current_user_can('manage_options')) {
    return new WP_Error('forbidden', 'Insufficient permissions', ['status' => 403]);
}
```

### SQL Safety

```php
// Always use $wpdb->prepare() for queries with variables
global $wpdb;

$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}my_table WHERE id = %d AND status = %s",
        $id,
        $status
    )
);
```

---

## REST API Standards

### Endpoint Registration

```php
register_rest_route('{namespace}/v1', '/items', [
    [
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => [$this, 'get_items'],
        'permission_callback' => [$this, 'check_read_permission'],
    ],
    [
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => [$this, 'create_item'],
        'permission_callback' => [$this, 'check_write_permission'],
        'args'                => $this->get_create_args(),
    ],
]);
```

### Response Format

```php
// Success
return rest_ensure_response([
    'success' => true,
    'data'    => $item,
]);

// Error
return new WP_Error(
    'not_found',
    'Item not found',
    ['status' => 404]
);
```

---

## Error Handling

### PHP

```php
// Throw exceptions for unexpected errors
if (!$file_exists) {
    throw new \RuntimeException('Configuration file not found');
}

// Return WP_Error for expected failures
if (empty($name)) {
    return new WP_Error('validation_error', 'Name is required');
}

// Log errors appropriately
if (WP_DEBUG) {
    error_log("[{PREFIX}] Failed to process: " . $e->getMessage());
}
```

### TypeScript

```typescript
// Use try/catch with typed errors
try {
  const result = await saveItem(item);
} catch (error) {
  if (error instanceof ValidationError) {
    showToast({ type: 'error', message: error.message });
  } else {
    console.error('Unexpected error:', error);
    showToast({ type: 'error', message: 'An unexpected error occurred' });
  }
}
```

---

## Performance

### PHP

1. **Cache expensive operations**: Use transients for external API calls
2. **Lazy load**: Only load classes when needed
3. **Database queries**: Use indexes, limit results, avoid N+1 queries

### JavaScript

1. **Code splitting**: Separate admin/public bundles
2. **Lazy components**: Load modals/dialogs on demand
3. **Debounce**: Debounce search inputs and autosave

### Assets

1. **Local assets only**: Never load from external CDNs
2. **Minification**: All production assets minified
3. **Cache busting**: Use version parameter for updates

---

## Accessibility

### Requirements

1. **Keyboard navigation**: All interactive elements keyboard accessible
2. **ARIA labels**: Buttons/icons without text need `aria-label`
3. **Focus management**: Modals trap focus, return focus on close
4. **Color contrast**: Meet WCAG AA (4.5:1 for text)
5. **Screen readers**: Test with screen reader software

---

## Git Conventions

### Commit Messages

```
type(scope): short description

Longer description if needed.

🤖 Generated with Claude Code
```

**Types**: `feat`, `fix`, `docs`, `style`, `refactor`, `perf`, `test`, `chore`

### Branch Naming

- `feature/short-description`
- `fix/issue-description`
- `refactor/component-name`
