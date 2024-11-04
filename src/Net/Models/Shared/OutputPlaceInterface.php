<?php

declare(strict_types=1);

namespace Zodimo\PN\Net\Models\Shared;

use Zodimo\PN\Net\Models\TokenInterface;

/**
 * @template TOKENCOLOURSET
 */
interface OutputPlaceInterface
{
    /**
     * @param TokenInterface<TOKENCOLOURSET> $token
     */
    public function push(TokenInterface $token): void;
}
