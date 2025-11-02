CREATE TABLE IF NOT EXISTS PREFIX_iqit_product_flag (
    id_iqit_product_flag INT AUTO_INCREMENT NOT NULL,
    position INT NOT NULL DEFAULT 0,
    hook INT NOT NULL DEFAULT 0,
    config TEXT DEFAULT NULL,
    from_date DATETIME DEFAULT NULL,
    to_date DATETIME DEFAULT NULL,
    enable TINYINT(1) NOT NULL,
    PRIMARY KEY (id_iqit_product_flag)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS PREFIX_iqit_product_flag_shop (
    id_iqit_product_flag INT NOT NULL,
    id_shop INT NOT NULL,
    PRIMARY KEY (id_iqit_product_flag, id_shop),
    INDEX IDX_FLAG_SHOP_FLAG (id_iqit_product_flag),
    INDEX IDX_FLAG_SHOP_SHOP (id_shop),
    FOREIGN KEY (id_iqit_product_flag) 
        REFERENCES PREFIX_iqit_product_flag (id_iqit_product_flag) 
        ON DELETE CASCADE,
    FOREIGN KEY (id_shop) 
        REFERENCES PREFIX_shop (id_shop) 
        ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS PREFIX_iqit_product_flag_lang (
    id_iqit_product_flag INT NOT NULL,
    id_lang INT NOT NULL,
    title VARCHAR(255) DEFAULT NULL,
    description LONGTEXT DEFAULT NULL,
    link VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (id_iqit_product_flag, id_lang),
    FOREIGN KEY (id_iqit_product_flag) 
        REFERENCES PREFIX_iqit_product_flag (id_iqit_product_flag) 
        ON DELETE CASCADE,
    FOREIGN KEY (id_lang) 
        REFERENCES PREFIX_lang (id_lang) 
        ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS PREFIX_iqit_product_flag_category (
    id_iqit_product_flag INT NOT NULL,
    id_category INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (id_iqit_product_flag, id_category),
    FOREIGN KEY (id_iqit_product_flag) 
        REFERENCES PREFIX_iqit_product_flag (id_iqit_product_flag) 
        ON DELETE CASCADE,
    FOREIGN KEY (id_category) 
        REFERENCES PREFIX_category (id_category) 
        ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
