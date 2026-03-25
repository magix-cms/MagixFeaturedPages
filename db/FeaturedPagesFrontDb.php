<?php
declare(strict_types=1);

namespace Plugins\MagixFeaturedPages\db;

use App\Frontend\Db\BaseDb;
use Magepattern\Component\Database\QueryBuilder;

class FeaturedPagesFrontDb extends BaseDb
{
    /**
     * Récupère uniquement le tableau des IDs des pages mises en avant, trié par position
     */
    public function getFeaturedPageIds(): array
    {
        $qb = new QueryBuilder();
        $qb->select('id_pages')
            ->from('mc_plug_featured_pages')
            ->orderBy('position', 'ASC');

        $results = $this->executeAll($qb);

        if (empty($results)) {
            return [];
        }

        // On transforme le tableau associatif en un simple tableau d'IDs plats: [1, 5, 12]
        return array_column($results, 'id_pages');
    }
}