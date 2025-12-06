<?php

declare(strict_types=1);

namespace PrestaShop\Module\TbiroPayment\Form;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;

/**
 * Configuration is used to save data to configuration table and retrieve from it.
 */
final class TbiroPaymentConfigurationDataConfiguration implements DataConfigurationInterface
{
    public const CREDITTBIRO_STATUS = 'CREDITTBIRO_STATUS';
    public const CREDITTBIRO_SHOW_STATUS = 'CREDITTBIRO_SHOW_STATUS';
    public const CREDITTBIRO_UNICID = 'CREDITTBIRO_UNICID';
    public const CREDITTBIRO_STORE_ID = 'CREDITTBIRO_STORE_ID';
    public const CREDITTBIRO_USERNAME = 'CREDITTBIRO_USERNAME';
    public const CREDITTBIRO_PASSWORD = 'CREDITTBIRO_PASSWORD';
    public const CREDITTBIRO_FIRSTLABEL = 'CREDITTBIRO_FIRSTLABEL';
    public const CREDITTBIRO_SECONDLABEL = 'CREDITTBIRO_SECONDLABEL';
    public const CREDITIRISRO_FIRSTLABEL = 'CREDITIRISRO_FIRSTLABEL';
    public const CREDITIRISRO_SECONDLABEL = 'CREDITIRISRO_SECONDLABEL';
    public const CREDITTBIRO_IRIS_IBAN = 'CREDITTBIRO_IRIS_IBAN';
    public const CREDITTBIRO_IRIS_KEY = 'CREDITTBIRO_IRIS_KEY';

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getConfiguration(): array
    {
        $return = [];

        $return['credittbiro_status'] = $this->configuration->get(static::CREDITTBIRO_STATUS);
        $return['credittbiro_show_status'] = $this->configuration->get(static::CREDITTBIRO_SHOW_STATUS);
        $return['credittbiro_unicid'] = $this->configuration->get(static::CREDITTBIRO_UNICID);
        $return['credittbiro_store_id'] = $this->configuration->get(static::CREDITTBIRO_STORE_ID);
        $return['credittbiro_username'] = $this->configuration->get(static::CREDITTBIRO_USERNAME);
        $return['credittbiro_password'] = $this->configuration->get(static::CREDITTBIRO_PASSWORD);
        $return['credittbiro_firstlabel'] = $this->configuration->get(static::CREDITTBIRO_FIRSTLABEL);
        $return['credittbiro_secondlabel'] = $this->configuration->get(static::CREDITTBIRO_SECONDLABEL);
        $return['creditirisro_firstlabel'] = $this->configuration->get(static::CREDITIRISRO_FIRSTLABEL);
        $return['creditirisro_secondlabel'] = $this->configuration->get(static::CREDITIRISRO_SECONDLABEL);
        $return['credittbiro_iris_iban'] = $this->configuration->get(static::CREDITTBIRO_IRIS_IBAN);
        $return['credittbiro_iris_key'] = $this->configuration->get(static::CREDITTBIRO_IRIS_KEY);
        return $return;
    }

    public function updateConfiguration(array $configuration): array
    {
        $errors = [];

        if ($this->validateConfiguration($configuration)) {
            $this->configuration->set(static::CREDITTBIRO_STATUS, $configuration['credittbiro_status']);
            $this->configuration->set(static::CREDITTBIRO_SHOW_STATUS, $configuration['credittbiro_show_status']);
            $this->configuration->set(static::CREDITTBIRO_UNICID, $configuration['credittbiro_unicid']);
            $this->configuration->set(static::CREDITTBIRO_STORE_ID, $configuration['credittbiro_store_id']);
            $this->configuration->set(static::CREDITTBIRO_USERNAME, $configuration['credittbiro_username']);
            $this->configuration->set(static::CREDITTBIRO_PASSWORD, $configuration['credittbiro_password']);
            $this->configuration->set(static::CREDITTBIRO_FIRSTLABEL, $configuration['credittbiro_firstlabel']);
            $this->configuration->set(static::CREDITTBIRO_SECONDLABEL, $configuration['credittbiro_secondlabel']);
            $this->configuration->set(static::CREDITIRISRO_FIRSTLABEL, $configuration['creditirisro_firstlabel']);
            $this->configuration->set(static::CREDITIRISRO_SECONDLABEL, $configuration['creditirisro_secondlabel']);
            $this->configuration->set(static::CREDITTBIRO_IRIS_IBAN, $configuration['credittbiro_iris_iban']);
            $this->configuration->set(static::CREDITTBIRO_IRIS_KEY, $configuration['credittbiro_iris_key']);
        }

        /* Errors are returned here. */
        return $errors;
    }

    /**
     * Ensure the parameters passed are valid.
     *
     * @return bool Returns true if no exception are thrown
     */
    public function validateConfiguration(array $configuration): bool
    {
        return
            isset($configuration['credittbiro_status']) &&
            isset($configuration['credittbiro_show_status']) &&
            isset($configuration['credittbiro_unicid']) &&
            isset($configuration['credittbiro_store_id']) &&
            isset($configuration['credittbiro_username']) &&
            isset($configuration['credittbiro_password']) &&
            isset($configuration['credittbiro_firstlabel']) &&
            isset($configuration['credittbiro_secondlabel']);
    }
}
