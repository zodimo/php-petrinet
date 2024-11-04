<?php

declare(strict_types=1);

namespace Zodimo\PN\Net\Models\Instance;

/**
 * @template TOKENCOLOURSET
 */
interface OutputPlaceInterface
{
    /**
     * @param InstanceTokenInterface<TOKENCOLOURSET> $token
     */
    public function push(InstanceTokenInterface $token): void;
}
