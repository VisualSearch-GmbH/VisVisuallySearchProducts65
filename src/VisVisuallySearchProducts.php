<?php declare(strict_types=1);
/*
 * (c) VisualSearch GmbH <office@visualsearch.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with the source code.
 */

namespace Vis\VisuallySearchProducts;

use Shopware\Core\Framework\Api\Util\AccessKeyHelper;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\System\Integration\IntegrationDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;
use Vis\VisuallySearchProducts\Util\SwHosts;

class VisVisuallySearchProducts extends Plugin
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $shopwareVersion = $container->getParameter('kernel.shopware_version');

        switch (true) {
            case version_compare($shopwareVersion, '6.4', '<'):
                $prefix = '63';
                break;
            case version_compare($shopwareVersion, '6.5', '<'):
                $prefix = '64';
                break;
            case version_compare($shopwareVersion, '6.6', '<'):
                $prefix = '65';
                break;
            case version_compare($shopwareVersion, '6.6', '>='):
            default:
                $prefix = '66';
        }

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . "/DependencyInjection/{$prefix}/"));
        $loader->load('decorators.xml');
    }

    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);

        $this->createRole();
        $keys = $this->createIntegration();

        // util class retrieve hosts
        $retrieveHosts = new SwHosts($this->container->get('sales_channel.repository'));
        $hosts = $retrieveHosts->getLocalHosts();

        // send notification
        $this->notification($hosts, $keys, 'shopware6;install');
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        if (!$uninstallContext->keepUserData()) {
            $this->deleteRole();
            $this->deleteIntegration();
        }

        // util class retrieve hosts
        $retrieveHosts = new SwHosts($this->container->get('sales_channel.repository'));
        $hosts = $retrieveHosts->getLocalHosts();

        // send notification
        $this->notification($hosts, '', 'shopware6;uninstall');
    }

    public function notification($hosts, $keys, $type): void
    {
        $url = 'https://api.visualsearch.wien/installation_notify';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Vis-API-KEY: marketing',
            'Vis-SYSTEM-HOSTS:' . $hosts,
            'Vis-SYSTEM-KEY:' . $keys,
            'Vis-SYSTEM-TYPE: VisVisuallySearchProducts;' . $type,
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }

    public function createRole()
    {
        /** @var EntityRepository $aclRoleRepository */
        $aclRoleRepository = $this->container->get('acl_role.repository');

        $aclRoleObject = [
            'id' => '6082295f733345d5a37b8c9c4da8820a',
            'name' => 'VisuallySearchProducts',
            'description' => 'Berechtigung Produkte zu bearbeiten / Permission to edit products',
            'privileges' => [
                "category:read",
                "cms_block:read",
                "cms_page:read",
                "cms_section:read",
                "cms_slot:read",
                "currency:read",
                "custom_field:read",
                "custom_field_set:read",
                "custom_field_set_relation:read",
                "delivery_time:read",
                "document_base_config:read",
                "landing_page:read", "language:read",
                "locale:read", "mail_template:read",
                "mail_template_media:read",
                "main_category:create",
                "main_category:read",
                "media:create",
                "media:read",
                "media:update",
                "media_default_folder:create",
                "media_default_folder:read",
                "media_default_folder:update",
                "media_folder:create",
                "media_folder:delete",
                "media_folder:read",
                "media_folder:update",
                "media_folder_configuration:create",
                "media_folder_configuration:delete",
                "media_folder_configuration:read",
                "media_folder_configuration:update",
                "media_folder_configuration_media_thumbnail_size:create",
                "media_folder_configuration_media_thumbnail_size:delete",
                "media_folder_configuration_media_thumbnail_size:update",
                "media_thumbnail_size:create",
                "media_thumbnail_size:delete",
                "media_thumbnail_size:read",
                "media_thumbnail_size:update",
                "message_queue_stats:read",
                "number_range:read",
                "number_range_type:read",
                "payment_method:read",
                "product.editor",
                "product.viewer",
                "product:read",
                "product:update",
                "product_category:create",
                "product_category:delete",
                "product_category:read",
                "product_configurator_setting:create",
                "product_configurator_setting:delete",
                "product_configurator_setting:read",
                "product_configurator_setting:update",
                "product_cross_selling:create",
                "product_cross_selling:delete",
                "product_cross_selling:read",
                "product_cross_selling_assigned_products:create",
                "product_cross_selling_assigned_products:delete",
                "product_cross_selling_assigned_products:read",
                "product_feature_set:create",
                "product_feature_set:delete",
                "product_feature_set:read",
                "product_feature_set:update",
                "product_manufacturer:create",
                "product_manufacturer:delete",
                "product_manufacturer:read",
                "product_media:create",
                "product_media:delete",
                "product_media:read",
                "product_option:create",
                "product_price:create",
                "product_price:delete",
                "product_price:read",
                "product_property:create",
                "product_property:delete",
                "product_property:read",
                "product_review:create",
                "product_review:delete",
                "product_review:read",
                "product_sorting:read",
                "product_stream:create",
                "product_stream:delete",
                "product_stream:read",
                "product_stream_filter:read",
                "product_tag:create",
                "product_tag:delete",
                "product_tag:read",
                "product_visibility:create",
                "product_visibility:delete",
                "product_visibility:read",
                "property_group:read",
                "property_group_option:read",
                "review:delete",
                "review:read",
                "rule:read",
                "sales_channel:read",
                "sales_channel_type:read",
                "seo_url:read",
                "shipping_method:read",
                "tag:create",
                "tag:read",
                "tax:read",
                "unit:read",
                "user:read",
                "user_config:create",
                "user_config:read",
                "user_config:update"
            ]
        ];

        $aclRoleRepository->upsert([$aclRoleObject], Context::createDefaultContext());
    }

    public function deleteRole()
    {
        /** @var EntityRepository $aclRoleRepository */
        $aclRoleRepository = $this->container->get('acl_role.repository');

        $aclRoleRepository->delete([['id' => '6082295f733345d5a37b8c9c4da8820a']], Context::createDefaultContext());
    }

    public function createIntegration(): string
    {
        /** @var EntityRepository $integrationRepository */
        $integrationRepository = $this->container->get('integration.repository');

        $access_key = AccessKeyHelper::generateAccessKey('integration');
        $secret_access_key = AccessKeyHelper::generateSecretAccessKey();

        /** @var IntegrationDefinition $integrationObject */
        $integrationObject = [
            'id' => '1af0f21128c24cf3b4c74504d0d6c1f3',
            'writeAccess' => false,
            'accessKey' => $access_key,
            'secretAccessKey' => $secret_access_key,
            'label' => 'VisuallySearchProducts',
            'admin' => false,
            'aclRoles' => [['id' => '6082295f733345d5a37b8c9c4da8820a']]
        ];

        $integrationRepository->upsert([$integrationObject], Context::createDefaultContext());

        return implode(";", [$access_key, $secret_access_key]);
    }

    public function deleteIntegration()
    {
        /** @var EntityRepository $integrationRepository */
        $integrationRepository = $this->container->get('integration.repository');

        $integrationRepository->delete([['id' => '1af0f21128c24cf3b4c74504d0d6c1f3']], Context::createDefaultContext());
    }
}
