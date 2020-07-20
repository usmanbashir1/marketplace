<?php

namespace WeltPixel\TitleRewrite\Block;

use Magento\Framework\Registry;

class Rewrite
{
	private $registry;
	
	protected $pageConfig;
	protected $currentEntity;
	
	public function __construct(
		Registry $registry,
		\Magento\Backend\Block\Template\Context $context
	)
	{
		$this->registry = $registry;
		$this->pageConfig = $context->getPageConfig();
		$this->currentEntity = $this->getCurrentEntity();
	}
	
	public function getCurrentEntity()
	{
		$registry = $this->registry;
		
		if ($registry->registry('current_product')) {
			return $registry->registry('current_product');
		} elseif ($registry->registry('current_category')) {
			return $registry->registry('current_category');
		}
		
		return false;
	}
	
    public function afterGetPageHeading($subject, $result)
    {
		if ($this->currentEntity && $this->currentEntity->getTitleRewrite()) {
			return $this->currentEntity->getTitleRewrite();
		}
		
		return $result;
    }
}
