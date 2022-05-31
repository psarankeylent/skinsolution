<?php

declare(strict_types=1);

namespace Ssmd\GraphqlChanges\Model\Resolver;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Model\Configuration;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Store\Model\ScopeInterface;

class LowQtyStock implements ResolverInterface
{ /**
 * @var ScopeConfigInterface
 */
    private $scopeConfig;

    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StockRegistryInterface $stockRegistry
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StockRegistryInterface $stockRegistry
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->stockRegistry = $stockRegistry;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!array_key_exists('model', $value) || !$value['model'] instanceof ProductInterface) {
            throw new LocalizedException(__('"model" value should be specified'));
        }

        /* @var $product ProductInterface */
        $product = $value['model'];
        return $this->isLowQtyProduct($product);
    }

    /**
     * @param ProductInterface $product
     * @return bool|null
     */
    private function isLowQtyProduct(ProductInterface $product): ?bool
    {
        $thresholdQty = (float)$this->scopeConfig->getValue(
            Configuration::XML_PATH_STOCK_THRESHOLD_QTY,
            ScopeInterface::SCOPE_STORE
        );
        if ($thresholdQty === 0) {
            return false;
        }

        $stockItem = $this->stockRegistry->getStockItem($product->getId());

        $stockCurrentQty = $this->stockRegistry->getStockStatus(
            $product->getId(),
            $product->getStore()->getWebsiteId()
        )->getQty();

        $stockLeft = $stockCurrentQty - $stockItem->getMinQty();

        $thresholdQty = (float)$this->scopeConfig->getValue(
            Configuration::XML_PATH_STOCK_THRESHOLD_QTY,
            ScopeInterface::SCOPE_STORE
        );

        if ($stockCurrentQty > 0 && $stockLeft <= $thresholdQty) {
            return true;
        }

        return false;
    }
}
