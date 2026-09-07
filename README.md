# ILIST Kado

Plugin WordPress / WooCommerce de gestion de **listes de cadeaux** — naissance,
mariage, anniversaire, Noël… — avec **financement participatif** (cagnotte).

- Contributors: [DEV By INFORMATUX](https://dev.informatux.com)
- Requires at least: 6.0
- Tested up to: 7.1
- Requires PHP: 7.4
- WC requires at least: 8.0
- WC tested up to: 11.1
- License: [GPLv3](http://www.gnu.org/licenses/gpl-3.0.txt)

> Depuis la version 2.2.0, ILIST Kado est **libre et public** : plus aucun
> numéro de licence n'est demandé pour accéder aux fonctionnalités.

## Fonctionnalités

- Création de listes par les clients (depuis leur compte WooCommerce ou en tant
  que visiteur), avec date d'événement, image, description et accès protégé par
  mot de passe.
- Ajout de produits de la boutique à une liste, gestion des variations,
  marquage des favoris, notes d'administration.
- Achat d'un produit d'une liste par un proche, avec message à l'attention du
  propriétaire de la liste.
- **Cagnotte / crowdfunding** : participation libre au financement d'un produit,
  avec montant minimum et pas de participation paramétrables.
- Recherche de liste par e-mail, nom, prénom ou nom de famille, avec
  autocomplétion.
- Partage (réseaux sociaux, e-mail, lien) et impression de la liste.
- E-mails automatiques au propriétaire de la liste et aux administrateurs, avec
  sujets et corps personnalisables par jetons.
- Intégration à l'administration WooCommerce : colonne « Liste » sur la liste
  des commandes, encart et message de l'acheteur sur la fiche commande.
- Statistiques de liste (ventes réalisées) — câblage en place depuis la 2.3.0,
  contenu à définir.
- API REST en lecture (`ilist/v2`).
- Compatible **HPOS** (High-Performance Order Storage) et multisite.

## Installation

1. Copier le dossier `ilist` dans `wp-content/plugins/`.
2. Activer le plugin depuis l'administration WordPress (WooCommerce doit être
   installé et actif).
3. Menu **ILIST Kado** dans l'administration pour la configuration.

Les tables et les options sont créées automatiquement à la première requête
suivant l'activation.

## Shortcodes

| Shortcode | Rôle |
|-----------|------|
| `[ilist]` | Parcours complet : création de liste, connexion, ajout/retrait de produits, achat, participation, partage, impression. Également branché sur l'onglet « Mes listes » du compte client WooCommerce. |
| `[ilist_search]` | Formulaire de recherche d'une liste. |

## Points d'extension

Le plugin expose des filtres et des actions pour adapter son comportement sans
modifier son code — chacun est documenté avec un exemple d'usage dans
`class/ilist.php` :

`ilist_pot_no_min`, `ilist_pot_display_participation`,
`ilist_pot_product_cart_title`, `ilist_pot_separator_text`,
`ilist_pot_not_activated`, `ilist_protected_password_list_text`,
`ilist_before_login`, `ilist_login`, `ilist_after_login`,
`ilist_admin_list_products_after`, `ilist_extra_nav_tabs`,
`ilist_list_extra_row_actions`, `ilist_statistics_content`.

## Changelog

**2.3.0**
- Intégration de l'addon « ILIST Kado - List Statistics » : l'onglet
  **Statistiques de liste**, l'action « Statistiques » du tableau des listes et
  le bouton sous les produits font désormais partie du plugin. Le contenu des
  statistiques reste à définir et se branche sur l'action
  `ilist_statistics_content`.
- Le filtre `ilist_list_extra_row_actions` reçoit désormais la ligne courante
  en second argument, ce qui permet à une action ajoutée de cibler la liste
  concernée. Les callbacks à un seul argument restent compatibles.

**2.2.0**
- Le plugin devient public : retrait complet du système de licence (plus de
  clé à saisir, plus d'écran d'activation, plus d'appel au serveur de
  licences).
- Sécurité : correction de deux injections SQL exploitables sans
  authentification (shortcode `[ilist_search]` et endpoint AJAX
  `ilist_live_search`), et d'une troisième réservée aux comptes authentifiés
  (API REST). Requêtes passées à `$wpdb->prepare()`.
- Sécurité : le journal de débogage `log.txt` n'est plus accessible par une
  URL publique ; il est lu côté serveur depuis l'administration.
- Compatibilité **HPOS** : déclaration de compatibilité, colonne « Liste » et
  metabox rétablies sur le nouvel écran des commandes, métadonnées de commande
  lues et écrites via le CRUD WooCommerce.
- Compatibilité **PHP 8.4** : `strftime()` remplacée, propriétés dynamiques
  déclarées, `get_magic_quotes_gpc()` retirée.
- Performance : `flush_rewrite_rules()` n'est plus appelée à chaque requête.
- Correction des hooks d'activation / désactivation, qui n'étaient jamais
  déclenchés (déclarés depuis un fichier inclus).
- En-tête du plugin : versions WordPress / WooCommerce / PHP cohérentes,
  `Text Domain` et `Domain Path` corrigés.

## Licence

GPL-3.0+ — voir [LICENSE](LICENSE).
