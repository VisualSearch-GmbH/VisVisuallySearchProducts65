<?php declare(strict_types=1);
/*
 * (c) VisualSearch GmbH <office@visualsearch.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with the source code.
 */

namespace Vis\VisuallySearchProducts\Api\Client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Vis\VisuallySearchProducts\Api\Exception\VisuallySearchApiException;

abstract class AbstractClient
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param ClientInterface $client
     * @param LoggerInterface $logger
     */
    public function __construct(
        ClientInterface $client,
        LoggerInterface $logger
    ) {
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * @param string $uri
     * @param array $options
     * @return array
     */
    protected function post(string $uri, array $options): array
    {
        return $this->request(Request::METHOD_POST, $uri, $options);
    }

    /**
     * @param string $uri
     * @param array $options
     * @return array
     */
    protected function get(string $uri, array $options = []): array
    {
        return $this->request(Request::METHOD_GET, $uri, $options);
    }

    /**
     * @param string $uri
     * @param array $options
     * @return array
     */
    protected function patch(string $uri, array $options): array
    {
        return $this->request(Request::METHOD_PATCH, $uri, $options);
    }

    /**
     * @param string $uri
     * @param array $options
     * @return array
     */
    protected function delete(string $uri, array $options = []): array
    {
        return $this->request(Request::METHOD_DELETE, $uri, $options);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function request(string $method, string $uri, array $options = []): array
    {
        $this->logger->debug(
            'Sending {method} request to {uri} with the following content: {content}',
            [
                'method' => \mb_strtoupper($method),
                'uri' => $uri,
                'content' => $options,
            ]
        );

        try {
            $response = $this->client->request($method, $uri, $options);
            $body = $response->getBody()->getContents();
        } catch (RequestException $requestException) {
            throw $this->handleRequestException($requestException, $options);
        }

        $this->logger->debug(
            'Received {code} from {method} {uri} with following response: {response}',
            [
                'method' => \mb_strtoupper($method),
                'code' => \sprintf('%s %s', $response->getStatusCode(), $response->getReasonPhrase()),
                'uri' => $uri,
                'response' => $body,
            ]
        );

        return \json_decode($body, true) ?? [];
    }

    /**
     * @param RequestException $requestException
     * @param array $data
     * @return VisuallySearchApiException
     */
    private function handleRequestException(RequestException $requestException, array $data): VisuallySearchApiException
    {
        $exceptionMessage = $requestException->getMessage();
        $exceptionResponse = $requestException->getResponse();

        if ($exceptionResponse === null) {
            $this->logger->error($exceptionMessage, [$data]);

            return new VisuallySearchApiException($exceptionMessage);
        }

        $error = \json_decode($exceptionResponse->getBody()->getContents(), true);

        $message = $error['message'];

        $this->logger->error(\sprintf('%s %s', $exceptionMessage, $message), [$error, $data]);

        return new VisuallySearchApiException($message, (int) $requestException->getCode());
    }
}
