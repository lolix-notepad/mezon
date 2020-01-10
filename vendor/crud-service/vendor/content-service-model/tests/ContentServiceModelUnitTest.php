<?php
require_once (__DIR__ . '/../../../../../autoloader.php');

class ContentServiceModelUnitTest
{

    /**
     * Method tests increment_views method
     */
    public function testIncrementViews()
    {
        // setup
        $Connection = $this->get_connection_mock();
        $Connection->expects($this->once())
            ->method('update');
        $Mock = $this->get_model_mock($Connection);

        // test body and assertions
        $Mock->increment_views(false, 1);
    }
}
