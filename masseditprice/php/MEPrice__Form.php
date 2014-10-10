<?php

/**
 * Builder for the configuration form
 */
class MEPrice__Form {
    /**
     * Forms for user input
     * @var array
     */
    protected $forms;
    /**
     * @var Module 
     */
    protected $module;
    /**
     * Input values
     * @var array
     */
    protected $values;


    public function __construct($module) {
        $this->forms = array();
        $this->module = $module;
        $this->values = array();
    }
    
    /**
     * Get input forms
     * @return array
     */
    public function build() {
       return $this->forms; 
    }

    /**
     * Get form input values
     * @return array
     */
    public function getValues() {
        return $this->values;
    }

    /**
     * Set items to build user input
     * @param array $items
     */
    public function setItems($items) {
        $forms = array();
        
        foreach ($items as $id_product => $item) {
            $name_format = 'product['.$id_product.'][%s]';
            
            $inputs = array(
                $this->input(sprintf($name_format, 'price'), $item['price'], 'text', 'Price (tax excl.)'),
                $this->input(sprintf($name_format, 'wholesale_price'), $item['wholesale_price'], 'text', 'Wholesale price (tax excl.)'),
                #$this->input(sprintf($name_format, 'unit_price_ratio'), $item['unit_price_ratio'], 'text')
            );
            
            //add user input for each product attribute, if any
            //hidden input are needed for Product->updateAttribute()
            foreach ($item['attributes'] as $id_product_attribute => $pattr) {
                $name_format = 'product['.$id_product.'][attributes]['.$id_product_attribute.'][%s]';
                
                $inputs[] = $this->input(sprintf($name_format, 'price'), $pattr['price'], 'text', $pattr['name'].' - Price (tax incl.)');
                $inputs[] = $this->input(sprintf($name_format, 'wholesale_price'), $pattr['wholesale_price'], 'text', $pattr['name'].' - Wholesale price (tax incl.)');
                $inputs[] = $this->input(sprintf($name_format, 'weight'), $pattr['price'], 'hidden');
                $inputs[] = $this->input(sprintf($name_format, 'unit_price_impact'), $pattr['unit_price_impact'], 'hidden');
                $inputs[] = $this->input(sprintf($name_format, 'ecotax'), $pattr['ecotax'], 'hidden');
                $inputs[] = $this->input(sprintf($name_format, 'reference'), $pattr['reference'], 'hidden');
                $inputs[] = $this->input(sprintf($name_format, 'ean13'), $pattr['ean13'], 'hidden');
                $inputs[] = $this->input(sprintf($name_format, 'default_on'), $pattr['default_on'], 'hidden');
            }
            
            $forms[] = array(
                'legend' => array(
                    'title' => $item['name']
                ),
                'input' => $inputs,
                'submit' => array(
                    'title' => $this->l('Save')
                )
            );
        }
        
        $this->forms = $forms;
    }
    
    /**
     * Update Product with user values
     */
    public function updateOnPOST() {
        if (Tools::getValue('bettermasseditproduct')) {
            $POST = filter_input_array(INPUT_POST);
            $products = isset($POST['product']) ? $POST['product'] : array();
            
            //there should be only one product, but in case there're more..
            foreach ($products as $id_product => $product) {
                $product_obj = new Product($id_product);
                
                //update db and item in $items (to update form)
                $product_obj->price = (float)$product['price'];
                $product_obj->wholesale_price = (float)$product['wholesale_price'];
                
                //update combinations
                foreach ($product['attributes'] as $id_product_attribute => $pattr) {
                    //update db
                    $product_obj->updateAttribute(
                            $id_product_attribute,
                            (float)$pattr['wholesale_price'],
                            (float)$pattr['price'],
                            $pattr['weight'],
                            $pattr['unit_price_impact'],
                            $pattr['ecotax'],
                            array(), //id_images
                            $pattr['reference'],
                            $pattr['ean13'],
                            $pattr['default_on']
                        );
                }
                
                $product_obj->update();
                
                return array(
                    'code' => 200,
                    'msg' => 'done'
                );
            }
        }
        else
            return array(
                'code' => 500,
                'msg' => 'not POST request'
            );
    }
    
    /**
     * Build an input
     * @param string $name
     * @param mixed $value
     * @param string $type
     * @param string $label
     * @return array
     */
    protected function input($name, $value, $type, $label = '') {
        $this->values[$name] = $value;
        
        if (!$label)
            $label = ucfirst(str_replace ('_', ' ', $name));
        
        return array(
            'name' => $name,
            'type' => $type,
            'label' => $this->l($label)
        );
    }
    
    protected function l($str) {
        return $this->module ? $this->module->l($str) : $str;
    }
    
}
