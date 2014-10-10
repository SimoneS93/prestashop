<?php

if (!defined('_PS_VERSION_'))
	exit;

require dirname(__FILE__).'/php/MEPrice__Abstract.php';
require dirname(__FILE__).'/php/MEPrice__Product.php';
require dirname(__FILE__).'/php/MEPrice__Form.php';


class MassEditPrice extends Module
{
    protected $id_lang;


    public function __construct()
    {
        global $cookie;
        
        $this->name = 'masseditprice';
        $this->tab = 'quick_bulk_update';
        $this->version = '1.0';
        $this->author = 'Simone Salerno';

        parent::__construct();

        $this->displayName = $this->l('Mass edit - Price edition');
        $this->description = $this->l('Mass edit products prices (with combinations).');
        $this->bootstrap = true;
        
        $this->id_lang = $cookie->id_lang;
    }
    
    public function install()
    {
        if (!parent::install())
            return false;
        
        return true;
    }
    
    public function getContent()
    {           
        $form = new MEPrice__Form($this);
        $form->updateOnPOST();
        $products = new MEPrice__Product();     
        $form->setItems($products->toArray());
        $values = $form->getValues();
        $forms = $form->build();
        $helper = $this->formHelper();
        $helper->tpl_vars['fields_value'] = $values;
        $return = '';
        
        foreach ($forms as $f) {
            $return .= $helper->generateForm(array(array('form' => $f)));
        }        
        
        return $return;
    }
    
    /**
     * HelperForm boilerplate
     * @param Module $module
     * @param Context $context
     * @param type $identifier
     * @param type $table
     * @param type $uri
     * @param type $id_lang
     * @return \HelperForm
     */
    protected function formHelper() {
        $module = $this;
        $context = $this->context;
        $identifier = $this->identifier;
        $table = $this->table;
        $uri = $this->getPathUri();
        $id_lang = $this->id_lang;
        
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table =  $table;
        $helper->default_form_language = $id_lang;
        $helper->module = $module;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
        $helper->identifier = $identifier;
        $helper->submit_action = $module->name;
        $query = '&configure='.$module->name.'&tab_module='.$module->tab.'&module_name='.$module->name;
        $helper->currentIndex = $context->link->getAdminLink('AdminModules', false).$query;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
          'uri' => $uri,
          'languages' => $context->controller->getLanguages(),
          'id_language' => $context->language->id
        );
        return $helper;
    }
}
