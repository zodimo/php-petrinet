<?php

declare(strict_types=1);

namespace Zodimo\PN\Net\Models;

/**
 * @template TIN
 * @template TOUT
 */
interface OutputArcInterface
{
    /**
     * PUSH value from transition to output place.
     *
     * @param TIN $token
     */
    public function pushUnsafe($token): void;

    /**
     * @param TIN $token
     */
    public function isEnabledBy($token): bool;
}
