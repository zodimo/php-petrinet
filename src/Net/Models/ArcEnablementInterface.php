<?php

declare(strict_types=1);

namespace Zodimo\PN\Net\Models;

/**
 * @template TIN
 */
interface ArcEnablementInterface
{
    /**
     * @param TIN $token
     */
    public function acceptsToken($token): bool;
}
