CREATE TABLE IF NOT EXISTS `mc_plug_featured_pages` (
    `id_pages` int UNSIGNED NOT NULL,
    `instance_slug` varchar(64) NOT NULL DEFAULT 'default',
    `position` int UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (`id_pages`, `instance_slug`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;