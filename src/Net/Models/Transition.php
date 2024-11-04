<?php

declare(strict_types=1);

namespace Zodimo\PN\Net\Models;

use Zodimo\PN\Net\Models\Instance\InputArcInterface as InstanceInputArcInterface;
use Zodimo\PN\Net\Models\Instance\OutputArcInterface as InstanceOutputArcInterface;
use Zodimo\PN\Net\Models\Shared\InputArcInterface as SharedInputArcInterface;
use Zodimo\PN\Net\Models\Shared\OutputArcInterface as SharedOutputArcInterface;

/**
 * @template TVALUE
 */
class Transition
{
    /**
     * @param array<InputArcInterface<mixed,mixed>>  $inputArcs
     * @param callable(mixed,?mixed):TVALUE          $transitionFunction
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
     * @param callable(mixed,?mixed):TFOUT           $transitionFunction
     * @param array<OutputArcInterface<mixed,mixed>> $outputArcs
     *
     * @return Transition<TFOUT>
     */
    public static function create(array $inputArcs, callable $transitionFunction, array $outputArcs): Transition
    {
        return new self($inputArcs, $transitionFunction, $outputArcs);
    }

    /**
     * Unsafe.
     *
     * @throws \RuntimeException
     */
    public function fireUnsafe(string $instanceId): void
    {
        $collectedtInputs = [];
        foreach ($this->inputArcs as $inputArc) {
            switch (true) {
                case $inputArc instanceof InstanceInputArcInterface:
                    $inputArc->pop($instanceId)->match(
                        function ($token) use (&$collectedtInputs) {
                            $collectedtInputs[] = $token;
                        },
                        fn () => null,// noop
                    );

                    break;

                case $inputArc instanceof SharedInputArcInterface:
                    $inputArc->pop()->match(
                        function ($token) use (&$collectedtInputs) {
                            $collectedtInputs[] = $token;
                        },
                        fn () => null,// noop
                    );

                    break;

                default:
                    // this is not so good....
                    throw new \RuntimeException('Unsupported InputArcType: '.get_class($inputArc));
            }
        }
        if (count($collectedtInputs) == count($this->inputArcs)) {
            $result = call_user_func_array($this->transitionFunction, $collectedtInputs);
            foreach ($this->outputArcs as $outputArc) {
                switch (true) {
                    case $outputArc instanceof InstanceOutputArcInterface:
                        $outputArc->pushUnsafe($instanceId, $result);

                        break;

                    case $outputArc instanceof SharedOutputArcInterface:
                        $outputArc->pushUnsafe($result);

                        break;

                    default:
                        // this is not so good....
                        throw new \RuntimeException('Unsupported InputArcType: '.get_class($outputArc));
                }
            }
        }
    }

    public function isEnabled(string $instanceId): bool
    {
        $reportedInputs = $this->reportInputs($instanceId);

        return count($this->inputArcs) == count(array_filter($reportedInputs));
    }

    /**
     * @return array<bool>
     */
    public function reportInputs(string $instanceId): array
    {
        $arcEnablements = [];
        foreach ($this->inputArcs as $inputArc) {
            switch (true) {
                case $inputArc instanceof InstanceInputArcInterface:
                    $arcEnablements[] = $inputArc->isEnabled($instanceId);

                    break;

                case $inputArc instanceof SharedInputArcInterface:
                    $arcEnablements[] = $inputArc->isEnabled();

                    break;

                default:
                    // this is not so good....
                    throw new \RuntimeException('Unsupported InputArcType: '.get_class($inputArc));
            }
        }

        return $arcEnablements;
    }
}
