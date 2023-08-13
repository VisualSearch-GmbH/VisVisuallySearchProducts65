<?php declare(strict_types=1);
/*
 * (c) VisualSearch GmbH <office@visualsearch.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with the source code.
 */

namespace Vis\VisuallySearchProducts\Api\Client;

use Psr\Log\LoggerInterface;
use Vis\VisuallySearchProducts\Api\Authentication\OAuthCredentials;
use Vis\VisuallySearchProducts\Api\BaseUrl;
use Vis\VisuallySearchProducts\Service\HelperServiceInterface;

/**
 *
 */
class VisuallySearchClientFactory implements VisuallySearchClientFactoryInterface
{
    /**
     * @var HelperServiceInterface
     */
    private $helperService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param HelperServiceInterface $helperService
     * @param LoggerInterface $logger
     */
    public function __construct(
        HelperServiceInterface $helperService,
        LoggerInterface $logger
    ) {
        $this->helperService = $helperService;
        $this->logger = $logger;
    }

    /**
     * @return VisuallySearchClient
     */
    public function createClient(): VisuallySearchClient
    {
        $credentials = $this->createCredentialsObject();

        return new VisuallySearchClient($credentials, $this->logger);
    }

    /**
     * @return OAuthCredentials
     */
    private function createCredentialsObject(): OAuthCredentials
    {
        $apiKey = $this->helperService->getPluginConfig('apiKey');

        $credentials = new OAuthCredentials();
        $credentials->setBaseUrl(BaseUrl::LIVE);
        $credentials->setApiKey($apiKey);

        return $credentials;
    }
}
