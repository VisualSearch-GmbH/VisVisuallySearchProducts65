<?php declare(strict_types=1);
/*
 * (c) VisualSearch GmbH <office@visualsearch.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with the source code.
 */

namespace Vis\VisuallySearchProducts\Api\Client;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Vis\VisuallySearchProducts\Api\Authentication\OAuthCredentials;
use Vis\VisuallySearchProducts\Api\RequestHeader;

class VisuallySearchClient extends AbstractClient implements VisuallySearchClientInterface
{
    /**
     * @param OAuthCredentials $credentials
     * @param LoggerInterface $logger
     */
    public function __construct(
        OAuthCredentials $credentials,
        LoggerInterface $logger
    ) {
        $client = new Client([
            'base_uri' => $credentials->getBaseUrl(),
            RequestOptions::ALLOW_REDIRECTS => false,
            RequestOptions::TIMEOUT => 30,
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Charset' => 'UTF-8',
                RequestHeader::VIS_API_KEY_HEADER => $credentials->getApiKey()
            ]
        ]);

        parent::__construct($client, $logger);
    }

    /**
     * @param string $resourceUri
     * @param array $query
     * @param array $headers
     * @return array
     */
    public function sendGetRequest(string $resourceUri, array $query = [], array $headers = []): array
    {
        $options = [
            RequestOptions::HEADERS => $headers,
        ];

        if (!empty($query)) {
            $options[RequestOptions::QUERY] = $query;
        }

        return $this->get($resourceUri, $options);
    }

    /**
     * @param string $resourceUri
     * @param array $data
     * @param array $headers
     * @return array
     */
    public function sendPostRequest(string $resourceUri, array $data = [], array $headers = []): array
    {
        $options = [
            RequestOptions::HEADERS => $headers,
            RequestOptions::BODY => \json_encode($data)
        ];

        return $this->post($resourceUri, $options);
    }
}
