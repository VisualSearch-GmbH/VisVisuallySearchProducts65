<?php declare(strict_types=1);
/*
 * (c) VisualSearch GmbH <office@visualsearch.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with the source code.
 */

namespace Vis\VisuallySearchProducts\Service;

use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Symfony\Component\HttpFoundation\Response;
use Vis\VisuallySearchProducts\Api\Client\VisuallySearchClientInterface;
use Vis\VisuallySearchProducts\Api\Exception\VisuallySearchApiException;
use Vis\VisuallySearchProducts\Api\RequestHeader;
use Vis\VisuallySearchProducts\Api\RequestUri;

class VisuallySearchApiService implements VisuallySearchApiServiceInterface
{
    /**
     * @var VisuallySearchClientInterface
     */
    private $visuallySearchClient;

    /**
     * @var EntityRepository
     */
    private $salesChannelRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param VisuallySearchClientInterface $visuallySearchClient
     * @param EntityRepository $salesChannelRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        VisuallySearchClientInterface $visuallySearchClient,
        EntityRepository $salesChannelRepository,
        LoggerInterface $logger
    ) {
        $this->salesChannelRepository = $salesChannelRepository;
        $this->visuallySearchClient = $visuallySearchClient;
        $this->logger = $logger;
    }

    /**
     * @param string $image
     * @throw VisuallySearchApiException
     * @return array
     */
    public function searchSingle(string $image): array
    {
        if (empty($image)) {
            return [];
        }
        try {
            $response = $this->visuallySearchClient->sendPostRequest(RequestUri::SEARCH_SINGLE_RESOURCE, [
                'image_data' => $image
            ], [
                RequestHeader::VIS_SYSTEM_HOSTS_HEADER => $this->getSystemHosts(),
                RequestHeader::VIS_SYSTEM_TYPE_HEADER => RequestHeader::HEADER_SYSTEM_TYPE_SHOPWARE6,
                RequestHeader::VIS_SOLUTION_TYPE_HEADER => RequestHeader::HEADER_SOLUTION_TYPE_SEARCH
            ]);
            $message = $response['message'] ?? 'API error';
            $code = $response['code'] ?? Response::HTTP_BAD_REQUEST;
            $result = $response['result'] ?? [];
            if ($code === Response::HTTP_OK && is_array($result)) {
                return $result;
            }

            $this->logger->error($message, [
                'code' => $code
            ]);
            throw new VisuallySearchApiException($message, $code);
        } catch (VisuallySearchApiException $exception) {
            $this->logger->error($exception->getMessage(), [
                'code' => $exception->getStatusCode()
            ]);
            throw $exception;
        }
    }

    /**
     * @param array $products
     * @return string
     */
    public function similarCompute(array $products): string
    {
        try {
            $response = $this->visuallySearchClient->sendPostRequest(RequestUri::SIMILAR_COMPUTE_RESOURCE, [
                'products' => $products
            ], [
                RequestHeader::VIS_SYSTEM_HOSTS_HEADER => $this->getSystemHosts(),
                RequestHeader::VIS_SYSTEM_TYPE_HEADER => RequestHeader::HEADER_SYSTEM_TYPE_SHOPWARE6
            ]);
            return $response['message'];
        } catch (VisuallySearchApiException $exception) {
            $this->logger->error($exception->getMessage(), [
                'code' => $exception->getStatusCode()
            ]);
            return $exception->getMessage();
        }
    }

    /**
     * @return bool
     */
    public function verifyApiKey(): bool
    {
        try {
            $response = $this->visuallySearchClient->sendPostRequest(RequestUri::API_KEY_VERIFY_RESOURCE, [
            ], [
                RequestHeader::VIS_SOLUTION_TYPE_HEADER => RequestHeader::HEADER_SOLUTION_TYPE_SEARCH
            ]);
            if ($response['code'] === Response::HTTP_OK && $response['message'] === "API key ok") {
                return true;
            }
            $this->logger->error($response['message'], [
                'code' => $response['code']
            ]);
        } catch (VisuallySearchApiException $exception) {
            $this->logger->error($exception->getMessage(), [
                'code' => $exception->getStatusCode()
            ]);
        }
        return false;
    }

    /**
     * @return string
     */
    private function getSystemHosts(): string
    {
        $hosts = [];
        $salesChannels = $this->getSalesChannels();

        foreach ($salesChannels->getElements() as $salesChannel) {
            foreach ($salesChannel->getDomains()->getElements() as $element) {
                $hosts[] = $element->getUrl();
            }
        }

        return implode(";", $hosts);
    }

    /**
     * @return EntitySearchResult
     */
    private function getSalesChannels(): EntitySearchResult
    {
        $criteria = new Criteria();
        $criteria->addAssociation('domains');

        return $this->salesChannelRepository->search(
            $criteria,
            Context::createDefaultContext()
        );
    }
}