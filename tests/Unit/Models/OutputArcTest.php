<?php

declare(strict_types=1);

namespace Zodimo\PN\Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use Zodimo\BaseReturnTest\MockClosureTrait;
use Zodimo\PN\Net\Models\OutputArc;
use Zodimo\PN\Net\Models\OutputArcExcpression;
use Zodimo\PN\Net\Models\OutputArcInterface;
use Zodimo\PN\Net\Models\Place;

/**
 * @internal
 *
 * @coversNothing
 */
class OutputArcTest extends TestCase
{
    use MockClosureTrait;

    public function testCanCreate(): void
    {
        $id = 'some-id';
        $initialMarkings = [];
        $place = Place::create($id, $initialMarkings);
        $outputArcExpression = OutputArcExcpression::create(fn ($x) => true, fn ($x) => $x);
        $outputArc = OutputArc::create($place, $outputArcExpression);
        $this->assertInstanceOf(OutputArcInterface::class, $outputArc);
    }

    public function testCanPushAToken(): void
    {
        $id = 'some-id';
        $initialMarkings = [];
        $place = Place::create($id, $initialMarkings);
        $outputArcExpression = OutputArcExcpression::create(fn (int $x) => 10 == $x, fn (int $x): int => $x);
        $outputArc = OutputArc::create($place, $outputArcExpression);

        $this->assertTrue($outputArc->isEnabledBy(10));
        $outputArc->pushUnsafe(10);
        $this->assertEquals([10], $place->getTokens());
    }
}
