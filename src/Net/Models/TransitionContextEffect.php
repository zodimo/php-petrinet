<?php

declare(strict_types=1);

namespace Zodimo\PN\Net\Models;

class TransitionContextEffect
{
    private const TAG_SET = 'set';
    private const TAG_GET = 'get';

    /**
     * @param array<string,mixed> $args
     */
    private function __construct(private string $tag, private string $variableName, private array $args = []) {}

    /**
     * @param mixed $value
     */
    public static function set(string $variableName, $value): TransitionContextEffect
    {
        return new TransitionContextEffect(self::TAG_SET, $variableName, ['value' => $value]);
    }

    public static function get(string $variableName): TransitionContextEffect
    {
        return new TransitionContextEffect(self::TAG_GET, $variableName);
    }

    /**
     * @phpstan-assert-if-true array{value:mixed} $this->getArgs()
     */
    public function isSet(): bool
    {
        return self::TAG_SET === $this->tag;
    }

    /**
     * @phpstan-assert-if-true array{} $this->getArgs()
     */
    public function isGet(): bool
    {
        return self::TAG_GET === $this->tag;
    }

    public function getVariableName(): string
    {
        return $this->variableName;
    }

    /**
     * @return array<string,mixed>
     */
    public function getArgs(): array
    {
        return $this->args;
    }
}
