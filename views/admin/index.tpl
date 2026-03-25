{extends file="layout.tpl"}

{block name='head:title'}Pages en page d'accueil{/block}

{block name='article'}
    <div class="row">
        {* COLONNE DE GAUCHE : LA RECHERCHE *}
        <div class="col-md-5 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-search"></i> Ajouter une page</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 position-relative">
                        <label class="form-label text-muted small">Rechercher par nom</label>
                        <input type="text" id="ajaxSearchInput" class="form-control" placeholder="Taper au moins 2 caractères..." autocomplete="off">

                        {* Container des résultats AJAX *}
                        <div id="ajaxSearchResults" class="list-group position-absolute w-100 shadow mt-1" style="z-index: 1050; display: none; max-height: 250px; overflow-y: auto;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {* COLONNE DE DROITE : LES SÉLECTIONNÉS *}
        <div class="col-md-7 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-star-fill text-warning"></i> Pages en page d'accueil</h5>
                    <span class="badge bg-primary" id="countSelected">{$selected_pages|count}</span>
                </div>
                <div class="card-body">
                    <form method="post" action="index.php?controller=MagixFeaturedPages" class="validate_form">
                        <input type="hidden" name="hashtoken" value="{$hashtoken}">

                        <div class="alert alert-info py-2 small">
                            <i class="bi bi-info-circle me-1"></i> Utilisez les flèches pour modifier l'ordre d'affichage.
                        </div>

                        {* La liste des pages choisies (Draggable) *}
                        <ul class="list-group mb-4" id="selectedPagesList">
                            {foreach $selected_pages as $p}
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-light border-bottom cursor-move" draggable="true" data-id="{$p.id_pages}">
                                    <input type="hidden" name="featured_pages[]" value="{$p.id_pages}">
                                    <div class="d-flex align-items-center w-100">
                                        <i class="bi bi-grip-vertical text-muted me-3 fs-5"></i>
                                        <div>
                                            <strong class="d-block text-dark">{$p.name_pages}</strong>
                                            <small class="text-muted">{$p.url_pages|default:'/'}</small>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove ms-2" title="Retirer"><i class="bi bi-x-lg"></i></button>
                                </li>
                            {/foreach}
                        </ul>

                        <div id="saveIndicator" class="text-success text-center fw-bold" style="display: none;">
                            <i class="bi bi-check-circle"></i> Sauvegardé automatiquement
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
{/block}

{block name="javascripts" append}
    {* Assurez-vous que sortable.js est chargé dans votre layout global *}
    <script src="templates/js/MagixItemSelector.min.js?v={$smarty.now}"></script>
    <script>
        {literal}
        document.addEventListener('DOMContentLoaded', function() {
            const token = document.querySelector('input[name="hashtoken"]').value;

            new MagixItemSelector({
                searchInputId: 'ajaxSearchInput',
                searchResultsId: 'ajaxSearchResults',
                selectedListId: 'selectedPagesList',
                countBadgeId: 'countSelected',
                searchUrl: 'index.php?controller=MagixFeaturedPages&action=search&q=',
                saveUrl: 'index.php?controller=MagixFeaturedPages',
                inputName: 'featured_pages[]',
                token: token,

                // Comment afficher un résultat de recherche
                renderResultItem: (item) => `<strong>${item.name_pages}</strong>`,

                // Comment afficher l'élément une fois ajouté dans la liste (avec la poignée Sortable)
                renderAddedItem: (item) => `
                    <div class="d-flex align-items-center w-100">
                        <i class="bi bi-grip-vertical text-muted me-3 fs-5 cursor-move"></i>
                        <div>
                            <strong class="d-block text-dark">${item.name_pages}</strong>
                            <small class="text-muted">Nouvelle page ajoutée</small>
                        </div>
                    </div>
                `
            });
        });
        {/literal}
    </script>
{/block}