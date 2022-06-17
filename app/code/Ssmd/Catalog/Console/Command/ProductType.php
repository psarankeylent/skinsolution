<?php
declare(strict_types=1);

namespace Ssmd\Catalog\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Ssmd\Catalog\Helper\Product;

class ProductType extends Command
{

    const NAME_ARGUMENT = "name";
    const NAME_OPTION = "option";

    /**
     * @var Product
     */
    private $helper;

    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    public function __construct(
        Product $helper,
        \Magento\Framework\App\State $state
    )
    {
        $this->helper = $helper;
        $this->state = $state;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
        $this->helper->updateProductTypeAttribute();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("ssmd:autofill_product_type");
        $this->setDescription("Prepopulates the Product Type attribute of the Product ( implemented for the purpose of filtering products by bundle/simple products on frontend with the help of GraphQL");

        parent::configure();
    }
}

