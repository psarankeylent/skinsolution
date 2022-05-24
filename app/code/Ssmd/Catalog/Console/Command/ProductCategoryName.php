<?php
declare(strict_types=1);

namespace Ssmd\Catalog\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Ssmd\Catalog\Helper\Product;

class ProductCategoryName extends Command
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
        $this->helper->updateAllProductsCategoryNameAttribute();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("ssmd:autofill_product_categoryname");
        $this->setDescription("Prepopulates the Category Name attribute of the Product ( implemented for the purpose of search products by category name on frontend with the help of GraphQL");

        parent::configure();
    }
}

