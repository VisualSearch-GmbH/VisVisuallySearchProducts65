<?php declare(strict_types=1);
/*
 * (c) VisualSearch GmbH <office@visualsearch.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with the source code.
 */

namespace Vis\VisuallySearchProducts\Api;

final class RequestUri
{
    public const API_KEY_VERIFY_RESOURCE = 'api_key_verify';
    public const SEARCH_SINGLE_RESOURCE = 'search_single';
    public const SIMILAR_COMPUTE_RESOURCE = 'similar_compute';

    private function __construct()
    {
    }
}
