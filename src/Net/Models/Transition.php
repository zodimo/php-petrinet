<?php

declare(strict_types=1);

namespace Zodimo\PN\Net\Models;

/**
 * @template TVALUE
 */
class Transition
{
    /**
     * @param array<InputArcInterface<mixed,mixed>>  $inputArcs
     * @param callable(mixed):TVALUE                 $transitionFunction
     * @param array<OutputArcInterface<mixed,mixed>> $outputArcs
     */
    private function __construct(
        private array $inputArcs,
        private $transitionFunction,
        private array $outputArcs,
    ) {}

    /**
     * @template TFOUT
     *
     * @param array<InputArcInterface<mixed,mixed>>  $inputArcs
     * @param callable(mixed):TFOUT                  $transitionFunction
     * @param array<OutputArcInterface<mixed,mixed>> $outputArcs
     *
     * @return Transition<TFOUT>
     */
    public static function create(array $inputArcs, callable $transitionFunction, array $outputArcs): Transition
    {
        return new self($inputArcs, $transitionFunction, $outputArcs);
    }

    public function fire(): void
    {
        $collectedtInputs = [];
        foreach ($this->inputArcs as $inputArc) {
            $inputArc->pop()->match(
                function ($token) use (&$collectedtInputs) {
                    $collectedtInputs[] = $token;
                },
                fn () => null,// noop
            );
        }
        if (count($collectedtInputs) == count($this->inputArcs)) {
            $result = call_user_func_array($this->transitionFunction, $collectedtInputs);
            foreach ($this->outputArcs as $outputArc) {
                $outputArc->pushUnsafe($result);
            }
        }
    }

    /**
     * @return array<bool>
     */
    public function reportInputs(): array
    {
        return array_map(fn (InputArcInterface $arc) => $arc->isEnabled(), $this->inputArcs);
    }
}
