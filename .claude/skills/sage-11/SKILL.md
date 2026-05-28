---
name: sage-11
description: >
  Référence complète pour le développement de thèmes WordPress avec la stack
  Roots : Sage 11, Acorn, Bedrock, MetaBox (MB Blocks), Polylang, WooCommerce.
  Utiliser ce skill pour toute tâche liée à ce projet : créer un bloc Gutenberg,
  un CPT, un View Composer, un Service Provider, un Service, modifier theme.json,
  gérer les assets Vite, écrire des requêtes WP_Query, intégrer WooCommerce ou
  Polylang, ou déboguer des problèmes courants. Déclencher dès que la conversation
  mentionne Sage, Acorn, MB Blocks, MetaBox, Roots, ou une tâche WordPress sur ce projet.
---

# Sage 11 — Skill de développement

## Conventions globales

- PHP : PSR-12, tout typer (paramètres et retours)
- Pas de commentaires superflus — le code doit se lire seul
- Zéro logique dans les vues Blade — tout passe par les View Composers ou les Services
- Jamais `{{ }}` pour afficher du texte — toujours `{!! !!}` (problèmes d'encodage avec les accents français)
- Pas de `@php` dans les vues
- Hooks WordPress déclarés dans les Service Providers, jamais dans `functions.php`
- `get_post_meta()` plutôt que les helpers MetaBox (`rwmb_meta`, `rwmb_get_value`)
- **Jamais de `<img src="...">` en dur** — toujours `wp_get_attachment_image($id, 'size')` (génère automatiquement `srcset`, `sizes`, `alt`, `loading`, `decoding`). Le Composer expose un `*Id()` (l'ID d'attachment), la vue appelle `{!! wp_get_attachment_image($id(), 'large') !!}`. Exceptions tolérées uniquement quand on n'a pas d'attachment WP (logo SVG inline, image distante).

---

## Structure du thème

```
web/app/themes/default/
├── app/
│   ├── Blocks/                  # Blocs Gutenberg (MB Blocks)
│   ├── PostTypes/               # Meta boxes par post type (ex: Page.php)
│   ├── Providers/               # Service Providers Acorn/Laravel
│   ├── Services/                # Services métier (logique, hooks)
│   │   └── BlockEngine.php      # Classe abstraite de rendu des blocs
│   ├── View/
│   │   ├── Composers/           # View Composers (+ sous-dossier Blocks/)
│   │   └── Components/
│   ├── translations.php         # pll_register_string() si Polylang actif
│   └── setup.php                # after_setup_theme, enqueues globaux
├── config/
│   └── post-types.php           # Déclaration CPT et taxonomies
├── public/build/                # Assets compilés Vite
├── resources/
│   ├── css/
│   │   ├── app.scss
│   │   ├── editor.scss
│   │   ├── config/              # variables, reset, fonts, external
│   │   ├── base/                # global, layout, animation
│   │   ├── sections/            # header, footer
│   │   ├── templates/           # CSS par template (ex: woocommerce.scss)
│   │   └── blocks/              # Un .scss par bloc (scan Vite auto)
│   ├── js/
│   │   ├── app.js
│   │   ├── editor.js
│   │   └── blocks/              # Un .js par bloc (scan Vite auto, optionnel)
│   └── views/
│       ├── blocks/
│       ├── components/
│       ├── layouts/
│       ├── partials/
│       ├── sections/
│       └── woocommerce/         # Si WooCommerce actif
├── theme.json
├── vite.config.js
└── functions.php
```

---

## 1. Post Types et Taxonomies

### Config : `config/post-types.php`

Les CPT et taxonomies sont déclarés dans ce fichier de config, via la librairie [extended-cpts](https://github.com/johnbillion/extended-cpts).

```php
return [
    'post_types' => [
        'evenement' => [
            'menu_icon'     => 'dashicons-calendar-alt',
            'supports'      => ['title', 'editor', 'thumbnail'],
            'public'        => true,
            'show_in_rest'  => true,
            'has_archive'   => true,
            'menu_position' => 4,
            'names' => [
                'singular' => 'Événement',
                'plural'   => 'Événements',
                'slug'     => 'evenements',
            ],
            'admin_cols' => [
                'date_event' => [
                    'title'    => "Date de l'événement",
                    'meta_key' => 'date_event',
                    'function' => function () {
                        global $post;
                        $date = get_post_meta($post->ID, 'date_event', true);
                        if ($date) {
                            $date_obj = DateTime::createFromFormat('Ymd', $date);
                            echo $date_obj ? $date_obj->format('d/m/Y') : esc_html($date);
                        }
                    },
                ],
            ],
        ],
    ],
    'taxonomies' => [],
];
```

### Service Provider : `PostTypesServiceProvider`

- Lit `config/post-types.php` et appelle `register_extended_post_type()` / `register_extended_taxonomy()`
- Scanne `app/PostTypes/` et appelle `addMetas()` sur chaque classe via le filtre `rwmb_meta_boxes`

### Meta boxes : `app/PostTypes/<PostType>.php`

```php
namespace App\PostTypes;

class Page
{
    private string $post_type = 'page';

    public function addMetas(array $meta_boxes): array
    {
        $meta_boxes[] = [
            'title'      => 'Réglages',
            'post_types' => $this->post_type,
            'fields'     => [
                [
                    'id'   => 'display_title',
                    'name' => 'Afficher le titre de la page ?',
                    'type' => 'checkbox',
                    'std'  => 1,
                ],
            ],
        ];

        return $meta_boxes;
    }
}
```

> **Important :** après tout ajout ou modification de CPT ou taxonomie, vider les permaliens : `wp rewrite flush` ou dans l'admin → Réglages → Permaliens → Enregistrer.

---

## 2. Blocs Gutenberg (MB Blocks)

Les blocs utilisent **MetaBox MB Blocks**, pas `block.json`. ACF n'est pas utilisé.

### Créer un bloc — TOUJOURS passer par la commande

> **Règle absolue :** ne jamais créer les fichiers d'un bloc manuellement. Toujours utiliser `wp acorn make:block` — c'est déterministe, ça génère la structure correcte et ça évite les erreurs de registration.
```bash
wp acorn make:block nom-du-bloc          # CSS uniquement
wp acorn make:block nom-du-bloc --js     # Avec JS
wp acorn make:block nom-du-bloc --vc     # Avec View Composer
wp acorn make:block nom-du-bloc --js --vc
```

La commande génère : classe PHP, vue Blade, SCSS, JS (optionnel), Composer (optionnel). Aucune registration manuelle nécessaire.

**Workflow correct pour créer un bloc :**
1. Exécuter `wp acorn make:block nom-du-bloc [--js] [--vc]` via bash
2. Modifier les fichiers générés pour ajouter champs, vue, CSS. Le but est d'avoir un bloc prêt à l'emploi.
3. Ne jamais créer les fichiers manuellement, même si bash n'est pas disponible — demander à l'utilisateur d'exécuter la commande et attendre sa confirmation

### Anatomie d'une classe de bloc

```php
namespace App\Blocks;

use App\Services\BlockEngine;
use Illuminate\Support\Facades\Vite;

class MonBloc extends BlockEngine
{
    public static function register_block(array $meta_boxes): array
    {
        $meta_boxes[] = [
            'title'           => 'Mon Bloc',
            'id'              => 'mon-bloc',
            'type'            => 'block',
            'category'        => 'sur-mesure',
            'icon'            => 'admin-generic',
            'render_callback' => [parent::class, 'renderBlock'],
            'enqueue_assets'  => function () {
                wp_enqueue_style('mon-bloc', Vite::asset('resources/css/blocks/mon-bloc.scss'), [], null);
                if (!is_admin()) {
                    wp_enqueue_script_module('mon-bloc-js', Vite::asset('resources/js/blocks/mon-bloc.js'), [], null);
                }
            },
            'supports' => [
                'align'           => true,
                'anchor'          => true,
                'customClassName' => true,
            ],
            'fields' => [],
        ];

        return $meta_boxes;
    }
}
```

> `wp_enqueue_script_module` (ES modules natifs) est toujours wrappé dans `!is_admin()` pour éviter les conflits éditeur.

### Vue Blade

Pas de commentaire de chemin en haut du fichier — c'est inutile.

Toujours inclure `align{!! $attributes['align'] ?? 'normal' !!}` dans les classes du bloc pour que l'alignement Gutenberg (`alignfull`, `alignwide`, `alignnormal`…) soit reflété en CSS.

```blade
<section class="block-mon-bloc {{ $attributes['className'] ?? '' }} align{!! $attributes['align'] ?? 'normal' !!}">
    {{-- $data = champs MetaBox, $attributes = attrs Gutenberg, $is_preview = bool --}}
</section>
```

### BEM et SCSS des blocs

- La classe racine du bloc est `.block-{id}` (ex : `.block-hero`).
- Les sous-éléments suivent le préfixe complet : `block-{id}__element` (ex : `block-hero__bg`), **pas** `{id}__element`.
- Tout le SCSS est imbriqué sous `.block-{id}` via `&__element` — pas de classes BEM à plat.

```scss
.block-mon-bloc {
    // styles du bloc

    &__titre {
        // styles de l'élément
    }

    &__image {
        // styles de l'élément
    }
}
```

- Pour un bloc pleine hauteur, utiliser `min-height: 100dvh` (pas `height`) afin de permettre le débordement de contenu.

### InnerBlocks vs champs MetaBox

> **`<InnerBlocks />` est le choix par défaut pour tout contenu éditorial.** Le champ `wysiwyg` MetaBox est à proscrire — il isole le contenu de l'expérience Gutenberg et prive le rédacteur des blocs natifs.

| Situation | Approche |
|-----------|----------|
| Tout contenu que le rédacteur composerait avec des blocs | `<InnerBlocks />` |
| Texte court non mis en forme (titre, label, slug…) | Champ `text` MetaBox |
| URL, lien externe | Champ `url` MetaBox |
| Données structurées non-éditoriales (options, coordonnées…) | Champ MetaBox adapté |

**Exemples concrets :**
```
Bloc Card (titre + contenu libre + CTA) :
❌ titre (text) + contenu (wysiwyg) + cta_label (text) + cta_url (url)
✓  titre (text) + <InnerBlocks /> pour le contenu + cta_label (text) + cta_url (url)

Bloc Hero (visuel + texte + bouton) :
❌ image (single_image) + texte (wysiwyg) + bouton (text + url)
✓  image (single_image) + <InnerBlocks /> pour le texte et le bouton
```

**Règle simple :** doute → `<InnerBlocks />`. Le champ `wysiwyg` n'a quasiment aucun cas d'usage légitime.

### JS par bloc — règle

N'utiliser `--js` dans `wp acorn make:block` que si le bloc nécessite **effectivement** du JS. Si aucun JS n'est requis :
- Ne pas passer `--js`
- Ne pas créer de fichier JS vide ou commenté (le glob Vite le bundle inutilement)
- Ne pas mettre `wp_enqueue_script_module` dans `enqueue_assets`

---

> **⚠️ Ce skill ne doit jamais être modifié sans approbation explicite de l'utilisateur.** Aucune mise à jour, ajout ou suppression de contenu dans ce fichier sans que l'utilisateur ait dit de le faire.

## 2b. Blocs Gutenberg natifs (sans MetaBox)

Pour des blocs avec **InnerBlocks libres** ou des relations parent/enfant complexes, utiliser `registerBlockType` JS + `register_block_type` PHP — pas MetaBox, pas `wp acorn make:block`.

**Cas d'usage :** bloc parent qui ne contient que des enfants d'un type précis (ex: slider/slide), bloc sans champs MetaBox.

### Organisation des fichiers

```
resources/js/admin/blocks/    # registerBlockType (éditeur) — importé depuis editor.js
resources/js/blocks/          # JS frontend — scan Vite auto
resources/css/blocks/         # CSS — scan Vite auto
resources/views/blocks/       # Vues Blade — render_callback
app/Blocks/                   # Classe PHP — auto-découverte par BlockServiceProvider
```

### Classe PHP — `app/Blocks/MonBloc.php`

Utiliser `boot()` (auto-appelé par `BlockServiceProvider`). La signature `render_callback` reçoit `$content` = HTML des inner blocks déjà rendus.

```php
namespace App\Blocks;

use Illuminate\Support\Facades\Vite;
use function Roots\view;

class Slider
{
    public static function boot(): void
    {
        register_block_type('sur-mesure/slider', [
            'render_callback' => [self::class, 'renderSlider'],
        ]);

        register_block_type('sur-mesure/slide', [
            'render_callback' => [self::class, 'renderSlide'],
        ]);

        wp_enqueue_style('heat/slider', Vite::asset('resources/css/blocks/slider.scss'), [], null);

        if (! is_admin()) {
            wp_enqueue_script_module('heat/slider', Vite::asset('resources/js/blocks/slider.js'), [], null);
        }
    }

    public static function renderSlider(array $attributes, string $content): string
    {
        return view('blocks.slider', compact('attributes', 'content'))->render();
    }

    public static function renderSlide(array $attributes, string $content): string
    {
        return view('blocks.slide', compact('attributes', 'content'))->render();
    }
}
```

> Différence clé avec MB Blocks : `render_callback(array $attributes, string $content)` — pas de `$is_preview`, pas de `$data`. La vue reçoit `$attributes` et `$content`.

### JS éditeur — `resources/js/admin/blocks/mon-bloc-editor.js`

Pas de JSX — utiliser `createElement` (même convention que les autres fichiers `admin/`).

```js
import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { createElement as el } from '@wordpress/element';

registerBlockType('sur-mesure/slider', {
  title: 'Slider',
  category: 'sur-mesure',
  icon: 'slides',
  attributes: {},
  supports: {
    align: ['wide', 'full'],  // toolbar alignement automatique
  },

  edit: () => {
    const blockProps = useBlockProps({ className: 'editor-slider' });
    return el('div', blockProps,
      el(InnerBlocks, { allowedBlocks: ['sur-mesure/slide'] })
    );
  },

  save: () => el(InnerBlocks.Content, null),
});
```

**Bloc enfant exclusif** (`parent`) :

```js
registerBlockType('sur-mesure/slide', {
  parent: ['sur-mesure/slider'],  // uniquement insérable dans un slider
  // ...
  edit: () => el('div', useBlockProps(), el(InnerBlocks, {})),
  save: () => el(InnerBlocks.Content, null),
});
```

Importer depuis `editor.js` :
```js
import '@scripts/admin/blocks/slider-editor';
import '@scripts/admin/blocks/slide-editor';
```

### Vue Blade

La vue reçoit `$content` (inner blocks rendus) et `$attributes`. Pas de `$data`, pas de `$is_preview`.

```blade
<div class="block-slider splide {{ $attributes['className'] ?? '' }} {{ isset($attributes['align']) ? 'align' . $attributes['align'] : '' }}">
    <div class="splide__track">
        <ul class="splide__list">
            {!! $content !!}
        </ul>
    </div>
</div>
```

Bloc enfant (li pour Splide) :
```blade
<li class="splide__slide block-slide">
    {!! $content !!}
</li>
```

### `supports.align` vs attribut manuel

Toujours utiliser `supports: { align: [...] }` — WP injecte l'attribut automatiquement + toolbar. Ne jamais déclarer `align` manuellement dans `attributes`.

```js
// ✓
supports: { align: ['wide', 'full'] }

// ❌ — WP ne gère pas la toolbar
attributes: { align: { type: 'string', default: 'left' } }
```

En Blade : `{{ isset($attributes['align']) ? 'align' . $attributes['align'] : '' }}`

---

## 3. Champs MetaBox

> **Référence complète :** lire `references/metabox-fields.md` pour la liste des 40+ types de champs, leurs settings et les patterns de récupération. À charger dès qu'une tâche implique de déclarer ou lire des champs MetaBox.

Types les plus courants : `text`, `textarea`, `checkbox`, `select`, `select_advanced`, `date`, `single_image`, `image_advanced`, `post`, `group` (extension MB Group requise).

---

## 4. Fichiers de configuration (`config/`)

Acorn expose le dossier `config/` du thème via le helper `config()`, comme dans Laravel. On peut y créer autant de fichiers que nécessaire.

### Fichiers existants

| Fichier | Usage |
|---------|-------|
| `config/post-types.php` | Déclaration CPT et taxonomies (lu par `PostTypesServiceProvider`) |
| `config/app.php` | Valeurs générales du thème (nom, debug, etc.) |
| Tout autre fichier | Accessible via `config('nom-fichier.cle')` |

### Créer un fichier de config

```php
// config/mon-config.php
return [
    'api_key' => env('MON_API_KEY', ''),
    'option'  => true,
];
```

### Lire une valeur

```php
config('mon-config.api_key')
config('mon-config.option', false) // avec valeur par défaut
config('app.name')
```

### Cas d'usage typiques

- Options globales du thème qui dépendent de variables d'environnement (`.env`)
- Constantes de configuration partagées entre plusieurs Services
- Tout ce qui ne mérite pas une option WordPress mais doit être configurable par projet

---

## 5. View Composers

Auto-découverts depuis `app/View/Composers/`. Les Composers de blocs sont dans `app/View/Composers/Blocks/`.

### Conventions

- Toute la logique de données est dans le Composer, jamais dans la vue
- Accès aux données MetaBox via `get_post_meta()`, pas les helpers rwmb
- Accès aux données du bloc : `$this->view->getData()['data'][...]`
- Avec Acorn dernière version (Laravel récent) : déclarer des méthodes publiques directement, sans `with()`. Les méthodes sont appelables dans la vue comme `$maMethode()`

### Composer standard

```php
namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class Post extends Composer
{
    protected static $views = ['partials.content', 'partials.page-header'];

    public function shouldDisplayTitle(): bool
    {
        if (is_page()) {
            return (bool) get_post_meta(get_the_ID(), 'display_title', true);
        }

        return true;
    }
}
```

### Composer de bloc (accès aux champs MetaBox)

```php
namespace App\View\Composers\Blocks;

use Roots\Acorn\View\Composer;

class MonBloc extends Composer
{
    protected static $views = ['blocks.mon-bloc'];

    public function membres(): array
    {
        return $this->view->getData()['data']['membres'] ?? [];
    }
}
```

```blade
@foreach($membres() as $membre)
    <div>{{ $membre['nom'] }}</div>
@endforeach
```

### Composer avec `with()` (ancienne syntaxe, toujours valide)

```php
public function with(): array
{
    return [
        'images' => $this->view->getData()['data']['images'][0] ?? [],
    ];
}
```

---

## 6. Services et Service Providers

Pattern systématique : un **Service** contient la logique, un **ServiceProvider** enregistre les hooks et injecte le Service via le container.

### Service Provider

```php
namespace App\Providers;

use App\Services\MonService;
use Illuminate\Support\ServiceProvider;

class MonServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(MonService::class, fn() => new MonService());
    }

    public function boot(MonService $service): void
    {
        add_action('init', [$service, 'maMethode']);
        add_filter('mon_filtre', [$service, 'autreMethode'], 10, 2);
    }
}
```

### Service

```php
namespace App\Services;

class MonService
{
    public function maMethode(): void
    {
        // logique
    }
}
```

### Enregistrement dans `functions.php`

```php
Application::configure()
    ->withProviders([
        ThemeServiceProvider::class,
        PostTypesServiceProvider::class,
        BlockServiceProvider::class,
        MonServiceProvider::class,
    ])
    ->boot();
```

> `ThemeServiceProvider` étend `SageServiceProvider` d'Acorn — ne rien y mettre de spécifique, il bootstrappe Acorn.

---

## 7. Assets (Vite)

### Entry points

| Fichier | Usage |
|---------|-------|
| `resources/css/app.scss` | CSS front principal |
| `resources/js/app.js` | JS front principal |
| `resources/css/editor.scss` | Styles éditeur Gutenberg |
| `resources/js/editor.js` | Scripts éditeur Gutenberg |
| `resources/css/blocks/*.scss` | CSS par bloc (scan auto) |
| `resources/js/blocks/*.js` | JS par bloc (scan auto) |

### `vite.config.js`

```js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import { wordpressPlugin } from '@roots/vite-plugin'
import { globSync } from 'glob'

if (!process.env.APP_URL) {
  process.env.APP_URL = 'https://monsite.test'
}

export default defineConfig({
  base: '/app/themes/default/public/build/',
  plugins: [
    laravel({
      input: [
        'resources/css/app.scss',
        'resources/js/app.js',
        'resources/css/editor.scss',
        'resources/js/editor.js',
        ...globSync('resources/{css,js}/blocks/*.{scss,js}'),
      ],
      refresh: true,
    }),
    wordpressPlugin(),
  ],
  resolve: {
    alias: {
      '@scripts': '/resources/js',
      '@styles':  '/resources/css',
      '@fonts':   '/resources/fonts',
      '@images':  '/resources/images',
    },
  },
})
```

### Enqueues conditionnels (setup.php)

```php
// CSS/JS global → géré par setup.php via Vite
// CSS/JS par bloc → géré dans enqueue_assets du bloc (chargé uniquement si le bloc est présent)

// Enqueue conditionnel hors bloc :
add_action('wp_enqueue_scripts', function () {
    if (has_block('woocommerce/cart') || is_cart()) {
        wp_enqueue_script_module('cart-js', Vite::asset('resources/js/cart.js'), [], null);
    }
});
```

### Commandes

```bash
npm run dev      # Dev avec HMR
npm run build    # Build production
```

---

## 8. theme.json

`theme.json` est édité manuellement (non généré par Vite).

**Principes :**
- `defaultPalette: false` et `defaultFontSizes: false` → désactive les presets WordPress par défaut
- `defaultGradients: false`, `defaultDuotone: false` → idem
- `appearanceTools: true` → active les outils de mise en page Gutenberg
- `fluid: false` au niveau global, avec `fluid: { min, max }` uniquement sur les grandes tailles
- Les variables CSS sont générées automatiquement par WordPress sur `:root`

### Variables CSS générées

Pattern : `--wp--preset--{category}--{slug}`

```scss
.mon-element {
    color: var(--wp--preset--color--bordeaux);
    font-size: var(--wp--preset--font-size--large);
    font-family: var(--wp--preset--font-family--instrument);
}
```

Toujours utiliser ces variables CSS (pas de valeurs hardcodées) pour les couleurs, tailles de police et familles.

### Structure minimale

```json
{
  "$schema": "https://schemas.wp.org/trunk/theme.json",
  "version": 3,
  "settings": {
    "appearanceTools": true,
    "layout": { "contentSize": "720px", "wideSize": "816px" },
    "color": {
      "defaultPalette": false,
      "defaultGradients": false,
      "defaultDuotone": false,
      "palette": [
        { "slug": "noir", "color": "#000000", "name": "Noir" }
      ]
    },
    "typography": {
      "defaultFontSizes": false,
      "customFontSize": true,
      "fluid": false,
      "fontFamilies": [
        { "name": "Inter", "slug": "inter", "fontFamily": "Inter, sans-serif" }
      ],
      "fontSizes": [
        { "name": "Large", "slug": "large", "size": "36px", "fluid": { "min": "24px", "max": "36px" } }
      ]
    }
  }
}
```

---

## 9. Polylang (optionnel)

Polylang n'est pas toujours actif. Ne l'utiliser que si le projet est multilingue.

### Enregistrer les chaînes traduisibles

```php
// app/translations.php
if (function_exists('pll_register_string')) {
    pll_register_string('slogan', 'Là où les idées prennent force.', 'theme');
}
```

### Dans les vues

```blade
{{-- Toujours {!! !!} pour éviter les problèmes d'encodage --}}
{!! pll__('Voir tous les événements') !!}
```

### Dans les Composers / Services

```php
pll__('Suivant &raquo;')
pll_current_language()   // 'fr' | 'en'
pll_home_url()           // URL de base de la langue courante
```

### WP_Query avec Polylang

Les requêtes `WP_Query` et `get_posts` fonctionnent sans configuration particulière — Polylang filtre automatiquement par langue courante.

---

## 10. WooCommerce (optionnel)

WooCommerce n'est pas toujours actif. Utiliser le package [sage-woocommerce](https://github.com/generoi/sage-woocommerce).

### Structure

```
resources/views/woocommerce/
├── archive-product.blade.php
├── single-product.blade.php
└── content-product-custom-image.blade.php
resources/css/templates/
├── woocommerce.scss
├── woocommerce-single-product.scss
└── woocommerce-account.scss
app/Providers/WooCommerceServiceProvider.php
app/Services/WooCommerceShop.php
app/Services/WooCommerceSingleProduct.php
app/Services/WooCommerceAccount.php
```

### Service Provider pattern

```php
class WooCommerceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WooCommerceShop::class, fn() => new WooCommerceShop());
    }

    public function boot(WooCommerceShop $shop, WooCommerceSingleProduct $single): void
    {
        add_action('init', [$shop, 'removeWrappers'], 1, 2);
        add_action('init', [$shop, 'removeBreadcrumbs'], 1, 2);
        add_action('wp_enqueue_scripts', [$shop, 'removeWooCommerceStyles'], 1, 2);
        add_action('wp_enqueue_scripts', [$shop, 'addWooCommerceStyles'], 1, 2);
        // ...
    }
}
```

### Bonnes pratiques WooCommerce

- Supprimer les styles WooCommerce par défaut : `add_filter('woocommerce_enqueue_styles', '__return_empty_array')`
- Supprimer les wrappers natifs et les recréer en Blade
- Enqueuer les CSS WooCommerce via Vite (`resources/css/templates/woocommerce.scss`)
- Activer les supports dans `setup.php` : `wc-product-gallery-zoom`, `wc-product-gallery-lightbox`, `wc-product-gallery-slider`
- Utiliser `view('woocommerce.mon-template', [...])→render()` pour les templates custom dans les hooks WooCommerce

---

## 11. WP_Query

Utiliser `get_posts()` pour des listes simples, `WP_Query` quand on a besoin de la pagination ou des métadonnées de résultat (`max_num_pages`, etc.).

### Conventions

- `'fields' => 'ids'` quand on n'a besoin que des IDs (perf)
- Les dates MetaBox stockées en `Ymd` se comparent en `NUMERIC`
- Décomposer les meta_query complexes en méthodes privées

### Exemple : query avec filtres et meta_query imbriquée

```php
private function getConcertIds(array $filters): array
{
    return get_posts([
        'post_type'      => 'concert',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'meta_value_num',
        'meta_key'       => 'date',
        'order'          => 'ASC',
        'fields'         => 'ids',
        'meta_query'     => $this->buildMetaQuery($filters),
    ]);
}

private function buildMetaQuery(array $filters): array
{
    $metaQuery = [
        'relation' => 'AND',
        [
            'relation' => 'OR',
            ['key' => 'date',     'value' => date('Ymd'), 'compare' => '>=', 'type' => 'NUMERIC'],
            ['key' => 'date_fin', 'value' => date('Ymd'), 'compare' => '>=', 'type' => 'NUMERIC'],
        ],
    ];

    if ($filters['zone'] && $filters['zone'] !== 'all') {
        $metaQuery[] = ['key' => 'zone', 'value' => $filters['zone'], 'compare' => '='];
    }

    return $metaQuery;
}
```

### Exemple : WP_Query avec pagination dans un Composer

```php
private WP_Query $query;

public function with(): array
{
    $paged = get_query_var('paged') ?: (get_query_var('page') ?: 1);

    $this->query = new WP_Query([
        's'              => get_query_var('search_term'),
        'posts_per_page' => 12,
        'paged'          => $paged,
        'post_type'      => ['post', 'campagne'],
    ]);

    return [
        'results'    => $this->getResults(),
        'pagination' => $this->getPagination(),
    ];
}
```

---

## 12. Étendre les blocs core Gutenberg

Pour modifier le comportement d'un bloc WP core (rendu PHP, attributs, inspector controls), ne pas utiliser un View Composer — créer une classe dans `app/Blocks/` avec une méthode `boot()` statique.

### Classe `CoreButton` — pattern de référence

```php
namespace App\Blocks;

class CoreButton
{
    public static function boot(): void
    {
        add_filter('register_block_type_args', [static::class, 'registerAttributes'], 10, 2);
        add_filter('render_block', [static::class, 'addIcon'], 10, 2);
    }

    public static function registerAttributes(array $args, string $name): array
    {
        if ($name !== 'core/button') {
            return $args;
        }
        $args['attributes']['iconColor'] = ['type' => 'string'];
        $args['attributes']['iconPosition'] = ['type' => 'string', 'default' => 'right'];
        return $args;
    }

    public static function addIcon(string $html, array $block): string
    {
        if ($block['blockName'] !== 'core/button') return $html;
        // modification du HTML rendu...
        return $html;
    }
}
```

`BlockServiceProvider` appelle automatiquement `boot()` sur toutes les classes de `app/Blocks/` qui l'implémentent.

> **Important :** pour enregistrer des attributs custom sur un bloc MB Blocks, utiliser le même filtre `register_block_type_args` — la clé `attributes` dans le tableau `meta_boxes` est ignorée par MB Blocks pour le schéma REST.

### Ajouter des InspectorControls côté JS

Pattern dans `resources/js/admin/{feature}.js`, importé depuis `editor.js` :

```js
import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorControls } from '@wordpress/block-editor';
import { createElement as el, Fragment } from '@wordpress/element';
import {
  __experimentalToggleGroupControl as ToggleGroupControl,
  __experimentalToggleGroupControlOption as ToggleGroupControlOption,
  __experimentalToolsPanel as ToolsPanel,
  __experimentalToolsPanelItem as ToolsPanelItem,
  __experimentalUnitControl as UnitControl,
} from '@wordpress/components';

// 1. Enregistrer l'attribut
addFilter('blocks.registerBlockType', 'theme/mon-feature', (settings, name) => {
  if (name !== 'core/button') return settings;
  settings.attributes = { ...settings.attributes, monAttr: { type: 'string' } };
  return settings;
});

// 2. Ajouter le contrôle
const withMonControl = createHigherOrderComponent((BlockEdit) => (props) => {
  if (props.name !== 'core/button') return el(BlockEdit, props);
  return el(Fragment, {},
    el(BlockEdit, props),
    el(InspectorControls, { group: 'styles' },
      el(ToolsPanel, { label: 'Mon réglage', resetAll: () => props.setAttributes({ monAttr: undefined }) },
        el(ToolsPanelItem, {
          label: 'Mon attribut',
          isShownByDefault: true,
          hasValue: () => !!props.attributes.monAttr,
          onDeselect: () => props.setAttributes({ monAttr: undefined }),
        },
          el(ToggleGroupControl, { value: props.attributes.monAttr || 'defaut', onChange: (val) => props.setAttributes({ monAttr: val }), isBlock: true },
            el(ToggleGroupControlOption, { value: 'option-a', label: 'A' }),
            el(ToggleGroupControlOption, { value: 'option-b', label: 'B' }),
          ),
        ),
      ),
    ),
  );
}, 'withMonControl');

addFilter('editor.BlockEdit', 'theme/mon-control', withMonControl);
```

**Namespace des blocs MB Blocks en JS :** `meta-box/{block-id}` (ex. `meta-box/masked-image`)

**Slots InspectorControls disponibles :**
- `group: 'color'` → onglet Styles, section Couleur (`panelId: props.clientId` requis)
- `group: 'styles'` → onglet Styles, section générale (utiliser `ToolsPanel` + `ToolsPanelItem` sans `panelId`)
- `group: 'dimensions'` → onglet Styles, section Dimensions
- Pas de group → onglet Paramètres

**Composants expérimentaux à utiliser (préfixe `__experimental`) :**
- `ToggleGroupControl` + `ToggleGroupControlOption`
- `ToolsPanel` + `ToolsPanelItem`
- `UnitControl`

---

## 13. JS frontend — animations et scroll (performance)

### Anti-pattern à proscrire

```js
// ❌ getBoundingClientRect dans un scroll handler = layout reflow synchrone par frame
window.addEventListener('scroll', () => {
  const rect = el.getBoundingClientRect();
  // ...
}, { passive: true });
```

Même avec `passive: true`, lire la géométrie d'un élément force un reflow (~16ms+ sur pages complexes). Avant d'écrire un scroll handler, toujours évaluer `position: sticky` (CSS) puis `IntersectionObserver` (JS).

### Widget qui suit le scroll et s'arrête au-dessus d'un autre élément

`position: sticky` + DOM sibling — pas de JS scroll-time, le footer (ou élément suivant) pousse naturellement le widget vers le haut :

```scss
.block-hero__secteurs {
  position: sticky;
  bottom: 2rem;
  margin-top: calc(-100px - 2rem); /* rentrer visuellement dans la zone du bloc précédent */
  margin-bottom: 2rem;
}
```

**Contraintes de markup :**
- L'élément doit être **sibling** (pas enfant) du bloc dont il « suit la fin » — sinon `overflow: hidden` du parent clippe le sticky
- Si Gutenberg ne permet pas le placement naturel dans le DOM, normaliser au runtime :
  ```js
  document.querySelector('main').appendChild(this.el);
  ```

### IntersectionObserver pour réagir à la position dans le viewport

```js
new IntersectionObserver(
  ([entry]) => {
    this.lastIntersecting = entry.isIntersecting;
    this.sync();
  },
  { threshold: 0, rootMargin: '-95% 0px 0px 0px' }, // fire quand 95% du haut a scrollé
).observe(this.target);
```

`rootMargin` règle finement le déclencheur. Off-main-thread, aucun coût scroll.

### Resize debouncé + cache de mesures

```js
onResize() {
  clearTimeout(this.resizeTimer);
  this.resizeTimer = setTimeout(() => this.measure(), 150);
}

measure() {
  if (this.isCollapsed) return; // garde la dernière mesure naturelle
  this.naturalWidth = this.el.offsetWidth;
}
```

Re-mesurer uniquement quand les dimensions sont représentatives.

### Pattern sync — réconcilier état observé et exécution

Quand un observer signale des changements pendant qu'une animation tourne, séparer **état désiré** et **exécution** :

```js
sync() {
  if (this.isAnimating) return;
  if (!this.lastIntersecting && !this.isCollapsed) this.collapse();
  else if (this.lastIntersecting && this.isCollapsed) this.expand();
}

// L'observer alimente l'état
observer.callback = ([entry]) => {
  this.lastIntersecting = entry.isIntersecting;
  this.sync();
};

// Les animations rappellent sync() en fin d'exécution
animate(target, {
  // ...
  onComplete: () => {
    this.isAnimating = false;
    this.sync(); // réconcilie si l'état a changé pendant l'animation
  },
});
```

Évite les pertes d'état lors d'un scroll rapide (collapse/expand entrelacés).

### Anime.js v4 — conventions du projet

- Import : `import { animate, stagger } from 'animejs'`
- Easings courants : `out(2)`, `out(3)`, `inOut(2)`, `outElastic(1, 0.5)`, `inOutElastic(0.1, 3)`
- Stagger inversé : `stagger(40, { from: 'last' })`
- Animations parallèles indépendantes : plusieurs `animate()` simultanés — pas besoin de timeline
- `onComplete` fire après terminaison de tous les targets (stagger total inclus)

---

## 13b. Scroll horizontal (système global)

Le thème embarque un système de scroll horizontal réutilisable : au scroll vertical, un contenu plus large que le viewport se translate horizontalement dans un wrapper `position: sticky`. Activé uniquement ≥ 1000px.

### Fichiers

| Fichier | Rôle |
|---------|------|
| `resources/js/global/horizontal-scroll.js` | Classe `HorizontalScroll` + `initHorizontalScroll()` |
| `resources/css/base/horizontal-scroll.scss` | Styles `.horizontal-scroll` (BEM) |

Déjà importés dans `app.js` et `app.scss`. Pas besoin de les enqueuer par bloc.

### HTML attendu

```html
<div class="horizontal-scroll" js-horizontal-scroll>
  <div class="horizontal-scroll__wrapper" js-horizontal-scroll_wrapper>
    <div class="horizontal-scroll__view" js-horizontal-scroll_view>
      <!-- Items horizontaux -->
      <div class="item">…</div>
      <div class="item">…</div>
    </div>
    <div class="horizontal-scroll__scrollbar"></div>
  </div>
  <div class="horizontal-scroll__scroller" js-horizontal-scroll_scroller></div>
</div>
```

Les 4 attributs `js-horizontal-scroll*` sont obligatoires. Le `__scroller` est vide — sa hauteur (`200vh`/`250vh`) détermine la durée du défilement horizontal.

### Variables CSS pilotées par le JS

| Variable | Cible | Rôle |
|---|---|---|
| `--transformation` | `.horizontal-scroll__view` | Translation horizontale en `px` (négative) |
| `--scrollbar` | `::after` de `.horizontal-scroll__scrollbar` | Largeur barre de progression (`0%` → `100%`) |

### Intégration dans un bloc

1. Dans la vue Blade, reproduire la structure HTML avec les attributs `js-*`
2. Les classes des items (largeurs en `vw`, `white-space`, etc.) se définissent dans le SCSS du bloc — pas dans `horizontal-scroll.scss`
3. La hauteur du `__scroller` peut être overridée en SCSS du bloc si la vitesse par défaut ne convient pas

### Comportement mobile

Sous 1000px : JS inactif (`matchMedia` check), scrollbar et scroller `display: none`, view perd son `inline-flex`. Prévoir un layout vertical des items dans le SCSS du bloc.

---

## 14. Champs custom sur les éléments de menu (nav menu items)

Quand on a besoin d'ajouter des options au menu WP (image, style de rendu, flag…), respecter la séparation Sage : **Service** + **Provider** + **Blade** + **JS séparé**. Jamais de HTML inline en PHP, jamais de jQuery, jamais d'`add_action` dans `app/filters.php` pour ce genre de feature.

### Pattern

```
app/Services/NavMenuFields.php           # logique : enregistrer/afficher/sauver les champs
app/Providers/NavMenuFieldsServiceProvider.php
resources/views/admin/menu-fields.blade.php   # markup des champs (par item, par profondeur)
resources/js/admin/menu-fields.js        # picker média natif (wp.media), pas de jQuery
resources/css/admin/menu-fields.scss     # optionnel
```

### Service

```php
namespace App\Services;

use function Roots\view;

class NavMenuFields
{
    public const STYLES = [
        'normal' => 'Normal',
        'fleche' => 'Flèche',
        'bouton' => 'Bouton',
    ];

    public function render(int $itemId, object $item, int $depth): void
    {
        echo view('admin.menu-fields', [
            'itemId'   => $itemId,
            'depth'    => $depth,
            'imageId'  => (int) get_post_meta($itemId, '_menu_image', true),
            'imageUrl' => $this->imageUrl($itemId),
            'style'    => get_post_meta($itemId, '_menu_style', true) ?: 'normal',
            'styles'   => self::STYLES,
        ])->render();
    }

    public function save(int $menuId, int $itemId): void
    {
        if (isset($_POST['menu_item_image'][$itemId])) {
            $id = (int) $_POST['menu_item_image'][$itemId];
            $id > 0
                ? update_post_meta($itemId, '_menu_image', $id)
                : delete_post_meta($itemId, '_menu_image');
        }

        if (isset($_POST['menu_item_style'][$itemId])) {
            $style = sanitize_key($_POST['menu_item_style'][$itemId]);
            if (array_key_exists($style, self::STYLES)) {
                update_post_meta($itemId, '_menu_style', $style);
            }
        }
    }

    public function enqueue(string $hook): void
    {
        if ($hook !== 'nav-menus.php') {
            return;
        }
        wp_enqueue_media();
        wp_enqueue_script('heat/menu-fields', \Illuminate\Support\Facades\Vite::asset('resources/js/admin/menu-fields.js'), [], null, true);
    }

    private function imageUrl(int $itemId): string
    {
        $id = (int) get_post_meta($itemId, '_menu_image', true);
        return $id ? (wp_get_attachment_image_url($id, 'medium') ?: '') : '';
    }
}
```

### Provider

```php
namespace App\Providers;

use App\Services\NavMenuFields;
use Illuminate\Support\ServiceProvider;

class NavMenuFieldsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(NavMenuFields::class, fn() => new NavMenuFields());
    }

    public function boot(NavMenuFields $service): void
    {
        // Signature WP : ($item_id, $item, $depth, $args, $id)
        add_action('wp_nav_menu_item_custom_fields', [$service, 'render'], 10, 3);
        add_action('wp_update_nav_menu_item', [$service, 'save'], 10, 2);
        add_action('admin_enqueue_scripts', [$service, 'enqueue']);
    }
}
```

### Blade — `resources/views/admin/menu-fields.blade.php`

Le scoping par profondeur se fait **ici**, dans la vue, pas par un `if` éparpillé. `$depth === 0` = niveau 1 (top), `$depth === 1` = niveau 2.

```blade
@if($depth === 0)
    <p class="field-menu-image description description-wide">
        <label>
            <strong>Image (megamenu)</strong>
            <input type="hidden" class="menu-item-image-id"
                   name="menu_item_image[{{ $itemId }}]"
                   value="{{ $imageId ?: '' }}">
            <span class="menu-item-image-preview">
                @if($imageUrl)
                    <img src="{!! $imageUrl !!}" style="max-width:80px;height:auto;display:block;">
                @endif
            </span>
            <button type="button" class="button menu-item-image-select">Choisir</button>
            <button type="button" class="button menu-item-image-remove" @if(!$imageId) hidden @endif>Retirer</button>
        </label>
    </p>
@endif

@if($depth === 1)
    <p class="field-menu-style description description-wide">
        <label>
            <strong>Style de colonne</strong>
            <select name="menu_item_style[{{ $itemId }}]">
                @foreach($styles as $key => $label)
                    <option value="{!! $key !!}" @selected($style === $key)>{!! $label !!}</option>
                @endforeach
            </select>
        </label>
    </p>
@endif
```

### JS admin — `resources/js/admin/menu-fields.js`

Pas de jQuery. `wp.media` est exposé globalement par `wp_enqueue_media()`.

```js
document.addEventListener('click', (e) => {
  const select = e.target.closest('.menu-item-image-select');
  const remove = e.target.closest('.menu-item-image-remove');

  if (select) {
    e.preventDefault();
    const wrap = select.closest('label');
    const input = wrap.querySelector('.menu-item-image-id');
    const preview = wrap.querySelector('.menu-item-image-preview');
    const removeBtn = wrap.querySelector('.menu-item-image-remove');

    const frame = window.wp.media({
      title: 'Sélectionner une image',
      multiple: false,
      library: { type: 'image' },
    });

    frame.on('select', () => {
      const att = frame.state().get('selection').first().toJSON();
      const url = att.sizes?.medium?.url || att.url;
      input.value = att.id;
      preview.innerHTML = `<img src="${url}" style="max-width:80px;height:auto;display:block;">`;
      removeBtn.hidden = false;
    });

    frame.open();
  }

  if (remove) {
    e.preventDefault();
    const wrap = remove.closest('label');
    wrap.querySelector('.menu-item-image-id').value = '';
    wrap.querySelector('.menu-item-image-preview').innerHTML = '';
    remove.hidden = true;
  }
});
```

### Vite — déclarer l'entry admin

Si `resources/js/admin/*.js` n'est pas déjà scanné par un glob, l'ajouter explicitement dans `vite.config.js` ou via un glob :

```js
input: [
  // ...
  ...globSync('resources/js/admin/*.js'),
],
```

### Récupération côté front (View Composer)

```php
private function decorate(array $items): array
{
    foreach ($items as $item) {
        $imageId = (int) get_post_meta($item->id, '_menu_image', true);
        $item->image = $imageId ? wp_get_attachment_image_url($imageId, 'large') : '';
        $item->style = get_post_meta($item->id, '_menu_style', true) ?: 'normal';

        if (!empty($item->children)) {
            $item->children = $this->decorate($item->children);
        }
    }
    return $items;
}
```

### Règles à respecter

- Le scoping par niveau hiérarchique (top vs niveau 2 vs niveau N) se gère dans la **vue Blade** via `$depth`, pas en stockant les champs partout puis en filtrant côté front.
- Ne **jamais** mélanger PHP procédural et HTML : tout markup admin passe par une vue Blade dédiée, même quand WP attend un `echo` direct dans un hook.
- Ne **jamais** utiliser jQuery ni `wp_add_inline_script('jquery-core', ...)`. Vanilla JS + `wp.media` couvrent tous les besoins.
- Le Provider est enregistré dans `functions.php` via `withProviders([...])`, pas via `app/filters.php`.

---

## 15. Troubleshooting

### Cache Acorn

Vider le cache après : ajout d'un Provider, d'un Composer, modification de config.

```bash
wp acorn optimize:clear   # Vide tout (config, views, routes) — à privilégier
wp acorn cache:clear      # Cache applicatif uniquement
wp acorn view:clear       # Cache Blade uniquement
```

### Permaliens

Après tout ajout ou modification de CPT, taxonomie ou règle de réécriture :

```bash
wp rewrite flush
# Ou : admin → Réglages → Permaliens → Enregistrer
```

### Vite HMR ne fonctionne pas

Vérifier que `APP_URL` est défini dans `.env` et correspond au vhost local. Vite en a besoin pour le proxy HMR.

```bash
APP_URL=https://monsite.test
```

### Encodage / accents

Ne jamais utiliser `{{ }}` pour du texte — toujours `{!! !!}`. Les doubles accolades échappent les entités HTML ce qui casse les accents et caractères spéciaux français.

```blade
{{-- ❌ --}}
{{ $titre }}

{{-- ✓ --}}
{!! $titre !!}
```

### `wp_enqueue_script_module` dans les blocs

Toujours wrapper dans `!is_admin()` pour éviter les conflits avec l'éditeur Gutenberg :

```php
'enqueue_assets' => function () {
    wp_enqueue_style('mon-bloc', Vite::asset('resources/css/blocks/mon-bloc.scss'), [], null);
    if (!is_admin()) {
        wp_enqueue_script_module('mon-bloc-js', Vite::asset('resources/js/blocks/mon-bloc.js'), [], null);
    }
},
```

### Composer ou Provider non pris en compte

1. Vérifier que le Provider est bien enregistré dans `functions.php`
2. Vider le cache Acorn : `wp acorn optimize:clear`
3. Pour un Composer : vérifier que `$views` correspond exactement au nom de la vue Blade (`blocks.mon-bloc` pour `resources/views/blocks/mon-bloc.blade.php`)
