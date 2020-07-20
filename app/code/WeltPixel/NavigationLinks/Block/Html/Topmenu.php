<?php

namespace WeltPixel\NavigationLinks\Block\Html;

use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Data\TreeFactory;
use Magento\Framework\View\Element\Template;
use Magento\Cms\Model\BlockRepository;
use Magento\Cms\Model\Template\FilterProvider;

class Topmenu extends \Magento\Theme\Block\Html\Topmenu
{
    /**
     * @var BlockRepository
     */
    protected $staticBlockRepository;

    /**
     * @var FilterProvider
     */
    protected $_filterProvider;

    /**
     * Topmenu constructor.
     * @param Template\Context $context
     * @param NodeFactory $nodeFactory
     * @param TreeFactory $treeFactory
     * @param BlockRepository $staticBlockRepository
     * @param FilterProvider $filterProvider
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
        BlockRepository $staticBlockRepository,
        FilterProvider $filterProvider,
        array $data = []
    )
    {
        $this->_filterProvider = $filterProvider;
        $this->staticBlockRepository = $staticBlockRepository;

        parent::__construct($context, $nodeFactory, $treeFactory, $data);
    }

    /**
     * Recursively generates top menu html from data that is specified in $menuTree
     *
     * @param \Magento\Framework\Data\Tree\Node $menuTree
     * @param string $childrenWrapClass
     * @param int $limit
     * @param array $colBrakes
     * @return string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _getHtml(
        \Magento\Framework\Data\Tree\Node $menuTree,
        $childrenWrapClass,
        $limit,
        array $colBrakes = []
    )
    {

        if (!$this->_scopeConfig->getValue(
            'weltpixel_megamenu/megamenu/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )) {
            return parent::_getHtml($menuTree, $childrenWrapClass, $limit, $colBrakes);
        }

        $html = '';

        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = $parentLevel === null ? 0 : $parentLevel + 1;

        $counter = 1;
        $itemPosition = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

        /**
         * Mega Menu: check if any child of the current parent has children
         */
        $hasChildrenArr = [];
        foreach ($children as $child) {
            if ($childLevel == 1) {
                if ($child->hasChildren()) {
                    $hasChildrenArr[$child->getParent()->getId()] = true;
                    break;
                }
            }
        }
        /**
         * End Mega Menu
         */

        $liGroup = false;
        $remainingColumnsNumber = 0;


        foreach ($children as $child) {
            if ($childLevel === 0 && $child->getData('is_parent_active') === false) {
                continue;
            }

            /**
             * Mega Menu: settings
             */
            $parent = $child->getParent();
            $hasChildren = '';

            $forceBreak = false;
            $width = '';

            if ($childLevel == 1) {

                if (isset($hasChildrenArr[$parent->getId()])) {
                    $hasChildren = 'data-has-children="1"';
                }

                $columnsNumber = $this->_getColumnsNumber($parent);
                if ($columnsNumber) {
                    // group items only if the number of subcetegories is bigger than columns numbers value
                    if ($forceBreak || $childrenCount / $columnsNumber < 1) {
                        $liGroup = 1;
                    } else {
                        $liGroup = (int)ceil($childrenCount / $columnsNumber);
                    }

                    if ($remainingColumnsNumber == 0) $remainingColumnsNumber = $columnsNumber;
                }

                // force break up ul if remaining children are not enough to fill all the remaining columns
                if (!$forceBreak) {
                    $remainingChildren = ($childrenCount - $counter) + 1;

                    if ($remainingChildren && $columnsNumber && $remainingChildren == $remainingColumnsNumber) {
                        $liGroup = 1;
                        $forceBreak = true;
                    }
                }

                $columnWidth = $this->_getColumnWidth($parent, $columnsNumber);
                $width = $columnWidth ? 'style="width: ' . $columnWidth . '"' : 'style="width: auto"';
                /**
                 * Mega Menu: open columns-group
                 */
                if ($parent->getWeltpixelMmDisplayMode() != 'default' && $counter == 1) {
                    $html .= '<ul class="columns-group starter" ' . $width . '>';
                }
            }
            /**
             * End Mega Menu
             */

            $child->setLevel($childLevel);
            $child->setIsFirst($counter == 1);
            $child->setIsLast($counter == $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);

            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            $openInNewTab = '';
            if ($child->getData('open_in_newtab')) {
                $openInNewTab = ' target="_blank" ';
            }

            if ($childLevel == 0 && $outermostClass) {
                $outermostClassCode = ' class="' . $outermostClass . '" ';
                $child->setClass($outermostClass);
            }

//            if ($colBrakes && count($colBrakes) && $colBrakes[$counter]['colbrake']) {
            if (is_array($colBrakes) && count($colBrakes) && $colBrakes[$counter]['colbrake']) {
                $html .= '</ul></li><li class="column"><ul>';
            }

            $forceWidth = $childLevel == 1 && $parent->getWeltpixelMmDisplayMode() == 'sectioned' && $width != '' ? $width : '';

            $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . ' ' . $hasChildren . ' ' . $forceWidth . ' data-test="test">';
            $html .= '<a' . $openInNewTab . ' href="' . $child->getUrl() . '" ' . $outermostClassCode . '><span>' . $this->escapeHtml(
                    $child->getName()
                ) . '</span></a>' . $this->_addSubMenu(
                    $child,
                    $childLevel,
                    $childrenWrapClass,
                    $limit
                ) . '</li>';

            /**
             * Mega Menu: close and re-open columns-group
             */
            if ($parent->getWeltpixelMmDisplayMode() != 'default' && $liGroup && $childLevel == 1) {
                if ($liGroup == 1 && $counter != $childrenCount) {
                    $html .= '</ul>';
                    $html .= '<ul class="columns-group inner" ' . $width . '>';
                    $remainingColumnsNumber--;
                } else {
                    if (
                        ($counter % $liGroup == 0 && $counter > 1 && $counter != $childrenCount) ||
                        ($forceBreak && $counter != $childrenCount)
                    ) {
                        $html .= '</ul>';
                        $html .= '<ul class="columns-group inner"' . $width . '>';
                        $remainingColumnsNumber--;
                    }
                }
            }

            /**
             * Mega Menu: close columns-group
             */
            if ($childLevel == 1 && $parent->getWeltpixelMmDisplayMode() != 'default' && $counter == $childrenCount) {
                $html .= '<span class="close columns-group last"></span>';
                $html .= '</ul>';
            }

            $itemPosition++;
            $counter++;
        }

//        if ($colBrakes && count($colBrakes) && $limit) {
        if (is_array($colBrakes) && count($colBrakes) && $limit) {
            $html = '<li class="column"><ul>' . $html . '</ul>';
        }

        return $html;
    }

    /**
     * Add sub menu HTML code for current menu item
     *
     * @param \Magento\Framework\Data\Tree\Node $child
     * @param string $childLevel
     * @param string $childrenWrapClass
     * @param int $limit
     * @return string HTML code
     */
    protected function _addSubMenu($child, $childLevel, $childrenWrapClass, $limit)
    {
        if (!$this->_scopeConfig->getValue(
            'weltpixel_megamenu/megamenu/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )) {
            return parent::_addSubMenu($child, $childLevel, $childrenWrapClass, $limit);
        }
        $html = '';

        /**
         * Mega Menu: settings
         */
        $megaMenuClass = '';
        if ($childLevel == 0) {
            $megaMenuClass = $child->getWeltpixelMmDisplayMode() ? $child->getWeltpixelMmDisplayMode() : 'sectioned';
            $megaMenuClass .= $child->getWeltpixelMmMobHideAllcat() ? ' hide-all-category' : '';
        }
        /**
         * End Mega Menu
         */

        if (!$child->hasChildren()) {
            return $html;
        }

        $colStops = [];
        if ($childLevel == 0 && $limit) {
            $colStops = $this->_columnBrake($child->getChildren(), $limit);
        }

        /**
         * Get Menu Blocks
         */
        $menuBlocks = [];
        $submenuClass = '';
        $submenuChildClass = '';
        if ($childLevel == 0) {
            $blocks = [
                'weltpixel_mm_top_block',
                'weltpixel_mm_right_block',
                'weltpixel_mm_bottom_block',
                'weltpixel_mm_left_block',
            ];
            $menuBlocks = $this->_getMenuBlock($blocks, $child);
            $submenuClass = $this->_getSubmenuClass($menuBlocks);
            $submenuChildClass = $this->_getSubmenuChildClass($menuBlocks);
        }

        // columns settings
        $columnsNumber = $this->_getColumnsNumber($child);
        $columnWidth = $this->_getColumnWidth($child, $columnsNumber);
        $width = $columnWidth ? 'style="width: ' . $columnWidth . '"' : 'style="width: auto"';

        $html .= '<ul class="level' . $childLevel . ' submenu ' . $megaMenuClass . ' ' . $submenuClass . ' ' . $submenuChildClass . '" style="display: none;">';
        if ($childLevel == 0) {
            $html .= '<li class="submenu-child">';
        }

        if ($childLevel == 0 && strpos($megaMenuClass, 'fullwidth') !== false) {
            $html .= '<div class="fullwidth-wrapper">';
            $html .= '<div class="fullwidth-wrapper-inner">';
        }

        /**
         * Insert top and left blocks
         */
        if ($child->getWeltpixelMmDisplayMode() != 'default') {
            $blockCode = 'weltpixel_mm_top_block';
            if ($childLevel == 0 && $menuBlocks[$blockCode] !== false) {
                if (strpos($megaMenuClass, 'fullwidth') !== false) {
                    $html .= '<ul class="columns-group columns-group-block top-group inner">';
                    $html .= '<li class="submenu-child top-block-child">';
                    $html .= '<div class="menu-block top-block block-container">' . $menuBlocks[$blockCode] . '</div>';
                    $html .= '</li>';
                    $html .= '</ul>';
                } else {
                    $html .= '<div class="menu-block top-block block-container">' . $menuBlocks[$blockCode] . '</div>';
                }

                if (strpos($megaMenuClass, 'fullwidth') === false && $megaMenuClass != 'default') {
                    $html .= '</li><!-- close submenu-child -->';
                    $html .= '<li class="submenu-child"><!-- re-open submenu-child -->';
                }
            }

            $blockCode = 'weltpixel_mm_left_block';
            if ($childLevel == 0 && $menuBlocks[$blockCode] !== false) {
                if (strpos($megaMenuClass, 'fullwidth') !== false) {
                    $html .= '<ul class="columns-group columns-group-block left-group inner" ' . $width . '>';
                    $html .= '<li class="submenu-child left-block-child">';
                    $html .= '<div class="menu-block left-block block-container">' . $menuBlocks[$blockCode] . '</div>';
                    $html .= '</li>';
                    $html .= '</ul>';
                } else {
                    $html .= '<div class="menu-block left-block block-container">' . $menuBlocks[$blockCode] . '</div>';
                }

                if (strpos($megaMenuClass, 'fullwidth') === false && $megaMenuClass != 'default') {
                    $html .= '</li><!-- close submenu-child -->';
                    $html .= '<li class="submenu-child"><!-- re-open submenu-child -->';
                }
            }
        }

        $html .= $this->_getHtml($child, $childrenWrapClass, $limit, $colStops);

        /**
         * Insert right and bottom blocks
         */
        if ($child->getWeltpixelMmDisplayMode() != 'default') {
            $blockCode = 'weltpixel_mm_right_block';
            if ($childLevel == 0 && $menuBlocks[$blockCode] !== false) {
                if (strpos($megaMenuClass, 'fullwidth') !== false) {
                    $html .= '<ul class="columns-group columns-group-block right-group inner" ' . $width . '>';
                    $html .= '<li class="submenu-child right-block-child">';
                    $html .= '<div class="menu-block right-block block-container">' . $menuBlocks[$blockCode] . '</div>';
                    $html .= '</li>';
                    $html .= '</ul>';
                } else {
                    $html .= '</li><!-- close submenu-child -->';
                    $html .= '<li class="submenu-child right-block-child"><!-- re-open submenu-child -->';
                    $html .= '<div class="menu-block right-block block-container">' . $menuBlocks[$blockCode] . '</div>';
                    $html .= '</li><!-- close submenu-child -->';
                }
            }

            $blockCode = 'weltpixel_mm_bottom_block';
            if ($childLevel == 0 && $menuBlocks[$blockCode] !== false) {
                if (strpos($megaMenuClass, 'fullwidth') !== false) {
                    $html .= '<ul class="columns-group columns-group-block bottom-group inner">';
                    $html .= '<li class="submenu-child bottom-block-child">';
                    $html .= '<div class="menu-block bottom-block block-container">' . $menuBlocks[$blockCode] . '</div>';
                    $html .= '</li>';
                    $html .= '</ul>';
                } else {
                    $html .= '</li><!-- close submenu-child -->';
                    $html .= '<li class="submenu-child bottom-block-child"><!-- re-open submenu-child -->';
                    $html .= '<div class="menu-block bottom-block block-container">' . $menuBlocks[$blockCode] . '</div>';
                    $html .= '</li><!-- close submenu-child -->';
                }
            }
        }

        if ($childLevel == 0 && strpos($megaMenuClass, 'fullwidth') !== false) {
            $html .= '</div>';
            $html .= '</div>';
        }

        if ($childLevel == 0) {
            $html .= '</li><!-- end submenu-child -->';
        }

        $html .= '</ul><!-- end submenu -->';

        return $html;
    }

    /**
     * Returns array of menu item's classes
     *
     * @param \Magento\Framework\Data\Tree\Node $item
     * @return array
     */
    protected function _getMenuItemClasses(\Magento\Framework\Data\Tree\Node $item)
    {
        $classes = [];

        /**
         * Mega Menu: settings
         */
        $classes[] = 'megamenu';

        if ($item->getLevel() == 0) {
            $displayMode = $item->getWeltpixelMmDisplayMode() ? $item->getWeltpixelMmDisplayMode() : 'sectioned';
            $classes[] = $item->getClass() . '-' . $displayMode;
            $item->getUrl() == $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]) ?
                $classes[] = 'active' :
                $classes[] = '';
        }
        /**
         * End Mega Menu
         */

        $classes[] = 'level' . $item->getLevel();
        $classes[] = $item->getPositionClass();

        if ($item->getIsCategory()) {
            $classes[] = 'category-item';
        }

        if ($item->getIsFirst()) {
            $classes[] = 'first';
        }

        if ($item->getIsActive()) {
            $classes[] = 'active';
        } elseif ($item->getHasActive()) {
            $classes[] = 'has-active';
        }

        if ($item->getIsLast()) {
            $classes[] = 'last';
        }

        if ($item->getClass()) {
            $classes[] = $item->getClass();
        }

        if ($item->hasChildren()) {
            $classes[] = 'parent';
        }

        return $classes;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $this->setModuleName($this->extractModuleName('Magento\Theme\Block\Html\Topmenu'));
        return parent::_toHtml();
    }

    /**
     * Get columns number width from category config
     *
     * @param $item
     * @return string
     */
    protected function _getColumnsNumber($item)
    {
        if ($item->getWeltpixelMmDisplayMode() == 'boxed') {
            $columnsNumber = '1';
        } else {
            $columnsNumber = $item->getWeltpixelMmColumnsNumber() ? trim($item->getWeltpixelMmColumnsNumber()) : '4';
        }

        return $columnsNumber;
    }

    /**
     * Get column width from category config
     *
     * @param $item
     * @param $columnsNumber
     * @return bool|string
     */
    protected function _getColumnWidth($item, $columnsNumber)
    {
        $numbers = (float)preg_replace('/[^0-9.]*/', '', trim($item->getWeltpixelMmColumnWidth()));
        $characters = preg_replace('/[^a-zA-Z%]/', '', trim($item->getWeltpixelMmColumnWidth()));

        switch ($item->getWeltpixelMmDisplayMode()) {
            case 'fullwidth':
                switch (trim(strtolower($characters))) {
                    case '%':
                        $columnWidth = (float)$numbers . '%';
                        break;
                    case 'px':
                        $columnWidth = (int)$numbers . 'px';
                        break;
                    default:
                        $columnWidth = (float)100 / $columnsNumber . '%';
                }
                break;
            case 'sectioned':
                $columnWidth = 'auto';
                break;
            case 'boxed':
                $columnWidth = false;
                break;
            default:
                $columnWidth = false;
                break;
        }

        return $columnWidth;
    }

    /**
     * Generates string with all attributes that should be present in menu item element
     *
     * @param \Magento\Framework\Data\Tree\Node $item
     * @return string
     */
    protected function _getRenderedMenuItemAttributes(\Magento\Framework\Data\Tree\Node $item)
    {
        $html = '';
        $attributes = $this->_getMenuItemAttributes($item);

        if (strpos($item->getUrl(), 'javascript:void') !== false && isset($attributes['class'])) {
            $attributes['class'] .= ' disabled-link';
        }

        foreach ($attributes as $attributeName => $attributeValue) {
            $html .= ' ' . $attributeName . '="' . str_replace('"', '\"', $attributeValue) . '"';
        }
        return $html;
    }

    /**
     * @param $blocks
     * @param $child
     * @return array
     */
    protected function _getMenuBlock($blocks, $child)
    {
        $menuBlocks = [];
        foreach ($blocks as $block) {
            // continue if no block is set
            if ($child->getData($block . '_type') == 'none') {
                $menuBlocks[$block] = false;
                continue;
            }

            $content = '';
            if ($child->getData($block . '_type') == 'block_cms') {
                $cmsBlock = $this->staticBlockRepository->getById($child->getData($block . '_cms'));
                if ($cmsBlock && $cmsBlock->getIsActive()) {
                    $content = $cmsBlock->getContent();
                }
            } else {
                $content = $child->getData($block);
            }

            if ($content != '') {
                $this->templateEnginePool;
                $menuBlocks[$block] = $this->_filterProvider->getPageFilter()->filter($content);
            } else {
                $menuBlocks[$block] = false;
            }
        }

        return $menuBlocks;
    }

    /**
     * @param $menuBlocks
     * @return string
     */
    protected function _getSubmenuClass($menuBlocks)
    {
        $class = '';
        foreach ($menuBlocks as $code => $content) {
            if ($content) {
                $class .= ' has-menu-block';
                break;
            }
        }

        return $class;
    }

    /**
     * @param $menuBlocks
     * @return string
     */
    protected function _getSubmenuChildClass($menuBlocks)
    {
        $class = '';
        foreach ($menuBlocks as $code => $content) {
            $codeArr = explode('_', $code);
            if ($content) {
                $class .= $codeArr[2] . '-block-child ';
            }
        }

        return $class;
    }
}