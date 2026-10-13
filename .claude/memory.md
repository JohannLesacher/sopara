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
- **CPTs (2026-10-08)**: `expert` (slug `expert`, metas `fonction`, `expertise`, `annees_experience`, `linkedin`) ; blog = `post` natif (metas `experts` multiple, `reading_time_override`, `related_posts`, `_reading_time` auto)
- **Taxonomies (2026-10-08)**: `type_contenu`, `secteur_activite` sur `post` (noindex Yoast) ; term meta `intro` sur `category`
- **SEO (2026-10-08)**: Yoast en prod (TSF supprimé). Yoast ne crée pas d'indexables hors `WP_ENVIRONMENT_TYPE=production` → fil d'Ariane local sans catégorie, normal
- **mu-plugin `hide-posts.php`**: supprimé en local, **à supprimer en prod** (bloque l'admin Articles) ; commentaires gérés par mu-plugin `disable-comments.php`
- **Pre-commit**: Husky → `npm run format:all:check` (Prettier + Pint + Blade Formatter)

## Architectural decisions

- **Résumé d'article = extrait natif WP**: affiché en tête d'article (`.single-post__summary`), exigence GEO « réponse directe 2-3 phrases » (conservé, décision 2026-10-09)
- **Listing blog**: page « Blog » = Page des articles (Réglages > Lecture, traduite par langue) ; `home.blade.php` rend la bannière auto + le contenu Gutenberg de cette page, où se place le bloc `meta-box/post-listing` (main query → pagination native `/blog/page/N/`)

- **Atomic Design for CSS**: components organized by complexity (if applicable)
- **`@include()` for Blade**: components included with `@include()`, not `<x-component>`
- **Extended CPTs config-based**: post types and taxonomies defined in `config/post-types.php`, auto-registered
- **Storybook as source of truth**: if configured, it's the source of truth for components
- **`<section>` tag rule**: ACF blocks and macro components (page section organisms, excluding header/footer) use `<section>` as root tag

- **CTA = compositions réutilisables, pas de bloc dédié** (décision 2026-10-09) : « Contactez-nous » (`wp_block` 984) et « Échangez avec un expert » (`wp_block` 1500, bouton « Prendre rendez-vous »), toutes deux vers la page Contact, traduisibles Polylang (à créer par langue en prod). Bloc `meta-box/cta` supprimé. Le lien du bouton est géré par l'utilisateur en admin

## Components créés

- **Hero** (en cours de planification) — image de fond, zone titre centrée verticalement, zone texte bas (InnerBlocks), secteurs d'activité (position absolue bas-droite)

## Règles utilisateur (confirmées le 2026-10-09)

> Seules ces règles s'appliquent partout. Les autres entrées sont des constats techniques ou des décisions ponctuelles (cf. retours de revue ≠ règles).

- **Pas de complication inutile** : choisir la solution la plus simple ; optimisations (préchargement, cache…) au cas par cas, jamais par défaut.
- **Pas de fonction core privée** (préfixe `_`, ex. `_prime_post_caches()`).
- **Globales** : si nécessaire, `global $var;`, jamais `$GLOBALS['var']`.
- **Un Service = un Service Provider dédié**, déclaré dans `functions.php`. Exception : `app/PostTypes/` a déjà son provider automatique (`PostTypesServiceProvider`), ne rien y enregistrer d'autre.
- **Gutenberg natif au maximum** : `InnerBlocks` pour le contenu éditorial, réglages via `theme.json` et `supports` (color picker natif, spacing…) plutôt que des champs MetaBox équivalents.
- **Un bloc = un élément, si l'élément fait sens seul** (ex. `KeyFigure`, mis en grille via `core/columns`). Pour un bloc de type listing dont les items n'ont pas de sens isolément, un champ clonable est légitime (ex. `ImageHotspots` et sa liste de spots).
- **Pas de bloc dédié quand une composition réutilisable suffit** (ex. CTA = compositions « Contactez-nous » / « Échangez avec un expert »).
- **Composers : `with()`** plutôt que des méthodes publiques appelées dans la vue (une méthode publique est réexécutée à chaque appel).
- **Composers : les méthodes d'une ligne sont écrites inline dans `with()`** ; ne garder en `protected` que les méthodes multi-lignes.

**Non-règles (décisions ponctuelles)** : pas de préchargement sur le blog (décision propre au blog, ~2 requêtes/carte acceptées) ; hover des cartes blog en voile d'opacité (choix pour ce composant, pas une règle « opacity > transform ») ; convention `_seed_blog` = simple note technique pour les données de test.

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
- **Shared scroll listener util** (`resources/js/global/scroll.js`): un seul `scroll` listener global avec rAF + `Set` de callbacks et dedupe `lastY`. Modules (`header.js`, `secteurs.js`) consomment via `onScroll(fn)`. Évite duplication des handlers et respecte la sensibilité perf de l'utilisateur.
- **Decoupling cross-block UI via theme settings**: pour un fragment UI partagé entre pages (ex. secteurs jadis dans Hero), déplacer le contenu dans `ThemeSettings` (i18n par langue) + toggle `display_*` en page meta (`PostTypes/Page.php`) + composer dédié + partial inclus dans `content-page.blade.php`. Le bloc d'origine ne porte plus la donnée.
- **MB Settings tabs by language**: pour une page de settings multilingue, déclarer `tabs` sur la page (`['fr' => 'FR', ..., 'global' => 'Global']`) + `tab_style => 'left'`, et assigner `'tab' => $lang` sur chaque meta box. Onglet `global` pour champs non-i18n (réseaux sociaux, images partagées). Scale à N langues sans alourdir l'UI.
- **Sticky element placement**: ne pas déplacer un élément `position: sticky` par JS après le premier enfant d'un grid parent — casse le containing block du sticky. Laisser l'élément en fin de container (rendu via Blade), `bottom: Xrem` suffit à le faire flotter visuellement en bas du premier bloc tant que le container reste visible.
- **Blog read API**: `App\Services\BlogPost` (singleton enregistré dans `BlogPostServiceProvider` — [2026-10-09] PAS dans `PostTypesServiceProvider`, réservé à l'auto-chargement de `app/PostTypes/` ; 1 service = 1 provider dédié, ajouté dans `functions.php`) (lecture seule) ; écriture/hooks du post type dans `PostTypes/Post.php`, branchés explicitement dans `PostTypesServiceProvider::boot()`
- **Yoast schema extension**: filtres `wpseo_schema_*` dans `StructuredData` + pièce `App\Services\Schema\ExpertPerson` (étend `Abstract_Schema_Piece`, ajoutée via `wpseo_schema_graph_pieces`)
- **Contexte des pièces de schéma Yoast (2026-10-09)**: dans une `Abstract_Schema_Piece`, lire la page courante via `$this->context->indexable` (`object_type`, `object_sub_type`, `object_id`), pas `is_singular()` / `get_queried_object()`. `$this->context->post` n'est pas toujours rempli → ne pas s'y fier
- **Bannière auto (templates sans Gutenberg)**: `partials/banner.blade.php` reproduit le markup du `core/cover` style `hero-banner` (core/cover est statique, `render_block()` ne génère rien sans innerHTML). Image : thumbnail (post, page Blog) → meta terme `banner_image` → `theme_settings.banner_default`
- **`.alignfull` doit être enfant direct de `.main`**: seule la règle `.main > .alignfull` (layout.scss) en fait une grille qui centre le contenu en colonne `content`. Dans un wrapper (`<article class="innergrid alignfull">`), une bannière cover a son H1 collé à gauche → inclure `partials.banner` avant `<article>`
- **Libellés FR des CPT**: extended-cpts génère des libellés anglais à partir de `names` → ajouter une clé `labels` complète en français dans `config/post-types.php` (ex. `expert`, image à la une renommée « Photo »), puis `wp acorn optimize:clear`
- **Limiter les niveaux de titre par post type**: `register_block_type_args` (PHP) n'a pas le contexte du post type → filtre JS `blocks.registerBlockType` dans `resources/js/admin/heading.js`, condition `window.typenow === 'post'`, défaut `levelOptions: [2, 3, 4]`
- **Polylang `expert` / `type_contenu` / `secteur_activite`**: config `post_types` / `taxonomies` vide → non traduisibles tant qu'on ne les active pas (Langues > Réglages > Types de contenu personnalisés et taxonomies)
- **Réglages Yoast via `wpseo_titles`**: `post_types-post-maintax = category` (catégorie dans le fil d'Ariane), `noindex-tax-{taxonomy} = true` ; `wp yoast index` refusé hors `WP_ENVIRONMENT_TYPE=production`
- **Seed de contenu via wp-cli**: markup MB Blocks = attributs JSON dans le commentaire (`<!-- wp:meta-box/key-figures {"data":{"chiffres":[...]}} /-->`) ; FAQ = `<!-- wp:accordion {"addToFaqSchema":true} -->` + accordion-item/heading/panel (markup à copier depuis la page d'accueil, ID 4). Appeler `kses_remove_filters()` (wp-cli sans utilisateur → kses actif)
- **Convention seed/cleanup**: marquer tout contenu de test avec la meta `_seed_blog = 1` (posts, experts, termes) et nettoyer via `meta_key` + `'lang' => ''` (sinon Polylang filtre par langue)
- **Bloc = 1 élément, mise en grille via `core/columns`**: ex. `KeyFigure` (un seul chiffre clé), pas de clone group. Fond/couleur/padding via `supports` (`color`, `spacing`) + `$blockClass()`/`$blockStyle()` du Composer `Blocks/Common`, pas de fond codé en dur
- **Couleurs dans un bloc → palette native Gutenberg** : `supports.color` (text/background) et, pour une 2e couleur (ex. chiffre de `KeyFigure`), un attribut custom + `ColorGradientSettingsDropdown` dans `InspectorControls` `group: 'color'` (pattern `CoreButton` + `button-icon-color.js`), attribut déclaré aussi en PHP via `register_block_type_args`. Pas de select MetaBox de couleurs (noms de slugs pas parlants pour le client)
- **Espacement auto des contenus rédactionnels (articles, experts)**: reset `* { margin: 0 }` + `blockGap` limité aux `is-layout-*` → aucun espacement natif dans `.single-post__content` / `.expert__content`. Rythme vertical dans `sections/blog.scss` : parent en classe (0,1,0, pour battre les styles core chargés avant), enfants en `:where()` ; `* + *` = spacing-50, titres/médias/blocs = spacing-70, après un titre = spacing-40. Les pages gardent l'espacement manuel (sections/spacer)
- **Pas de `_prime_post_caches()` / `primeCaches()`** (supprimés le 2026-10-09) : fonction core privée ; les listes d'IDs sont résolues avec `get_posts()` (`post__in` + `orderby => post__in`)
- **Mesurer les requêtes SQL d'une page (sans Query Monitor connecté)**: mu-plugin temporaire `web/app/mu-plugins/zz-tmp-querycount.php` qui affiche `<!-- QCOUNT {$wpdb->num_queries} -->` sur `shutdown`, `curl` puis `grep`, et **supprimer le fichier après usage**. Comparer à une page de référence (Contact ≈ 122 requêtes, dues au header/mégamenu)
- **[2026-10-09] Correction coût des cartes blog**: sans préchargement, chaque carte coûte ~2 requêtes (vignette + experts) → listing 10 cartes ≈ +25 requêtes vs Contact (147), article complet 160, catégorie 130. L'estimation « gain de 3 à 6 requêtes » donnée avant était fausse (mesurée sur un seed plus léger). Alternative publique si besoin : `update_post_thumbnail_cache()`
- **[2026-10-09] Décision : pas de préchargement sur le blog** — ~2 requêtes/carte jugées acceptables par l'utilisateur (9 articles/page). Ne pas réintroduire `update_post_thumbnail_cache()` ni priming sans demande
- **[2026-10-09] Composers : `with()` plutôt que méthodes publiques appelées dans la vue** — Acorn réexécute une méthode publique à chaque appel (`@if($x()) {!! $x() !!}` = 2 exécutions, confirmé par l'utilisateur). `with()` calcule une fois (pattern de `Composers/Secteurs.php`). Les clés de `with()` restent soumises aux collisions avec les données du parent → garder les préfixes `card*` / `grid*`. Refactor appliqué le 2026-10-09 : Composers blog en `with()` + méthodes `protected` (style `Secteurs`), vues en `$x` au lieu de `$x()` ; `Home` (vue `home`) séparé d'`Archive` (vue `taxonomy`) pour ne pas calculer le contenu de la page Blog sur les catégories
- **[2026-10-09] `expert` traduisible Polylang**: `pll_is_translated_post_type('expert')` = true → un expert sans langue ou sans traduction dans la langue de l'article n'apparaît plus (ni chapeau, ni JSON-LD). En prod : traduire chaque expert dans chaque langue. Experts de test (1485, 1486) passés en FR
- **Composers : inline dans `with()` les méthodes d'une ligne** (demande utilisateur 2026-10-09) — ne garder en `protected` que les méthodes multi-lignes ; l'ID courant calculé une fois dans `with()` et stocké en propriété (`$this->postId`), comme `Secteurs`
- **Insérer / dupliquer une composition synchronisée via wp-cli**: dans un contenu, référence `<!-- wp:block {"ref":ID} /-->` (le rendu suit les modifs de la composition). Duplication : `wp_insert_post` (`post_type => wp_block`) avec le `post_content` source modifié par `str_replace` + `pll_set_post_language($id, 'fr')` ; pas de meta `wp_pattern_sync_status` = synchronisée (cas de 984 et 1500)
- **Hiérarchie de templates : catégorie ≠ taxonomie** — `category-{slug}` → `category` → `archive` → `index` ; `taxonomy.php` ne sert qu'aux taxonomies custom. Sans `category.blade.php` les catégories tombent sur `index.blade.php`. Garder `category.blade.php` = `@extends('taxonomy')` (choix 2026-10-09, plutôt qu'un `archive.blade.php` qui capterait aussi auteur/date/étiquettes)
- **Globales : style `global $wp_query;`** (règle utilisateur 2026-10-09) — si une globale est nécessaire, la déclarer avec `global $var;`, jamais `$GLOBALS['var']`. Sur le blog, la requête principale est lue via `BlogPost::mainQueryPosts()` (réutilise la main query déjà paginée + une exclue par `pre_get_posts` ; `paginate_links()` s'appuie dessus)

## Recurring errors to avoid

- **MB Blocks InnerBlocks + JS libraries (ex. Splide)**: MB Blocks enveloppe `<InnerBlocks />` dans un `<div>` anonyme en PHP → les slides/items ne sont PAS fils directs du conteneur attendu par la lib JS. Résolution non trouvée proprement à ce jour — le workaround JS (`querySelector + classList.add`) a été rejeté comme hacky. À résoudre avant de recréer un slider avec InnerBlocks.

- **`image_advanced` vs `single_image`**: utiliser `single_image` pour une image unique (ex. fond de hero), `image_advanced` pour une liste d'images (ex. galerie secteurs)
- **MB Blocks custom attributes via `attributes` key**: la clé `attributes` dans le tableau meta_boxes est ignorée par MB Blocks pour le schéma REST → toujours utiliser `add_filter('register_block_type_args', ...)` à la place
- **MB Blocks namespace JS**: le namespace est `meta-box/{block-id}` (pas `mbblocks/`)

- **Acorn Composer name collisions**: les données d'un parent `@include` priment sur les méthodes du Composer de la vue incluse → préfixer les méthodes des partials réutilisés (`cardTitle`, `gridPagination`) et ne pas passer en param d'`@include` une clé portant le nom d'une méthode du Composer cible
- **Variables font-size theme.json**: les slugs `h1`…`h6` donnent `--wp--preset--font-size--h-1`…`h-6` (tiret avant le chiffre), pas `--h1`
- **2e couleur custom (slug + hex)**: stocker `valueColor` (slug palette, via `getColorObjectByColorValue`) ou `customValueColor` (hex). Si hex présent, ne pas sortir la classe `has-{slug}-color` (elle est `!important` et écraserait le style inline) — cf. `Composers/Blocks/KeyFigure.php`
- **`a:hover { text-decoration: underline }` global** (`base/global.scss`, spécificité 0,1,1) : bat une classe seule sur un lien-bloc (carte) → ajouter `&:hover { text-decoration: none }` sur le lien. Cartes blog : hover = titre en `primary-0` + voile d'opacité sur l'image
- **Hover cartes blog** : voile `::after` `primary-0` en `mix-blend-mode: multiply`, seule l'`opacity` est animée (0 → 0.35) ; le zoom d'image était saccadé (décision propre aux cartes, pas une règle)
- **Alignement vertical d'un lien `inline-flex` dans un `<li>`**: il se pose sur la ligne de base (espace des descendantes) → décalé vers le haut à côté d'autres items flex. Mettre le `li` et le lien en `display: flex` (ex. experts du chapeau article)
- **`paginate_links(['type' => 'list'])`**: le `<ul>` porte aussi la classe `page-numbers` → styler `:is(a, span).page-numbers` et pas `.page-numbers` seul (sinon bordure/inline-flex sur le `ul`)
- **Préréglage de couleur effaçable (attribut)**: `default: 'primary-0'` sur l'attribut ; à l'effacement, enregistrer `''` (pas `undefined`) → Gutenberg ne réapplique le défaut que si l'attribut est absent, donc `''` est sérialisé et la couleur hérite du texte. Aucun repli en CSS/PHP (cf. `resources/js/admin/key-figure.js`, `CLEARED_COLOR`)
- **Polylang : pas de repli sur l'ID source** — `pll_get_post($id) ?: $id` + `'lang' => ''` affiche un contenu dans la mauvaise langue (article lié / expert FR sur page EN). Ne garder que les IDs traduits dans la langue courante (bug relevé et corrigé le 2026-10-09 : `translatedIds()` ne garde que `pll_get_post($id)` non nul, `'lang' => ''` retiré de `publishedPosts()`). NB : `pll_get_post()` renvoie 0 en wp-cli (pas de langue courante)
- **Paramètre d'@include et collisions** : `PostListing` expose `featured` (WP_Post) → le paramètre de carte s'appelle `isFeatured` (sinon la clé héritée du parent rendrait toutes les cartes « à la une ») ; `post-grid` passe `'isFeatured' => false` explicitement
- **`make:block --vc` stub**: le Composer généré n'a pas `use Roots\Acorn\View\Composer;` → à ajouter
- **zsh + boucle `wp acorn make:block $b`**: zsh ne découpe pas les variables non quotées → lancer une commande par bloc

## Audit blog en cours (2026-10-09)

Faits : A1, A2, A3, A4 (`fetchpriority`/`eager` retirés de `post-featured`, WP ajoute `loading="lazy"`), A6 (format date `j F Y`, fait par l'utilisateur en admin), A5 (libellés FR complets `type_contenu` / `secteur_activite` dans `config/post-types.php`, 29 clés `labels` dont descriptions des champs de terme). B7 (un seul partial `post-card`, paramètre `isFeatured` → `cardModifier` / `cardImageSize`, `post-featured` et `post-card-meta` supprimés). B8 (`breadcrumb.blade.php` = `{!! $breadcrumb !!}` seul). B9 (`category.blade.php` = `@extends('taxonomy')`). B10 (`BlogPost::mainQueryPosts()` encapsule `global $wp_query`). Restants, un point à la fois avec validation : B11 `key-figure` via `with()` · B12 `ExpertPerson` via `$this->context` · C13 Pint fichiers blog · C14 `blog.scss` rem → presets spacing · C15 `post-listing.scss` · D16 (option) preload image bannière

Mise à jour 2026-10-09 : B11 fait (`valeur`/`unite`/`libelle` exposés par `with()` du Composer `KeyFigure`, Blade en `{{ }}`). B12 fait (`ExpertPerson::experts()` = `match` sur `$this->context->indexable->object_sub_type`). Restants : C13 → D16

## Important files

## Ajouts 2026-10-09 (fin audit blog C13→D16)

- **Audit blog terminé** : B11, B12, C13, C14, C15, D16 faits (remplace la liste « Restants » ci-dessus).
- **Learned pattern — marges de bloc MB Blocks** : pas de SCSS dédié pour une marge fixe ; `supports.spacing.margin => true` + `class="… {!! $blockClass() !!}" style="{!! $blockStyle() !!}"` dans le Blade → réglage dans l'éditeur (ex. `post-listing`, fichier `post-listing.scss` supprimé).
- **Learned pattern — preload image de bannière** : `BlogPost::bannerImageUrl()` (source unique, utilisée par le Composer `Banner`) + `BlogPost::preloadBannerImage()` branché sur `wp_head` prio 1 dans `BlogPostServiceProvider::boot()`. Conditions = templates qui incluent `partials.banner` : `is_home()`, `is_category()`, `is_tax()`, `is_singular(['post', 'expert'])`. Si un template ajoute/retire la bannière, mettre à jour cette condition.
- **Design tokens — marges verticales** : correspondance rem → preset utilisée dans `blog.scss` : 1,5–2rem → `spacing--60`, 3rem → `spacing--70`, 4rem → `spacing--80`.
- **Pint** : preset Laravel par défaut (pas de `pint.json`), accepte `{}` pour les constructeurs vides ; le stub `make:block` produit un alignement des `=>` que Pint corrige (`binary_operator_spaces`).
- **Recurring error — zsh** : `vendor/bin/pint $F` avec une variable multi-chemins échoue (zsh ne découpe pas les mots) → lister les chemins en clair.
- **Règle utilisateur — DI** : propriété privée explicite + constructeur classique (`$this->blogPost = $blogPost;`), pas de promotion `__construct(private X $x) {}` même si Pint l'accepte. Appliqué aux 8 classes qui injectent `BlogPost`.
- **Recurring error — constructeur promu** : en injectant un service dans un Composer/Service/PostType, ne pas écrire `__construct(private X $x) {}` (réflexe PHP 8 / Pint) → toujours déclarer `private X $x;` puis l'assigner dans un constructeur multi-lignes.
- **Convention providers** : `use Illuminate\Support\ServiceProvider;` + `extends ServiceProvider`, sans alias `as LaravelServiceProvider` (harmonisé sur tous les providers le 2026-10-09). Ne pas recopier d'alias en dupliquant un provider existant.
- **Bannière (`partials/banner`)** : plus d'imitation de `core/cover`. Markup BEM `.page-banner` / `__image` / `__inner` / `__title` + `sections/banner.scss`, zéro style inline. Image via `wp_get_attachment_image($bannerImageId, 'very-large', …, sizes 100vw, eager, fetchpriority high)` ; le Composer `Banner` expose `bannerImageId` (service `BlogPost::bannerImageId()`). Preload avec `imagesrcset`/`imagesizes="100vw"` pour éviter un double téléchargement.
- **Learned pattern — parallaxe sans `background-image`** : `<img>` en `position: fixed; inset: 0; height: 100%; object-fit: cover` + parent `clip-path: inset(0); isolation: isolate` = équivalent de `background-attachment: fixed`. Repli `position: absolute` sous `@supports (-webkit-touch-callout: inherit)` (iOS) et `prefers-reduced-motion`. Voile = `::after` (pas de span).
- **Recurring error — enfant absolu dans `.main > .alignfull`** : `.main > .alignfull > *` force `grid-column: content` → un enfant `absolute` ne couvre que la colonne content. Remettre `grid-column: initial` (sélecteur ≥ 0,2,0, ex. `.page-banner > &`).
- **Hero-banner (cover.js)** : la largeur max du contenu = largeur `content` de `.main` − 605px (≥1200px). Un `padding-right` sur un conteneur grille réduit la colonne `content` → le retirer pour que `calc(100% - 605px)` donne la même valeur.
- **SCSS par section** : `sections/blog.scss` (archives, cartes, pagination, single post), `sections/expert.scss` (single expert), `sections/banner.scss` (bannière). Le rythme vertical partagé `.single-post__content, .expert__content` reste dans `blog.scss`.
- **Temps de lecture = Yoast** : `BlogPost::readingTime()` lit `WPSEO_Meta::get_value('estimated-reading-time-minutes', $id)` (meta posée par Yoast à l'enregistrement dans l'éditeur). Ne pas passer par `YoastSEO()->meta->for_post()` : basé sur les indexables, absents hors production. Plus de calcul maison (`_reading_time`, `storeReadingTime`, `reading_time_override` supprimés).
- **Expert** : image mise en avant = fond de bannière (`queriedBannerImageId` couvre `is_singular(['post', 'expert'])`) ; photo de profil = meta MetaBox `photo` (`single_image`), utilisée par Composer `Expert`, `SinglePost::experts()` et schéma `ExpertPerson`.
- **Fil d'Ariane taxos blog** : filtre `wpseo_breadcrumb_links` → `StructuredData::taxonomyBreadcrumbLinks()` : sur `is_tax(['type_contenu', 'secteur_activite'])`, insère « Blog » (si absent, compare les URL) + nom de la taxonomie (`pll__($taxonomy->labels->name)`, chaînes enregistrées) avant le 1er crumb du terme. Crumb taxonomie sans URL → `'url' => ''` (clé obligatoire sinon Yoast juge le schéma cassé) + `'hide_in_schema' => true` (Google exige `item` sur les éléments intermédiaires).
- **Recurring error — `array_column` + index** : `array_column()` saute les éléments sans la clé et réindexe → l'index trouvé ne correspond plus au tableau source. Boucler avec `foreach` pour trouver l'index réel.
- **Schéma blog = JSON-LD maison (plus via Yoast)** : `StructuredData::printBlogSchema()` sur `wp_head` → articles : `@graph` BlogPosting (`#article`, author = `@id` des experts) + Person par expert ; experts : Person seul (`…/expert/x/#person`). Pas de `publisher` (Organization sortie par SASWP sur tout le site en prod). Filtres `wpseo_schema_*` et `Services/Schema/ExpertPerson` supprimés. Raison : en prod SASWP coupe le schéma Yoast (`wpseo_json_ld_output` → false) et doit rester actif.
- **Écart local/prod schéma** : en local (sans SASWP) Yoast sort aussi son `Article` + `Person` (auteur WP) → doublon visible uniquement en local, normal.
