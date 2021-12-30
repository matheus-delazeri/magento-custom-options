<?php

ini_set('max_execution_time', 0);
ini_set('output_buffering', 'Off');
header('Content-type: text/html; charset=utf-8');
if (ini_get('zlib.output_compression')) {
	ini_set('zlib.output_compression', 'Off');
}
date_default_timezone_set('America/Sao_Paulo');
define('PRODUCTS_PER_TIME', 100);

while (@ob_end_flush());
ob_start();

class Matheus_CustomOptions_StartController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
		/** Get data */
		$options = array();
		if (is_file(__DIR__ . "/../temp/temp.json")) {
			$options = json_decode(file_get_contents(__DIR__ . "/../temp/temp.json"), true);
		} else {
			$this->display_log(true, "temp.json not created.");
		}
		/** Init prints */
		if (sizeof($options['products_ok']) <= PRODUCTS_PER_TIME) {
			$this->display_log(false, "Products found <b>[" . sizeof($options['all_products']) . "]</b>");
			$this->display_log(false, "Starting...<p id='products'></p>");
		}
		$p_count = 0;
		if ($options['title'] == '') {
			$this->display_log(true, "Field <b>Title</b> can't be empty.");
		}
		$custom_option = $this->buildCustomOption($options);
		foreach ($options['products_now'] as $product_id) {
			$product = Mage::getModel('catalog/product')->load($product_id);
			$optionInstance = $product->getOptionInstance()->unsetOptions();
			$product->setHasOptions(1);
			$optionInstance->addOption($custom_option);
			$optionInstance->setProduct($product);
			$product->save();
			$p_count += 1;
		}
		if(sizeof($options['all_products']) > 0) {
			$progress = round(100 * (sizeof($options['products_ok']) / sizeof($options['all_products'])), 2).'%';
		} else {
			$progress = 'No product found.';
		}
		$this->display_log(false, "Progress: <b>" . $progress . "</b>", "products", false);
		if (end($options['products_left']) == $product_id) {
			$_cat = Mage::getModel('catalog/category')->load($options['category']);
			$this->display_log(true, "All products from <b>" . $_cat->getName() . "</b> updated.");
			ob_end_clean();
		}
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
		$titles = $options['option_titles'] == '' ? null : $this->breakStrInArray($options['option_titles']);
		if ($titles == null) {
			$this->display_log(true, "Add at least one line.");
		}
		$prices = $this->breakStrInArray($options['option_prices']);
		$price_types = $this->breakStrInArray($options['option_price_types']);
		$orders = $this->breakStrInArray($options['option_orders']);
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
		$arr = explode($limiter, $str);
		if (sizeof($arr) > 0) {
			return $arr;
		} else {
			return 0;
		}
	}
	function display_log($is_final, $msg, $el_id = "progress", $append = true)
	{
		if ($append) {
?>
			<script>
				parent.document.getElementById("<?php echo $el_id; ?>").innerHTML += "<p>[<?php echo date('H:i:s') ?>] <?php echo $msg; ?></p>";
			</script>
		<?php
		} else {
		?>
			<script>
				parent.document.getElementById("<?php echo $el_id; ?>").innerHTML = "<p>[<?php echo date('H:i:s') ?>] <?php echo $msg; ?></p>";
			</script>
		<?php
		}
		if ($is_final) {
		?>
			<script>
				parent.document.getElementById("loader").innerHTML = "";
			</script>
<?php
			echo str_pad('', 4096) . "\n";
			flush();
			ob_flush();
			exit(0);
		}
		echo str_pad('', 4096) . "\n";
		flush();
		ob_flush();
	}
}
