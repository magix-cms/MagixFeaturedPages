# MagixFeaturedPages

[![Release](https://img.shields.io/github/release/magix-cms/MagixFeaturedPages.svg)](https://github.com/magix-cms/MagixFeaturedPages/releases/latest)
[![License](https://img.shields.io/github/license/magix-cms/MagixFeaturedPages.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D%208.2-blue.svg)](https://php.net/)
[![Magix CMS](https://img.shields.io/badge/Magix%20CMS-4.x-success.svg)](https://www.magix-cms.com/)

**MagixFeaturedPages** est un plugin officiel pour Magix CMS 4.x permettant de mettre en avant une sélection personnalisée de pages CMS (services, présentation, etc.) directement sur la page d'accueil de votre site.

## 🌟 Fonctionnalités principales

* **Recherche AJAX ultra-rapide** : Ajoutez des pages à votre sélection en les cherchant par leur nom, sans recharger l'interface.
* **Filtre intelligent** : Les pages déjà ajoutées à votre liste n'apparaissent plus dans les résultats de recherche pour éviter les doublons.
* **Drag & Drop fluide** : Modifiez l'ordre d'affichage de vos pages à la une d'un simple glisser-déposer (propulsé par `Sortable.js`).
* **Sauvegarde 100% automatique** : Chaque ajout, suppression ou déplacement est sauvegardé instantanément en arrière-plan via AJAX.
* **Intégration native au thème** : Le widget public réutilise la boucle de pages native (`pages-grid.tpl`) de votre thème. Vos pages phares auront exactement le même design (cartes, effets de survol) que le reste de votre site.
* **SEO Orienté** : Délégation complète au moteur central du CMS. Le plugin profite de la génération automatique du format WebP pour les images et des balises `JSON-LD` (Schema.org) configurées dans le `PagesPresenter`.

## ⚙️ Installation

1. Téléchargez la dernière version du plugin.
2. Décompressez l'archive et placez le dossier `MagixFeaturedPages` dans le répertoire `plugins/` de votre installation Magix CMS.
3. Connectez-vous à l'administration de Magix CMS.
4. Rendez-vous dans **Extensions > Plugins**.
5. Repérez **MagixFeaturedPages** dans la liste et cliquez sur **Installer**.

*Note : Lors de l'installation, le système créera automatiquement la table `mc_plug_featured_pages` dans votre base de données et greffera le widget sur le hook `displayHomeBottom`.*

## 🚀 Utilisation

### Côté Administration
1. Accédez à la configuration du plugin depuis votre panneau de contrôle.
2. Utilisez la barre de recherche à gauche pour trouver une page CMS.
3. Cliquez sur la page dans la liste des résultats pour l'ajouter à votre sélection.
4. Dans la colonne de droite, utilisez l'icône de poignée (⋮⋮) pour réorganiser vos pages par glisser-déposer.
5. Une notification verte `MagixToast` vous confirmera la sauvegarde automatique à chaque action.

### Côté Public (Frontend)
Le plugin s'affiche automatiquement sur votre page d'accueil via le hook défini dans le `manifest.json`. Si aucune page n'est sélectionnée dans l'administration, le widget devient totalement invisible pour ne pas polluer le code source.

## 🛠️ Architecture Technique (Pour les développeurs)

Ce plugin a été conçu en respectant l'architecture stricte de **Magix CMS V4** et le principe **DRY** (Don't Repeat Yourself) :

* **Frontend** : Il ne refait pas de requêtes complexes avec des jointures multiples. Il se contente de récupérer une liste d'IDs et délègue la récupération des datas complètes au cœur du CMS via `PagesDb::getPagesByIds()`.
* **Backend UI** : L'interface d'administration repose sur la classe Javascript mutualisée `MagixItemSelector.js` qui orchestre Fetch API, Sortable.js et MagixToast.
* **Sécurité (Sandboxing)** : Intégration d'un système de `try/catch` global dans le `FrontendController`. Si le plugin rencontre une erreur technique, il n'entraîne pas d'Erreur Fatale PHP et laisse le reste du site public s'afficher sans interruption.

## 📄 Licence

Ce projet est sous licence **GPLv3**. Voir le fichier [LICENSE](LICENSE) pour plus de détails.
Copyright (C) 2008 - 2026 Gerits Aurelien (Magix CMS)
Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier selon les termes de la Licence Publique Générale GNU telle que publiée par la Free Software Foundation ; soit la version 3 de la Licence, ou (à votre discrétion) toute version ultérieure.