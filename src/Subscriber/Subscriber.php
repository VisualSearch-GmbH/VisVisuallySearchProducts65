<?php declare(strict_types=1);
/*
 * (c) VisualSearch GmbH <office@visualsearch.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with the source code.
 */

namespace Vis\VisuallySearchProducts\Subscriber;

use Shopware\Core\Framework\Struct\ArrayStruct;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Subscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'Shopware\Storefront\Page\Search\SearchPageLoadedEvent' => ['onSearchPageLoadedEvent']
        ];
    }

    /**
     * @param \Shopware\Storefront\Page\Search\SearchPageLoadedEvent $event
     */
    public function onSearchPageLoadedEvent($event)
    {
        $page = $event->getPage();
        $request = $event->getRequest();

        $vis = $request->query->get('vis');
        if (empty($vis)) {
            return;
        }
        $vis = array_combine(
            array_map(function ($value): string {
                return "vis[{$value}]";
            }, range(0, count($vis) - 1)),
            $vis
        );
        $page->addExtension('vis', new ArrayStruct($vis));
    }
}