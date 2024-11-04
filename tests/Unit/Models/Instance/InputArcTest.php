<?php

declare(strict_types=1);

namespace Zodimo\PN\Tests\Unit\Models\Instance;

use PHPUnit\Framework\TestCase;
use Zodimo\BaseReturnTest\MockClosureTrait;
use Zodimo\PN\Net\Models\InputArcExcpression;
use Zodimo\PN\Net\Models\InputArcInterface;
use Zodimo\PN\Net\Models\Instance\BasicToken;
use Zodimo\PN\Net\Models\Instance\InputArc;
use Zodimo\PN\Net\Models\Instance\Place;

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
        $placeId = 'some-id';

        /**
         * @var array<\Zodimo\PN\Net\Models\Instance\InstanceTokenInterface<mixed>>
         */
        $initialMarkings = [];
        $place = Place::create($placeId, $initialMarkings);
        $inputArcExpression = InputArcExcpression::create(fn ($x) => true, fn ($x) => $x);
        $inputArc = InputArc::create($place, $inputArcExpression);
        $this->assertInstanceOf(InputArcInterface::class, $inputArc);
    }

    public function testCanPullAToken(): void
    {
        $id = 'some-id';
        $instanceId = '1234';
        $initialMarkings = [BasicToken::create($instanceId, 10)];
        $place = Place::create($id, $initialMarkings);
        $inputArcExpression = InputArcExcpression::create(fn ($x) => 10 == $x, fn ($x) => $x);
        $inputArc = InputArc::create($place, $inputArcExpression);
        $this->assertInstanceOf(InputArcInterface::class, $inputArc);
        $this->assertTrue($inputArc->isEnabled($instanceId));
        $tokenOption = $inputArc->pop($instanceId);
        $this->assertTrue($tokenOption->isSome());
        $this->assertEquals(10, $tokenOption->unwrap($this->createClosureNotCalled()));
    }
}
