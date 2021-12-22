<?php

ini_set('max_execution_time', 0);
ini_set('output_buffering', 'Off');
header('Content-type: text/html; charset=utf-8');
if (ini_get('zlib.output_compression')) {
	ini_set('zlib.output_compression', 'Off');
}
date_default_timezone_set('America/Sao_Paulo');

while (@ob_end_flush());
ob_start();

class Matheus_CustomOptions_StartController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
		$this->display_log();
		/** Get data */
		if (is_file("temp.json")) {
			$options = json_decode(file_get_contents(__DIR__ . "/../temp/temp.json"), true);
			unlink(__DIR__ . "/../temp/temp.json");
		}
		$p_count = 0;
		if ($options['title'] == '') {
?>
			<script>
				parent.document.getElementById("progress").innerHTML = "[<?php echo date('H:i:s') ?>] O campo <b>Título</b> não pode ser vazio.</p>";
				parent.document.getElementById("loader").innerHTML = "";
			</script>
		<?php
			exit(0);
		}
		$this->display_log();
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
		?>
			<script>
				parent.document.getElementById("progress").innerHTML = "[<?php echo date('H:i:s'); ?>] Produtos atualizados <b>[<?php echo $p_count; ?>]</b></p>";
			</script>
		<?php
		}
		$this->display_log();
		?>
		<script>
			parent.document.getElementById("progress").innerHTML += "[<?php echo date('H:i:s'); ?>] Processo finalizado!</p>";
			parent.document.getElementById("loader").innerHTML = "";
		</script>
<?php
		$this->display_log();
		ob_end_clean();
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
			'values' => $this->getCustomOptions($options),
		);
	}
	private function getCustomOptions($options)
	{
		$custom_options = array();
		$titles = $this->breakStrInArray($options['option_titles']);
		$prices = $this->breakStrInArray($options['option_prices']);
		$price_types = $this->breakStrInArray($options['option_price_types']);
		$orders = $this->breakStrInArray($options['option_orders']);
		if(sizeof($titles)==0) {
			?>
			<script>
				parent.document.getElementById("progress").innerHTML = "[<?php echo date('H:i:s') ?>] Nenhuma linha adicionada.</p>";
				parent.document.getElementById("loader").innerHTML = "";
			</script>
		<?php
			exit(0);
		}
		for ($i = 0; $i < sizeof($titles); $i++) {
			array_push($custom_options, array(
				'title' => $titles[$i],
				'price' => $prices[$i],
				'price_type' => $price_types[$i],
				'sort_order' => $orders[$i],
			));
		}
		return $custom_options;
	}
	private function breakStrInArray($str)
	{
		$limiter = ";";
		return explode($limiter, $str);
	}
	function display_log()
	{
		echo str_pad('', 4096) . "\n";
		flush();
		ob_flush();
	}
}
