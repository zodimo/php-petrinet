<?php

declare(strict_types=1);

namespace Zodimo\PN\Net\Models;

interface MarkingsInterface
{
    public function placeMarkings(string $placecId): PlaceMarkingsInterface;
}
