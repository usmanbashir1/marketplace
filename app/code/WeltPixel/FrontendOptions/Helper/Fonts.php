<?php

namespace WeltPixel\FrontendOptions\Helper;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Fonts extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var string
     */
    protected $_googlefontUrl = 'https://fonts.googleapis.com/css?display=swap&family=';

    /**
     * @var array
     */
    protected $_fontFamilyOptions = [
        'h1__font____family',
        'h2__font____family',
        'h3__font____family',
        'h4__font____family',
        'h5__font____family',
        'h6__font____family',
        'font____family__base',
        'button__font____family',
        'form____element____input__font____family'
    ];

    /**
     * @var array
     */
    protected $avilableFontFamilys = [
        [
            'font' => 'h1/h1__font____family',
            'weight' => 'h1/h1__font____weight',
            'characterset' => 'h1/h1__font____family_characterset'
        ],
        [
            'font' => 'h2/h2__font____family',
            'weight' => 'h2/h2__font____weight',
            'characterset' => 'h2/h2__font____family_characterset'
        ],
        [
            'font' => 'h3/h3__font____family',
            'weight' => 'h3/h3__font____weight',
            'characterset' => 'h3/h3__font____family_characterset'
        ],
        [
            'font' => 'h4/h4__font____family',
            'weight' => 'h4/h4__font____weight',
            'characterset' => 'h4/h4__font____family_characterset'
        ],
        [
            'font' => 'h5/h5__font____family',
            'weight' => 'h5/h5__font____weight',
            'characterset' => 'h5/h5__font____family_characterset'
        ],
        [
            'font' => 'h6/h6__font____family',
            'weight' => 'h6/h6__font____weight',
            'characterset' => 'h6/h6__font____family_characterset'
        ],
        [
            'font' => 'default/font____family__base',
            'weight' => 'default/font____weight__regular',
            'characterset' => 'default/font____family__base_characterset',
        ],
        [
            'font' => 'default_buttons/button__font____family',
            'weight' => 'default_buttons/button__font____weight',
            'characterset' => 'default_buttons/button__font____family_characterset',
        ],
        [
            'font' => 'form_inputs/form____element____input__font____family',
            'weight' => 'form_inputs/form____element____input__font____weight',
            'characterset' => 'form_inputs/form____element____input__font____family_characterset'
        ]
    ];


    /**
     * @return array
     */
    public function getFontFamilyOptions()
    {
        return $this->_fontFamilyOptions;
    }

    /**
     * @return bool|string
     */
    public function getGoogleFonts()
    {
        $baseUrl = $this->_googlefontUrl;

        $fontUrl = $this->_getFontFamilyMergedUrl();

        if (strlen(trim($fontUrl))) {
            return $baseUrl . $fontUrl;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getAsyncFontFamilyOptions()
    {
        $fontsArrayOptions = [];
        foreach ($this->avilableFontFamilys as $availableFamily) {
            $fontFamily = str_replace(' ', '+', $this->scopeConfig->getValue('weltpixel_frontend_options/' . $availableFamily['font'], \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
            if ($fontFamily) {
                $fontWeight = $this->scopeConfig->getValue('weltpixel_frontend_options/' . $availableFamily['weight'], \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $fontCharacterset = $this->scopeConfig->getValue('weltpixel_frontend_options/' . $availableFamily['characterset'], \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                if ($fontWeight) {
                    $fontsArrayOptions[$fontFamily]['weight'][] = array_map('trim', explode(',', $fontWeight));
                }
                if ($fontCharacterset) {
                    $fontsArrayOptions[$fontFamily]['characterset'][] = explode(',', $fontCharacterset);
                }

            }
        }

        return $this->_buildAsyncOptions($fontsArrayOptions);
    }

    /**
     *Gets all the font options from the backend and it will construct the final font url
     * @return boolean|string
     */
    private function _getFontFamilyMergedUrl()
    {
        $fontsArray = [];
        foreach ($this->avilableFontFamilys as $availableFamily) {
            $fontFamily = str_replace(' ', '+', $this->scopeConfig->getValue('weltpixel_frontend_options/' . $availableFamily['font'], \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
            if ($fontFamily) {
                $fontWeight = $this->scopeConfig->getValue('weltpixel_frontend_options/' . $availableFamily['weight'], \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $fontCharacterset = $this->scopeConfig->getValue('weltpixel_frontend_options/' . $availableFamily['characterset'], \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                if ($fontWeight) {
                    $fontsArray[$fontFamily][] = array_map('trim', explode(',', $fontWeight));
                }
                if ($fontCharacterset) {
                    $fontsArray['_characterset'][] = explode(',', $fontCharacterset);
                }

            }
        }

        return $this->_buildUrl($fontsArray);
    }

    /**
     * Normalizes the admin options and constructs the final url into one merged font url
     * @param array $fontsArray
     * @return boolean|string
     */
    private function _buildUrl($fontsArray)
    {
        if (empty($fontsArray)) {
            return false;
        }

        $normalizedFontOptions = [];
        $subset = '';

        foreach ($fontsArray as $fontKey => $fontOptions) {
            $tmpArray = [];
            foreach ($fontOptions as $options) {
                $tmpArray = array_unique(array_merge($tmpArray, $options));
            }

            if ($fontKey == '_characterset') {
                $subset = implode(',', $tmpArray);
            } else {
                $normalizedFontOptions[] = $fontKey . ":" . implode(',', $tmpArray);
            }
        }

        $fontUrl = implode('%7C', $normalizedFontOptions);

        if ($subset) {
            $fontUrl .= '&subset=' . $subset;
        }

        return $fontUrl;
    }

    /**
     * @param array $fontsArray
     * @return array
     */
    private function _buildAsyncOptions($fontsArray)
    {
        if (empty($fontsArray)) {
            return [];
        }

        $fontOptionsArray = [];

        foreach ($fontsArray as $fontKey => $fontOptions) {
            $tmpWeightArray = [];
            $tmpCharactersetArray = [];
            foreach ($fontOptions['weight'] as $options) {
                $tmpWeightArray = array_unique(array_merge($tmpWeightArray, $options));
            }

            foreach ($fontOptions['characterset'] as $options) {
                $tmpCharactersetArray = array_unique(array_merge($tmpCharactersetArray, $options));
            }

            $fontArrayOption = $fontKey . ":" . implode(',', $tmpWeightArray);
            if ($tmpCharactersetArray) {
                $subset = implode(',', $tmpCharactersetArray);
                $fontArrayOption .= ':' . $subset;
            }
            $fontOptionsArray[] = $fontArrayOption;
        }

        $lastElement = array_slice($fontOptionsArray, -1, 1);
        $lastElement = $lastElement[0] . '&display=swap';
        $fontOptionsArray[count($fontOptionsArray) - 1] = $lastElement;

        return $fontOptionsArray;
    }

}
