<?php

declare(strict_types=1);

namespace Zodimo\PN\Net\Models\Shared;

use Zodimo\BaseReturn\Option;
use Zodimo\PN\Net\Models\InputArcInterface as BaseArcInterface;

/**
 * @template TIN
 * @template TOUT
 *
 * @template-extends BaseArcInterface<TIN,TOUT>
 */
interface InputArcInterface extends BaseArcInterface
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
