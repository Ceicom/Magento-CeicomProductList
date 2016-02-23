<?php
class Ceicom_ProductList_Model_System_Config_Source_Attribute
{
	protected $_options;

	public function toOptionArray()
    {
    	if (!$this->_options) {
    		$attributeCollection = Mage::getResourceModel('catalog/product_attribute_collection')
    			->addFieldToFilter('additional_table.is_visible', 1)
    			->setOrder('frontend_label', Varien_Data_Collection::SORT_ORDER_ASC);

			$this->_options[] = array(
                'label' => Mage::helper('ceicom_productlist')->__('-- Please Select an Attribute --'),
                'value' => ''
            );

    		foreach ($attributeCollection as $attribute) {
    			$this->_options[] = array(
        			'label' => $attribute->getFrontendLabel(),
        			'value' => $attribute->getAttributeCode()
        		);
    		}
	    }

    	return $this->_options;
    }
}