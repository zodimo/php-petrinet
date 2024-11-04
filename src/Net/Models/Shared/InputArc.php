<?php

declare(strict_types=1);

namespace Zodimo\PN\Net\Models\Shared;

use Zodimo\BaseReturn\Option;
use Zodimo\PN\Net\Models\InputArcExpressionInterface;

/**
 * @template TIN
 * @template TOUT
 *
 * @template-implements InputArcInterface<TIN,TOUT>
 */
class InputArc implements InputArcInterface
{
    /**
     * @param InputPlaceInterface<TIN>              $inputPlace
     * @param InputArcExpressionInterface<TIN,TOUT> $expression
     */
    private function __construct(
        private InputPlaceInterface $inputPlace,
        private InputArcExpressionInterface $expression,
    ) {}

    /**
     * @template PLACE_TOKENCOLOURSET
     * @template EXPRESSION_OUTPUT
     *
     * @param InputPlaceInterface<PLACE_TOKENCOLOURSET>                           $inputPlace
     * @param InputArcExpressionInterface<PLACE_TOKENCOLOURSET,EXPRESSION_OUTPUT> $expression
     *
     * @return InputArc<PLACE_TOKENCOLOURSET,EXPRESSION_OUTPUT>
     */
    public static function create(InputPlaceInterface $inputPlace, InputArcExpressionInterface $expression): InputArc
    {
        return new self($inputPlace, $expression);
    }

    public function isEnabled(): bool
    {
        return $this->inputPlace->canEnable($this->expression);
    }

    /**
     * POP value from input place into transition.
     *
     * @return Option<TOUT>
     */
    public function pop(): Option
    {
        return $this->inputPlace->pop($this->expression)->map(fn ($token) => $this->expression->getToken($token));
    }

    /**
     * @return TOUT
     */
    public function popUnsafe()
    {
        return $this->inputPlace->pop($this->expression)->match(
            fn ($token) => $this->expression->getToken($token),
            fn () => throw new \RuntimeException('No valid token found')
        );
    }
}
