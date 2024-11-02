<?php

declare(strict_types=1);

namespace Zodimo\PN\Net\Models;

use Zodimo\BaseReturn\Option;

/**
 * @template TIN
 * @template TOUT
 */
interface InputArcInterface
{
    public function isEnabled(): bool;

    /**
     * POP value from input place into transition.
     *
     * @return Option<TOUT>
     */
    public function pop(): Option;

    /**
     * @return TOUT
     */
    public function popUnsafe();
}
