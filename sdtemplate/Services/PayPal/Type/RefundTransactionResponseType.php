<?php
/**
 * @package Services_PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'Services/PayPal/Type/AbstractResponseType.php';

/**
 * RefundTransactionResponseType
 *
 * @package Services_PayPal
 */
class RefundTransactionResponseType extends AbstractResponseType
{
    function RefundTransactionResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
    }

}
