<?php

declare(strict_types=1);

namespace Zodimo\PN\Net\Models\Instance;

use Zodimo\PN\Net\Models\OutputArcInterface as BaseArcInterface;

/**
 * @template TIN
 * @template TOUT
 *
 * @template-extends BaseArcInterface<TIN,TOUT>
 */
interface OutputArcInterface extends BaseArcInterface
{
    /**
     * PUSH value from transition to output place.
     *
     * @param TIN $token
     */
    public function pushUnsafe(string $instanceId, $token): void;

    /**
     * @param TIN $token
     */
    public function isEnabledBy($token): bool;
}
