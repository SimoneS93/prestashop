<?php

require_once dirname(__FILE__).'/MEPrice__Attribute.php';

/**
 * Relates attributes to product_attributes
 */
class MEPrice__Combination extends MEPrice__Abstract {
    protected $attributes;
    
    public function __construct() {
        parent::__construct();
        $this->attributes = new MEPrice__Attribute();
    }

    /**
     * Get attribute name from id_product_attribute
     * @param int $id_product_attribute
     * @return string
     */
    public function getAttributeName($id_product_attribute) {
        $combination = $this->at($id_product_attribute);
        $attribute = $this->attributes->at($combination['id_attribute']);
        return $attribute ? $attribute['name'] : 'unknown';
    }

    protected function tableName() {
        return 'product_attribute_combination';
    }    
    
    /**
     * Index items by id_product_attribute
     * @param int $index
     * @param mixed $item
     * @return array
     */
    protected function map($index, $item) {
        $new_index = $item['id_product_attribute'];
        $new_item = array(
            'id_attribute' => $item['id_attribute']
        );
        
        return array($new_index, $new_item, null);
    }
}
