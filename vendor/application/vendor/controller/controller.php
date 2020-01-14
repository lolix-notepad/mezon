<?php
namespace Mezon\Application\Controller;

/**
 * Class Controller
 *
 * @package Mezon
 * @subpackage Controller
 * @author Dodonov A.A.
 * @version v.1.0 (2020/01/12)
 * @copyright Copyright (c) 2020, aeon.org
 */

/**
 * Base class for all views
 */
class Controller implements \Mezon\Application\ControllerInterface
{

    /**
     * Controllers's name
     *
     * @var string
     */
    protected $ControllerName = '';

    /**
     * Constructor
     *
     * @param string $ControllerName
     *            Controller name to be executed
     */
    public function __construct(string $ControllerName = '')
    {
        $this->ControllerName = $ControllerName;
    }

    /**
     * Method runs controller
     *
     * @param string ControllerName
     *            Controller name to be run
     * @return mixed result of the controller
     */
    public function run(string $ControllerName = '')
    {
        if ($ControllerName === '') {
            $ControllerName = $this->ControllerName;
        }

        if ($ControllerName === '') {
            $ControllerName = 'Default';
        }

        if (method_exists($this, 'controller' . $ControllerName)) {
            return (call_user_func([
                $this,
                'controller' . $ControllerName
            ]));
        }

        throw (new \Exception('Controller ' . $ControllerName . ' was not found'));
    }

    /**
     * Method returns controller name
     *
     * @return string controller name
     */
    public function getControllerName(): string
    {
        return ($this->ControllerName);
    }
}
