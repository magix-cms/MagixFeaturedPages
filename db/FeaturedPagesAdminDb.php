<?php
declare(strict_types=1);

namespace Plugins\MagixFeaturedPages\db;

use App\Backend\Db\BaseDb;
use Magepattern\Component\Database\QueryBuilder;

class FeaturedPagesAdminDb extends BaseDb
{
    public function searchActivePages(string $term, int $idLang): array
    {
        $qb = new QueryBuilder();
        $qb->select(['p.id_pages', 'c.name_pages'])
            ->from('mc_cms_page', 'p')
            ->join('mc_cms_page_content', 'c', 'p.id_pages = c.id_pages')
            ->where('c.id_lang = :lang', ['lang' => $idLang])
            ->where('c.published_pages = 1')
            ->where('c.name_pages LIKE :term', ['term' => '%' . $term . '%'])
            ->limit(10);

        return $this->executeAll($qb) ?: [];
    }

    /**
     * Récupère les pages groupées par instance_slug
     */
    public function getSelectedPagesFull(int $idLang): array
    {
        $qb = new QueryBuilder();
        $qb->select(['p.id_pages', 'c.name_pages', 'c.url_pages', 'feat.position', 'feat.instance_slug'])
            ->from('mc_plug_featured_pages', 'feat')
            ->join('mc_cms_page', 'p', 'feat.id_pages = p.id_pages')
            ->join('mc_cms_page_content', 'c', 'p.id_pages = c.id_pages AND c.id_lang = ' . $idLang)
            ->where('c.published_pages = 1')
            ->orderBy('feat.position', 'ASC');

        $results = $this->executeAll($qb) ?: [];

        $grouped = [];
        foreach ($results as $row) {
            $grouped[$row['instance_slug']][] = $row;
        }
        return $grouped;
    }

    /**
     * Sauvegarde massive par instance
     * @param array $data Structure: ['slug_1' => [id, id], 'slug_2' => [id]]
     */
    public function saveAllInstances(array $data): bool
    {
        $this->executeRawSql('TRUNCATE TABLE mc_plug_featured_pages');

        foreach ($data as $slug => $ids) {
            if (!is_array($ids)) continue;
            foreach ($ids as $pos => $id) {
                $qb = new QueryBuilder();
                $qb->insert('mc_plug_featured_pages', [
                    'id_pages'      => (int)$id,
                    'instance_slug' => $slug,
                    'position'      => $pos
                ]);
                $this->executeInsert($qb);
            }
        }
        return true;
    }

    /**
     * Récupère la liste des instances déclarées dans le Layout
     */
    public function getRegisteredInstances(): array
    {
        $qb = new QueryBuilder();
        $qb->select('item_slug')
            ->from('mc_hook_item')
            ->where('module_name = "MagixFeaturedPages"');

        $res = $this->executeAll($qb);
        $instances = [];

        if ($res) {
            foreach ($res as $row) {
                $slug = !empty($row['item_slug']) ? $row['item_slug'] : 'default';
                if (!in_array($slug, $instances)) {
                    $instances[] = $slug;
                }
            }
        }
        return empty($instances) ? ['default'] : $instances;
    }
}