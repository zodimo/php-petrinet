<?php

declare(strict_types=1);

namespace Zodimo\PN\Tests\Unit\Models\Instance;

use PHPUnit\Framework\TestCase;
use Zodimo\BaseReturnTest\MockClosureTrait;
use Zodimo\PN\Net\Models\Instance\BasicToken;
use Zodimo\PN\Net\Models\Instance\InstanceTokenInterface;
use Zodimo\PN\Net\Models\Instance\OutputArc;
use Zodimo\PN\Net\Models\Instance\Place;
use Zodimo\PN\Net\Models\OutputArcExcpression;
use Zodimo\PN\Net\Models\OutputArcInterface;

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

        /**
         * @var array<InstanceTokenInterface<mixed>> $initialMarkings
         */
        $initialMarkings = [];
        $place = Place::create($id, $initialMarkings);
        $outputArcExpression = OutputArcExcpression::create(fn ($x) => true, fn ($x) => $x);
        $outputArc = OutputArc::create($place, $outputArcExpression);
        $this->assertInstanceOf(OutputArcInterface::class, $outputArc);
    }

    public function testCanPushATokenToSharedPlace(): void
    {
        $placeId = 'some-id';
        $token = 10;
        $instanceId = '12345';

        /**
         * @var array<InstanceTokenInterface<mixed>> $initialMarkings
         */
        $initialMarkings = [];
        $basicToken = BasicToken::create($instanceId, $token);
        $place = Place::create($placeId, $initialMarkings);
        $outputArcExpression = OutputArcExcpression::create(fn (int $x) => 10 == $x, fn (int $x): int => $x);
        $outputArc = OutputArc::create($place, $outputArcExpression);

        $this->assertTrue($outputArc->isEnabledBy($token));
        $outputArc->pushUnsafe($instanceId, 10);
        $this->assertEquals([$basicToken], $place->getTokens());
    }
}
