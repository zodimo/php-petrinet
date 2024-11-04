<?php

declare(strict_types=1);

namespace Zodimo\PN\Tests\Unit\Models\Shared;

use PHPUnit\Framework\TestCase;
use Zodimo\BaseReturnTest\MockClosureTrait;
use Zodimo\PN\Net\Models\InputArcExcpression;
use Zodimo\PN\Net\Models\Shared\Place;

/**
 * @internal
 *
 * @coversNothing
 */
class PlaceTest extends TestCase
{
    use MockClosureTrait;

    public function testCanCreate(): void
    {
        $placeId = 'place-id';

        $place = Place::create($placeId, []);

        $this->assertInstanceOf(Place::class, $place);
        $this->assertEquals($placeId, $place->getId());
        $this->assertEquals([], $place->getTokens());
    }

    public function testCanCreateWithInialMarkings(): void
    {
        $placeId = 'place-id';
        $initialMarkings = [10, 11, 12];
        $place = Place::create($placeId, $initialMarkings);

        $this->assertInstanceOf(Place::class, $place);
        $this->assertEquals($placeId, $place->getId());
        $this->assertEquals($initialMarkings, $place->getTokens());
    }

    public function testCanPopToken(): void
    {
        $placeId = 'place-id';

        $initialMarkings = [10, 11, 12];
        $place = Place::create($placeId, $initialMarkings);
        $arcEnablement = InputArcExcpression::create(fn ($x) => true, fn ($x) => $x);

        $this->assertTrue($place->canEnable($arcEnablement));
        $poppedTokenOption = $place->pop($arcEnablement);
        $this->assertTrue($poppedTokenOption->isSome());
        $poppedToken = $poppedTokenOption->unwrap($this->createClosureNotCalled());
        $this->assertNotContains($poppedToken, $place->getTokens());
    }

    public function testCanPopSpecificToken(): void
    {
        $placeId = 'place-id';
        $token = 11;
        $initialMarkings = [10, $token, 12];
        $place = Place::create($placeId, $initialMarkings);
        $arcEnablement = InputArcExcpression::create(fn ($x) => 11 == $x, fn ($x) => $x);

        $expextedTokensAfterPop = [10, 12];

        $this->assertTrue($place->canEnable($arcEnablement));
        $poppedTokenOption = $place->pop($arcEnablement);
        $this->assertTrue($poppedTokenOption->isSome());
        $this->assertEquals($token, $poppedTokenOption->unwrap($this->createClosureNotCalled()));
        $this->assertEquals($expextedTokensAfterPop, $place->getTokens());
    }

    public function testCanPushToken(): void
    {
        $placeId = 'place-id';
        $token = 11;
        $initialMarkings = [10, 12];
        $place = Place::create($placeId, $initialMarkings);

        $expextedTokensAfterPop = [10, 12, 11];

        $place->push($token);
        $this->assertEquals($expextedTokensAfterPop, $place->getTokens());
    }
}
