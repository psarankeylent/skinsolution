<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Lof
 * @package   Lof\GiftSalesRule
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 * @license   http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\GiftSalesRule\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\ProductMetadataInterface;
/**
 * Helper: Config
 *
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 */
class Config extends AbstractHelper
{
    protected $storeManager;
    /**#@+
     * Config paths.
     */
    const KEY_CONFIG_AUTOMATIC_ADD = 'lof_gift_sales_rule/configuration/automatic_add';
    /**#@-*/

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->storeManager                  = $storeManager;
    }
    /**
     * Get the config value for automatic_add.
     *
     * @return bool
     */
    public function isAutomaticAddEnabled()
    {
        $value = (int) $this->scopeConfig->getValue(self::KEY_CONFIG_AUTOMATIC_ADD);

        return ($value == 1);
    }

    public function getConfig($key)
    {
        $result = $this->scopeConfig->getValue('lof_gift_sales_rule/'.$key);
        return $result;
    }

    public function getButtonLabel(){
        return $this->getConfig("button_design/button_label");
    }

    public function getButtonText(){
        return $this->getConfig("button_design/button_text");
    }

    public function isShowNoticeText(){
        return (int)$this->getConfig("configuration/show_notice_gift");
    }

    public function getNoticeText(){
        return $this->getConfig("configuration/notice_text");
    }

    /**
     * Get Html Block of Free Gift Info Block.
     *
     * @param int $productId
     * @param Magento/Catalog/Model/Product|null $product
     * @return html
     */
    public function getFreegiftInfoBlock($productId, $product = null, $css_class = 'lof-msg-box lof-info')
    {
        $html = '<div class="free-gift-info">';
        $html .= $this->getNoticeText();
        $html .= '</div>';
        return $html;
    }

    /**
     * @return string
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getGiftIcon()
    {
        $image = "";
        $icon= $this->getConfig("configuration/gift_icon");
        if($icon){
            $imageSrc = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'lof/freegift/' . $icon;
            $image = __('<img src="%1" class="freegift-icon" alt="Free Gift" width="20px"/>', $imageSrc);
        }
        return $image;
    }

    /**
     * @param $ver
     * @param string $operator
     *
     * @return mixed
     */
    public function versionCompare($ver, $operator = '>=')
    {
        $productMetadata = $this->objectManager->get(ProductMetadataInterface::class);
        $version = $productMetadata->getVersion(); //will return the magento version

        return version_compare($version, $ver, $operator);
    }
}
