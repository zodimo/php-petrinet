<?php

declare(strict_types=1);

namespace Zodimo\PN\Tests\Unit\Models\Shared;

use PHPUnit\Framework\TestCase;
use Zodimo\BaseReturnTest\MockClosureTrait;
use Zodimo\PN\Net\Models\OutputArcExcpression;
use Zodimo\PN\Net\Models\OutputArcInterface;
use Zodimo\PN\Net\Models\Shared\OutputArc;
use Zodimo\PN\Net\Models\Shared\Place;

/**
 * @internal
 *
 * @coversNothing
 */
class OutputArcTest extends TestCase
{
    use MockClosureTrait;

    public function testCanCreateFroSharedPlace(): void
    {
        $id = 'some-id';
        $initialMarkings = [];
        $place = Place::create($id, $initialMarkings);
        $outputArcExpression = OutputArcExcpression::create(fn ($x) => true, fn ($x) => $x);
        $outputArc = OutputArc::create($place, $outputArcExpression);
        $this->assertInstanceOf(OutputArcInterface::class, $outputArc);
    }

    public function testCanPushATokenToSharedPlace(): void
    {
        $id = 'some-id';
        $initialMarkings = [];
        $basicToken = 10;
        $place = Place::create($id, $initialMarkings);
        $outputArcExpression = OutputArcExcpression::create(fn (int $x) => 10 == $x, fn (int $x): int => $x);
        $outputArc = OutputArc::create($place, $outputArcExpression);

        $this->assertTrue($outputArc->isEnabledBy($basicToken));
        $outputArc->pushUnsafe($basicToken);
        $this->assertEquals([$basicToken], $place->getTokens());
    }
}
