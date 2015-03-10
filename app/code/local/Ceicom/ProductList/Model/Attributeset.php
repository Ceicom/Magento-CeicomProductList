<?php
class Ceicom_ProductList_Model_Attributeset extends Mage_Core_Model_Abstract
{

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
