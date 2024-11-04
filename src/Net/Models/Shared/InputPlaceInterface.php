<?php

declare(strict_types=1);

namespace Zodimo\PN\Net\Models\Shared;

use Zodimo\BaseReturn\Option;
use Zodimo\PN\Net\Models\ArcEnablementInterface;

/**
 * @template TOKENCOLOURSET
 */
interface InputPlaceInterface
{
    /**
     * @param ArcEnablementInterface<TOKENCOLOURSET> $arcEnablement
     */
    public function canEnable(ArcEnablementInterface $arcEnablement): bool;

    /**
     * @param ArcEnablementInterface<TOKENCOLOURSET> $arcEnablement
     *
     * @return Option<TOKENCOLOURSET>
     */
    public function pop(ArcEnablementInterface $arcEnablement): Option;
}
