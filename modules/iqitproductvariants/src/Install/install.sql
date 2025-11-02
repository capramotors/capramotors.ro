CREATE TABLE IF NOT EXISTS `_DB_PREFIX_iqitproductvariants_product_variant`
(
    `id_product` INT(11) UNSIGNED NOT NULL UNIQUE,
    `variants`  VARCHAR(255) NOT NULL DEFAULT '',
    `date_add`   DATETIME         NOT NULL,
    `date_upd`   DATETIME         NOT NULL,
    PRIMARY KEY(`id_product`)
) ENGINE = _MYSQL_ENGINE_ DEFAULT CHARSET = utf8;
