<?php declare(strict_types=1);
/*
 * (c) VisualSearch GmbH <office@visualsearch.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with the source code.
 */

namespace Vis\VisuallySearchProducts\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface HelperServiceInterface
{
    /**
     * @param string $name
     * @return mixed
     */
    public function getPluginConfig(string $name = '');

    /**
     * @param UploadedFile|null $image
     * @return string
     */
    public function imageToBase64(?UploadedFile $image): string;
}