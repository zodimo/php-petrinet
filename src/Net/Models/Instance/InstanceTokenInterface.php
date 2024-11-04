<?php

declare(strict_types=1);

namespace Zodimo\PN\Net\Models\Instance;

/**
 * @template TOKENCOLOURSET
 */
interface InstanceTokenInterface
{
    public function getInstanceId(): string;

    /**
     * @return TOKENCOLOURSET
     */
    public function unwrap();
}
