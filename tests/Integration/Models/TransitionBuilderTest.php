<?php

declare(strict_types=1);

namespace Zodimo\PN\Tests\Integration\Models;

use PHPUnit\Framework\TestCase;
use Zodimo\PN\Net\Models\InputArcExcpression;
use Zodimo\PN\Net\Models\Instance\BasicToken;
use Zodimo\PN\Net\Models\Instance\InputArc;
use Zodimo\PN\Net\Models\Instance\InstanceTokenInterface;
use Zodimo\PN\Net\Models\Instance\OutputArc;
use Zodimo\PN\Net\Models\Instance\Place;
use Zodimo\PN\Net\Models\OutputArcExcpression;
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
        $instanceId = '12345';
        $basicToken = BasicToken::create($instanceId, 10);

        $inputGuard = fn (int $x): bool => 10 == $x;
        $inputTansform = fn (int $x): int => $x;
        $inputArcExpression = InputArcExcpression::create($inputGuard, $inputTansform);
        $inputPlace = Place::create('123', [$basicToken]);

        $inputArc = InputArc::create($inputPlace, $inputArcExpression);

        $ouputGuard = fn (string $x): bool => '10' === $x;

        $outputTansform = fn (string $x): string => "Value: {$x}";
        $outputArcExpression = OutputArcExcpression::create($ouputGuard, $outputTansform);

        /**
         * @var array<InstanceTokenInterface<mixed>> $initialTokens
         */
        $initialTokens = [];
        $outputPlace = Place::create('456', $initialTokens);

        $expectedOutputBasicToken = BasicToken::create($instanceId, $outputTansform('10'));

        $outputArc = OutputArc::create($outputPlace, $outputArcExpression);

        $transitionFunction = fn (int $x): string => (string) $x;

        $transitionBuilder = TransitionBuilder::create($inputArc, $transitionFunction);
        $transitionBuilder = $transitionBuilder->addOutputArc($outputArc);

        $transition = $transitionBuilder->buildUnsafe();

        // before
        $this->assertEquals([$basicToken], $inputPlace->getTokens($instanceId));
        $this->assertEquals([], $outputPlace->getTokens($instanceId));
        $reportBefore = $transition->reportInputs($instanceId);
        $this->assertEquals([true], $reportBefore);
        $this->assertTrue($transition->isEnabled($instanceId));
        $transition->fireUnsafe($instanceId);
        // after
        $reportAfter = $transition->reportInputs($instanceId);
        $this->assertEquals([false], $reportAfter);
        $this->assertFalse($transition->isEnabled($instanceId));
        $this->assertEquals([], $inputPlace->getTokens($instanceId));
        $this->assertEquals([$expectedOutputBasicToken], $outputPlace->getTokens($instanceId));
    }
}
