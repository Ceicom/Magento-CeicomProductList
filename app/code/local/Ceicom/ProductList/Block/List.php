<?php
class Ceicom_ProductList_Block_List extends Mage_Catalog_Block_Product_Abstract implements Mage_Widget_Block_Interface 
{

    protected function _toHtml()
    {
        $arguments = array(
            'categoryId' => explode('/', $this->getData('category'))[1],
            'attributeCode' => $this->getData('attribute_set'),
            'attributeValue' => $this->getData('attribute_value'),
            'maxProductList' => $this->getData('max_product_list'),
            'showInStock' => $this->getData('show_in_stock')
        );
        
        if (($arguments['maxProductList'] == '') || ($arguments['maxProductList'] == NULL)) {
            $arguments['maxProductList'] = 5;
        }

        $this->assign('title',$this->getData('title'));
        $this->assign('showInStock',$arguments['showInStock']);
        $template = trim($this->getData('custom_template'));
        $templateExtension = substr($template, - 5);

        if (($template != '')&&($templateExtension == 'phtml')) {
            $this->setTemplate($template);
        } else {
            $this->setTemplate('ceicom/productlist/list.phtml');
        }
        
        $this->setProductCollectionFiltered($arguments);

        return parent::_toHtml();
    }

    protected function setProductCollectionFiltered($arguments)
    {
        $category = Mage::getModel('catalog/category')->load($arguments['categoryId']);
        $products = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect(array('name', 'price', 'small_image', 'short_description', 'special_price'))
			->addMinimalPrice()
			->addFinalPrice()
            ->addCategoryFilter($category)
            ->addAttributeToSort('created_at', 'desc')
            ->setPageSize($arguments['maxProductList'])
            ->setCurPage(1);

        if (!$arguments['showInStock']) {
            Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($products);
        }
        
        if ($arguments['attributeValue'] != '') {            
            $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $arguments['attributeCode']);            
            $attributeOptionId = $attribute->getSource()->getOptionId($arguments['attributeValue']);            
            $products->addAttributeToFilter($arguments['attributeCode'], $attributeOptionId);
        }

        $this->setProductCollection($products);
    }

}