<?php
require_once (__DIR__ . '/../../functional/functional.php');
require_once (__DIR__ . '/../../singleton/singleton.php');
require_once (__DIR__ . '/../template-engine.php');

class TemplateEngineTest extends PHPUnit\Framework\TestCase
{

    /**
     * Simple vars
     */
    public function test_simple_substitutions_array()
    {
        $Data = array(
            'var1' => 'v1',
            'var2' => 'v2'
        );
        $String = '{var1} {var2}';

        $String = TemplateEngine::print_record($String, $Data);

        $this->assertEquals($String, 'v1 v2', 'Invalid string processing');
    }

    /**
     * Simple vars
     */
    public function test_simple_substitutions_object()
    {
        $Data = new stdClass();
        $Data->var1 = 'v1';
        $Data->var2 = 'v2';
        $String = '{var1} {var2}';

        $String = TemplateEngine::print_record($String, $Data);

        $this->assertEquals($String, 'v1 v2', 'Invalid string processing');
    }

    /**
     * Invalid objects
     */
    public function test_simple_substitutions_invalid_objects()
    {
        $Msg = '';

        try {
            $String = '';
            $String = TemplateEngine::print_record($String, false);
        } catch (Exception $e) {
            $Msg = $e->getMessage();
        }

        $this->assertEquals('Invalid record was passed', $Msg, 'Invalid behavior');

        try {
            $String = '';
            $String = TemplateEngine::print_record($String, null);
        } catch (Exception $e) {
            $Msg = $e->getMessage();
        }

        $this->assertEquals('Invalid record was passed', $Msg, 'Invalid behavior');

        try {
            $String = '';
            $String = TemplateEngine::print_record($String, 1234);
        } catch (Exception $e) {
            $Msg = $e->getMessage();
        }

        $this->assertEquals('Invalid record was passed', $Msg, 'Invalid behavior');

        try {
            $String = '';
            $String = TemplateEngine::print_record($String, 'string');
        } catch (Exception $e) {
            $Msg = $e->getMessage();
        }

        $this->assertEquals('Invalid record was passed', $Msg, 'Invalid behavior');
    }

    /**
     * Data provider for tests of the switch macro
     *
     * @return array data sets
     */
    public function switch_tests_data(): array
    {
        return (json_decode('[["{switch:1}{case:1}1{~case}{case:2}2{~case}{~switch}",[],"1"],["{foreach:field}{content}{~foreach}",{"field":[{"content":"1"},{"content":"2"}]},"12"],["{foreach:field}{n}{~foreach}",{"field":[{"f":1},{"f":2}]},"12"],["{switch:2}{case:1}1{~case}{case:2}2{~case}{~switch}",[],"2"],["{switch:0}{case:0}0{~case}{case:1}1{~case}{~switch}",[],"0"],["{switch:{value}}{case:0}0{~case}{case:1}1{~case}{~switch}",[],"{switch:{value}}{case:0}0{~case}{case:1}1{~case}{~switch}"],["{print:field}{content1}{content2}{~print}",{"field":[{"content1":"1"},{"content2":"2"}]},"12"],["{switch:{field3}}{case:3}Done!{~case}{~switch}",{"field1":1,"field2":{"f1":"11","f2":"22"},"field3":3},"Done!"],["{var1} {var2} {var3}",{"var1":"v1","var2":"v2","field":{"var3":"v3"}},"v1 v2 v3"]]', true));
    }

    /**
     * Method tests switch macro
     *
     * @dataProvider switch_tests_data
     */
    public function test_switch_macro(string $Str, array $Data, string $Result)
    {
        // test body
        $Data = TemplateEngine::print_record($Str, $Data);

        // assertions
        $this->assertEquals($Result, $Data, 'Invalid blocks parsing');
    }
}

?>