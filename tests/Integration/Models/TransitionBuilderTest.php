<?php

declare(strict_types=1);

namespace Zodimo\PN\Tests\Integration\Models;

use PHPUnit\Framework\TestCase;
use Zodimo\PN\Net\Models\InputArc;
use Zodimo\PN\Net\Models\InputArcExcpression;
use Zodimo\PN\Net\Models\OutputArc;
use Zodimo\PN\Net\Models\OutputArcExcpression;
use Zodimo\PN\Net\Models\Place;
use Zodimo\PN\Net\Models\TransitionBuilder;

/**
 * @internal
 *
 * @coversNothing
 */
class TransitionBuilderTest extends TestCase
{
    public function testCanCreatePassthrough(): void
    {
        $inputGuard = fn (int $x): bool => 10 == $x;
        $inputTansform = fn (int $x): int => $x;
        $inputArcExpression = InputArcExcpression::create($inputGuard, $inputTansform);
        $inputPlace = Place::create('123', [10]);

        $inputArc = InputArc::create($inputPlace, $inputArcExpression);

        $ouputGuard = fn (string $x): bool => '10' === $x;

        $outputTansform = fn (string $x): string => "Value: {$x}";
        $outputArcExpression = OutputArcExcpression::create($ouputGuard, $outputTansform);
        $outputPlace = Place::create('456', []);

        $outputArc = OutputArc::create($outputPlace, $outputArcExpression);

        $transitionFunction = fn (int $x): string => (string) $x;

        $transitionBuilder = TransitionBuilder::create($inputArc, $transitionFunction);
        $transitionBuilder = $transitionBuilder->addOutputArc($outputArc);

        $transition = $transitionBuilder->buildUnsafe();

        // before
        $this->assertEquals([10], $inputPlace->getTokens());
        $this->assertEquals([], $outputPlace->getTokens());
        $reportBefore = $transition->reportInputs();
        $this->assertEquals([true], $reportBefore);
        $transition->fire();
        // after
        $reportAfter = $transition->reportInputs();
        $this->assertEquals([false], $reportAfter);
        $this->assertEquals([], $inputPlace->getTokens());
        $this->assertEquals(['Value: 10'], $outputPlace->getTokens());
    }
}
