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

    public function fireUnsafe(): void
    {
        // if all inputs are enabled
        foreach ($this->inputArcs as $inputArc) {
            if (!$inputArc->isEnabled()) {
                return;
            }
        }
        $collectedtInputs = array_map(fn (InputArcInterface $arc) => $arc->popUnsafe(), $this->inputArcs);

        $result = call_user_func_array($this->transitionFunction, $collectedtInputs);
        foreach ($this->outputArcs as $outputArc) {
            $outputArc->pushUnsafe($result);
        }
    }

    // public function fire(): void
    // {
    //     // if all inputs are enabled
    //     foreach ($this->inputArcs as $inputArc) {
    //         if (!$inputArc->isEnabled()) {
    //             return;
    //         }
    //     }
    //     $collectedtInputs = array_map(fn (InputArcInterface $arc) => $arc->pop(), $this->inputArcs);

    //     $result = call_user_func_array($this->transitionFunction, $collectedtInputs);
    //     foreach ($this->outputArcs as $outputArc) {
    //         $outputArc->pushUnsafe($result);
    //     }
    // }
}
