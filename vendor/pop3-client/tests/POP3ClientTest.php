<?php
require_once (__DIR__ . '/../../conf/conf.php');
require_once (__DIR__ . '/../pop3-client.php');

class POP3ClientTest extends PHPUnit\Framework\TestCase
{

    /**
     * Email server.
     */
    var $Server = 'ssl://pop.yandex.ru';

    /**
     * Email login.
     */
    var $Login = 'pop-m-test@yandex.ru';

    /**
     * Email password.
     */
    var $Password = 'pop3test';

    /**
     * Invalid server.
     */
    public function testInvalidServer()
    {
        try {
            new POP3Client('some-server', 'login', 'password');

            $this->assertEquals(true, false, 'Server validation failed');
        } catch (Exception $e) {
            $this->assertEquals(true, true, 'OK');
        }
    }

    /**
     * Login validation.
     */
    public function testInvalidLogin()
    {
        try {
            new POP3Client($this->Server, 'unexisting-1024', 'password', 5, 995);

            $this->assertEquals(true, false, 'Login validation failed');
        } catch (Exception $e) {
            $this->assertEquals(true, true, 'OK');
        }
    }

    /**
     * Password validation.
     */
    public function testInvalidPassword()
    {
        try {
            new POP3Client($this->Server, $this->Login, 'password', 5, 995);

            $this->assertEquals(true, false, 'Password validation failed');
        } catch (Exception $e) {
            $this->assertEquals(true, true, 'OK');
        }
    }

    /**
     * Normal connect.
     */
    public function testConnect()
    {
        try {
            new POP3Client($this->Server, $this->Login, $this->Password, 5, 995);

            $this->assertEquals(true, true, 'OK');
        } catch (Exception $e) {
            $this->assertEquals(true, false, 'Connection failed');
        }
    }

    /**
     * Get emails count.
     */
    public function testGetCount()
    {
        $Client = new POP3Client($this->Server, $this->Login, $this->Password, 5, 995);

        $this->assertEquals($Client->get_count() > 0, true, 'No emails were fetched');
    }

    /**
     * Get emails headers.
     */
    public function testGetHeaders()
    {
        $Client = new POP3Client($this->Server, $this->Login, $this->Password, 5, 995);

        $Headers = $Client->get_message_headers(1);

        $this->assertNotEquals(strpos($Headers, 'From: '), false, 'No "From" header');
        $this->assertNotEquals(strpos($Headers, 'To: '), false, 'No "To" header');
        $this->assertNotEquals(strpos($Headers, 'Subject: '), false, 'No "Subject" header');
    }

    /**
     * Delete email.
     */
    public function testDeleteEmail()
    {
        $Client = new POP3Client($this->Server, $this->Login, $this->Password, 5, 995);

        $Headers = $Client->get_message_headers(1);

        $MessageId = POP3Client::get_message_id($Headers);

        $Client->delete_message(1);

        $Client->quit();

        $Client->connect($this->Server, $this->Login, $this->Password, 5, 995);

        $Headers = $Client->get_message_headers(1);

        $MessageId2 = POP3Client::get_message_id($Headers);

        $this->assertNotEquals($MessageId, $MessageId2, 'Message was not deleted');
    }
}

?>