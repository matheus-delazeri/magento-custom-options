<?php

class Matheus_CustomOptions_TempController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $data = array(
            'category' => $_GET['category'],
            'title' => $_GET['title'],
            'field_type' => $_GET['field_type'],
            'is_require' => $_GET['is_require'],
            'option_titles' => $_GET['option_titles'],
            'option_prices' => $_GET['option_prices'],
            'option_price_types' => $_GET['option_price_types'],
            'option_orders' => $_GET['option_orders'],
        );
        if (!is_file(__DIR__ . "/../temp/temp.json")) {
            file_put_contents(__DIR__ . "/../temp/temp.json", json_encode($data));
        }
    }
}
