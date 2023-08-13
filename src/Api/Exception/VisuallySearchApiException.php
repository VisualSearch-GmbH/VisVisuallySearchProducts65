<?php declare(strict_types=1);
/*
 * (c) VisualSearch GmbH <office@visualsearch.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with the source code.
 */

namespace Vis\VisuallySearchProducts\Api\Exception;

use Shopware\Core\Framework\ShopwareHttpException;
use Symfony\Component\HttpFoundation\Response;

class VisuallySearchApiException extends ShopwareHttpException
{
    /**
     * @var int|null
     */
    private $visuallySearchApiStatusCode;

    /**
     * @param string $message
     * @param int $visuallySearchApiStatusCode
     */
    public function __construct(
        string $message,
        int $visuallySearchApiStatusCode = Response::HTTP_INTERNAL_SERVER_ERROR
    ) {
        parent::__construct(
            'An error occurred with the following message: {{ message }}',
            ['message' => $message]
        );
        $this->visuallySearchApiStatusCode = $visuallySearchApiStatusCode;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->visuallySearchApiStatusCode ?? parent::getStatusCode();
    }

    /**
     * @return string
     */
    public function getErrorCode(): string
    {
        return 'VIS_VISUALLYSEARCH__API_EXCEPTION';
    }
}
