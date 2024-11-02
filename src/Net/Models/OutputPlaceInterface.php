<?php

namespace Zodimo\PN\Net\Models;

/**
 * @template TOKENCOLOUR
 */
interface OutputPlaceInterface
{
    /**
     * @param TOKENCOLOUR $token
     * @return void
     */
    public function push($token):void;
}