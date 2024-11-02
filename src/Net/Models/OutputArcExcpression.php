<?php

declare(strict_types=1);

namespace Zodimo\PN\Net\Models;

/**
 * @template TIN
 * @template TOUT
 *
 * @template-implements OutputArcExpressionInterface<TIN,TOUT>
 */
class OutputArcExcpression implements OutputArcExpressionInterface
{
    /**
     * @param callable(TIN):bool $guard
     * @param callable(TIN):TOUT $transform
     */
    private function __construct(
        private $guard,
        private $transform
    ) {}

    /**
     * @template _TIN
     * @template _TOUT
     *
     * @param callable(_TIN):bool  $guard
     * @param callable(_TIN):_TOUT $transform
     *
     * @return OutputArcExpressionInterface<_TIN,_TOUT>
     */
    public static function create(callable $guard, callable $transform): OutputArcExpressionInterface
    {
        return new self($guard, $transform);
    }

    /**
     * @param TIN $token
     *
     * @return TOUT
     */
    public function getToken($token)
    {
        return call_user_func($this->transform, $token);
    }

    public function acceptsToken($token): bool
    {
        return call_user_func($this->guard, $token);
    }
}
