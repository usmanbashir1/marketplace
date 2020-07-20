<?php

declare(strict_types=1);

namespace Cminds\Marketplace\Model\Order;

use Cminds\Marketplace\Model\Fields;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Payment\Helper\Data;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\Order\Pdf\Config;
use Magento\Sales\Model\Order\Pdf\Invoice;
use Magento\Sales\Model\Order\Pdf\ItemsFactory;
use Magento\Sales\Model\Order\Pdf\Total\Factory;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Zend_Pdf;
use Zend_Pdf_Style;
use Cminds\Marketplace\Helper\Data as DataHelper;

class PdfInvoice extends Invoice
{
    /**
     * @var DataHelper
     */
    private $dataHelper;

    /**
     * Fields object.
     *
     * @var Fields
     */
    protected $fields;

    public function __construct(
        Data $paymentData,
        StringUtils $string,
        ScopeConfigInterface $scopeConfig,
        Filesystem $filesystem,
        Config $pdfConfig,
        Factory $pdfTotalFactory,
        ItemsFactory $pdfItemsFactory,
        TimezoneInterface $localeDate,
        StateInterface $inlineTranslation,
        Renderer $addressRenderer,
        StoreManagerInterface $storeManager,
        ResolverInterface $localeResolver,
        DataHelper $dataHelper,
        Fields $fields,
        array $data = []
    ) {
        parent::__construct(
            $paymentData,
            $string,
            $scopeConfig,
            $filesystem,
            $pdfConfig,
            $pdfTotalFactory,
            $pdfItemsFactory,
            $localeDate,
            $inlineTranslation,
            $addressRenderer,
            $storeManager,
            $localeResolver,
            $data
        );
        $this->dataHelper = $dataHelper;
        $this->fields = $fields;
    }

    /**
     * Return PDF document
     *
     * @param array|Collection $invoices
     * @return Zend_Pdf
     */
    public function getPdf($invoices = [])
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($invoices as $invoice) {
            if ($invoice->getStoreId()) {
                $this->_localeResolver->emulate($invoice->getStoreId());
                $this->_storeManager->setCurrentStore($invoice->getStoreId());
            }
            $page = $this->newPage();
            $order = $invoice->getOrder();
            /* Add image */
            $this->insertLogo($page, $invoice->getStore());
            if ($this->dataHelper->getSupplierId()) {
                $this->insertSupplierInfo($page, $invoice->getStore());
            }
            /* Add address */
            $this->insertAddress($page, $invoice->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $order,
                $this->_scopeConfig->isSetFlag(
                    self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID,
                    ScopeInterface::SCOPE_STORE,
                    $order->getStoreId()
                )
            );
            /* Add document text and number */
            $this->insertDocumentNumber($page, __('Invoice # ') . $invoice->getIncrementId());
            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($invoice->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
            /* Add totals */
            $this->insertTotals($page, $invoice);
            if ($invoice->getStoreId()) {
                $this->_localeResolver->revert();
            }
        }
        $this->_afterGetPdf();
        return $pdf;
    }

    /**
     * @param $page
     * @param null $store
     * @throws \Zend_Pdf_Exception
     */
    protected function insertSupplierInfo(&$page, $store = null)
    {
        $supplier = $this->dataHelper->getLoggedSupplier();
        if (!empty($supplier)) {
            $this->drawSupplierData($page, $supplier);
            $this->drawAdditionalAttributesBlock($page, $supplier, $store);
        }
    }

    /**
     * @param $page
     * @param $supplier
     * @throws \Zend_Pdf_Exception
     */
    protected function drawSupplierData(&$page, $supplier)
    {
        $logo = $this->dataHelper->getSupplierLogo();
        $this->y = $this->y ? $this->y : 1085;
        $top = $this->y;
        if ($logo) {
            $widthLimit = 120;
            $heightLimit = 120;

            $imagePath = $this->dataHelper->getSupplierLogoPath();
            $logo = \Zend_Pdf_Image::imageWithPath($imagePath);

            $width = $logo->getPixelWidth();
            $height = $logo->getPixelHeight();

            $ratio = $width / $height;
            if ($ratio > 1 && $width > $widthLimit) {
                $width = $widthLimit;
                $height = $width / $ratio;
            } elseif ($ratio < 1 && $height > $heightLimit) {
                $height = $heightLimit;
                $width = $height * $ratio;
            } elseif ($ratio == 1 && $height > $heightLimit) {
                $height = $heightLimit;
                $width = $widthLimit;
            }

            $y1 = $top - $height;
            $y2 = $top;
            $x1 = 25;
            $x2 = $x1 + $width;

            $page->drawImage($logo, $x1, $y1, $x2, $y2);

            $this->y = $y1 - 10;
        }

        $this->y -= 10;
        $supplierName = $this->getSupplierName($supplier);
        $lines[0][] = ['text' => $supplierName, 'feed' => 25, 'align' => 'left'];
        $lineBlock = ['lines' => $lines, 'height' => 10];
        $this->drawLineBlocks($page, [$lineBlock], ['table_header' => false]);

        $top -= 10;
        $this->y = $this->y > $top ? $top : $this->y;
    }

    /**
     * @param $supplier
     * @return string
     */
    protected function getSupplierName($supplier)
    {
        $supplierName = $supplier->getFirstname() . ' ' . $supplier->getLastname();
        if (empty($supplier->getData('supplier_name'))) {
            return $supplierName;
        }
        return $supplier->getData('supplier_name');
    }

    /**
     * @param $page
     * @param $supplier
     * @param $store
     */
    protected function drawAdditionalAttributesBlock(&$page, $supplier, $store)
    {
        $top = $this->y + 150;
        $this->y -= 10;
        $customAttributes = $this->getCustomFieldsValues($supplier, $store);
        if (!empty($customAttributes) && is_array($customAttributes)) {
            $fontSize = 10;
            $font = $this->_setFontRegular($page, $fontSize);
            $page->setLineWidth(0);
            $lineBlock = ['lines' => [], 'height' => 15];
            foreach ($customAttributes as $label => $value) {
                $label = $label . ': ';
                $lineWidth = $this->widthForStringUsingFontSize($label, $font, $fontSize);
                $valueWidth = 25;
                $titleFeedPosition = $valueWidth + $lineWidth + 15;
                $lineBlock['lines'][] = [
                    [
                        'text' => $label,
                        'feed' => $valueWidth,
                        'align' => 'left',
                        'font_size' => $fontSize,
                        'font' => 'bold',
                    ],
                    [
                        'text' => $value,
                        'feed' => $titleFeedPosition,
                        'align' => 'left',
                        'font_size' => $fontSize,
                        'font' => 'normal'
                    ],
                ];
                $top -= 10;
            }
            $page = $this->drawLineBlocks($page, [$lineBlock]);
        }
        $this->y = $this->y > $top ? $top : $this->y;
    }

    /**
     * @param $customer
     * @param $store
     * @return array|null
     */
    public function getCustomFieldsValues($customer, $store)
    {
        $additionalAttributesConfig = $this->dataHelper->getEmailAdditionalAttributes($store);
        if (empty(trim($additionalAttributesConfig))) {
            return null;
        }

        $dbValues = [];

        if ($customer->getCustomFieldsValues()) {
            $dbValues = unserialize($customer->getCustomFieldsValues());
        }

        if (empty($dbValues)) {
            return null;
        }

        $attributes = array_map(function ($item) {
            return trim($item);
        }, explode(',', $additionalAttributesConfig));

        $ret = [];
        foreach ($dbValues AS $value) {
            if (in_array($value['name'], $attributes)) {
                $v = $this->fields->load($value['name'], 'name');
                if (isset($v)) {
                    $ret[$v->getLabel()] = $value['value'];
                }
            }
        }

        return $ret;
    }
}
