<?php
class Ceicom_ProductList_Model_System_Config_Source_Category
{
	protected $_options;

	public function toOptionArray()
    {
    	if (!$this->_options) {
	    	$categoryCollection = Mage::getModel('catalog/category')->getCollection()
	    		->addAttributeToSelect('name')
	    		->addIsActiveFilter()
	    		->addAttributeToSort('path', 'asc');

	    	foreach ($categoryCollection as $category) {
	    		if ($category->getLevel() > 1) {
	    			$labelPrefix = str_repeat('—', $category->getLevel()) . ' ';
	    		} else {
	    			$labelPrefix = '';
	    		}

	    		$this->_options[] = array(
        			'label' => $labelPrefix . $category->getName(),
        			'value' => $category->getId()
        		);
	    	}
	    }

    	return $this->_options;
    }
}