{extends file="layout.tpl"}

{block name='head:title'}Pages mises en avant{/block}

{block name="stylesheets" append nocache}
    <link href="{$site_url}/{$baseadmin}/templates/css/tom-select.bootstrap5.min.css" rel="stylesheet">
{/block}

{block name='article'}
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h1 class="h2 mb-0"><i class="bi bi-file-earmark-star text-warning me-2"></i>Pages mises en avant</h1>
    </div>

    <form method="post" action="index.php?controller=MagixFeaturedPages" class="validate_form">
        <input type="hidden" name="hashtoken" value="{$hashtoken}">

        {* --- 1. LES ONGLETS (TABS) POUR CHAQUE INSTANCE --- *}
        <ul class="nav nav-tabs" id="featuredTabs" role="tablist">
            {foreach $instances as $index => $slug}
                <li class="nav-item" role="presentation">
                    <button class="nav-link {if $index == 0}active fw-bold{/if}"
                            id="tab-{$slug}-btn"
                            data-bs-toggle="tab"
                            data-bs-target="#tab-{$slug}"
                            type="button" role="tab">
                        {if $slug == 'default'}
                            <i class="bi bi-box me-1"></i> Bloc Principal
                        {else}
                            <i class="bi bi-diagram-3 me-1"></i> Bloc : <span class="text-primary">{$slug}</span>
                        {/if}
                    </button>
                </li>
            {/foreach}
        </ul>

        {* --- 2. LE CONTENU DES ONGLETS --- *}
        <div class="tab-content bg-white p-4 border border-top-0 rounded-bottom shadow-sm mb-4" id="featuredTabsContent">

            {foreach $instances as $index => $slug}
                <div class="tab-pane fade {if $index == 0}show active{/if}" id="tab-{$slug}" role="tabpanel">
                    <div class="row">

                        {* COLONNE RECHERCHE (TomSelect) *}
                        <div class="col-md-5 mb-4 mb-md-0">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-muted mb-3"><i class="bi bi-search me-2"></i>Ajouter à ce bloc</h6>
                                    {* Le select qui sera transformé par TomSelect *}
                                    <select class="tom-select-pages" data-slug="{$slug}" placeholder="Rechercher une page..."></select>
                                    <div class="form-text small mt-2">Tapez au moins 2 lettres pour lancer la recherche.</div>
                                </div>
                            </div>
                        </div>

                        {* COLONNE RÉSULTATS (Sortable) *}
                        <div class="col-md-7">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 fw-bold"><i class="bi bi-list-ol me-2"></i>Pages sélectionnées</h6>
                                <span class="badge bg-secondary rounded-pill" id="count-{$slug}">{if isset($selected_pages[$slug])}{$selected_pages[$slug]|count}{else}0{/if}</span>
                            </div>

                            <div class="alert alert-info py-2 small mb-3">
                                <i class="bi bi-arrows-move me-1"></i> Glissez-déposez les lignes pour modifier l'ordre.
                            </div>

                            <ul class="list-group sortable-list" id="list-{$slug}">
                                {if isset($selected_pages[$slug])}
                                    {foreach $selected_pages[$slug] as $p}
                                        <li class="list-group-item d-flex justify-content-between align-items-center bg-white border-bottom cursor-move">
                                            {* 🟢 LA CORRECTION EST ICI : featured_pages[$slug][] *}
                                            <input type="hidden" name="featured_pages[{$slug}][]" value="{$p.id_pages}">

                                            <div class="d-flex align-items-center w-100">
                                                <i class="bi bi-grip-vertical text-muted me-3 fs-5 drag-handle" style="cursor: move;"></i>
                                                <div>
                                                    <strong class="d-block text-dark">{$p.name_pages}</strong>
                                                    <small class="text-muted">{$p.url_pages|default:'/'}</small>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove ms-2" title="Retirer" onclick="removeFeaturedPage(this, '{$slug}')">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </li>
                                    {/foreach}
                                {/if}
                            </ul>
                        </div>
                    </div>
                </div>
            {/foreach}

        </div>

        <button type="submit" class="btn btn-primary btn-lg shadow-sm">
            <i class="bi bi-save me-2"></i> Enregistrer les blocs
        </button>
    </form>
{/block}

{block name="javascripts" append}
    <script src="{$site_url}/{$baseadmin}/templates/js/vendor/tom-select.complete.min.js"></script>

    <script>
        {literal}
        document.addEventListener('DOMContentLoaded', function() {

            // 1. Initialisation de TomSelect pour chaque bloc
            document.querySelectorAll('.tom-select-pages').forEach(function(selectEl) {
                const slug = selectEl.getAttribute('data-slug');

                new TomSelect(selectEl, {
                    valueField: 'id_pages',
                    labelField: 'name_pages',
                    searchField: 'name_pages',
                    placeholder: "Rechercher par nom...",
                    load: function(query, callback) {
                        if (!query.length) return callback();

                        // Appel AJAX vers le BackendController du plugin
                        fetch('index.php?controller=MagixFeaturedPages&action=search&q=' + encodeURIComponent(query))
                            .then(response => response.json())
                            .then(json => {
                                callback(json);
                            }).catch(() => {
                            callback();
                        });
                    },
                    render: {
                        option: function(item, escape) {
                            return `<div><strong class="text-dark">${escape(item.name_pages)}</strong></div>`;
                        },
                        item: function(item, escape) {
                            return `<div>${escape(item.name_pages)}</div>`;
                        }
                    },
                    onChange: function(value) {
                        if (!value) return;
                        const item = this.options[value];
                        if (item) {
                            // On ajoute la ligne dans le bon onglet
                            addPageToList(slug, item.id_pages, item.name_pages);
                            // On vide le champ de recherche
                            this.clear();
                        }
                    }
                });
            });

            // 2. Initialisation du Drag & Drop (SortableJS)
            document.querySelectorAll('.sortable-list').forEach(function(listEl) {
                if (typeof Sortable !== 'undefined') {
                    new Sortable(listEl, {
                        animation: 150,
                        handle: '.drag-handle',
                        ghostClass: 'bg-light'
                    });
                }
            });
        });

        // Fonction pour ajouter un élément visuel à la liste et mettre à jour le compteur
        function addPageToList(slug, id, name) {
            const ul = document.getElementById('list-' + slug);

            // Sécurité : vérifier que la page n'est pas déjà dans ce bloc précis
            if (ul.querySelector(`input[value="${id}"]`)) {
                if (typeof MagixToast !== 'undefined') {
                    MagixToast.warning("Cette page est déjà dans ce bloc.");
                } else {
                    alert("Cette page est déjà dans ce bloc.");
                }
                return;
            }

            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center bg-white border-bottom cursor-move';

            // 🟢 LE CŒUR DU SYSTÈME MULTI-INSTANCES : name="featured_pages[slug][]"
            li.innerHTML = `
                <input type="hidden" name="featured_pages[${slug}][]" value="${id}">
                <div class="d-flex align-items-center w-100">
                    <i class="bi bi-grip-vertical text-muted me-3 fs-5 drag-handle" style="cursor: move;"></i>
                    <div>
                        <strong class="d-block text-dark">${name}</strong>
                        <small class="text-muted text-success">Nouvelle page ajoutée</small>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove ms-2" title="Retirer" onclick="removeFeaturedPage(this, '${slug}')">
                    <i class="bi bi-x-lg"></i>
                </button>
            `;
            ul.appendChild(li);
            updateBadgeCount(slug);
        }

        // Fonction pour retirer une page d'un bloc
        window.removeFeaturedPage = function(btn, slug) {
            btn.closest('li').remove();
            updateBadgeCount(slug);
        }

        function updateBadgeCount(slug) {
            const ul = document.getElementById('list-' + slug);
            const count = ul.querySelectorAll('li').length;
            document.getElementById('count-' + slug).textContent = count;
        }
        {/literal}
    </script>
{/block}