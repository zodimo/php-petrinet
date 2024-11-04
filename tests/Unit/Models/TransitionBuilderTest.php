<?php

declare(strict_types=1);

namespace Zodimo\PN\Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use Zodimo\PN\Net\Models\ConnectedTransition;
use Zodimo\PN\Net\Models\InputArcInterface;
use Zodimo\PN\Net\Models\Instance\InputArcInterface as InstanceInputArcInterface;
use Zodimo\PN\Net\Models\OutputArcInterface;
use Zodimo\PN\Net\Models\TransitionBuilder;

/**
 * @internal
 *
 * @coversNothing
 */
class TransitionBuilderTest extends TestCase
{
    public function testCanCreate(): void
    {
        $inputArc = $this->createMock(InputArcInterface::class);
        $builder = TransitionBuilder::create($inputArc, fn ($x) => $x);
        $this->assertInstanceOf(TransitionBuilder::class, $builder);
    }

    public function testInvalidWithEmptyOutputArcs(): void
    {
        $inputArc = $this->createMock(InputArcInterface::class);
        $builder = TransitionBuilder::create($inputArc, fn ($x) => $x);
        $this->assertFalse($builder->validate());
    }

    public function testValidWithNonEmptyOutputArcs(): void
    {
        $inputArc = $this->createMock(InstanceInputArcInterface::class);
        $builder = TransitionBuilder::create($inputArc, fn ($x) => $x);
        $outputArc = $this->createMock(OutputArcInterface::class);
        $builder = $builder->addOutputArc($outputArc);
        $this->assertTrue($builder->validate());
        $this->assertInstanceOf(ConnectedTransition::class, $builder->buildUnsafe());
    }

    public function testInValidNonEmptyOutputArcsAndZeroInstanceInputs(): void
    {
        $inputArc = $this->createMock(InputArcInterface::class);
        $builder = TransitionBuilder::create($inputArc, fn ($x) => $x);
        $outputArc = $this->createMock(OutputArcInterface::class);
        $builder = $builder->addOutputArc($outputArc);
        $this->assertFalse($builder->validate());
    }
}
