<?php

declare(strict_types=1);

namespace Zodimo\PN\Net\Models\Shared;

use Zodimo\PN\Net\Models\OutputArcExpressionInterface;

/**
 * @template TIN
 * @template TOUT
 *
 * @template-implements OutputArcInterface<TIN,TOUT>
 */
class OutputArc implements OutputArcInterface
{
    /**
     * @param OutputPlaceInterface<TOUT>             $outputPlace
     * @param OutputArcExpressionInterface<TIN,TOUT> $expression
     */
    private function __construct(private OutputPlaceInterface $outputPlace, private OutputArcExpressionInterface $expression) {}

    /**
     * @template _TIN
     * @template _TOUT
     *
     * @param OutputPlaceInterface<_TOUT>              $outputPlace
     * @param OutputArcExpressionInterface<_TIN,_TOUT> $expression
     *
     * @return OutputArcInterface<_TIN,_TOUT>
     */
    public static function create(OutputPlaceInterface $outputPlace, OutputArcExpressionInterface $expression): OutputArcInterface
    {
        return new self($outputPlace, $expression);
    }

    /**
     * Summary of pushUnsafe.
     *
     * @param TIN $token
     *
     * @throws \RuntimeException
     */
    public function pushUnsafe($token): void
    {
        if ($this->expression->acceptsToken($token)) {
            $this->outputPlace->push($this->expression->getToken($token));
        } else {
            throw new \RuntimeException('Token not accepted here...');
        }
    }

    /**
     * @param TIN $token
     */
    public function isEnabledBy($token): bool
    {
        return $this->expression->acceptsToken($token);
    }
}
