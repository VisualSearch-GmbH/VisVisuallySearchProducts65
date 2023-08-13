<?php declare(strict_types=1);
/*
 * (c) VisualSearch GmbH <office@visualsearch.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with the source code.
 */

namespace Vis\VisuallySearchProducts\Service;

use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class HelperService implements HelperServiceInterface
{
    /**
     * @var SystemConfigService
     */
    private $systemConfigService;

    /**
     * @param SystemConfigService $systemConfigService
     */
    public function __construct(
        SystemConfigService $systemConfigService
    ) {
        $this->systemConfigService = $systemConfigService;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getPluginConfig(string $name = '')
    {
        $domain = $this->systemConfigService->getDomain('VisVisuallySearchProducts');
        $keys = array_map(function ($key) {
            return str_replace('VisVisuallySearchProducts.config.', '', $key);
        }, array_keys($domain));
        $config = array_combine($keys, array_values($domain));

        if (!empty($name)) {
            return $config[$name];
        }

        return $config;
    }

    /**
     * @param UploadedFile|null $image
     * @return string
     */
    public function imageToBase64(?UploadedFile $image): string
    {
        if (!$image instanceof UploadedFile) {
            return '';
        }
        $content = file_get_contents($image->getPathname());
        if (!$content) {
            return '';
        }
        $base64 = base64_encode($content);
        if (!$this->isBase64($base64)) {
            return '';
        }
        $type = $image->getClientMimeType();
        return "data:{$type};base64,{$base64}";
    }

    /**
     * @param string $base64
     * @return bool
     */
    private function isBase64(string $base64): bool
    {
        $str = base64_decode($base64, true);
        if ($str !== false && base64_encode($str) === $base64) {
            return true;
        }
        return false;
    }
}