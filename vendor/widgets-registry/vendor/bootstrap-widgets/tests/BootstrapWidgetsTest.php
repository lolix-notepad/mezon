<?php

class BootstrapWidgetsTest extends PHPUnit\Framework\TestCase
{

	/**
	 * Getting template
	 */
	public function testGetTemplate()
	{
		// setup
		$BootstrapWidgets = new \Mezon\WidgetsRegistry\BootstrapWidgets();

		// test body
		$Widget = $BootstrapWidgets->getWidget('table-cell-start');

		// assertions
		$this->assertContains('<td', $Widget, 'Content of the widget "table-cell-start" was not loaded');
	}
}

?>