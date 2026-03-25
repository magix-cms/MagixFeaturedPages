CREATE TABLE IF NOT EXISTS `mc_plug_featured_pages` (
    `id_pages` int UNSIGNED NOT NULL,
    `position` int UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (`id_pages`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;