<?php

require dirname(__FILE__).'/MEPrice__Combination.php';

/**
 * Relates product to attributes
 */
class MEPrice__ProductAttribute extends MEPrice__Abstract {
    protected $combinations;
    
    public function __construct() {
        $this->combinations = new MEPrice__Combination();
        parent::__construct();
    }

    protected function tableName() {
        return 'product_attribute';
    }    
    
    /**
     * Index items by id_product
     * sub-index by id_product_attribute
     * @param int $index
     * @param mixed $item
     * @return array
     */
    protected function map($index, $item) {
        $new_index = $item['id_product'];
        $reserve_index = $item['id_product_attribute'];
        //all this are needed for Product->updateAttribute()
        $new_item = array(
            'name' => $this->combinations->getAttributeName($item['id_product_attribute']),
            'wholesale_price' => $item['wholesale_price'],
            'price' => $item['price'],
            'weight' => $item['weight'],
            'unit_price_impact' => $item['unit_price_impact'],
            'ecotax' => $item['ecotax'],
            'reference' => $item['reference'],
            'ean13' => $item['ean13'],
            'default_on' => $item['default_on']
        );
        
        return array($new_index, $new_item, $reserve_index);
    }
}
