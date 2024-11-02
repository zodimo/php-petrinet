<?php

declare(strict_types=1);

namespace Zodimo\PN\Net\Models;

use Zodimo\BaseReturn\Option;

/**
 * @template TOKENCOLOUR
 */
interface InputPlaceInterface
{
    /**
     * @param ArcEnablementInterface<TOKENCOLOUR> $arcEnablement
     */
    public function canEnable(ArcEnablementInterface $arcEnablement): bool;

    /**
     * @param ArcEnablementInterface<TOKENCOLOUR> $arcEnablement
     *
     * @return Option<TOKENCOLOUR>
     */
    public function pop(ArcEnablementInterface $arcEnablement): Option;
}
