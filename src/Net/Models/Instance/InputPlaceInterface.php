<?php

declare(strict_types=1);

namespace Zodimo\PN\Net\Models\Instance;

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
    public function canEnable(string $instanceId, ArcEnablementInterface $arcEnablement): bool;

    /**
     * @param ArcEnablementInterface<TOKENCOLOURSET> $arcEnablement
     *
     * @return Option<TOKENCOLOURSET>
     */
    public function pop(string $instanceId, ArcEnablementInterface $arcEnablement): Option;
}
