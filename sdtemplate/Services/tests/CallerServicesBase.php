<?php

require_once 'Services/PayPal.php';
require_once 'Services/PayPal/Profile/Handler/Array.php';
require_once 'Services/PayPal/Profile/API.php';

class CallerServicesBase extends PHPUnit_TestCase
{
    var $caller;
    var $profile;

    function CallerServicesBase($name)
    {
        parent::PHPUnit_TestCase($name);
        $this->profile =& $this->getProfile();
        $this->caller =& Services_PayPal::getCallerServices($this->profile);
    }

    function &getProfile()
    {
        include dirname(__FILE__) . '/config.php';
        $handler =& ProfileHandler_Array::getInstance(array(
            'username' => $apiusername,
            'certificateFile' => $certfile,
            'subject' => $subject,
            'environment' => $environment));

        $id = $handler->generateID();

        $profile =& APIProfile::getInstance($id, $handler);
        $profile->setAPIPassword($apipassword);

        return $profile;
    }

}
