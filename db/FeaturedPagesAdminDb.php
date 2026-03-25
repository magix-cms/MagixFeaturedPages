<?php
declare(strict_types=1);

namespace Plugins\MagixFeaturedPages\db;

use App\Backend\Db\BaseDb;
use Magepattern\Component\Database\QueryBuilder;

class FeaturedPagesAdminDb extends BaseDb
{
    /**
     * Recherche AJAX : Ne ramène que 10 résultats max correspondant à la frappe
     */
    public function searchActivePages(string $term, int $idLang): array
    {
        $qb = new QueryBuilder();
        $qb->select([
            'p.id_pages', 'c.name_pages'
        ])
            ->from('mc_cms_page', 'p')
            ->join('mc_cms_page_content', 'c', 'p.id_pages = c.id_pages')
            ->where('c.id_lang = :lang', ['lang' => $idLang])
            ->where('c.published_pages = 1')
            ->where('c.name_pages LIKE :term', ['term' => '%' . $term . '%'])
            ->limit(10); // Sécurité anti-surcharge

        return $this->executeAll($qb) ?: [];
    }

    /**
     * Récupère TOUTES les infos des pages DEJA sélectionnées, triées par position
     */
    public function getSelectedPagesFull(int $idLang): array
    {
        $qb = new QueryBuilder();
        $qb->select([
            'p.id_pages', 'c.name_pages', 'c.url_pages', 'feat.position'
        ])
            ->from('mc_plug_featured_pages', 'feat')
            ->join('mc_cms_page', 'p', 'feat.id_pages = p.id_pages')
            ->join('mc_cms_page_content', 'c', 'p.id_pages = c.id_pages AND c.id_lang = ' . $idLang)
            ->where('c.published_pages = 1') // Optionnel, mais sécurise si la page a été désactivée entre temps
            ->orderBy('feat.position', 'ASC');

        return $this->executeAll($qb) ?: [];
    }

    /**
     * Sauvegarde l'ordre et la sélection des pages
     */
    public function saveFeaturedPages(array $pageIds): bool
    {
        $this->executeRawSql('TRUNCATE TABLE mc_plug_featured_pages');
        if (empty($pageIds)) return true;

        foreach ($pageIds as $index => $id) {
            $qb = new QueryBuilder();
            $qb->insert('mc_plug_featured_pages', [
                'id_pages' => (int)$id,
                'position' => $index // L'ordre du tableau $_POST détermine la position
            ]);
            $this->executeInsert($qb);
        }
        return true;
    }
}