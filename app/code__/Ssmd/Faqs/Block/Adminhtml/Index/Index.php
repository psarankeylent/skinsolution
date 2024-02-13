<?php

namespace Ssmd\Faqs\Block\Adminhtml\Index;

class Index extends \Magento\Backend\Block\Widget\Container
{
	protected $faqCollectionFactory;

	protected $faqFactory;

    protected $backendUrlManager;

	public function __construct(
		\Magento\Backend\Block\Widget\Context $context,
		\Ssmd\Faqs\Model\ResourceModel\Faq\CollectionFactory $faqCollectionFactory,
		\Ssmd\Faqs\Model\FaqFactory $faqFactory,
        \Magento\Backend\Model\Url $backendUrlManager,
        array $data = []
	)
	{
		$this->faqCollectionFactory = $faqCollectionFactory;
		$this->faqFactory = $faqFactory;
        $this->backendUrlManager  = $backendUrlManager;

		parent::__construct($context, $data);
	}

	public function getFaqs()
	{
		$faqCollection = $this->faqCollectionFactory->create();
		$faqCollection->addFieldToFilter('status',1);

		return $faqCollection;
	}

    public function getAdminUrl($path, $params = [])
    {
        return $this->backendUrlManager->getRouteUrl($path, $params);
    }
}
