<?php

declare(strict_types=1);

namespace Zodimo\PN\Net\Models;

use Zodimo\BaseReturn\Option;

class TransitionContext
{
    /**
     * @param array<string,mixed> $context
     */
    private function __construct(private string $instanceId, private array $context = []) {}

    /**
     * @param array<string,mixed> $context
     */
    public static function create(string $instanceId, array $context = []): TransitionContext
    {
        return new self($instanceId, $context);
    }

    /**
     * @param mixed $value
     */
    public function set(string $variableName, $value): TransitionContext
    {
        $this->context[$variableName] = $value;

        return $this;
    }

    /**
     * @return Option<mixed>
     */
    public function get(string $variableName): Option
    {
        if (key_exists($variableName, $this->context)) {
            return Option::some($this->context[$variableName]);
        }

        return Option::none();
    }

    /**
     * Cloned version of inner context.
     *
     * @return array<string,mixed>
     */
    public function getContext(): array
    {
        return array_merge([], $this->context);
    }

    public function getInstanceId(): string
    {
        return $this->instanceId;
    }
}
