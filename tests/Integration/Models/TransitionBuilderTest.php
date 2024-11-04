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
        $this->assertEquals([$basicToken], $inputPlace->getInstanceTokens($instanceId));
        $this->assertEquals([], $outputPlace->getInstanceTokens($instanceId));
        $reportBefore = $transition->reportInputs($instanceId);
        $this->assertEquals([true], $reportBefore);
        $this->assertTrue($transition->isEnabled($instanceId));
        $transition->fireUnsafe($instanceId);
        // after
        $reportAfter = $transition->reportInputs($instanceId);
        $this->assertEquals([false], $reportAfter);
        $this->assertFalse($transition->isEnabled($instanceId));
        $this->assertEquals([], $inputPlace->getInstanceTokens($instanceId));
        $this->assertEquals([$expectedOutputBasicToken], $outputPlace->getInstanceTokens($instanceId));
    }

    public function testInstanceTokenOverMultipleInputplacesAreSyncronized(): void
    {
        $instanceId1 = 'instanceId1';
        $instanceId2 = 'instanceId2';
        $instanceId3 = 'instanceId3';

        $place1Id = 'place1Id';
        $place2Id = 'place2Id';
        $place3Id = 'place3Id';

        $succeedFN = fn ($_) => true;
        $idFN = fn ($x) => $x;

        // setup place 1

        $place1InitialMarkings = [
            BasicToken::create($instanceId3, 5),
            BasicToken::create($instanceId1, 10),
            BasicToken::create($instanceId2, 20),
        ];
        $place1 = Place::create($place1Id, $place1InitialMarkings);
        $inputArc1 = InputArc::create($place1, InputArcExcpression::create($succeedFN, $idFN));

        // setup place 2

        $place2InitialMarkings = [
            BasicToken::create($instanceId1, 15),
        ];
        $place2 = Place::create($place2Id, $place2InitialMarkings);
        $inputArc2 = InputArc::create($place2, InputArcExcpression::create($succeedFN, $idFN));

        /**
         * @var array<InstanceTokenInterface<int>> $place3InitialMarkings
         */
        $place3InitialMarkings = [];
        $place3 = Place::create($place3Id, $place3InitialMarkings);
        $outputArc = OutputArc::create($place3, OutputArcExcpression::create($succeedFN, $idFN));

        $transitionBuilder = TransitionBuilder::create2($inputArc1, $inputArc2, fn ($x, $y) => $x + $y);
        $transitionBuilder = $transitionBuilder->addOutputArc($outputArc);

        // Expectation setup
        $afterFirePlace1Markings = [
            BasicToken::create($instanceId3, 5),
            BasicToken::create($instanceId2, 20),
        ];
        $afterFirePlace2Markings = [];
        $afterFirePlace3Markings = [
            BasicToken::create($instanceId1, 25),
        ];

        $this->assertTrue($transitionBuilder->validate());
        $transition = $transitionBuilder->buildUnsafe();
        // before fire
        $this->assertTrue($transition->isEnabled($instanceId1));
        $this->assertFalse($transition->isEnabled($instanceId2));
        $this->assertFalse($transition->isEnabled($instanceId3));

        $this->assertEquals($place1InitialMarkings, $place1->getTokens());
        $this->assertEquals($place2InitialMarkings, $place2->getTokens());
        $this->assertEquals($place3InitialMarkings, $place3->getTokens());

        $transition->fireUnsafe($instanceId1);
        // after fire
        $this->assertFalse($transition->isEnabled($instanceId1));
        $this->assertFalse($transition->isEnabled($instanceId2));
        $this->assertFalse($transition->isEnabled($instanceId3));

        $this->assertEquals($afterFirePlace1Markings, $place1->getTokens());
        $this->assertEquals($afterFirePlace2Markings, $place2->getTokens());
        $this->assertEquals($afterFirePlace3Markings, $place3->getTokens());
    }
}
