<?php

class ContentServiceModelTest
{

    /**
     * Method tests increment_views method
     */
    public function test_increment_views()
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

?>