<?php

/**
 * Used only to grab attributes names for ProductAttributes
 */
class MEPrice__Attribute extends MEPrice__Abstract {
    
    protected function tableName() {
        return 'attribute';
    }    
    
    /**
     * Join attributes with attributes_lang
     * @return array
     */
    protected function fetch() {
        global $cookie;

        $this->query->leftJoin('attribute_lang', 'al', 'a.id_attribute = al.id_attribute');
        $this->query->select('a.id_attribute, al.name');
        //don't know why doens't allow $query->where('al.id_lang', $cookie->id_lang)
        $this->query->where('al.id_lang = ' . pSQL($cookie->id_lang));
        
        return $this->query('a');
    }
    
    /**
     * Index items by id_attribute
     * @param int $index
     * @param mixed $item
     * @return array
     */
    protected function map($index, $item) {
        $new_index = $item['id_attribute'];
        $new_item = array(
            'name' => $item['name']
        );
        
        return array($new_index, $new_item, null);
    }
}
