<?php

class Matheus_CustomOptions_TempController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $data = array(
            'category' => $_POST['category'],
            'title' => $_POST['title'],
            'field_type' => $_POST['field_type'],
            'is_require' => $_POST['is_require'],
            'option_titles' => $_POST['option_titles'],
            'option_prices' => $_POST['option_prices'],
            'option_price_types' => $_POST['option_price_types'],
            'option_orders' => $_POST['option_orders'],
        );
        if (!is_file(__DIR__ . "/../temp/temp.json")) {
            file_put_contents(__DIR__ . "/../temp/temp.json", json_encode($data));
        }
    }
}
