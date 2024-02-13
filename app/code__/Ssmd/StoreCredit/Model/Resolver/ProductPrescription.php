<?php
declare(strict_types=1);

namespace Prescriptions\PrescriptionsGraphql\Model\Resolver;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class ProductPrescription implements ResolverInterface
{
    /**
     * @var AttributeRepositoryInterface
     */
    protected $eavAttributeRepositoryInterface;

    protected $productRepositoryInterface;

    protected $prescriptionsFactory;

    public function __construct(
        \Prescriptions\PrescriptionsCollection\Model\PrescriptionsFactory  $prescriptionsFactory,
        AttributeRepositoryInterface $eavAttributeRepositoryInterface,
        ProductRepositoryInterface $productRepositoryInterface
    ) {
        $this->prescriptionsFactory = $prescriptionsFactory;
        $this->eavAttributeRepositoryInterface = $eavAttributeRepositoryInterface;
        $this->productRepositoryInterface = $productRepositoryInterface;
    }


    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $prescriptionId = $this->productRepositoryInterface
            ->getById($value['model']->getId())
            ->getData('prescription');

        $prescription = $this->prescriptionsFactory->create()
            ->load($prescriptionId, 'id');

        if ($prescription->getId()) {
            return $prescription;
        }
        return null;
    }

}

