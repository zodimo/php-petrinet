<?php

declare(strict_types=1);

namespace Zodimo\PN\Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use Zodimo\BaseReturnTest\MockClosureTrait;
use Zodimo\PN\Net\Models\InputArc;
use Zodimo\PN\Net\Models\InputArcExcpression;
use Zodimo\PN\Net\Models\InputArcInterface;
use Zodimo\PN\Net\Models\Place;

/**
 * @internal
 *
 * @coversNothing
 */
class InputArcTest extends TestCase
{
    use MockClosureTrait;

    public function testCanCreate(): void
    {
        $id = 'some-id';
        $initialMarkings = [];
        $place = Place::create($id, $initialMarkings);
        $inputArcExpression = InputArcExcpression::create(fn ($x) => true, fn ($x) => $x);
        $inputArc = InputArc::create($place, $inputArcExpression);
        $this->assertInstanceOf(InputArcInterface::class, $inputArc);
    }

    public function testCanPullAToken(): void
    {
        $id = 'some-id';
        $initialMarkings = [10];
        $place = Place::create($id, $initialMarkings);
        $inputArcExpression = InputArcExcpression::create(fn ($x) => 10 == $x, fn ($x) => $x);
        $inputArc = InputArc::create($place, $inputArcExpression);
        $this->assertInstanceOf(InputArcInterface::class, $inputArc);
        $this->assertTrue($inputArc->isEnabled());
        $tokenOption = $inputArc->pop();
        $this->assertTrue($tokenOption->isSome());
        $this->assertEquals(10, $tokenOption->unwrap($this->createClosureNotCalled()));
    }
}
