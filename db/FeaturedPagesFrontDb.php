<?php
declare(strict_types=1);

namespace Plugins\MagixFeaturedPages\db;

use App\Frontend\Db\BaseDb;
use Magepattern\Component\Database\QueryBuilder;

class FeaturedPagesFrontDb extends BaseDb
{
    /**
     * Récupère les IDs des pages pour une instance spécifique
     */
    public function getFeaturedPageIds(string $instanceSlug = 'default'): array
    {
        $qb = new QueryBuilder();
        $qb->select('id_pages')
            ->from('mc_plug_featured_pages')
            ->where('instance_slug = :slug', ['slug' => $instanceSlug])
            ->orderBy('position', 'ASC');

        $results = $this->executeAll($qb);

        return $results ? array_column($results, 'id_pages') : [];
    }
}