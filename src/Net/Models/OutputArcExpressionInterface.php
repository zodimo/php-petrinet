<?php

declare(strict_types=1);

namespace Zodimo\PN\Net\Models;

/**
 * @template TIN
 * @template TOUT
 *
 * @template-extends ArcEnablementInterface<TIN>
 */
interface OutputArcExpressionInterface extends ArcEnablementInterface
{
    /**
     * Return possibly transformed token.
     *
     * @param TIN $token
     *
     * @return TOUT
     */
    public function getToken($token);
}
