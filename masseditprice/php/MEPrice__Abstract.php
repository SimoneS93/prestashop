<?php

/**
 * A parent to repetitive tasks for query the DB
 * and manipulate a list of items
 */
abstract class MEPrice__Abstract {
    /**
     * List of items
     * @var array
     */
    protected $items;
    /**
     * Query builder
     * @var DbQueryCore
     */
    protected $query;


    /**
     * Get db table name
     * @return string
     */
    protected abstract function tableName();

    
    public function __construct() {
        $this->query = new DbQueryCore();
        $this->items = $this->fetch();
    }
    
    /**
     * Get item at index
     * @param mixed $index
     * @return array
     */
    public function at($index) {
        return isset($this->items[$index]) ? $this->items[$index] : array();
    }

    /**
     * Dump items
     */
    public function dump() {
        print '<pre>';
        print_r($this->items);
        print '</pre>';
    }
    
    /**
     * Get items as array
     * @return array
     */
    public function toArray() {
        return $this->items;
    }
    
    /**
     * Update item prop
     * @param mixed $index
     * @param mixed $prop
     * @param mixed $value
     */
    public function updateProp($index, $prop, $value, $reserve_index = null) {
        $item = $this->at($index);
        if ($item) {
            if ($reserve_index)
                $item[$reserve_index][$prop] = $value;
            else
                $item[$prop] = $value;
        }
    }

    /**
     * Populate items
     * super method does nothing, here for override
     * @return array
     */
    protected function fetch() {
        return $this->query();
    }

    /**
     * Query db
     * @param string $alias
     * @return array
     */
    protected function query($alias = null) {
        //force 'from' source
        $this->query->from($this->tableName(), $alias);
        $sql = $this->query->build();
        $data = Db::getInstance()->executeS($sql);
        $map = array();

        foreach ($data as $index => $item) {
            list($new_index, $new_item, $reserve_index) = $this->map($index, $item);
            //allow 2-level depth in mapped result (for ProductAttribute)
            if ($reserve_index)
                $map[$new_index][$reserve_index] = $new_item;
            else
                $map[$new_index] = $new_item;
        }
        
        return $map;
    }
    
    /**
     * Map db results
     * super method does nothing, here for override
     * @param int $index
     * @param array $item
     * @return array
     */
    protected function map($index, $item) {
        return array($index, $item, null);
    }
    
}
