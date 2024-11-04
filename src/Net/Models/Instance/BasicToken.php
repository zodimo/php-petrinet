<?php

declare(strict_types=1);

namespace Zodimo\PN\Net\Models\Instance;

/**
 * @template TOKENCOLOUTSET
 *
 * @template-implements InstanceTokenInterface<TOKENCOLOUTSET>
 */
class BasicToken implements InstanceTokenInterface
{
    /**
     * @param TOKENCOLOUTSET $value
     */
    private function __construct(private string $instanceId, private $value) {}

    /**
     * @template _TOKENCOLOUTSET
     *
     * @param _TOKENCOLOUTSET $value
     *
     * @return BasicToken<_TOKENCOLOUTSET>
     */
    public static function create(string $instanceId, $value): InstanceTokenInterface
    {
        return new self($instanceId, $value);
    }

    /**
     * @return TOKENCOLOUTSET
     */
    public function unwrap()
    {
        return $this->value;
    }

    public function getInstanceId(): string
    {
        return $this->instanceId;
    }
}
