<?php

class Matheus_CustomOptions_StartController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
		/** Get data */
		$options = array(
			'category' => $this->getRequest()->getPost('categories'),
			'title' => $this->getRequest()->getPost('title'),
			'field_type' => $this->getRequest()->getPost('field_type'),
			'is_require' => $this->getRequest()->getPost('is_require'),
		);
		$p_count = 0;
		$custom_option = $this->buildCustomOption($options);
		/** Get all products in the store */
		$allProducts = Mage::getModel('catalog/product')->getCollection()
			->addAttributeToSelect('*');
		foreach ($allProducts as $product) {
			$inChosenCat = $this->checkIfInChosenCat($product->getCategoryIds(), $options['category']);
			if ($inChosenCat) {
				$product = Mage::getModel('catalog/product')->load($product->getId());
				$optionInstance = $product->getOptionInstance()->unsetOptions();
				$product->setHasOptions(1);
				$optionInstance->addOption($custom_option);
				$optionInstance->setProduct($product);
				$product->save();
				$p_count += 1;
			}
		}
		?>
			<h3 style="color: #39C16C; font-weight: 500">As opções personalizadas foram aplicadas a <b><?php echo $p_count;?></b> produtos!</h3>
		<?php
	}

	private function checkIfInChosenCat($catIds, $chosenCat)
	{
		foreach ($catIds as $category) {
			if ($category == $chosenCat) {
				return true;
			}
		}
		return false;
	}
	private function buildCustomOption($options)
	{
		return array(
			'title' => $options['title'],
			'type' => $options['field_type'],
			'is_require' => $options['is_require'],
			'sort_order' => 0,
			'values' => $this->getCustomOptions(),
		);
	}
	private function getCustomOptions()
	{
		$custom_options = array();
		$titles =  $this->getRequest()->getPost('option_titles');
		for($i = 0; $i < sizeof($titles); $i++){
			array_push($custom_options, array(
				'title' => $titles[$i],
				'price' => $this->getRequest()->getPost('option_prices')[$i],
				'price_type' => $this->getRequest()->getPost('option_price_types')[$i],
				'sort_order' => $this->getRequest()->getPost('option_orders')[$i],
			));
		}
		return $custom_options;
	}
}