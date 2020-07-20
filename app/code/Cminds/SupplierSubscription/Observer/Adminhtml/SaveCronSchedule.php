<?php
namespace Cminds\SupplierSubscription\Observer\Adminhtml;

use Magento\Framework\Event\ObserverInterface;

class SaveCronSchedule implements ObserverInterface
{

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $requestInterface;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $config;

    /**
     * SaveCronSchedule constructor.
     * @param \Magento\Framework\App\RequestInterface $requestInterface
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $config
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $requestInterface,
        \Magento\Framework\App\Config\Storage\WriterInterface $config
    ) {
        $this->requestInterface = $requestInterface;
        $this->config = $config;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $postData   = $this->requestInterface->getParams();
        $time       = $postData['groups']['notification']['fields']['time']['value'];

        $hour       = !empty($time) ? $time[0] : '00';
        $minutes    = !empty($time) ? $time[1] : '00';

        $cronSchedule = "$minutes $hour * * *";

        $this->config->save(
            'subscriptions_configuration/notification/cron_schedule',
            $cronSchedule,
            'default',
            0
        );
    }
}
