<?php

declare(strict_types=1);

namespace Zodimo\PN\Tests\Unit\Models\Instance;

use PHPUnit\Framework\TestCase;
use Zodimo\BaseReturnTest\MockClosureTrait;
use Zodimo\PN\Net\Models\ArcEnablementInterface;
use Zodimo\PN\Net\Models\Instance\BasicToken;
use Zodimo\PN\Net\Models\Instance\Place;

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

        // @phpstan-ignore argument.templateType
        $place = Place::create($placeId, []);

        $this->assertInstanceOf(Place::class, $place);
        $this->assertEquals($placeId, $place->getId());
        $this->assertEquals([], $place->getTokens());
    }

    public function testCanCreateWithInialMarkings(): void
    {
        $placeId = 'place-id';
        $initialMarkings = [
            BasicToken::create('123', 10),
            BasicToken::create('124', 11),
            BasicToken::create('125', 12),
        ];
        $place = Place::create($placeId, $initialMarkings);

        $this->assertInstanceOf(Place::class, $place);
        $this->assertEquals($placeId, $place->getId());
        $this->assertEquals($initialMarkings, $place->getTokens());
    }

    public function testCanPopToken(): void
    {
        $placeId = 'place-id';
        $instanceToken = BasicToken::create('124', 11);
        $initialMarkings = [
            BasicToken::create('123', 10),
            $instanceToken,

            BasicToken::create('125', 12),
        ];
        $place = Place::create($placeId, $initialMarkings);
        $arcEnablementMock = $this->createMock(ArcEnablementInterface::class);
        $arcEnablementMock->expects($this->exactly(2))->method('acceptsToken')->with($instanceToken->unwrap())->willReturn(true);
        $expextedTokensAfterPop = [
            BasicToken::create('123', 10),
            BasicToken::create('125', 12),
        ];

        $this->assertTrue($place->canEnable($instanceToken->getInstanceId(), $arcEnablementMock));
        $poppedTokenOption = $place->pop($instanceToken->getInstanceId(), $arcEnablementMock);
        $this->assertTrue($poppedTokenOption->isSome());
        $this->assertEquals($instanceToken->unwrap(), $poppedTokenOption->unwrap($this->createClosureNotCalled()));
        $this->assertEquals($expextedTokensAfterPop, $place->getTokens());
    }

    public function testCanPushToken(): void
    {
        $placeId = 'place-id';
        $instanceToken = BasicToken::create('124', 11);
        $initialMarkings = [
            BasicToken::create('123', 10),
            BasicToken::create('125', 12),
        ];
        $place = Place::create($placeId, $initialMarkings);

        $expextedTokensAfterPop = [
            BasicToken::create('123', 10),
            BasicToken::create('125', 12),
            $instanceToken,
        ];

        $place->push($instanceToken);
        $this->assertEquals($expextedTokensAfterPop, $place->getTokens());
    }
}
