<?php

declare(strict_types=1);

namespace Zodimo\PN\Tests\Unit\Models\Shared;

use PHPUnit\Framework\TestCase;
use Zodimo\PN\Net\Models\Shared\Place;

/**
 * @internal
 *
 * @coversNothing
 */
class PlaceTest extends TestCase
{
    public function testCanCreate(): void
    {
        $id = 'some-id';
        $initialMarkings = [];
        $place = Place::create($id, $initialMarkings);
        $this->assertInstanceOf(Place::class, $place);
        $this->assertEquals($id, $place->getId());
    }
}
