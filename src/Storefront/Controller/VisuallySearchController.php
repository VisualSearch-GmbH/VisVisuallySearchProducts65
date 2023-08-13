<?php declare(strict_types=1);
/*
 * (c) VisualSearch GmbH <office@visualsearch.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with the source code.
 */

namespace Vis\VisuallySearchProducts\Storefront\Controller;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Vis\VisuallySearchProducts\Api\Exception\VisuallySearchApiException;
use Vis\VisuallySearchProducts\Service\HelperServiceInterface;
use Vis\VisuallySearchProducts\Service\VisuallySearchApiServiceInterface;

/**
 * @RouteScope(scopes={"storefront"})
 */
class VisuallySearchController extends AbstractController
{
    /**
     * @var VisuallySearchApiServiceInterface
     */
    private $visuallySearchApiService;

    /**
     * @var HelperServiceInterface
     */
    private $helperService;

    /**
     * @param VisuallySearchApiServiceInterface $visuallySearchApiService
     * @param HelperServiceInterface $helperService
     */
    public function __construct(
        VisuallySearchApiServiceInterface $visuallySearchApiService,
        HelperServiceInterface            $helperService
    )
    {
        $this->visuallySearchApiService = $visuallySearchApiService;
        $this->helperService = $helperService;
    }

    /**
     * @Route("/vis/search", name="frontend.vis.search.page", methods={"POST"})
     */
    public function search(Request $request, Context $context): RedirectResponse
    {
        $image = $request->files->get('image');
        $productIds = $this->searchByFile($image);
        return $this->redirectToRoute('frontend.search.page', [
            'vis' => $productIds,
            'search' => $image->getClientOriginalName()
        ]);
    }

    /**
     * @Route("/vis/url-search", name="frontend.vis.url-search.page", methods={"POST"})
     */
    public function searchByUrl(Request $request, Context $context): RedirectResponse
    {
        $publicDir = $this->container->getParameter('shopware.filesystem.public.config.root');
        $imageUrl = $request->request->get('image');
        $productId = $request->request->get('productId');
        if (empty($productId) || empty($imageUrl)) {
            return $this->redirectToRoute('frontend.search.page', [
                'vis' => []
            ]);
        }
        $parsedUrl = parse_url($imageUrl);
        $imagePath = rtrim($publicDir, '/') . $parsedUrl['path'];
        $image = new UploadedFile($imagePath, basename($parsedUrl['path']), mime_content_type($imagePath));
        $productIds = $this->searchByFile($image);
        array_unshift($productIds, $productId);
        return $this->redirectToRoute('frontend.search.page', [
            'vis' => $productIds,
            'search' => $image->getClientOriginalName()
        ]);
    }

    /**
     * @param string $snippet
     * @param array $parameters
     * @return string
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function trans(string $snippet, array $parameters = []): string
    {
        return $this->container
            ->get('translator')
            ->trans($snippet, $parameters);
    }

    /**
     * @param UploadedFile|null $image
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    private function searchByFile(?UploadedFile $image): array
    {
        $base64 = $this->helperService->imageToBase64($image);
        try {
            $productIds = $this->visuallySearchApiService->searchSingle($base64);
        } catch (VisuallySearchApiException $exception) {
            if ($exception->getStatusCode() === Response::HTTP_FORBIDDEN) {
                $this->addFlash('danger', $this->trans('visVisuallySearchProducts.invalidApiCredentialsErrorMessage'));
            }
            $productIds = [];
        }
        return $productIds;
    }
}
