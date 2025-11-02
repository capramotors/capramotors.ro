<?php

/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */
declare(strict_types=1);

namespace Iqit\IqitProductFlags\Presenter;

use Iqit\IqitProductFlags\Entity\IqitProductFlag;

class FlagPresenter
{
    /**
     * Przekształca obiekt Doctrine w tablicę.
     *
     * @param IqitProductFlag $flag
     *
     * @return array
     */
    public function present(IqitProductFlag $flag): array
    {
        $config = $flag->getConfig();
        $styleMap = [
            'txt_color' => 'color',
            'bg_color' => 'background',
        ];
        $fromDate = $flag->getFromDate();
        $toDate = $flag->getToDate();

        $style = '';
        foreach ($config as $key => $value) {
            if (isset($styleMap[$key])) {
                $style .= sprintf('%s: %s; ', $styleMap[$key], $value);
            }
        }

        return [
            'id' => $flag->getId(),
            'title' => $flag->getTitle(),
            'description' => $flag->getDescription(),
            'link' => $flag->getLink(),
            'position' => $flag->getPosition(),
            'enable' => $flag->getEnable(),
            'style' => trim($style), // Przekazujemy style jako string
            'from_date' => $fromDate ? $fromDate->format('Y-m-d H:i:s') : null,
            'to_date' => $toDate ? $toDate->format('Y-m-d H:i:s') : null,
        ];
    }

    /**
     * Przekształca kolekcję obiektów Doctrine w tablicę.
     *
     * @param array $flags
     *
     * @return array
     */
    public function presentCollection(array $flags): array
    {
        return array_map([$this, 'present'], $flags);
    }
}
