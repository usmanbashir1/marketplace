<?php

namespace WeltPixel\SampleData\Model;

use Magento\Framework\Setup\SampleData\Context as SampleDataContext;

class Owl
{
    /**
     * @var \WeltPixel\OwlCarouselSlider\Model\SliderFactory
     */
    protected $sliderFactory;

    /**
     * @var \WeltPixel\OwlCarouselSlider\Model\BannerFactory
     */
    protected $bannerFactory;

    /**
     * @param \WeltPixel\OwlCarouselSlider\Model\SliderFactory $sliderFactory
     * @param \WeltPixel\OwlCarouselSlider\Model\BannerFactory
     */
    public function __construct(
        \WeltPixel\OwlCarouselSlider\Model\SliderFactory $sliderFactory,
        \WeltPixel\OwlCarouselSlider\Model\BannerFactory $bannerFactory
    )
    {
        $this->sliderFactory = $sliderFactory;
        $this->bannerFactory = $bannerFactory;
    }

    /**
     * @return array
     */
    public function install()
    {
        $slider = $this->sliderFactory->create();

        $sliderData = array(
            'status' => 1,
            'title' => 'Homepage V1',
            'show_title' => 0,
            'slider_content' => NULL,
            'nav' => 1,
            'dots' => 1,
            'center' => 0,
            'items' => 1,
            'loop' => 1,
            'margin' => 0,
            'merge' => 1,
            'URLhashListener' => 0,
            'startPosition' => 0,
            'stagePadding' => 0,
            'lazyLoad' => 0,
            'transition' => 'fadeOut',
            'autoplay' => 0,
            'autoplayTimeout' => 5000,
            'autoplayHoverPause' => 0,
            'autoHeight' => 0,
            'nav_brk1' => 1,
            'items_brk1' => 1,
            'nav_brk2' => 1,
            'items_brk2' => 1,
            'nav_brk3' => 1,
            'items_brk3' => 1,
            'nav_brk4' => 1,
            'items_brk4' => 1,
        );

        $slider->addData($sliderData);
        $slider->save();

        $sliderId = $slider->getData('id');

        for ($i = 1; $i < 5; $i++) {
            $ext = 'jpg';
            if ($i == 3) {
                $ext = 'png';
            }
            $banner = $this->bannerFactory->create();
            $bannerData = array(
                'status' => 1,
                'title' => 'Slide ' . $i,
                'show_title' => 0,
                'description' => NULL,
                'show_description' => 0,
                'banner_type' => 1,
                'display_position' => 1,
                'display_position' => NULL,
                'slider_id' => $sliderId,
                'url' => NULL,
                'target' => '_self',
                'video' => NULL,
                'video' => NULL,
                'image' => "weltpixel/owlcarouselslider/images/h/1/h1_h{$i}.jpg",
                'mobile_image' => "weltpixel/owlcarouselslider/images/h/1/h1_h{$i}_1.{$ext}",
                'custom' => NULL,
                'alt_text' => NULL,
                'button_text' => NULL,
                'custom_content' => NULL,
                'valid_from' => '2015-01-01 12:00:00',
                'valid_to' => '2030-01-01 12:00:00',
                'sort_order' => $i
            );

            $banner->addData($bannerData);
            $banner->save();

            unset($banner);
        }

        return array(
            'slider_id' => $sliderId
        );
    }

    /**
     * @param $version
     * @return array
     */
    public function update($version)
    {
        $sliderIds = [];

        switch ($version) {
            case '1.1.2' :
                $slider = $this->sliderFactory->create();

                $sliderData = array(
                    'status' => 1,
                    'title' => 'Homepage V8 - 1',
                    'show_title' => 0,
                    'slider_content' => NULL,
                    'nav' => 0,
                    'dots' => 1,
                    'center' => 0,
                    'items' => 3,
                    'loop' => 1,
                    'margin' => 0,
                    'stagePadding' => 0,
                    'lazyLoad' => 0,
                    'transition' => 'slide',
                    'autoplay' => 1,
                    'autoplayTimeout' => 4000,
                    'autoplayHoverPause' => 0,
                    'autoHeight' => 0,
                    'nav_brk1' => 0,
                    'items_brk1' => 1,
                    'nav_brk2' => 0,
                    'items_brk2' => 2,
                    'nav_brk3' => 1,
                    'items_brk3' => 2,
                    'nav_brk4' => 0,
                    'items_brk4' => 3,
                );

                $slider->addData($sliderData);
                $slider->save();

                $sliderId = $slider->getData('id');
                $sliderIds[] = $sliderId;

                $customContent = [
                    '1' => '<div class="arw-banner-content">
   <div class="content">
       <h3><a href="#">Women</a></h3>
      <ul class="list-categories">
         <li><a href="#">Dresses</a></li>
         <li><a href="#">Tops</a></li> 
         <li><a href="#">Blazers</a></li>
         <li><a href="#">Blouses</a></li>
         <li><a href="#">Pants</a></li>
      </ul>
       <a href="#" class="btn shop-now">Shop now</a>
  </div>
</div>',
                    '2' => '<div class="arw-banner-content">
   <div class="content">
       <h3><a href="#">Apartment </a></h3>
<ul class="list-categories">
         <li><a href="#">Bed</a></li>
         <li><a href="#">Pillow</a></li> 
         <li><a href="#">Lamp</a></li>
         <li><a href="#">Mattress</a></li>
         <li><a href="#">Blanket</a></li>
      </ul>
       <a href="#" class="btn shop-now">Shop now</a>
  </div>
</div>',
                    '3' => '<div class="arw-banner-content">
   <div class="content">
       <h3><a href="#">Men</a></h3>
<ul class="list-categories">
         <li><a href="#">Pants</a></li>
         <li><a href="#">Shirts</a></li> 
         <li><a href="#">Tops</a></li>
         <li><a href="#">Hoods</a></li>
         <li><a href="#">Shoes</a></li>
      </ul>
       <a href="#" class="btn shop-now">Shop now</a>  
  </div>
</div>'
                ];

                for ($i = 1; $i < 4; $i++) {
                    $banner = $this->bannerFactory->create();
                    $bannerData = array(
                        'status' => 1,
                        'title' => 'Slide ' . $i,
                        'show_title' => 0,
                        'description' => NULL,
                        'show_description' => 0,
                        'banner_type' => 1,
                        'display_position' => NULL,
                        'slider_id' => $sliderId,
                        'url' => NULL,
                        'target' => '_self',
                        'video' => NULL,
                        'image' => "weltpixel/owlcarouselslider/images/h/8/h8_h{$i}.jpg",
                        'custom' => NULL,
                        'alt_text' => NULL,
                        'button_text' => NULL,
                        'custom_content' => $customContent[$i],
                        'custom_css' => NULL,
                        'valid_from' => '2015-01-01 12:00:00',
                        'valid_to' => '2030-01-01 12:00:00',
                        'sort_order' => $i
                    );

                    $banner->addData($bannerData);
                    $banner->save();

                    unset($banner);
                }

                unset($slider);

                $slider = $this->sliderFactory->create();

                $sliderData = array(
                    'status' => 1,
                    'title' => 'Homepage V8 - Logo Slider',
                    'show_title' => 0,
                    'slider_content' => NULL,
                    'nav' => 0,
                    'dots' => 1,
                    'center' => 0,
                    'items' => 20,
                    'loop' => 1,
                    'margin' => 50,
                    'stagePadding' => 50,
                    'lazyLoad' => 0,
                    'transition' => 'slide',
                    'autoplay' => 1,
                    'autoplayTimeout' => 4000,
                    'autoplayHoverPause' => 1,
                    'autoHeight' => 0,
                    'nav_brk1' => 0,
                    'items_brk1' => 2,
                    'nav_brk2' => 0,
                    'items_brk2' => 3,
                    'nav_brk3' => 0,
                    'items_brk3' => 5,
                    'nav_brk4' => 1,
                    'items_brk4' => 7,
                );

                $slider->addData($sliderData);
                $slider->save();

                $sliderId = $slider->getData('id');
                $sliderIds[] = $sliderId;

                for ($i = 1; $i < 8; $i++) {
                    $banner = $this->bannerFactory->create();
                    $bannerData = array(
                        'status' => 1,
                        'title' => 'Client ' . $i,
                        'show_title' => 0,
                        'description' => NULL,
                        'show_description' => 0,
                        'banner_type' => 1,
                        'display_position' => NULL,
                        'slider_id' => $sliderId,
                        'url' => NULL,
                        'target' => '_self',
                        'video' => NULL,
                        'image' => "weltpixel/owlcarouselslider/images/c/l/client-{$i}.png",
                        'custom' => NULL,
                        'alt_text' => NULL,
                        'button_text' => NULL,
                        'custom_content' => NULL,
                        'custom_css' => NULL,
                        'valid_from' => '2015-01-01 12:00:00',
                        'valid_to' => '2030-01-01 12:00:00',
                        'sort_order' => $i
                    );

                    $banner->addData($bannerData);
                    $banner->save();

                    unset($banner);
                }

                unset($slider);

                break;
            case '1.1.6' : {
                $slider = $this->sliderFactory->create();

                $sliderData = array(
                    'status' => 1,
                    'title' => 'Homepage V7',
                    'show_title' => 0,
                    'slider_content' => NULL,
                    'nav' => 1,
                    'dots' => 0,
                    'center' => 0,
                    'items' => 1,
                    'loop' => 1,
                    'margin' => 0,
                    'stagePadding' => 0,
                    'lazyLoad' => 1,
                    'transition' => 'slide',
                    'autoplay' => 1,
                    'autoplayTimeout' => 3000,
                    'autoplayHoverPause' => 0,
                    'autoHeight' => 0,
                    'nav_brk1' => 1,
                    'items_brk1' => 1,
                    'nav_brk2' => 1,
                    'items_brk2' => 1,
                    'nav_brk3' => 1,
                    'items_brk3' => 1,
                    'nav_brk4' => 1,
                    'items_brk4' => 1,
                );

                $slider->addData($sliderData);
                $slider->save();

                $sliderId = $slider->getData('id');
                $sliderIds[] = $sliderId;

                for ($i = 1; $i < 3; $i++) {
                    $banner = $this->bannerFactory->create();
                    $bannerData = array(
                        'status' => 1,
                        'title' => 'Banner Slider V7 - ' . $i,
                        'show_title' => 0,
                        'description' => 'Banner Slider V7 - ' . $i,
                        'show_description' => 0,
                        'banner_type' => 1,
                        'display_position' => NULL,
                        'slider_id' => $sliderId,
                        'url' => NULL,
                        'target' => '_self',
                        'video' => NULL,
                        'image' => "weltpixel/owlcarouselslider/images/h/7/h7_h{$i}.jpg",
                        'mobile_image' => "weltpixel/owlcarouselslider/images/h/7/h7_h{$i}_1.jpg",
                        'custom' => NULL,
                        'alt_text' => NULL,
                        'button_text' => NULL,
                        'custom_content' => NULL,
                        'custom_css' => NULL,
                        'valid_from' => '2015-01-01 12:00:00',
                        'valid_to' => '2030-01-01 12:00:00',
                        'sort_order' => $i
                    );

                    $banner->addData($bannerData);
                    $banner->save();

                    unset($banner);
                }

                unset($slider);

                break;
            }
            case '1.1.7' : {
                $slider = $this->sliderFactory->create();

                $sliderData = array(
                    'status' => 1,
                    'title' => 'Homepage V9 - 1',
                    'show_title' => 0,
                    'slider_content' => NULL,
                    'nav' => 1,
                    'dots' => 0,
                    'center' => 0,
                    'items' => 1,
                    'loop' => 1,
                    'margin' => 0,
                    'stagePadding' => 0,
                    'lazyLoad' => 0,
                    'transition' => 'slide',
                    'autoplay' => 1,
                    'autoplayTimeout' => 4000,
                    'autoplayHoverPause' => 0,
                    'autoHeight' => 1,
                    'nav_brk1' => 0,
                    'items_brk1' => 1,
                    'nav_brk2' => 0,
                    'items_brk2' => 1,
                    'nav_brk3' => 0,
                    'items_brk3' => 1,
                    'nav_brk4' => 1,
                    'items_brk4' => 1,
                );

                $slider->addData($sliderData);
                $slider->save();

                $sliderId = $slider->getData('id');
                $sliderIds[] = $sliderId;

                $customContent = [
                    '1' => '<div class="row mg-top-slider">
    <div class="col-md-4 col-xs-12">
        <div id="slide1-slideInLeft">
            <img src="/pub/media/wysiwyg/pearl_theme/1445992344-67402800.png" alt="weltpixel" class="chair">
        </div>

    </div>
    <div class="col-md-4 col-xs-7 display-block-desktop">
        <div id="slide1-slideInDown">
            <div class="boder-table">
                <span class="separator">Chair Collection</span>
                <span class="year">2018</span>
            </div>
            <div class="small-text-v9-slider mg-top display-block-desktop">Without retails shops and middleman, luxury</div>
            <div class="small-text-v9-slider mg-top-bottom display-block-desktop">becomes affordable</div>
            <button class="btn btn-shop-now display-block-desktop">SHOP NOW</button>
        </div>
    </div>

    <div class="col-md-4 col-xs-4 display-block-desktop">
        <div id="slide1-slideInRight">
            <img src="/pub/media/wysiwyg/pearl_theme/1436359646-58496100.png" alt="weltpixel" class="chair">
        </div>
    </div>

    <div class="col-xs-12 display-block-mobile">
        <div class="small-text-v9-slider margin-top-20-mobile">Without retails shops and middleman, luxury</div>
        <div class="small-text-v9-slider">becomes affordable</div>
        <button class="btn btn-shop-now">SHOP NOW</button>
    </div>
</div>',
                    '2' => '<div class="row mg-top-slider">
    <div class="col-md-5 display-block-desktop">
        <div id="slide2-slideInLeft" >
            <div class="boder-table">
                <span class="separator">Sofa Collection</span>
                <span class="year">2018</span>
            </div>
            <div class="small-text-v9-slider mg-top">Without retails shops and middleman, luxury</div>
            <div class="small-text-v9-slider mg-top-bottom">becomes affordable</div>
            <button class="btn btn-shop-now">SHOP NOW</button>
        </div>
    </div>

    <div class="col-md-7 col-xs-12">
        <div id="slide2-lightSpeedIn">
            <img src="/pub/media/wysiwyg/pearl_theme/image_manager__resp600_16560_divaa2.png" alt="weltpixel" class="sofa">
        </div>
    </div>

    <div class="col-xs-12 display-block-mobile">
        <div id="slide2-slideInDown-mobile">
            <div class="small-text-v9-slider margin-top-20-mobile">Without retails shops and middleman, luxury</div>
            <div class="small-text-v9-slider">becomes affordable</div>
            <button class="btn btn-shop-now">SHOP NOW</button>
        </div>
    </div>
</div>',
                    '3' => '<div class="row mg-top-slider">
    <div class="col-md-7 col-xs-12 ">
        <div id="slide3-slideInDown">
            <img src="/pub/media/wysiwyg/pearl_theme/cassina_corbusier_lc6_dining_table_front.png" alt="weltpixel" class="table">
        </div>
    </div>

    <div class="col-md-5 display-block-desktop">
        <div id="slide3-lightSpeedIn" >
            <div class="boder-table">
                <span class="separator">Table Collection</span>
                <span class="year">2018</span>
            </div>
            <div class="small-text-v9-slider mg-top">Without retails shops and middleman, luxury</div>
            <div class="small-text-v9-slider mg-top-bottom">becomes affordable</div>
            <button class="btn btn-shop-now">SHOP NOW</button>
        </div>
    </div>

    <div class="col-xs-12 display-block-mobile">
        <div id="slide3-slideInDown-mobile">
            <div class="small-text-v9-slider margin-top-20-mobile">Without retails shops and middleman, luxury</div>
            <div class="small-text-v9-slider">becomes affordable</div>
            <button class="btn btn-shop-now">SHOP NOW</button>
        </div>
    </div>
</div>'
                ];

                for ($i = 1; $i < 4; $i++) {
                    $banner = $this->bannerFactory->create();
                    $bannerData = array(
                        'status' => 1,
                        'title' => 'Banner Slider V9 - ' . $i,
                        'show_title' => 0,
                        'description' => NULL,
                        'show_description' => 0,
                        'banner_type' => 1,
                        'display_position' => NULL,
                        'slider_id' => $sliderId,
                        'url' => NULL,
                        'target' => '_self',
                        'video' => NULL,
                        'image' => "weltpixel/owlcarouselslider/images/h/9/h9_h{$i}.png",
                        'custom' => NULL,
                        'alt_text' => NULL,
                        'button_text' => NULL,
                        'custom_content' => $customContent[$i],
                        'custom_css' => NULL,
                        'valid_from' => '2015-01-01 12:00:00',
                        'valid_to' => '2030-01-01 12:00:00',
                        'sort_order' => $i
                    );

                    $banner->addData($bannerData);
                    $banner->save();

                    unset($banner);
                }

                unset($slider);


                $slider = $this->sliderFactory->create();

                $sliderData = array(
                    'status' => 1,
                    'title' => 'Homepage V9 - Logo Slider',
                    'show_title' => 0,
                    'slider_content' => NULL,
                    'nav' => 0,
                    'dots' => 1,
                    'center' => 0,
                    'items' => 20,
                    'loop' => 1,
                    'margin' => 50,
                    'stagePadding' => 50,
                    'lazyLoad' => 0,
                    'transition' => 'slide',
                    'autoplay' => 1,
                    'autoplayTimeout' => 4000,
                    'autoplayHoverPause' => 1,
                    'autoHeight' => 0,
                    'nav_brk1' => 0,
                    'items_brk1' => 2,
                    'nav_brk2' => 0,
                    'items_brk2' => 3,
                    'nav_brk3' => 0,
                    'items_brk3' => 5,
                    'nav_brk4' => 1,
                    'items_brk4' => 7,
                );

                $slider->addData($sliderData);
                $slider->save();

                $sliderId = $slider->getData('id');
                $sliderIds[] = $sliderId;

                for ($i = 1; $i < 8; $i++) {
                    $banner = $this->bannerFactory->create();
                    $bannerData = array(
                        'status' => 1,
                        'title' => 'Client ' . $i,
                        'show_title' => 0,
                        'description' => NULL,
                        'show_description' => 0,
                        'banner_type' => 1,
                        'display_position' => NULL,
                        'slider_id' => $sliderId,
                        'url' => NULL,
                        'target' => '_self',
                        'video' => NULL,
                        'image' => "weltpixel/owlcarouselslider/images/c/l/client-{$i}.png",
                        'custom' => NULL,
                        'alt_text' => NULL,
                        'button_text' => NULL,
                        'custom_content' => NULL,
                        'custom_css' => NULL,
                        'valid_from' => '2015-01-01 12:00:00',
                        'valid_to' => '2030-01-01 12:00:00',
                        'sort_order' => $i
                    );

                    $banner->addData($bannerData);
                    $banner->save();

                    unset($banner);
                }

                unset($slider);

                break;
            }
            case '1.1.9' : {
                $slider = $this->sliderFactory->create();

                $sliderData = array(
                    'status' => 1,
                    'title' => 'Homepage V5',
                    'show_title' => 0,
                    'slider_content' => NULL,
                    'nav' => 1,
                    'dots' => 0,
                    'center' => 0,
                    'items' => 1,
                    'loop' => 1,
                    'margin' => 0,
                    'stagePadding' => 0,
                    'lazyLoad' => 1,
                    'transition' => 'slide',
                    'autoplay' => 0,
                    'autoplayTimeout' => 5000,
                    'autoplayHoverPause' => 0,
                    'autoHeight' => 1,
                    'nav_brk1' => 0,
                    'items_brk1' => 1,
                    'nav_brk2' => 0,
                    'items_brk2' => 1,
                    'nav_brk3' => 0,
                    'items_brk3' => 1,
                    'nav_brk4' => 1,
                    'items_brk4' => 1,
                );

                $slider->addData($sliderData);
                $slider->save();

                $sliderId = $slider->getData('id');
                $sliderIds[] = $sliderId;

                $customContent = [
                    '1' => '<div class="slide2">
    <div id="slide2-slideDown">
        <div class="sale_of_img"></div>
    </div>
    <div id="slide2-rotateIn">
        <p class="sale_of_text">Magento 2 </p>
        <p class="sale_of_text">Responsive Theme</p>
    </div>
</div>',
                    '2' => '<div class="slide3">
    <div id="slide3-flipInX">
        <p class="sale_of_text">New way to design</p>
    </div>
    <div id="slide3-slideDown-last">
        <p>Pearl Theme makes design simple</p>
    </div>
    <div id="slide3-slideDown-first">
        <p>for everyone.</p>
    </div>
</div>
'
                ];

                for ($i = 1; $i < 3; $i++) {
                    $banner = $this->bannerFactory->create();
                    $bannerData = array(
                        'status' => 1,
                        'title' => 'Banner Slider V5 - ' . $i,
                        'show_title' => 0,
                        'description' => 'Banner Slider V5 - ' . $i,
                        'show_description' => 0,
                        'banner_type' => 1,
                        'display_position' => NULL,
                        'slider_id' => $sliderId,
                        'url' => NULL,
                        'target' => '_self',
                        'video' => NULL,
                        'image' => "weltpixel/owlcarouselslider/images/h/5/h5_h{$i}.png",
                        'mobile_image' => "weltpixel/owlcarouselslider/images/h/5/h5_h{$i}_1.png",
                        'custom' => NULL,
                        'alt_text' => NULL,
                        'button_text' => NULL,
                        'custom_content' => $customContent[$i],
                        'custom_css' => NULL,
                        'valid_from' => '2015-01-01 12:00:00',
                        'valid_to' => '2030-01-01 12:00:00',
                        'sort_order' => $i
                    );

                    $banner->addData($bannerData);
                    $banner->save();

                    unset($banner);
                }

                unset($slider);

                break;
            }
            case '1.1.11' : {
                $slider = $this->sliderFactory->create();

                $sliderData = array(
                    'status' => 1,
                    'title' => 'Homepage V10',
                    'show_title' => 0,
                    'slider_content' => NULL,
                    'nav' => 1,
                    'dots' => 1,
                    'center' => 0,
                    'items' => 1,
                    'loop' => 1,
                    'margin' => 0,
                    'stagePadding' => 0,
                    'lazyLoad' => 0,
                    'transition' => 'slide',
                    'autoplay' => 1,
                    'autoplayTimeout' => 4000,
                    'autoplayHoverPause' => 0,
                    'autoHeight' => 1,
                    'nav_brk1' => 0,
                    'items_brk1' => 1,
                    'nav_brk2' => 0,
                    'items_brk2' => 1,
                    'nav_brk3' => 0,
                    'items_brk3' => 1,
                    'nav_brk4' => 1,
                    'items_brk4' => 1,
                );

                $slider->addData($sliderData);
                $slider->save();

                $sliderId = $slider->getData('id');
                $sliderIds[] = $sliderId;

                $customContent = [
                    '1' => '<div class="page-width">              
                        <h2>MAGENTO 2 THEME PEARL</h2>
                        <span class="sub-heading">Accessories</span>
                    </div>',
                    '2' => '<div class="page-width">
                        <h2>ACCESSORIES FOR MOBILE DEVICES</h2>
                        <span class="sub-heading">Crafted from the finest materials</span>
                    </div>',
                    '3' => '<div class="page-width">
                        <h2>NEW WAY TO DESIGN</h2>
                        <span class="sub-heading">Travel accessories</span>
                    </div>'
                ];

                for ($i = 1; $i < 4; $i++) {
                    $banner = $this->bannerFactory->create();
                    $bannerData = array(
                        'status' => 1,
                        'title' => 'Banner Slider V10 - ' . $i,
                        'show_title' => 0,
                        'description' => NULL,
                        'show_description' => 0,
                        'banner_type' => 1,
                        'display_position' => NULL,
                        'slider_id' => $sliderId,
                        'url' => '#',
                        'target' => '_self',
                        'video' => NULL,
                        'image' => "weltpixel/owlcarouselslider/images/h/10/h10_h{$i}.jpg",
                        'custom' => NULL,
                        'alt_text' => NULL,
                        'button_text' => NULL,
                        'custom_content' => $customContent[$i],
                        'custom_css' => NULL,
                        'valid_from' => '2015-01-01 12:00:00',
                        'valid_to' => '2030-01-01 12:00:00',
                        'sort_order' => $i
                    );

                    $banner->addData($bannerData);
                    $banner->save();

                    unset($banner);
                }

                unset($slider);

                break;
            }
            case '1.1.15' : {
                $slider = $this->sliderFactory->create();

                $sliderData = array(
                    'status' => 1,
                    'title' => 'Homepage V12',
                    'show_title' => 0,
                    'slider_content' => NULL,
                    'nav' => 1,
                    'dots' => 0,
                    'center' => 0,
                    'items' => 1,
                    'loop' => 1,
                    'margin' => 0,
                    'stagePadding' => 0,
                    'lazyLoad' => 1,
                    'transition' => 'slide',
                    'autoplay' => 1,
                    'autoplayTimeout' => 3000,
                    'autoplayHoverPause' => 0,
                    'autoHeight' => 0,
                    'nav_brk1' => 0,
                    'items_brk1' => 1,
                    'nav_brk2' => 0,
                    'items_brk2' => 1,
                    'nav_brk3' => 1,
                    'items_brk3' => 1,
                    'nav_brk4' => 1,
                    'items_brk4' => 1,
                );

                $slider->addData($sliderData);
                $slider->save();

                $sliderId = $slider->getData('id');
                $sliderIds[] = $sliderId;

                $customContent = [
                    '1' => '<div class="container-v12">
                        <div class="img-cont slider-text">
                            <div class="overlay-text overlay-right">
                                <div class="inner">
                                  <div class="text">
                                    <div class="text-custom-slider none-mob">
                                      <div>SS 2018 Collection</div>
                                      <div class="second-line">Out now</div>
                                    </div>
                                  </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    ',
                    '2' => '<div class="container-v12">
                        <div class="img-cont slider-text">
                            <div class="overlay-text overlay-right">
                                <div class="inner">
                                  <div class="text">
                                    <div class="text-custom-slider none-mob">
                                      <div>SS 2018 Collection</div>
                                      <div class="second-line">Out now</div>
                                    </div>
                                  </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    '
                ];

                for ($i = 1; $i < 3; $i++) {
                    $banner = $this->bannerFactory->create();
                    $imageExtension = 'jpg';
                    if ($i == 2) {
                        $imageExtension = 'png';
                    }
                    $bannerData = array(
                        'status' => 1,
                        'title' => 'Banner Slider V12 - ' . $i,
                        'show_title' => 0,
                        'description' => NULL,
                        'show_description' => 0,
                        'banner_type' => 1,
                        'display_position' => NULL,
                        'slider_id' => $sliderId,
                        'url' => '#',
                        'target' => '_self',
                        'video' => NULL,
                        'image' => "weltpixel/owlcarouselslider/images/h/12/h12_h{$i}.jpg",
                        'mobile_image' => "weltpixel/owlcarouselslider/images/h/12/h12_h{$i}_mobile.{$imageExtension}",
                        'custom' => NULL,
                        'alt_text' => NULL,
                        'button_text' => NULL,
                        'custom_content' => $customContent[$i],
                        'custom_css' => NULL,
                        'valid_from' => '2015-01-01 12:00:00',
                        'valid_to' => '2030-01-01 12:00:00',
                        'sort_order' => $i
                    );

                    $banner->addData($bannerData);
                    $banner->save();

                    unset($banner);
                }

                unset($slider);

                break;
            }
            case '1.1.20' : {

                $slider = $this->sliderFactory->create();

                $sliderData = array(
                    'status' => 1,
                    'title' => 'Favorite Brands Slider',
                    'show_title' => 0,
                    'slider_content' => NULL,
                    'nav' => 0,
                    'dots' => 1,
                    'center' => 0,
                    'items' => 20,
                    'loop' => 1,
                    'margin' => 50,
                    'stagePadding' => 50,
                    'lazyLoad' => 0,
                    'transition' => 'slide',
                    'autoplay' => 1,
                    'autoplayTimeout' => 4000,
                    'autoplayHoverPause' => 1,
                    'autoHeight' => 0,
                    'nav_brk1' => 0,
                    'items_brk1' => 2,
                    'nav_brk2' => 0,
                    'items_brk2' => 3,
                    'nav_brk3' => 0,
                    'items_brk3' => 5,
                    'nav_brk4' => 1,
                    'items_brk4' => 7,
                );

                $slider->addData($sliderData);
                $slider->save();

                $sliderId = $slider->getData('id');
                $sliderIds[] = $sliderId;

                for ($i = 1; $i < 8; $i++) {
                    $banner = $this->bannerFactory->create();
                    $bannerData = array(
                        'status' => 1,
                        'title' => 'Client ' . $i,
                        'show_title' => 0,
                        'description' => NULL,
                        'show_description' => 0,
                        'banner_type' => 1,
                        'display_position' => NULL,
                        'slider_id' => $sliderId,
                        'url' => NULL,
                        'target' => '_self',
                        'video' => NULL,
                        'image' => "weltpixel/owlcarouselslider/images/c/l/client-{$i}.png",
                        'custom' => NULL,
                        'alt_text' => NULL,
                        'button_text' => NULL,
                        'custom_content' => NULL,
                        'custom_css' => NULL,
                        'valid_from' => '2015-01-01 12:00:00',
                        'valid_to' => '2030-01-01 12:00:00',
                        'sort_order' => $i
                    );

                    $banner->addData($bannerData);
                    $banner->save();

                    unset($banner);
                }

                unset($slider);
                break;
            }
        }

        return $sliderIds;
    }
}
