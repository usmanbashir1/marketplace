<?php
namespace WeltPixel\ProductPage\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use WeltPixel\ProductPage\Model\ProductPageFactory;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;


    /**
     * @var ProductPageFactory
     */
    private $productPageFactory;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param ProductPageFactory $productPageFactory
     */
    public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager,
                                ProductPageFactory $productPageFactory)
    {
        $this->storeManager = $storeManager;
        $this->productPageFactory = $productPageFactory;
    }

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if ( version_compare( $context->getVersion(), '1.1.3' ) < 0 ) {
            $storeCollection = $this->storeManager->getStores();

            $versionsData = [
                [
                    'version_id' => 1,
                    'values'     => '[{"id":"weltpixel_product_page_general_display_swatch_tooltip","value":"1"},{"id":"weltpixel_product_page_general_image_area_width","value":"60%"},{"id":"weltpixel_product_page_general_move_description_tabs_under_info_area","value":"0"},{"id":"weltpixel_product_page_general_product_info_area_width","value":"35%"},{"id":"weltpixel_product_page_general_qty_select_maxvalue","value":"10"},{"id":"weltpixel_product_page_general_qty_type","value":"select"},{"id":"weltpixel_product_page_general_remove_breadcrumbs","value":"0"},{"id":"weltpixel_product_page_general_remove_compare","value":"1"},{"id":"weltpixel_product_page_general_remove_email","value":"0"},{"id":"weltpixel_product_page_general_remove_qty_box","value":"0"},{"id":"weltpixel_product_page_general_remove_sku","value":"0"},{"id":"weltpixel_product_page_general_remove_stock_availability","value":"0"},{"id":"weltpixel_product_page_general_remove_wishlist","value":"1"},{"id":"weltpixel_product_page_general_tabs_layout","value":"tab"},{"id":"weltpixel_product_page_general_tabs_version","value":"0"},{"id":"weltpixel_product_page_gallery_arrows","value":"true"},{"id":"weltpixel_product_page_gallery_caption","value":"false"},{"id":"weltpixel_product_page_gallery_keyboard","value":"true"},{"id":"weltpixel_product_page_gallery_loop","value":"true"},{"id":"weltpixel_product_page_gallery_nav","value":"thumbs"},{"id":"weltpixel_product_page_gallery_navarrows","value":"true"},{"id":"weltpixel_product_page_gallery_navdir","value":"vertical"},{"id":"weltpixel_product_page_gallery_navtype","value":"slides"},{"id":"weltpixel_product_page_gallery_transition_duration","value":"500"},{"id":"weltpixel_product_page_gallery_transition_effect","value":"slide"},{"id":"weltpixel_product_page_gallery_arrows_bg","value":null},{"id":"weltpixel_product_page_fullscreen_allowfullscreen","value":"false"},{"id":"weltpixel_product_page_magnifier_enabled","value":"false"},{"id":"weltpixel_product_page_magnifier_eventtype","value":"hover"},{"id":"weltpixel_product_page_magnifier_fullscreenzoom","value":"5"},{"id":"weltpixel_product_page_magnifier_height","value":null},{"id":"weltpixel_product_page_magnifier_left","value":null},{"id":"weltpixel_product_page_magnifier_top","value":null},{"id":"weltpixel_product_page_magnifier_width","value":null},{"id":"weltpixel_product_page_swatch_font_size","value":"10px"},{"id":"weltpixel_product_page_swatch_height","value":"25px"},{"id":"weltpixel_product_page_swatch_line_height","value":"24px"},{"id":"weltpixel_product_page_swatch_radius","value":"25px"},{"id":"weltpixel_product_page_swatch_width","value":"25px"},{"id":"weltpixel_product_page_css_tab_active_background","value":"#FFFFFF"},{"id":"weltpixel_product_page_css_tab_background","value":"#FFFFFF"},{"id":"weltpixel_product_page_css_tab_container_padding","value":"100px"},{"id":"weltpixel_product_page_css_tab_text_active_color","value":"#000000"},{"id":"weltpixel_product_page_css_tab_text_color","value":"#000000"},{"id":"weltpixel_product_page_css_thumbnail_border","value":"#CCCCCC"},{"id":"weltpixel_product_page_css_page_background_color","value":"#FFFFFF"},{"id":"weltpixel_product_page_css_page_background_color_top_v3","value":"#FFFFFF"},{"id":"weltpixel_product_page_css_page_background_color_bottom_v3","value":"#FFFFFF"},{"id":"weltpixel_product_page_images_main_image_height","value":"1000"},{"id":"weltpixel_product_page_images_main_image_width","value":"1250"},{"id":"weltpixel_product_page_images_thumb_image_height","value":null},{"id":"weltpixel_product_page_images_thumb_image_width","value":null}]'
                ],
                [
                    'version_id' => 2,
                    'values'     => '[{"id":"weltpixel_product_page_general_display_swatch_tooltip","value":"1"},{"id":"weltpixel_product_page_general_image_area_width","value":"60%"},{"id":"weltpixel_product_page_general_move_description_tabs_under_info_area","value":"1"},{"id":"weltpixel_product_page_general_product_info_area_width","value":"35%"},{"id":"weltpixel_product_page_general_qty_select_maxvalue","value":"10"},{"id":"weltpixel_product_page_general_qty_type","value":"select"},{"id":"weltpixel_product_page_general_remove_breadcrumbs","value":"0"},{"id":"weltpixel_product_page_general_remove_compare","value":"1"},{"id":"weltpixel_product_page_general_remove_email","value":"0"},{"id":"weltpixel_product_page_general_remove_qty_box","value":"0"},{"id":"weltpixel_product_page_general_remove_sku","value":"0"},{"id":"weltpixel_product_page_general_remove_stock_availability","value":"0"},{"id":"weltpixel_product_page_general_remove_wishlist","value":"1"},{"id":"weltpixel_product_page_general_tabs_layout","value":"accordion"},{"id":"weltpixel_product_page_general_accordion_version","value":"1"},{"id":"weltpixel_product_page_gallery_arrows","value":"true"},{"id":"weltpixel_product_page_gallery_caption","value":"false"},{"id":"weltpixel_product_page_gallery_keyboard","value":"true"},{"id":"weltpixel_product_page_gallery_loop","value":"true"},{"id":"weltpixel_product_page_gallery_nav","value":"thumbs"},{"id":"weltpixel_product_page_gallery_navarrows","value":"true"},{"id":"weltpixel_product_page_gallery_navdir","value":"vertical"},{"id":"weltpixel_product_page_gallery_navtype","value":"slides"},{"id":"weltpixel_product_page_gallery_transition_duration","value":"500"},{"id":"weltpixel_product_page_gallery_transition_effect","value":"slide"},{"id":"weltpixel_product_page_gallery_arrows_bg","value":null},{"id":"weltpixel_product_page_fullscreen_allowfullscreen","value":"true"},{"id":"weltpixel_product_page_magnifier_enabled","value":"false"},{"id":"weltpixel_product_page_magnifier_eventtype","value":"hover"},{"id":"weltpixel_product_page_magnifier_fullscreenzoom","value":"5"},{"id":"weltpixel_product_page_magnifier_height","value":null},{"id":"weltpixel_product_page_magnifier_left","value":null},{"id":"weltpixel_product_page_magnifier_top","value":null},{"id":"weltpixel_product_page_magnifier_width","value":null},{"id":"weltpixel_product_page_swatch_font_size","value":"10px"},{"id":"weltpixel_product_page_swatch_height","value":"25px"},{"id":"weltpixel_product_page_swatch_line_height","value":"24px"},{"id":"weltpixel_product_page_swatch_radius","value":"25px"},{"id":"weltpixel_product_page_swatch_width","value":"25px"},{"id":"weltpixel_product_page_css_tab_active_background","value":"#F9F7FC"},{"id":"weltpixel_product_page_css_tab_background","value":"#F9F7FC"},{"id":"weltpixel_product_page_css_tab_container_padding","value":"100px"},{"id":"weltpixel_product_page_css_tab_text_active_color","value":"#000000"},{"id":"weltpixel_product_page_css_tab_text_color","value":"#000000"},{"id":"weltpixel_product_page_css_thumbnail_border","value":"#FFFFFF"},{"id":"weltpixel_product_page_css_page_background_color","value":"#F9F7FC"},{"id":"weltpixel_product_page_css_page_background_color_top_v3","value":"#FFFFFF"},{"id":"weltpixel_product_page_css_page_background_color_bottom_v3","value":"#FFFFFF"},{"id":"weltpixel_product_page_images_main_image_height","value":"1000"},{"id":"weltpixel_product_page_images_main_image_width","value":"1250"},{"id":"weltpixel_product_page_images_thumb_image_height","value":null},{"id":"weltpixel_product_page_images_thumb_image_width","value":null}]'
                ],
                [
                    'version_id' => 3,
                    'values'     => '[{"id":"weltpixel_product_page_general_display_swatch_tooltip","value":"1"},{"id":"weltpixel_product_page_general_image_area_width","value":"60%"},{"id":"weltpixel_product_page_general_move_description_tabs_under_info_area","value":"0"},{"id":"weltpixel_product_page_general_product_info_area_width","value":"35%"},{"id":"weltpixel_product_page_general_qty_select_maxvalue","value":"10"},{"id":"weltpixel_product_page_general_qty_type","value":"select"},{"id":"weltpixel_product_page_general_remove_breadcrumbs","value":"0"},{"id":"weltpixel_product_page_general_remove_compare","value":"1"},{"id":"weltpixel_product_page_general_remove_email","value":"0"},{"id":"weltpixel_product_page_general_remove_qty_box","value":"0"},{"id":"weltpixel_product_page_general_remove_sku","value":"0"},{"id":"weltpixel_product_page_general_remove_stock_availability","value":"0"},{"id":"weltpixel_product_page_general_remove_wishlist","value":"1"},{"id":"weltpixel_product_page_general_tabs_layout","value":"tab"},{"id":"weltpixel_product_page_general_tabs_version","value":"1"},{"id":"weltpixel_product_page_gallery_arrows","value":"true"},{"id":"weltpixel_product_page_gallery_caption","value":"false"},{"id":"weltpixel_product_page_gallery_keyboard","value":"true"},{"id":"weltpixel_product_page_gallery_loop","value":"true"},{"id":"weltpixel_product_page_gallery_nav","value":"thumbs"},{"id":"weltpixel_product_page_gallery_navarrows","value":"false"},{"id":"weltpixel_product_page_gallery_navdir","value":"vertical"},{"id":"weltpixel_product_page_gallery_navtype","value":"thumbs"},{"id":"weltpixel_product_page_gallery_transition_duration","value":"500"},{"id":"weltpixel_product_page_gallery_transition_effect","value":"slide"},{"id":"weltpixel_product_page_gallery_arrows_bg","value":null},{"id":"weltpixel_product_page_fullscreen_allowfullscreen","value":"false"},{"id":"weltpixel_product_page_magnifier_enabled","value":"false"},{"id":"weltpixel_product_page_magnifier_eventtype","value":"hover"},{"id":"weltpixel_product_page_magnifier_fullscreenzoom","value":"5"},{"id":"weltpixel_product_page_magnifier_height","value":null},{"id":"weltpixel_product_page_magnifier_left","value":null},{"id":"weltpixel_product_page_magnifier_top","value":null},{"id":"weltpixel_product_page_magnifier_width","value":null},{"id":"weltpixel_product_page_swatch_font_size","value":"10px"},{"id":"weltpixel_product_page_swatch_height","value":"25px"},{"id":"weltpixel_product_page_swatch_line_height","value":"24px"},{"id":"weltpixel_product_page_swatch_radius","value":"25px"},{"id":"weltpixel_product_page_swatch_width","value":"25px"},{"id":"weltpixel_product_page_css_tab_active_background","value":"#FFFFFF"},{"id":"weltpixel_product_page_css_tab_background","value":"#FFFFFF"},{"id":"weltpixel_product_page_css_tab_container_padding","value":"100px"},{"id":"weltpixel_product_page_css_tab_text_active_color","value":"#000000"},{"id":"weltpixel_product_page_css_tab_text_color","value":"#000000"},{"id":"weltpixel_product_page_css_thumbnail_border","value":"#CCCCCC"},{"id":"weltpixel_product_page_css_page_background_color","value":"#F9F7FC"},{"id":"weltpixel_product_page_css_page_background_color_top_v3","value":"#F9F7FC"},{"id":"weltpixel_product_page_css_page_background_color_bottom_v3","value":"#FFFFFF"},{"id":"weltpixel_product_page_images_main_image_height","value":"1000"},{"id":"weltpixel_product_page_images_main_image_width","value":"1250"},{"id":"weltpixel_product_page_images_thumb_image_height","value":"110"},{"id":"weltpixel_product_page_images_thumb_image_width","value":"110"}]'
                ],
                [
                    'version_id' => 4,
                    'values'     => '[{"id":"weltpixel_product_page_general_display_swatch_tooltip","value":"1"},{"id":"weltpixel_product_page_general_image_area_width","value":"60%"},{"id":"weltpixel_product_page_general_move_description_tabs_under_info_area","value":"1"},{"id":"weltpixel_product_page_general_product_info_area_width","value":"25%"},{"id":"weltpixel_product_page_general_qty_select_maxvalue","value":"10"},{"id":"weltpixel_product_page_general_qty_type","value":"select"},{"id":"weltpixel_product_page_general_remove_breadcrumbs","value":"0"},{"id":"weltpixel_product_page_general_remove_compare","value":"1"},{"id":"weltpixel_product_page_general_remove_email","value":"0"},{"id":"weltpixel_product_page_general_remove_qty_box","value":"0"},{"id":"weltpixel_product_page_general_remove_sku","value":"0"},{"id":"weltpixel_product_page_general_remove_stock_availability","value":"0"},{"id":"weltpixel_product_page_general_remove_wishlist","value":"1"},{"id":"weltpixel_product_page_general_tabs_layout","value":"accordion"},{"id":"weltpixel_product_page_general_accordion_version","value":"0"},{"id":"weltpixel_product_page_gallery_arrows","value":"true"},{"id":"weltpixel_product_page_gallery_caption","value":"false"},{"id":"weltpixel_product_page_gallery_keyboard","value":"true"},{"id":"weltpixel_product_page_gallery_loop","value":"true"},{"id":"weltpixel_product_page_gallery_nav","value":"false"},{"id":"weltpixel_product_page_gallery_navarrows","value":"true"},{"id":"weltpixel_product_page_gallery_navdir","value":"vertical"},{"id":"weltpixel_product_page_gallery_navtype","value":"slides"},{"id":"weltpixel_product_page_gallery_transition_duration","value":"500"},{"id":"weltpixel_product_page_gallery_transition_effect","value":"slide"},{"id":"weltpixel_product_page_gallery_arrows_bg","value":null},{"id":"weltpixel_product_page_fullscreen_allowfullscreen","value":"false"},{"id":"weltpixel_product_page_magnifier_enabled","value":"false"},{"id":"weltpixel_product_page_magnifier_eventtype","value":"hover"},{"id":"weltpixel_product_page_magnifier_fullscreenzoom","value":"5"},{"id":"weltpixel_product_page_magnifier_height","value":null},{"id":"weltpixel_product_page_magnifier_left","value":null},{"id":"weltpixel_product_page_magnifier_top","value":null},{"id":"weltpixel_product_page_magnifier_width","value":null},{"id":"weltpixel_product_page_swatch_font_size","value":"10px"},{"id":"weltpixel_product_page_swatch_height","value":"25px"},{"id":"weltpixel_product_page_swatch_line_height","value":"24px"},{"id":"weltpixel_product_page_swatch_radius","value":"25px"},{"id":"weltpixel_product_page_swatch_width","value":"25px"},{"id":"weltpixel_product_page_css_tab_active_background","value":"#F3F3F3"},{"id":"weltpixel_product_page_css_tab_background","value":"#F3F3F3"},{"id":"weltpixel_product_page_css_tab_container_padding","value":"20px"},{"id":"weltpixel_product_page_css_tab_text_active_color","value":"#000000"},{"id":"weltpixel_product_page_css_tab_text_color","value":"#000000"},{"id":"weltpixel_product_page_css_thumbnail_border","value":"#000000"},{"id":"weltpixel_product_page_css_page_background_color","value":"#F9F7FC"},{"id":"weltpixel_product_page_css_page_background_color_top_v3","value":"#F3F3F3"},{"id":"weltpixel_product_page_css_page_background_color_bottom_v3","value":"#F3F3F3"},{"id":"weltpixel_product_page_images_main_image_height","value":"1000"},{"id":"weltpixel_product_page_images_main_image_width","value":"1000"},{"id":"weltpixel_product_page_images_thumb_image_height","value":null},{"id":"weltpixel_product_page_images_thumb_image_width","value":null}]'
                ],
            ];

            foreach ($storeCollection as $store) {
                foreach ($versionsData as $data) {
                    $data['store_id'] = $store->getData('store_id');
                    $productPage = $this->createVersion();
                    $productPage->loadByVersionAndStore($data['version_id'], $data['store_id']);
                    if (!$productPage->getEntityId()) {
                        $productPage->setData($data)->save();
                    }
                }
            }
        }
        $setup->endSetup();
    }

    /**
     * Create ProductPage version
     *
     * @return \WeltPixel\ProductPage\Model\ProductPage
     */
    public function createVersion()
    {
        return $this->productPageFactory->create();
    }
}
