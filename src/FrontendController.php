<?php
declare(strict_types=1);

namespace Plugins\MagixFeaturedPages\src;

use Plugins\MagixFeaturedPages\db\FeaturedPagesFrontDb;
use App\Frontend\Db\PagesDb;
use App\Frontend\Model\PagesPresenter;
use Magepattern\Component\Tool\SmartyTool;

class FrontendController
{
    public static function renderWidget(array $params = []): string
    {
        // 1. Récupération du contexte depuis les paramètres du Hook Smarty
        $currentLang = $params['current_lang'] ?? ['id_lang' => 1, 'iso_lang' => 'fr'];
        $idLang      = (int)$currentLang['id_lang'];
        $siteUrl     = $params['site_url'] ?? 'http://localhost';

        // On récupère les infos de l'entreprise et le thème pour le Presenter
        $companyInfo = $params['companyData'] ?? [];
        $skinFolder  = $params['mc_settings']['theme']['value'] ?? 'default';

        // 2. Le plugin récupère sa propre liste d'IDs
        $featuredDb = new FeaturedPagesFrontDb();
        $pageIds = $featuredDb->getFeaturedPageIds();

        if (empty($pageIds)) {
            return ''; // Aucune page configurée, on n'affiche rien
        }

        // 3. Délégation au cœur du CMS !
        $pagesDb = new PagesDb();
        $rawPages = $pagesDb->getPagesByIds($pageIds, $idLang);

        if (empty($rawPages)) {
            return '';
        }

        // 4. Formatage complet via le Presenter (qui génère les URL, les WebP, le JSON-LD...)
        $formattedPages = [];
        foreach ($rawPages as $row) {
            // On respecte les 5 arguments requis par votre PagesPresenter
            $formatted = PagesPresenter::format($row, $currentLang, $siteUrl, $companyInfo, $skinFolder);
            if ($formatted) {
                $formattedPages[] = $formatted;
            }
        }

        // 5. Envoi à Smarty
        $view = SmartyTool::getInstance('front');
        $view->assign('featured_pages', $formattedPages);

        return $view->fetch(ROOT_DIR . 'plugins/MagixFeaturedPages/views/front/widget.tpl');
    }
}