<?php

declare(strict_types=1);

namespace Zodimo\PN\Net\Models;

use Zodimo\BaseReturn\Option;
use Zodimo\PN\Net\Models\Instance\InputArcInterface as InstanceInputArcInterface;

/**
 * @template TVALUE
 */
class TransitionBuilder
{
    /**
     * @param array<InputArcInterface<mixed,mixed>> $inputArcs
     * @param callable(mixed,?mixed):TVALUE         $transitionFunction
     * @param array<mixed>                          $outputArcs
     */
    private function __construct(private array $inputArcs, private $transitionFunction, private array $outputArcs) {}

    /**
     * Typed during construction.
     *
     * @template AIN
     * @template AOUT
     * @template TFOUT
     *
     * @param InputArcInterface<AIN,AOUT> $inputArc
     * @param callable(AOUT):TFOUT        $transitionFunction
     *
     * @return TransitionBuilder<TFOUT>
     */
    public static function create(InputArcInterface $inputArc, callable $transitionFunction): TransitionBuilder
    {
        return new self([$inputArc], $transitionFunction, []);
    }

    /**
     * Typed during construction.
     *
     * @template A1IN
     * @template A1OUT
     * @template A2IN
     * @template A2OUT
     * @template TFOUT
     *
     * @param InputArcInterface<A1IN,A1OUT> $inputArc1
     * @param InputArcInterface<A2IN,A2OUT> $inputArc2
     * @param callable(A1OUT,A2OUT):TFOUT   $transitionFunction
     *
     * @return TransitionBuilder<TFOUT>
     */
    public static function create2(InputArcInterface $inputArc1, InputArcInterface $inputArc2, callable $transitionFunction): TransitionBuilder
    {
        return new self([$inputArc1, $inputArc2], $transitionFunction, []);
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
        /**
         * musst have at lease 1 instance place.
         */
        $hasAtleatOneInstanceInput = false;
        foreach ($this->inputArcs as $inputArc) {
            if ($inputArc instanceof InstanceInputArcInterface) {
                $hasAtleatOneInstanceInput = true;

                break;
            }
        }

        return !empty($this->inputArcs) && !empty($this->outputArcs) && $hasAtleatOneInstanceInput;
    }

    /**
     * @return ConnectedTransition<TVALUE>
     *
     * @throws \RuntimeException
     */
    public function buildUnsafe(): ConnectedTransition
    {
        if (!$this->validate()) {
            throw new \RuntimeException('Builder is not valid');
        }

        return ConnectedTransition::create($this->inputArcs, $this->transitionFunction, $this->outputArcs);
    }

    /**
     * @return Option<ConnectedTransition<TVALUE>>
     */
    public function build(): Option
    {
        if (!$this->validate()) {
            return Option::none();
        }

        return Option::some(ConnectedTransition::create($this->inputArcs, $this->transitionFunction, $this->outputArcs));
    }
}
