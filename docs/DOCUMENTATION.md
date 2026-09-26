# Documentation — Studio Theme Child

Thème enfant **Atelier Noir** (parent : **Hello Elementor**). Les personnalisations métier vivent ici ; ne modifiez pas le thème parent.

**Dans l’admin WordPress :** *Apparence → Studio · Documentation* affiche une version formatée de ce guide (`docs/documentation-admin.html`).

---

## 1. Structure des dossiers (aperçu)

- `style.css` — en-tête du thème enfant (nom, parent `hello-elementor`).
- `functions.php` — chargement des modules (`inc/`), scripts, styles, support WooCommerce / Elementor.
- `header.php` — en-tête HTML + header Studio (menu, devise, compte).
- `footer.php` — pied de page Studio (navigation, newsletter, paiements).
- `front-page.php` — page d’accueil : secours Personnaliseur ou contenu page / Elementor selon réglages.
- `template-*.php` — modèles de page (Contact, Services, légal…).
- `template-parts/` — bouts de templates réutilisables (header, accueil Personnaliseur…).
- `assets/css/` — feuilles de style (boutique, contact, services, légal…).
- `assets/js/` — scripts (header, animations, boutique…).
- `woocommerce/` — surcharges WooCommerce (cartes produits, e-mails, compte…).
- `inc/` — logique PHP : Personnaliseur, import contenu, devise, newsletter, etc.
- `docs/` — cette documentation.

---

## 2. Modifier l’apparence sans code

### Couleurs, typographies, loader

**Apparence → Personnaliser**

- Rubrique **Atelier Noir — couleurs** : couleur principale, fond, accent.
- **Typographie** : polices **titres** et **texte** (Google Fonts chargées par le thème).
- **Loader** (accueil) : activer/désactiver, textes, couleurs d’intro.

Les variables CSS (`--studio-font-heading`, `--studio-accent`, etc.) sont alimentées par ces réglages.

### Accueil « secours » (sans Elementor sur la page)

Si l’accueil utilise le **Personnaliseur** (sections Eyes catcher, fondateur, journal, boutique) :

- Rubriques **Atelier Noir — accueil** (ou équivalent dans votre liste) : URL **vidéo** hero, **poster**, textes overlay, bloc **Le studio** (titre, texte, image, lien CTA).

### Page Services (4 panneaux plein écran)

Modèle de page : **Atelier Noir — Services**.

- **Apparence → Personnaliser → Atelier Noir — page Services (modèle PHP)**  
  Pour chaque service (1 à 4) : image **médiathèque**, URL image/vidéo externe, titre, texte, vidéo oui/non, URL MP4, slug pour le lien Contact (`?service=…`), libellé du bouton.

Les valeurs par défaut du fichier `template-services.php` s’appliquent si le Personnaliseur n’a rien enregistré.

### Images (général)

- **Médiathèque** : téléversez les fichiers, copiez l’URL ou sélectionnez l’image dans le Personnaliseur / Elementor.
- Remplacez les visuels « stock » par les fichiers fournis par le client lorsque disponibles.

### Produits WooCommerce

**Produits** : nom, description courte/longue, image à la une, galerie, prix, stock — comme dans tout site WooCommerce. Les fiches utilisent les modèles du thème enfant (`woocommerce/content-product.php`, styles dans `assets/css/woocommerce.css`).

---

## 3. Pages : natif PHP vs Elementor

| Contenu | Où l’éditer |
|--------|-------------|
| **Atelier Noir — Services** (4 panneaux) | Personnaliseur (voir §2) |
| **Atelier Noir — Contact** (onglets + envoi e-mail) | Contenu dans `template-contact.php` pour la structure ; textes d’interface éditables côté code ou extensions formulaire |
| **CGV, mentions légales, confidentialité** | Gabarits PHP ; champs juridiques `[À compléter]` à remplir avec les données réelles |
| **Accueil / autres pages** en modèle par défaut | **Elementor** (si la page est construite avec Elementor) |
| **Journal** | Articles : **Articles → Ajouter** ; intro de la page liste si page « Journal » en Elementor |
| **Boutique** | Catalogue WooCommerce + éventuel texte sur la page « Boutique » |

---

## 4. Import « Studio · Import contenu »

**Apparence → Studio · Import contenu** (ou **Réglages → Studio · Import contenu**)

Applique un jeu de réglages Personnaliseur + pages Elementor + articles + produits d’exemple (si WooCommerce est actif). À utiliser surtout pour une **mise en route** ; le contenu doit ensuite être ajusté avec le client (images, prix, mentions légales).

---

## 5. Fichiers utiles si vous codez

- `inc/customizer-site.php` — définition des champs Personnaliseur.
- `inc/woocommerce-shop.php` — boutique (recherche, colonnes).
- `inc/currency.php` — sélecteur de devises.
- `inc/newsletter-popup.php` — popup newsletter (brancher un outil d’e-mailing si besoin).

---

## 6. Support

En cas de mise à jour WordPress / WooCommerce / Elementor, vérifier les surcharges dans `woocommerce/` et tester boutique, panier, checkout et compte client.
