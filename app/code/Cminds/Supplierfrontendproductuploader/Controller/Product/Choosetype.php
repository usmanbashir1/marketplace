<?php

namespace Cminds\Supplierfrontendproductuploader\Controller\Product;

use Cminds\Supplierfrontendproductuploader\Controller\AbstractController;
use Magento\Catalog\Model\Product\Type;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use Magento\GroupedProduct\Model\Product\Type\Grouped as GroupedType;
use Magento\Downloadable\Model\Product\Type as DownloadableType;

class Choosetype extends AbstractController
{
    public function execute()
    {
        if (!$this->canAccess()) {
            return $this->redirectToLogin();
        }

        $postData = $this->getRequest()->getParams();
        if ($postData) {
            if (!isset($postData['attribute_set_id'])) {
                $this->messageManager->addErrorMessage(
                    'There are no attribute sets available.'
                );

                return $this->_redirect('supplier/product/chooseType');
            }
            if ($postData['type'] === Type::TYPE_SIMPLE) {
                return $this->_redirect(
                    'supplier/product/create',
                    [
                        'attribute_set_id' => $postData['attribute_set_id'],
                        'type' => $postData['type'],
                    ]
                );
            }
            if ($postData['type'] === ConfigurableType::TYPE_CODE) {
                return $this->_redirect(
                    'supplier/product/createconfigurable',
                    [
                        'attribute_set_id' => $postData['attribute_set_id'],
                        'type' => $postData['type'],
                    ]
                );
            }
            if ($postData['type'] === GroupedType::TYPE_CODE) {
                return $this->_redirect(
                    'supplier/product/creategrouped',
                    [
                        'attribute_set_id' => $postData['attribute_set_id'],
                        'type' => $postData['type'],
                    ]
                );
            }
            if ($postData['type'] === Type::TYPE_VIRTUAL) {
                return $this->_redirect(
                    'supplier/product/create',
                    [
                        'attribute_set_id' => $postData['attribute_set_id'],
                        'type' => $postData['type'],
                    ]
                );
            }
            if ($postData['type'] === DownloadableType::TYPE_DOWNLOADABLE) {
                return $this->_redirect(
                    'supplier/product/create',
                    [
                        'attribute_set_id' => $postData['attribute_set_id'],
                        'type' => $postData['type'],
                    ]
                );
            }

            return $this->_redirect('supplier/product/chooseType');
        }

        $this->_view->loadLayout();
        $this->renderBlocks();
        $this->_view->renderLayout();
    }
}
