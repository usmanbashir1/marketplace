<?php

namespace WeltPixel\DesignElements\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CmsBlockLoadAfter implements ObserverInterface
{
    /**
     * FrontendOptions Helper Data
     *
     * @var \WeltPixel\FrontendOptions\Helper\Data
     */
    protected $_frontendOptionsHelperData;

    public function __construct(\WeltPixel\FrontendOptions\Helper\Data $frontendOptionsHelperData){
        $this->_frontendOptionsHelperData = $frontendOptionsHelperData;
    }

    public function execute(Observer $observer){
        $html = '<style>';
        $model = $observer->getDataObject();

        $smallMobileBkp = $this->getSmallMobileBreakpoint();
        $mobileBkp      = $this->getMobileBreakpoint();
        $smallTabletBkp = $this->getSmallTabletBreakpoint();
        $tabletBkp      = $this->getTabletBreakpoint();
        $deskBkp        = $this->getDeskBreakpoint();
	    $largeDeskBkp   = $this->getLargeDeskBreakpoint();
	    
        if($globalCss = trim($model->getCssGlobal())){
            $html .= $globalCss;
        }
        if($smallPhoneCss = trim($model->getCssPhoneSmall())){
            $html .= ' @media screen and (min-width: '.$smallMobileBkp.'){'.$smallPhoneCss.'}';
        }
        if($phoneCss = trim($model->getCssPhone())){
            $html .= ' @media (min-width: '.$mobileBkp.'){'.$phoneCss.'}';
        }
        if($smallTabletCss = trim($model->getCssTabletSmall())){
            $html .= ' @media (min-width: '.$smallTabletBkp.'){'.$smallTabletCss.'}';
        }
        if($tabletCss = trim($model->getCssTablet())){
            $html .= ' @media (min-width: '.$tabletBkp.'){'.$tabletCss.'}';
        }
        if($deskCss = trim($model->getCssDesktop())){
            $html .= ' @media (min-width: '.$deskBkp.'){'.$deskCss.'}';
        }
        if($largeDeskCss = trim($model->getCssDesktopLarge())){
            $html .= ' @media (min-width: '.$largeDeskBkp.'){'.$largeDeskCss.'}';
        }
        $html .='</style>';
        if($js = $model->getCustomJs()){
            $html .='<script>'.$js.'</script>';
        }

        if(strpos($model->getContent(),$html) === false){
            $model->setContent($model->getContent().$html);
        }

        return $this;
    }
    
    public function getSmallMobileBreakpoint(){
        $default = '320px';
        if($bp = $this->_frontendOptionsHelperData->getBreakpointXXS()){
            return $bp;
        }
        return $default;
    }

    public function getMobileBreakpoint(){
        $default = '480px';
        if($bp = $this->_frontendOptionsHelperData->getBreakpointXS()){
            return $bp;
        }
        return $default;
    }

    public function getSmallTabletBreakpoint(){
        $default = '640px';
        if($bp = $this->_frontendOptionsHelperData->getBreakpointS()){
            return $bp;
        }
        return $default;
    }

    public function getTabletBreakpoint(){
        $default = '800px';
        if($bp = $this->_frontendOptionsHelperData->getBreakpointM()){
            return $bp;
        }
        return $default;
    }

    public function getDeskBreakpoint(){
        $default = '1200px';
        if($bp = $this->_frontendOptionsHelperData->getBreakpointL()){
            return $bp;
        }
        return $default;
    }
    
    public function getLargeDeskBreakpoint(){
        $default = '1440px';
        if($bp = $this->_frontendOptionsHelperData->getBreakpointXL()){
            return $bp;
        }
        return $default;
    }


 }
