<?php

declare(strict_types=1);

namespace Zodimo\PN\Net\Models;

/**
 * @template TVALUE
 */
class TransitionBuilder
{
    /**
     * @param array<InputArcInterface<mixed,mixed>> $inputArcs
     * @param callable(mixed):TVALUE                $transitionFunction
     * @param array<mixed>                          $outputArcs
     */
    private function __construct(private array $inputArcs, private $transitionFunction, private array $outputArcs) {}

    /**
     * Typed during construction.
     *
     * @template A1IN
     * @template A1OUT
     * @template TFOUT
     *
     * @param InputArcInterface<A1IN,A1OUT> $inputArc
     * @param callable(A1OUT):TFOUT         $transitionFunction
     *
     * @return TransitionBuilder<TFOUT>
     */
    public static function create(InputArcInterface $inputArc, callable $transitionFunction): TransitionBuilder
    {
        return new self([$inputArc], $transitionFunction, []);
    }

    /**
     * @param OutputArcInterface<TVALUE,mixed> $outputArc
     *
     * @return TransitionBuilder<TVALUE>
     */
    public function addOutputArc(OutputArcInterface $outputArc): TransitionBuilder
    {
        $this->outputArcs[] = $outputArc;

        return $this;
    }

    public function validate(): bool
    {
        return !empty($this->inputArcs) && !empty($this->outputArcs);
    }

    /**
     * @return Transition<TVALUE>
     *
     * @throws \RuntimeException
     */
    public function buildUnsafe(): Transition
    {
        if (!$this->validate()) {
            throw new \RuntimeException('Builder is not valid');
        }

        return Transition::create($this->inputArcs, $this->transitionFunction, $this->outputArcs);
    }
}
