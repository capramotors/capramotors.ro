<?php

declare(strict_types=1);

namespace PrestaShop\Module\TbiroPayment\Form;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Provider is responsible for providing form data, in this case, it is returned from the configuration component.
 *
 * Class TbiroPaymentConfigurationFormDataProvider
 */
class TbiroPaymentConfigurationFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $tbiroPaymentConfigurationDataConfiguration;

    public function __construct(DataConfigurationInterface $tbiroPaymentConfigurationDataConfiguration)
    {
        $this->tbiroPaymentConfigurationDataConfiguration = $tbiroPaymentConfigurationDataConfiguration;
    }

    public function getData(): array
    {
        return $this->tbiroPaymentConfigurationDataConfiguration->getConfiguration();
    }

    public function setData(array $data): array
    {
        return $this->tbiroPaymentConfigurationDataConfiguration->updateConfiguration($data);
    }
}
