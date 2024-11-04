<?php

declare(strict_types=1);

namespace Zodimo\PN\Net\Models\Instance;

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
    public function isEnabled(string $instanceId): bool;

    /**
     * POP value from input place into transition.
     *
     * @return Option<TOUT>
     */
    public function pop(string $instanceId): Option;

    /**
     * @return TOUT
     */
    public function popUnsafe(string $instanceId);
}
