<?php
declare(strict_types=1);

namespace Plugins\MagixFeaturedPages\src;

use App\Backend\Controller\BaseController;
use Plugins\MagixFeaturedPages\db\FeaturedPagesAdminDb;
use Magepattern\Component\HTTP\Request;

class BackendController extends BaseController
{
    public function run(): void
    {
        $db = new FeaturedPagesAdminDb();
        $idLang = (int)($this->defaultLang['id_lang'] ?? 1);

        // 🟢 INTERCEPTION DE LA RECHERCHE AJAX
        if (isset($_GET['action']) && $_GET['action'] === 'search') {
            $term = $_GET['q'] ?? '';
            if (strlen($term) > 1) {
                $results = $db->searchActivePages($term, $idLang);
                echo json_encode($results);
            } else {
                echo json_encode([]);
            }
            exit;
        }

        // 🟢 SAUVEGARDE DU FORMULAIRE
        if (Request::isMethod('POST')) {
            $token = $_POST['hashtoken'] ?? '';
            if (!$this->session->validateToken($token)) {
                $this->jsonResponse(false, 'Session expirée.');
            }

            $selectedIds = $_POST['featured_pages'] ?? [];
            if ($db->saveFeaturedPages($selectedIds)) {
                $this->jsonResponse(true, 'Pages mises en avant sauvegardées avec succès.');
            } else {
                $this->jsonResponse(false, 'Erreur de sauvegarde.');
            }
            return;
        }

        // 🟢 AFFICHAGE DE LA PAGE
        $this->view->assign([
            'selected_pages' => $db->getSelectedPagesFull($idLang),
            'hashtoken'      => $this->session->getToken()
        ]);

        $this->view->display('index.tpl');
    }
}