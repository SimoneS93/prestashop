<?php

require dirname(__FILE__).'/MEPrice__ProductAttribute.php';

/**
 * Get products from db
 */
class MEPrice__Product extends MEPrice__Abstract {
    protected $product_attributes;
    
    /**
     * Init product attributes
     */
    public function __construct() {
        $this->product_attributes = new MEPrice__ProductAttribute();
        parent::__construct();
    }

    /**
     * Abstract method
     * get db table name
     * @return string
     */
    protected function tableName() {
        return 'product';
    }    
    
    /**
     * Join products with products_lang
     * //TODO: lang is not required, remove it
     * @return array
     */
    protected function fetch() {
        global $cookie;

        $this->query->leftJoin('product_lang', 'pl', 'p.id_product = pl.id_product');
        $this->query->select('p.id_product, p.price, p.wholesale_price, p.unit_price_ratio, pl.name');
        //don't know why doens't allow $query->where('al.id_lang', $cookie->id_lang)
        $this->query->where('pl.id_lang = ' . pSQL($cookie->id_lang));
        
        return $this->query('p');
    }
    
    /**
     * Index items by id_product
     * @param int $index
     * @param mixed $item
     * @return array
     */
    protected function map($index, $item) {
        $new_index = $item['id_product'];
        $new_item = array(
            'name' => $item['name'],
            'price' => $item['price'],
            'wholesale_price' => $item['wholesale_price'],
            'unit_price_ratio' => $item['unit_price_ratio'],
            'attributes' => $this->product_attributes->at($item['id_product'])
        );
        
        return array($new_index, $new_item, null);
    }
}
