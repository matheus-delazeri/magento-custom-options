<?php

define('PRODUCTS_PER_TIME', 100);

class Matheus_CustomOptions_TempController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $temp_dir = __DIR__ . "/../temp/temp.json";
        if (!is_file($temp_dir)) {
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
            $products_id = array();
            $products = Mage::getModel('catalog/category')->load($data['category'])
                ->getProductCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('visibility', 4);
            foreach ($products as $product) {
                array_push($products_id, $product->getId());
            }
            $data['all_products'] = $products_id;
            $data['products_left'] = $products_id;
            $data['products_now'] = array_slice($data['products_left'], 0, PRODUCTS_PER_TIME);
            $data['products_ok'] = $data['products_now'];
            file_put_contents($temp_dir, json_encode($data));
        } else {
            $data = json_decode(file_get_contents($temp_dir), true);
            $data['products_left'] = array_diff($data['products_left'], $data['products_ok']);
            $data['products_now'] = array_slice($data['products_left'], 0, PRODUCTS_PER_TIME);
            $data['products_ok'] = array_merge($data['products_ok'], $data['products_now']);
            unlink($temp_dir);
            file_put_contents($temp_dir, json_encode($data));
            if(sizeof($data['products_left']) == 0){
                unlink($temp_dir);
                echo 'end';
            } else {
                echo 'continue';
            }
        }
    }
}
