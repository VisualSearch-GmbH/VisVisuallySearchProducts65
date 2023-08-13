<?php declare(strict_types=1);
/*
 * (c) VisualSearch GmbH <office@visualsearch.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with the source code.
 */

namespace Vis\VisuallySearchProducts\Api\Client;

interface VisuallySearchClientInterface
{

    /**
     * @param string $resourceUri
     * @param array $query
     * @param array $headers
     * @return array
     */
    public function sendGetRequest(string $resourceUri, array $query = [], array $headers = []): array;

    /**
     * @param string $resourceUri
     * @param array $data
     * @param array $headers
     * @return array
     */
    public function sendPostRequest(string $resourceUri, array $data = [], array $headers = []): array;
}
