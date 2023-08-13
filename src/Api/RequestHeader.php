<?php declare(strict_types=1);
/*
 * (c) VisualSearch GmbH <office@visualsearch.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with the source code.
 */

namespace Vis\VisuallySearchProducts\Api;

final class RequestHeader
{
    public const VIS_API_KEY_HEADER = 'Vis-API-KEY';
    public const VIS_SYSTEM_HOSTS_HEADER = 'Vis-SYSTEM-HOSTS';
    public const VIS_SYSTEM_KEY_HEADER = 'Vis-SYSTEM-KEY';
    public const VIS_SYSTEM_TYPE_HEADER = 'Vis-SYSTEM-TYPE';
    public const VIS_SOLUTION_TYPE_HEADER = 'Vis-SOLUTION-TYPE';

    public const HEADER_SYSTEM_TYPE_SHOPWARE6 = 'shopware6';
    public const HEADER_SOLUTION_TYPE_SEARCH = 'search';
    public const HEADER_SOLUTION_TYPE_SIMILAR = 'similar';

    private function __construct()
    {
    }
}
