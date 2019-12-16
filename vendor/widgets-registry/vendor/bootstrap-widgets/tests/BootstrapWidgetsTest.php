<?php
require_once (__DIR__ . '/../bootstrap-widgets.php');

class BootstrapWidgetsTest extends PHPUnit\Framework\TestCase
{

	/**
	 * Getting template
	 */
	public function test_get_template()
	{
		// setup
		$BootstrapWidgets = new BootstrapWidgets();

		// test body
		$Widget = $BootstrapWidgets->get_widget('table-cell-start');

		// assertions
		$this->assertContains('<td', $Widget, 'Content of the widget "table-cell-start" was not loaded');
	}
}

?>