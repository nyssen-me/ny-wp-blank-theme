# CLAUDE.md: ny-blank-theme (Gutenberg + ACF starter theme)

This file briefs Claude Code on this project. Read it fully before making changes.

## What this project is

A reusable **starter theme** called `ny-blank-theme`, to be used as the base for all future client sites at Hillside Agency. It replaces the previous Classic Editor + ACF Flexible Content approach with the **Gutenberg block editor**, locked down so editors only get curated **ACF blocks**.

The developer (Sergi) has long experience with custom PHP themes and ACF Flexible Content. The goal is to keep that level of control while moving to modern block APIs.

### Goals

- Keep Gutenberg enabled, but disable default/freeform core blocks
- Provide only curated ACF blocks (plus a minimal set of core text blocks inside a content block)
- Lock layouts where needed; use templates and patterns for consistency
- Full PHP control of the page skeleton, header, footer, sidebars, wrappers around `the_content()`, grids, containers and structural classes
- Modern WordPress compatibility (WordPress 7.x), structured content, future-proofing, access to native block APIs

## Environment

- **Local only for now:** WAMP on Windows (site lives under the WAMP `www` folder)
- Latest WordPress core and latest **ACF PRO** (6.6 or later is required for ACF Blocks V3)
- PHP 8.2 or later recommended in WAMP
- Editor: VS Code with Claude Code
- Paths on Windows: use `get_theme_file_path()` / `get_theme_file_uri()` in PHP, never hard-coded paths or backslashes

### Folder overview

```
wp-content/
├── themes/ny-blank-theme/    # THE ONLY FOLDER WE DEVELOP IN
├── plugins/                  # Third-party plugins: do not edit
└── languages/                # Translation files
```

Never edit WordPress core or plugin files.

## Architecture decision: hybrid theme, NOT a block (FSE) theme

This is a **classic PHP theme with `theme.json`**.

- `header.php`, `footer.php`, `sidebar.php`, `page.php`, `single.php` etc. are PHP and fully controlled by us
- Gutenberg only manages what is inside `the_content()`
- Do NOT create HTML block templates, template parts or Site Editor support
- **Never put an `index.html` inside `/templates`** (or `/block-templates`): WordPress would then treat the theme as a block theme

### Markup and container strategy

The markup matches Sergi's previous Classic Editor theme (`wp-content/themes/hillside-theme`) and his CSS framework. Check that theme before inventing new markup.

- `<main id="maincontent" class="wrapper">` is full-bleed, with no article or `.entry-content` wrapper
- Every section block outputs `<section>` > `.container` > `.row` > `.column`, just like the old flexible content layouts. Sergi's grid (`assets/css/src/grids.css`) styles `.wrapper .container` (max width plus top and bottom padding), with modifiers such as `.container-narrow`, `.container-wide` and `.padding-long`, so block CSS should not add its own section padding
- In the editor there is no `main.wrapper`, so blocks add the `wrapper` class to their section only when `$is_preview` is true. That keeps the front-end markup identical to the old theme and the previews styled
- ACF's extra InnerBlocks wrapper div is switched off on the front end (`acf/blocks/wrap_frontend_innerblocks`), so inner content sits directly in `.column`
- Page headings use `.masthead-page` > `.page-heading` > `h1.page-title` (+ `p`), via `templates/partials/page-heading.php`, shared by `acf/hero` and the PHP templates
- Post lists use `ul.news-list` > `li.card` (`templates/partials/card.php`) and `nav.pag-nav` > `ul.pagination` (`ny_numeric_posts_nav()`)
- Use Sergi's helper classes, for example `.visually-hidden` (not `.screen-reader-text`) and `.skip-link`
- `align` support is switched off on all blocks, so editors cannot break the grid

## Theme structure

```
ny-blank-theme/
├── style.css                 # Theme header only
├── theme.json                # Editor settings and lockdown (version 3)
├── functions.php             # Only requires files from functions/
├── functions/
│   ├── setup.php             # Theme supports, menus, widget areas, image sizes, editor-styles
│   ├── cleanup.php           # Head clean-up, emoji, core block CSS off on the front end, body classes, admin bar, comments switched off
│   ├── admin.php             # Login page branding, SVG uploads (administrators only)
│   ├── navigation.php        # Menu output tweaks (no container div, menu item descriptions)
│   ├── template-tags.php     # Output helpers: SVG filtering, links, footer links, pagination
│   ├── enqueue.php           # Front-end and editor assets
│   ├── editor.php            # Block allow-list, editor settings lockdown
│   ├── blocks.php            # Auto-registers ACF blocks, registers block category
│   ├── post-templates.php    # Block templates and locking per post type
│   ├── patterns.php          # Pattern categories, removes core/remote patterns
│   └── acf.php               # ACF settings, options pages, field registration
├── templates/
│   ├── blocks/               # One folder per ACF block (self-contained)
│   │   └── hero/
│   │       ├── block.json
│   │       ├── render.php
│   │       ├── style.css     # Optional, only for heavy CSS used on few pages (see CSS section)
│   │       └── view.js       # Optional, front-end behaviour only
│   └── partials/             # Reusable components, shared by blocks AND PHP templates (page-heading, home-masthead, card, button)
├── patterns/                 # PHP pattern files (auto-registered by WordPress)
├── acf-json/                 # ACF Local JSON (local development)
├── assets/
│   ├── css/
│   │   ├── style-min.css     # Compiled, minified CSS (front end and editor)
│   │   └── src/              # Sergi's source CSS (reset.css, variables.css, grid.css, general.css, header.css, footer.css, etc.)
│   ├── js/
│   ├── images/
│   └── fonts/
├── header.php                # Header markup from the old theme; logo from Theme Settings
├── footer.php                # Footer markup from the old theme; details from Theme Settings
├── sidebar.php               # Search form plus widget-area-1 and widget-area-2
├── searchform.php
├── front-page.php            # Home page (static front page); falls back to index.php for latest posts
├── index.php                 # Blog, archives and search results
├── page.php
├── single.php                # Posts: text-only content in .grid-content, featured image in .grid-sidebar
├── 404.php
└── languages/
```

There is **no root `templates.php` router** any more. The block loader in `functions/blocks.php` replaces it.

## Conventions

- **Text domain:** `ny-blank-theme`
- **PHP function prefix:** `ny_` (e.g. `ny_register_blocks()`)
- **Block names:** `acf/<slug>` (e.g. `acf/hero`). Block names are saved into post content, so never rename a block once it is in use
- **Block category:** `ny-blocks`
- **Pattern slugs:** `ny-blank-theme/<slug>`; pattern categories prefixed `ny-`
- Follow WordPress Coding Standards for PHP
- Escape all output (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`); sanitise all input
- Make all user-facing strings translatable with the `ny-blank-theme` text domain
- Use `get_template_part()` with `$args` for partials
- Comments and documentation in **British English**
- **Never use em dashes** in any text written for Sergi (comments, docs, messages); use commas, full stops or words like "so" or "which"

## Implementation details

### 1. Page skeleton (`page.php` example)

```php
<?php get_header(); ?>
<main id="maincontent" class="wrapper">
  <?php while ( have_posts() ) : the_post(); ?>
    <?php the_content(); ?>
  <?php endwhile; ?>
</main>
<?php get_footer();
```

`front-page.php` uses the same skeleton for the home page. Posts are **text only** (paragraph, heading, list, quote, no ACF blocks and no post template), because `single.php` outputs their content inside its own `.grid-content` column, where section blocks do not fit.

### 2. `theme.json` (lockdown layer)

Use `"version": 3`. Switch off everything editors could use to restyle content:

```json
{
  "$schema": "https://schemas.wp.org/trunk/theme.json",
  "version": 3,
  "settings": {
    "appearanceTools": false,
    "blockVisibility": { "allowEditing": false },
    "color": {
      "background": false, "text": false, "link": false,
      "heading": false, "button": false, "caption": false,
      "custom": false, "customDuotone": false, "customGradient": false,
      "defaultPalette": false, "defaultGradients": false, "defaultDuotone": false,
      "duotone": [], "gradients": [],
      "palette": []
    },
    "typography": {
      "customFontSize": false, "defaultFontSizes": false, "dropCap": false,
      "fluid": false, "fontSizes": [],
      "fontStyle": false, "fontWeight": false, "letterSpacing": false, "lineHeight": false,
      "textAlign": false, "textColumns": false, "textDecoration": false,
      "textTransform": false, "writingMode": false
    },
    "spacing": {
      "customSpacingSize": false, "defaultSpacingSizes": false, "spacingSizes": [],
      "padding": false, "margin": false, "blockGap": false
    },
    "shadow": { "defaultPresets": false, "presets": [] }
  }
}
```

This is abridged: the theme's `theme.json` also switches off background, border, dimensions, layout, lightbox and sticky position settings.

- `color.background`/`text`/`link` etc. are `false` and the palette is empty, so no colour controls appear at all
- `blockVisibility.allowEditing` is `false` so editors cannot hide blocks or hide them per screen size (WordPress 7.0+)
- WordPress outputs its own default presets as CSS custom properties even when hidden. `ny_remove_default_presets()` in `functions/setup.php` removes them from the editor's global styles

`theme.json` is only the editor lockdown layer, not a source of design tokens. The global styles it generates are not loaded on the front end at all (see `functions/cleanup.php`), so never use `--wp--preset--*` variables in the CSS: colours, fonts and sizes come from Sergi's own variables in `assets/css/src/variables.css`.

### 3. Block allow-list and editor lockdown (`functions/editor.php`)

```php
add_filter( 'allowed_block_types_all', function ( $allowed, $context ) {
    $acf = array_values( array_filter(
        array_keys( WP_Block_Type_Registry::get_instance()->get_all_registered() ),
        fn( $name ) => str_starts_with( $name, 'acf/' )
    ) );
    $core = [ 'core/paragraph', 'core/heading', 'core/list', 'core/list-item', 'core/quote' ];
    // Vary by $context->post->post_type where needed.
    return array_merge( $acf, $core );
}, 10, 2 );

add_filter( 'block_editor_settings_all', function ( $s ) {
    $admin = current_user_can( 'manage_options' );
    $s['codeEditingEnabled']           = $admin;
    $s['canLockBlocks']                = $admin;
    $s['enableOpenverseMediaCategory'] = false;
    $s['fontLibraryEnabled']           = false;
    return $s;
} );

remove_action( 'enqueue_block_editor_assets', 'wp_enqueue_editor_block_directory_assets' );
add_filter( 'should_load_separate_core_block_assets', '__return_true' );
// Core and remote patterns are removed in functions/patterns.php.
```

On pages (every post type except `post`), paragraph, heading, list and quote can only be used inside ACF blocks, so there is never loose text outside the `section` > `.container` > `.row` > `.column` structure. `ny_restrict_top_level_text_blocks()` sets their `ancestor` to all `acf/` blocks on the `current_screen` hook, and each block's InnerBlocks `allowedBlocks` decides which blocks accept them. It cannot go in `register_block_type_args`, which applies to every post type, because posts need these blocks at the top level. Do not wrap `the_content()` in a container in `page.php`: the sections already output their own. Quote also loses its background, minimum height, layout and "Plain" style in `ny_restrict_core_block_supports()`. Its text alignment button is built into the block and cannot be removed, so `blocks.css` cancels `.has-text-align-*` on `.wp-block-quote`.

### 4. ACF blocks (`templates/blocks/<slug>/`)

Every block uses **ACF Blocks V3** and **`apiVersion: 3`**.

`block.json`:

```json
{
  "$schema": "https://schemas.wp.org/trunk/block.json",
  "apiVersion": 3,
  "name": "acf/hero",
  "title": "Hero",
  "category": "ny-blocks",
  "icon": "cover-image",
  "textdomain": "ny-blank-theme",
  "acf": { "blockVersion": 3, "mode": "preview", "renderTemplate": "render.php" },
  "supports": {
    "align": false, "anchor": true, "html": false, "customClassName": false,
    "customCSS": false, "color": false, "spacing": false, "typography": false, "jsx": true
  }
}
```

Block CSS normally goes in `assets/css/src/blocks.css` (see the CSS section), so `block.json` has no `style` key. For the exception (heavy CSS used on few pages), add `"style": [ "file:./style.css" ]` and a `style.css` in the block folder.

`render.php` for a section block (the pattern used by `acf/content` and `acf/text-media`):

```php
<?php
/**
 * Example section block.
 *
 * @var array $block      Block settings.
 * @var bool  $is_preview True in the editor.
 */
// "wrapper" only in the editor, where there is no main.wrapper around the blocks.
$wrapper = get_block_wrapper_attributes( $is_preview ? [ 'class' => 'wrapper' ] : [] );
$heading = get_field( 'heading' );
?>
<section <?php echo $wrapper; ?>>
  <div class="container">
    <div class="row">
      <div class="column">
        <?php if ( $heading ) : ?>
          <h2><?php echo esc_html( $heading ); ?></h2>
        <?php elseif ( $is_preview ) : ?>
          <p class="block__placeholder"><?php esc_html_e( 'Add a heading in the block settings.', 'ny-blank-theme' ); ?></p>
        <?php endif; ?>
        <?php get_template_part( 'templates/partials/button', null, [ 'link' => get_field( 'cta' ) ] ); ?>
      </div>
    </div>
  </div>
</section>
```

`acf/hero` is different: it outputs the page heading through `templates/partials/page-heading.php`, passing `get_block_wrapper_attributes( [ 'class' => 'masthead-page' ] )` as the outer attributes. On the page set as the front page it outputs `templates/partials/home-masthead.php` instead (`.masthead` with the masthead text and SVG from Theme Settings > Home, then `section` > `.container.padding-long.home-intro` with `h1.home-title`). This is automatic, there is no block style for editors to choose.

Rules for blocks:

- Always use `get_block_wrapper_attributes()` on the outer element (in WordPress 7 it also outputs the anchor as the `id`)
- Keep `"customCSS": false` in `supports`, which switches off WordPress 7.0's per-block custom CSS
- Always show a helpful placeholder when `$is_preview` and required fields are empty
- Block CSS goes in `assets/css/src/blocks.css`, compiled into `style-min.css` (front end and editor). Only heavy CSS used on few pages goes in a `style.css` in the block folder, declared in `block.json`
- Front-end JS: prefer the **Interactivity API** (`viewScriptModule`) over jQuery
- Inner content: use `<InnerBlocks allowedBlocks="..." template="..." templateLock="..." />` (requires `"jsx": true`)
- Controlled visual options: use `register_block_style()` (e.g. a "Dark" style), never colour pickers

Auto-registration (`functions/blocks.php`):

```php
add_action( 'init', function () {
    foreach ( glob( get_theme_file_path( 'templates/blocks/*/block.json' ) ) as $file ) {
        register_block_type( dirname( $file ) );
    }
} );

add_filter( 'block_categories_all', function ( $categories ) {
    array_unshift( $categories, [ 'slug' => 'ny-blocks', 'title' => __( 'Site blocks', 'ny-blank-theme' ) ] );
    return $categories;
} );
```

### 5. ACF field groups

- Field groups attach to blocks with the location rule **Block is equal to <Block title>**
- Sergi's workflow: field groups are built with the **ACF GUI locally**, and **registered in PHP for staging and production**
- Locally, enable ACF Local JSON saving to `acf-json/` so field definitions are version-controlled and readable
- When preparing for staging/production, export field groups to PHP under `functions/acf/` (e.g. `functions/acf/fields-hero.php`) and load them from `functions/acf.php`
- Ask Sergi before changing this workflow

Theme Settings options page (`functions/acf.php`, slug `theme-general-settings`, capability `edit_posts`), with the field group in `acf-json/group_ny_theme_settings.json`. Field names match the old theme: `main_logo` (SVG code, output through `ny_kses_svg()`, also used as the login page logo), `masthead_text` and `masthead_svg` (home masthead), `contact_button`, `address_heading`, `address`, `phone` (the `tel:` link is derived from it), `email`, `social_links` (repeater: `label`, `url`), `legal_text`. The footer legal links come from the "Footer menu" location. Read options with `get_field( 'name', 'option' )`.

### 6. Locking

Three levels:

1. **Per post type** (`functions/post-templates.php`): starting template for new posts.
   ```php
   add_action( 'init', function () {
       $page = get_post_type_object( 'page' );
       $page->template = [
           [ 'acf/hero', [ 'lock' => [ 'move' => true, 'remove' => true ] ] ],
           [ 'acf/content' ],
       ];
       $page->template_lock = false; // or 'insert', 'all', 'contentOnly'
   } );
   ```
   For custom post types, pass `template` and `template_lock` in `register_post_type()`. Templates only apply to new posts.
2. **Per block:** the `lock` attribute (`move`, `remove`).
3. **Inside a block:** `templateLock` on `<InnerBlocks />`.

`canLockBlocks` is restricted to admins, so editors cannot unlock anything.

### 7. Patterns (`patterns/`)

PHP files in `/patterns` are auto-registered. Patterns are **section patterns** (combinations of blocks), inserted from the inserter's Patterns tab. There are no full-page patterns, because pages start from the page template (see Locking).

Why: a post type template and the "choose a pattern" modal cannot be combined. The template inserts its blocks as soon as a new page opens, so the page is no longer empty and the modal (which only opens on an empty page) never appears. Sergi chose the template.

Example:

```php
<?php
/**
 * Title: Text and media, alternating
 * Slug: ny-blank-theme/text-media-alternating
 * Categories: ny-sections
 * Description: Two text and media sections, image right then left.
 */
?>
<!-- wp:acf/text-media {"name":"acf/text-media","data":{"image_position":"right","_image_position":"field_ny_text_media_image_position"},"mode":"preview"} -->
<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html__( 'Section heading', 'ny-blank-theme' ); ?></h2>
<!-- /wp:heading -->
<!-- /wp:acf/text-media -->
```

- Do not add `Block Types: core/post-content` (that is for the "choose a pattern" modal, which is not used)
- Never put `acf/hero` in a pattern: it is already in the page template and allows only one per page
- Wrap sample text in `esc_html__()` so it is translatable
- Core block markup must match what the block saves, or the editor flags the block as invalid. In WordPress 7: `<p>` with no class (paragraph `className` support is off), `<h2 class="wp-block-heading">`, `<ul class="wp-block-list">`, `<blockquote class="wp-block-quote">` (paragraphs inside, optional `<cite>`)
- Easiest way to create one: build the layout in the editor, copy the markup from the code editor, paste into the file
- Register pattern categories in `functions/patterns.php`
- WordPress caches the list of pattern files in a transient. During local development, add `define( 'WP_DEVELOPMENT_MODE', 'theme' );` to `wp-config.php` so new pattern files show up straight away

### 8. Editor iframe and editor styles (important)

WordPress 7.0 iframes the post editor when all inserted blocks are `apiVersion` 3, and full enforcement is coming in a later release. Therefore:

- `apiVersion: 3` on every block, no exceptions
- Canvas styles must load via `block.json` (`style` / `editorStyle`) or `add_editor_style()`
- **Never** use `enqueue_block_editor_assets` for styles that should apply to block content: they load outside the iframe and silently stop working
- `functions/setup.php` must include `add_theme_support( 'editor-styles' );` and `add_editor_style( 'assets/css/style-min.css' );`
- The same `style-min.css` loads on the front end and in the editor canvas, so previews match the front end. WordPress scopes it to the canvas and rewrites relative `url()` paths (fonts, images), so they keep working
- Editor JS must not assume `document`/`window` is the canvas document

### 9. CSS

- Plain CSS, no Sass and no npm build in the theme
- Sergi compiles CSS (and JS when needed) with **Prepros**, outside the theme code. Do not add another build step
- Global CSS: Sergi writes source files in `assets/css/src/` and Prepros compiles them into `assets/css/style-min.css`
- JS: Prepros compiles `assets/js/main.js` (with its `@prepros-prepend` plugins) into `assets/js/main-min.js`. `assets/js/editor.js` and any block `view.js` are loaded as written, uncompiled
- **Always enqueue, never hard-code `<link>` or `<script>` tags in `header.php`/`footer.php`.** `functions/enqueue.php` enqueues `style-min.css` (head) and `main-min.js` (footer, deferred) on the front end, versioned by file modification time, and skips either if the file does not exist
- If `main.js` bundles its own jQuery, a plugin that enqueues WordPress's jQuery would load a second copy. Prefer adding `'jquery'` as a dependency and dropping the jQuery prepend
- Block CSS: in `assets/css/src/blocks.css` (imported in `assets/css/style.css`, the Prepros entry file), so all CSS is in one compiled, cached file that also styles the editor previews. Exception: heavy CSS needed on few pages (a map or slider block, for example) can go in a `style.css` in the block folder, declared in `block.json` as `"style": [ "file:./style.css" ]`, so it only loads where the block is used. WordPress inlines small block stylesheets in the page (up to 20 KB per page by default)
- The PHP uses the framework's classes (see "Markup and container strategy"). Theme-specific classes the CSS should also cover: `.block__placeholder` (editor-only hints in blocks). `.screen-reader-text`, which WordPress core outputs in places, shares the `.visually-hidden` rule in `helpers.css`
- **No WordPress block CSS on the front end.** `functions/cleanup.php` dequeues `wp-block-library`, removes the theme.json global styles and stops core blocks (paragraph, heading, list, quote) loading their own stylesheets. Those styles mostly cover features the lockdown switches off, so `style-min.css` must style everything the blocks output, including plain `p`, `h2` to `h6`, `ul`/`ol`, `blockquote` and `cite`, and links inside `.column`. The editor canvas still loads WordPress's block CSS, so check that previews still match the front end. WordPress's inline `sizes="auto"` image fix is removed too: the same rule is in `reset.css` (keep it, as WordPress still adds `sizes="auto"` to lazy-loaded images). So `style-min.css` is the only CSS on the front end
- **Ask Sergi before adding any build tooling** (npm, Sass, PostCSS, webpack, @wordpress/scripts, Vite, etc.)

## Build order

Work through these in order, and stop for Sergi's review after each stage:

1. Theme base: `style.css`, `functions.php` loader, `functions/setup.php`, PHP templates (`header.php`, `footer.php`, `sidebar.php`, `index.php`, `page.php`, `single.php`, `404.php`), `theme.json`
2. `functions/editor.php` lockdown
3. Block loader, block category, and three proving blocks:
   - `acf/hero` (fields only)
   - `acf/content` (InnerBlocks with paragraph, heading, list, quote)
   - `acf/text-media` (text plus image, with a left/right layout option)
4. Post type templates and locking (`functions/post-templates.php`)
5. Patterns and pattern categories
6. CSS: enqueue `style-min.css` on the front end and as the editor style, block CSS in `assets/css/src/blocks.css`
7. Testing pass (see below)

## Testing checklist

- Test while logged in as an **Editor**, not an administrator
- Inserter shows only `ny-blocks` blocks plus the allowed core text blocks
- No colour, font size, spacing or alignment controls appear anywhere
- Code editor and lock controls are unavailable to editors
- Block previews in the editor match the front end
- Editor canvas is iframed (inspect the editor: the canvas should be inside an `iframe`)
- New pages get the page template (locked hero plus a content block); posts offer only paragraph, heading, list and quote
- Theme section patterns appear in the Patterns tab under "Site sections", and no core patterns appear
- Block CSS is in `style-min.css`; any block with its own `style.css` loads it only on pages using that block
- No PHP notices or warnings with `WP_DEBUG` enabled
- All output escaped; all strings translatable

## Ask Sergi first before

- Installing plugins, npm dependencies or any build tooling
- Changing the hybrid-theme architecture or the folder structure
- Renaming blocks, fields or slugs that may already be in use
- Anything that touches the database directly
