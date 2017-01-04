<?php
class Ceicom_ProductList_Block_Widget_List extends Mage_Catalog_Block_Product_Abstract
    implements Mage_Widget_Block_Interface
{
    const TEMPLATE_TYPE_GRID = 'grid';
    const TEMPLATE_TYPE_LIST = 'list';
    const TEMPLATE_TYPE_CUSTOM = 'custom';
    const DEFAULT_PRODUCTS_COUNT_LIMIT = 8;
    const DEFAULT_GRID_COLUMN_COUNT = 4;

    protected $_cacheKeyInfo;
    protected $_productCollection;

    protected function _construct()
    {
        parent::_construct();

        $this->addData(array('cache_lifetime' => 86400));
        $this->addCacheTag(Mage_Catalog_Model_Product::CACHE_TAG);
    }

    public function getCacheKeyInfo()
    {
        if (is_null($this->_cacheKeyInfo)) {
            $this->_cacheKeyInfo = array(
                'CEICOM_PRODUCT_LIST',
                Mage::app()->getStore()->getId(),
                Mage::getDesign()->getPackageName(),
                Mage::getDesign()->getTheme('template'),
                Mage::getSingleton('customer/session')->getCustomerGroupId(),
                Mage::app()->getStore()->isCurrentlySecure(),
                $this->getProductsCountLimit(),
                'template'               => $this->_getTemplate(),
                'category_ids'           => $this->getData('category_ids'),
                'filter_attribute_code'  => $this->getData('filter_attribute_code'),
                'filter_attribute_value' => $this->getData('filter_attribute_value'),
                'sort_attribute_code'    => $this->getData('sort_attribute_code'),
                'sort_attribute_order'   => $this->getData('sort_attribute_order'),
                'random_products'        => $this->getData('random_products')
            );

            if ((bool) $this->getData('cache_formkey')) {
                $this->_cacheKeyInfo[] = Mage::getSingleton('core/session')->getFormKey();
            }
        }

        return $this->_cacheKeyInfo;
    }

    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $this->_productCollection = Mage::getResourceModel('catalog/product_collection');
            $this->_productCollection->setVisibility(Mage::getSingleton('catalog/product_visibility')
                ->getVisibleInCatalogIds());
            $this->_productCollection = $this->_addProductAttributesAndPrices($this->_productCollection);
            $this->_addCategoryFilterToCollection();
            $this->_addAttributeFilterToCollection();
            $this->_sortCollection();

            if (!(bool) $this->getData('show_out_of_stock')) {
                Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($this->_productCollection);
            }

            if ((bool) $this->getData('random_products')) {
                $this->_productCollection->getSelect()->order('rand()');
            }

            $this->_productCollection->addStoreFilter()
                ->setPageSize($this->getProductsCountLimit())
                ->setCurPage(1);

            $this->_productCollection->getSelect()->group('e.entity_id');
        }

        return $this->_productCollection;
    }

    protected function _addCategoryFilterToCollection()
    {
        if ($this->hasData('category_ids')) {
            $categoryIds = explode(',', $this->getData('category_ids'));
            $this->_productCollection->joinField(
                'category_id',
                'catalog/category_product',
                'category_id',
                'product_id = entity_id',
                null,
                'left'
            )->addAttributeToFilter('category_id', array('in' => $categoryIds));
        }
    }

    protected function _addAttributeFilterToCollection()
    {
        if ($this->hasData('filter_attribute_code') && $this->hasData('filter_attribute_value')) {
            $this->_productCollection->addAttributeToFilter(
                $this->getData('filter_attribute_code'),
                $this->getData('filter_attribute_value')
            );
        }
    }

    protected function _sortCollection()
    {
        $attributeToSort = $this->hasData('sort_attribute_code') ? $this->getData('sort_attribute_code') : 'created_at';
        $sortOrder = $this->hasData('sort_attribute_order') ? $this->getData('sort_attribute_order') : 'desc';

        $this->_productCollection->addAttributeToSort($attributeToSort, $sortOrder);
    }

    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }

    public function getMode()
    {
        if ($this->_getTemplateType() == self::TEMPLATE_TYPE_LIST) {
            return 'list';
        } else {
            return 'grid';
        }
    }

    public function getColumnCount()
    {
        if (!$this->hasData('grid_column_count')) {
            $this->setData('grid_column_count', self::DEFAULT_GRID_COLUMN_COUNT);
        }

        return $this->getData('grid_column_count');
    }

    public function getProductsCountLimit()
    {
        if (!$this->hasData('products_count_limit')) {
            $this->setData('products_count_limit', self::DEFAULT_PRODUCTS_COUNT_LIMIT);
        }

        return $this->getData('products_count_limit');
    }

    private function _getTemplateType()
    {
        if (!$this->hasData('template_type')) {
            $this->setData('template_type', self::TEMPLATE_TYPE_GRID);
        }

        return $this->getData('template_type');
    }

    private function _getTemplate()
    {
        if ($this->_getTemplateType() == self::TEMPLATE_TYPE_CUSTOM && $this->hasData('custom_template')) {
            return $this->getData('custom_template');
        } else {
            return $this->getTemplate();
        }
    }

    protected function _beforeToHtml()
    {
        $this->setTemplate($this->_getTemplate());
        $this->setProductCollection($this->_getProductCollection());

        return parent::_beforeToHtml();
    }
}
