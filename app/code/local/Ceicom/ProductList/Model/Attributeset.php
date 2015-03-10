<?php
/* 
* @Author: Jonatan
* @Date:   2014-01-15 10:48:51
* @Last Modified by:   Jonatan
* @Last Modified time: 2015-03-10 10:32:22
*/
class Ceicom_ProductList_Model_Attributeset extends Mage_Core_Model_Abstract
{

    /**
     * Provides a value-label array of available options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAttributeSetList();
        
    }
    
    public function getAttributeSetList()
    {

    	$attributes = Mage::getModel('catalog/product')->getAttributes();
	    $attributeArray[] = array('label' => 'Selecione...', 'value' => '');

	    foreach($attributes as $a){
	        foreach ($a->getEntityType()->getAttributeCodes() as $attributeCode) {
	            $attributeArray[] = array(
	                'label' => $attributeCode,
	                'value' => $attributeCode
	            );
	        }

	        break;
	    }

	    return $attributeArray; 
	        
	}
}
