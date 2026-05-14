<?php
declare(strict_types=1);

namespace Plugins\MagixFeaturedPages\db;

use App\Frontend\Db\BaseDb;
use Magepattern\Component\Database\QueryBuilder;

class FeaturedPagesFrontDb extends BaseDb
{
    /**
     * Récupère les IDs des pages pour une instance spécifique (Avec Cache SQL)
     */
    public function getFeaturedPageIds(string $instanceSlug = 'default'): array
    {
        // 1. Instanciation du gestionnaire de cache SQL
        $cache = $this->getSqlCache();
        $qb = new QueryBuilder();

        $qb->select('id_pages')
            ->from('mc_plug_featured_pages')
            ->where('instance_slug = :slug', ['slug' => $instanceSlug])
            ->orderBy('position', 'ASC');

        // 2. Génération de la clé de cache avec le Tag unique du plugin
        $cacheKey = $cache->generateKey($qb->getSql(), $qb->getParams(), 'magixfeaturedpages');

        // 3. Vérification : Les données sont-elles déjà en cache ?
        $data = $cache->get($cacheKey);
        if ($data !== null) {
            return $data; // On retourne directement le tableau d'IDs
        }

        // 4. Si le cache est vide, on interroge la base de données
        $results = $this->executeAll($qb);

        // Formatage en tableau simple d'IDs
        $formattedIds = $results ? array_column($results, 'id_pages') : [];

        // 5. On met le résultat final en cache pour 24 heures (86400 secondes)
        // Note: On met en cache le tableau formaté, ce qui économise le array_column à chaque appel
        $cache->set($cacheKey, $formattedIds, 86400);

        return $formattedIds;
    }
}