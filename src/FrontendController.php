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
        // 🟢 1. SÉCURITÉ / AIGUILLAGE
        $hookName = $params['name'] ?? '';
        if (!str_starts_with($hookName, 'displayHome')) {
            return '';
        }

        // 🟢 2. TRAITEMENT NORMAL
        $currentLang = $params['current_lang'] ?? ['id_lang' => 1, 'iso_lang' => 'fr'];
        $idLang      = (int)$currentLang['id_lang'];
        $siteUrl     = $params['site_url'] ?? 'http://localhost';
        $companyInfo = $params['companyData'] ?? [];
        $skinFolder  = $params['mc_settings']['theme']['value'] ?? 'default';

        $featuredDb = new FeaturedPagesFrontDb();
        $pageIds = $featuredDb->getFeaturedPageIds();

        if (empty($pageIds)) return '';

        $pagesDb = new PagesDb();
        $rawPages = $pagesDb->getPagesByIds($pageIds, $idLang);

        if (empty($rawPages)) return '';

        $formattedPages = [];
        foreach ($rawPages as $row) {
            $formatted = PagesPresenter::format($row, $currentLang, $siteUrl, $companyInfo, $skinFolder);
            if ($formatted) {
                $formattedPages[] = $formatted;
            }
        }

        $view = SmartyTool::getInstance('front');
        $view->assign('featured_pages', $formattedPages);

        return $view->fetch(ROOT_DIR . 'plugins/MagixFeaturedPages/views/front/widget.tpl');
    }
}