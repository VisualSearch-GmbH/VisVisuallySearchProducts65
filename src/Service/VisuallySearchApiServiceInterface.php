<?php declare(strict_types=1);
/*
 * (c) VisualSearch GmbH <office@visualsearch.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with the source code.
 */

namespace Vis\VisuallySearchProducts\Service;

interface VisuallySearchApiServiceInterface
{
    /**
     * @param string $image
     * @return array
     */
    public function searchSingle(string $image): array;

    /**
     * @param array $products
     * @return string
     */
    public function similarCompute(array $products): string;

    /**
     * @return bool
     */
    public function verifyApiKey(): bool;
}