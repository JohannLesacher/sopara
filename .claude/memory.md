# Memory — Sage 11 Default

> Shared in the repo. Update when you discover patterns, errors, or important decisions.
> Structure: stable facts at the top, changing things at the bottom.
> **To fill in empty sections**: ask Claude Code to analyze the codebase.

## Update protocol

- **Only append**, never overwrite or remove existing data
- Add to the appropriate section (Key Facts, Learned patterns, Recurring errors)
- If a fact contradicts an existing one, add a note with date and context
- Maximum 1-2 lines per addition (concise and useful)

## Key Facts

- **Bundler**: Vite (NOT Bud). `vite.config.js` with `@tailwindcss/vite`, `laravel-vite-plugin`, `@roots/vite-plugin`
- **PHP**: 8.3 (DDEV), theme minimum 8.2
- **WordPress**: 6.x + Bedrock
- **CPTs**: `evenement` (slug: `evenements`) — avec meta `date_event`
- **Taxonomies**: aucune pour l'instant
- **Pre-commit**: Husky → `npm run format:all:check` (Prettier + Pint + Blade Formatter)

## Architectural decisions

- **Atomic Design for CSS**: components organized by complexity (if applicable)
- **`@include()` for Blade**: components included with `@include()`, not `<x-component>`
- **Extended CPTs config-based**: post types and taxonomies defined in `config/post-types.php`, auto-registered
- **Storybook as source of truth**: if configured, it's the source of truth for components
- **`<section>` tag rule**: ACF blocks and macro components (page section organisms, excluding header/footer) use `<section>` as root tag

## Components créés

- **Hero** (en cours de planification) — image de fond, zone titre centrée verticalement, zone texte bas (InnerBlocks), secteurs d'activité (position absolue bas-droite)

## Learned patterns

- **BlockServiceProvider auto-discovery**: scans `app/Blocks/` and calls `register_block()` (MB Blocks) AND `boot()` (core block extensions) on each class — no manual registration needed
- **CoreButton pattern**: pour étendre un bloc core (render_block, register_block_type_args), créer `app/Blocks/CoreButton.php` avec `boot()` statique — `BlockServiceProvider` l'appelle automatiquement
- **editor.js split**: logique éditeur Gutenberg séparée en fichiers `resources/js/admin/*.js` (un par fonctionnalité), editor.js ne contient que des imports
- **`!is_admin()` wrap for `wp_enqueue_script_module`**: always wrap JS enqueue in `if (!is_admin())` inside `enqueue_assets` to avoid editor conflicts
- **wysiwyg field with `'raw' => true`**: needed so MetaBox returns raw HTML instead of escaped content; required for `{!! !!}` rendering in Blade
- **`$is_preview` for editor placeholders**: use `@elseif ($is_preview)` branches to show placeholder content in Gutenberg editor when fields are empty
- **Block template path convention**: `BlockEngine::renderBlock()` resolves view as `blocks.{block-id}` (e.g. `blocks.card` → `resources/views/blocks/card.blade.php`)
- **InnerBlocks over wysiwyg**: for any free-form editorial content in a block, always use `<InnerBlocks />` — never `wysiwyg` MetaBox field. CTA uses separate `text` + `url` fields.
- **Floating UI panel in a block**: un élément visuellement indépendant du flux de contenu (ex. secteurs d'activité dans le hero) utilise `position: absolute` sur le parent `.block-*` (`position: relative`) avec des offsets identiques sur deux côtés (ex. `bottom: Xpx; right: Xpx`) pour l'ancrer sans perturber l'alignement des zones de contenu.

## Recurring errors to avoid

- **MB Blocks InnerBlocks + JS libraries (ex. Splide)**: MB Blocks enveloppe `<InnerBlocks />` dans un `<div>` anonyme en PHP → les slides/items ne sont PAS fils directs du conteneur attendu par la lib JS. Résolution non trouvée proprement à ce jour — le workaround JS (`querySelector + classList.add`) a été rejeté comme hacky. À résoudre avant de recréer un slider avec InnerBlocks.

- **`image_advanced` vs `single_image`**: utiliser `single_image` pour une image unique (ex. fond de hero), `image_advanced` pour une liste d'images (ex. galerie secteurs)
- **MB Blocks custom attributes via `attributes` key**: la clé `attributes` dans le tableau meta_boxes est ignorée par MB Blocks pour le schéma REST → toujours utiliser `add_filter('register_block_type_args', ...)` à la place
- **MB Blocks namespace JS**: le namespace est `meta-box/{block-id}` (pas `mbblocks/`)

## Important files
