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

        if (isset($_GET['action']) && $_GET['action'] === 'search') {
            $term = $_GET['q'] ?? '';
            echo json_encode($db->searchActivePages($term, $idLang));
            exit;
        }

        if (Request::isMethod('POST')) {
            if (!$this->session->validateToken($_POST['hashtoken'] ?? '')) {
                $this->jsonResponse(false, 'Session expirée.');
            }

            // On reçoit un tableau : featured_pages[instance_slug][]
            $data = $_POST['featured_pages'] ?? [];
            if ($db->saveAllInstances($data)) {
                $this->jsonResponse(true, 'Configuration sauvegardée.');
            }
            return;
        }

        $this->view->assign([
            'instances'      => $db->getRegisteredInstances(),
            'selected_pages' => $db->getSelectedPagesFull($idLang),
            'hashtoken'      => $this->session->getToken()
        ]);

        $this->view->display('index.tpl');
    }
}