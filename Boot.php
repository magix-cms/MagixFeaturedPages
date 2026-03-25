<?php
declare(strict_types=1);

namespace Plugins\MagixFeaturedPages;

use App\Component\Hook\HookManager;

class Boot
{
    public function register(): void
    {
        HookManager::register(
            'displayHomeBottom',
            'MagixFeaturedPages',
            [\Plugins\MagixFeaturedPages\src\FrontendController::class, 'renderWidget']
        );
    }
}