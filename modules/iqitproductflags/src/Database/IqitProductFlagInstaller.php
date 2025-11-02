<?php

/**
 * Copyright since 2025 iqit-commerce.com
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Envato Regular License,
 * which is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at the following URL:
 * https://themeforest.net/licenses/terms/regular
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@iqit-commerce.com so we can send you a copy immediately.
 *
 * @author    iqit-commerce.com <support@iqit-commerce.com>
 * @copyright Since 2025 iqit-commerce.com
 * @license   Envato Regular License
 */

declare(strict_types=1);

namespace Iqit\IqitProductFlags\Database;

use Doctrine\DBAL\Connection;

class IqitProductFlagInstaller
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(Connection $connection, string $dbPrefix)
    {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
    }

    /**
     * For now, we cannot use our module's doctrine entities during the installation's process, because our
     * new entities are not recognized by Doctrine yet.
     * This is why we execute an sql query to create our tables.
     *
     * @return bool
     */
    public function createTables(): bool
    {
        $this->dropTables();
        $sqlFile = __DIR__ . '/../../Resources/install.sql';
        $sqlQueries = file_get_contents($sqlFile);

        $sqlQueries = str_replace('PREFIX_', $this->dbPrefix, $sqlQueries);

        $queries = array_filter(array_map('trim', explode(';', $sqlQueries)));

        foreach ($queries as $query) {
            try {
                $this->connection->executeQuery($query);
            } catch (\Exception $e) {
                error_log('SQL Error: ' . $e->getMessage());

                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function dropTables(): bool
    {
        $tableNames = [
            'iqit_product_flag_shop',
            'iqit_product_flag_lang',
            'iqit_product_flag_category',
            'iqit_product_flag',
        ];
        foreach ($tableNames as $tableName) {
            $sql = 'DROP TABLE IF EXISTS ' . $this->dbPrefix . $tableName;
            try {
                $this->connection->executeQuery($sql);
            } catch (\Exception $e) {
                error_log('SQL Error: ' . $e->getMessage());

                return false;
            }
        }

        return true;
    }
}
