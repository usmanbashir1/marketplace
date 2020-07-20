<?php

namespace Cminds\DropshipNotification\Block\Tab;

use Cminds\DropshipNotification\Model\ResourceModel\Order\Item\Collection;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Cminds\DropshipNotification\ViewModel\DropShip as ViewModel;

class DropShip extends Template implements ArgumentInterface
{
    /** @var ViewModel */
    protected $viewModel;

    public function __construct(
        Context $context,
        ViewModel $viewModel,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->viewModel = $viewModel;
    }


    /**
     * Get grids meta info
     *
     * @return array
     */
    public function getGrids()
    {
        return $this->viewModel->getGrids();
    }

    /**
     * Get grid items collection
     *
     * @param int $statusFilter
     * @return Collection
     */
    public function getGridOrderItems($statusFilter)
    {
        return $this->viewModel->getItemsCollectionByFilter($this->getOrder(), $statusFilter);
    }

    /**
     * Can show tab
     *
     * @return bool
     */
    public function canShow()
    {
        return $this->viewModel->canShowTab($this->getOrder()->getId());
    }

}
