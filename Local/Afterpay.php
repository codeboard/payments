<?php namespace Codeboard\Payments\Local;
/**
 * AfterPay Class
 * Version: 1.0.1
 */

class Afterpay
{
    var $authorization;
    var $modus;
    var $order;
    var $order_lines = array();
    var $order_type;
    var $order_type_name;
    var $order_type_function;
    var $order_request;
    var $order_result;
    var $soap_client;
    var $total_order_amount = 0;
    var $wsdl;

    public function __construct() {
        $this->order = new \stdClass();
        $this->order->shopper = new \stdClass();
    }

    public function set_order( $order, $order_type ) {
        $this->set_order_type( $order_type );

        if( $this->order_type == 'B2C' ) {
            $billto_address = 'b2cbilltoAddress';
            $shipto_address = 'b2cshiptoAddress';
        } elseif ( $this->ordertype =='B2B' ) {
            $billto_address = 'b2bbilltoAddress';
            $shipto_address = 'b2bshiptoAddress';
        }

        $this->order->$billto_address = new \stdClass();
        $this->order->$billto_address->referencePerson = new \stdClass();
        $this->order->$shipto_address = new \stdClass();
        $this->order->$shipto_address->referencePerson = new \stdClass();

        $this->order->$billto_address->city = $order['billtoaddress']['city'];
        $this->order->$billto_address->housenumber = $order['billtoaddress']['housenumber'];
        $this->order->$billto_address->isoCountryCode = $order['billtoaddress']['isocountrycode'];
        $this->order->$billto_address->postalcode = $order['billtoaddress']['postalcode'];
        $this->order->$billto_address->referencePerson->dateofbirth = $order['billtoaddress']['referenceperson']['dob'];
        $this->order->$billto_address->referencePerson->emailaddress = $order['billtoaddress']['referenceperson']['email'];
        $this->order->$billto_address->referencePerson->gender = $order['billtoaddress']['referenceperson']['gender'];
        $this->order->$billto_address->referencePerson->initials = $order['billtoaddress']['referenceperson']['initials'];
        $this->order->$billto_address->referencePerson->isoLanguage = $order['billtoaddress']['referenceperson']['isolanguage'];
        $this->order->$billto_address->referencePerson->lastname = $order['billtoaddress']['referenceperson']['lastname'];
        $this->order->$billto_address->referencePerson->phonenumber1 = $order['billtoaddress']['referenceperson']['phonenumber'];
        $this->order->$billto_address->streetname = $order['billtoaddress']['streetname'];

        $this->order->$shipto_address->city = $order['shiptoaddress']['city'];
        $this->order->$shipto_address->housenumber = $order['shiptoaddress']['housenumber'];
        $this->order->$shipto_address->isoCountryCode = $order['shiptoaddress']['isocountrycode'];
        $this->order->$shipto_address->postalcode = $order['shiptoaddress']['postalcode'];
        $this->order->$shipto_address->referencePerson->dateofbirth = $order['shiptoaddress']['referenceperson']['dob'];
        $this->order->$shipto_address->referencePerson->emailaddress = $order['shiptoaddress']['referenceperson']['email'];
        $this->order->$shipto_address->referencePerson->gender = $order['shiptoaddress']['referenceperson']['gender'];
        $this->order->$shipto_address->referencePerson->initials = $order['shiptoaddress']['referenceperson']['initials'];
        $this->order->$shipto_address->referencePerson->isoLanguage = $order['shiptoaddress']['referenceperson']['isolanguage'];
        $this->order->$shipto_address->referencePerson->lastname = $order['shiptoaddress']['referenceperson']['lastname'];
        $this->order->$shipto_address->referencePerson->phonenumber1 = $order['shiptoaddress']['referenceperson']['phonenumber'];
        $this->order->$shipto_address->streetname = $order['shiptoaddress']['streetname'];

        $this->order->ordernumber = $order['ordernumber'];
        $this->order->bankaccountNumber = $order['bankaccountnumber'];
        $this->order->currency = $order['currency'];
        $this->order->ipAddress = $order['ipaddress'];
        $this->order->shopper->profilecreated = '2013-01-01T00:00:00';
        $this->order->parentTransactionreference = false;
        $this->order->orderlines = $this->order_lines;
        $this->order->totalOrderAmount =  $this->total_order_amount;
    }

    public function create_order_line( $id, $description, $quantity, $unit_price, $vat_category ) {
        $order_line = new \stdClass();
        $order_line->articleId = $id;
        $order_line->articleDescription = $description;
        $order_line->quantity = $quantity;
        $order_line->unitprice = $unit_price;
        $order_line->vatcategory = $vat_category;

        $this->total_order_amount = $this->total_order_amount + ( $quantity * $unit_price );

        $this->order_lines[] = $order_line;
    }


    public function validate_and_check_order( $authorization, $modus ) {
        $this->set_modus( $modus );
        $this->set_soap_client();
        $this->set_authorization( $authorization );

        $this->order_result = $this->soap_client->__soapCall(
            $this->order_type_name,
            array(
                $this->order_type_name => array(
                    'authorization' => $this->authorization,
                    $this->order_type_function => $this->order
                )
            )
        );
    }

    private function set_order_type( $order_type ) {
        switch ( $order_type ) {
            case 'B2C':
                $this->order_type = 'B2C';
                $this->order_type_name = 'validateAndCheckB2COrder';
                $this->order_type_function = 'b2corder';
                break;
            case 'B2B':
                $this->order_type = 'B2B';
                $this->order_type_name = 'validateAndCheckB2BOrder';
                $this->order_type_function = 'b2border';
                break;
            default:
                break;
        }
    }

    private function set_modus( $modus ) {
        switch ( $modus ) {
            case 'test':
                $this->modus = 'test';
                $this->wsdl = 'https://test.acceptgirodienst.nl/soapservices/rm/AfterPaycheck?wsdl';
                break;
            case 'live':
                $this->modus = 'live';
                $this->wsdl = 'https://www.acceptgirodienst.nl/soapservices/rm/AfterPaycheck?wsdl';
                break;
            default:
                break;
        }
    }

    private function set_soap_client() {
        $this->soap_client = new \SoapClient(
            $this->wsdl,
            array(
                'trace' => 0,
                'cache_wsdl' => WSDL_CACHE_NONE
            )
        );
    }

    private function set_authorization ( $authorization ) {
        $this->authorization = new \stdClass();
        $this->authorization->merchantId = $authorization['merchantid'];
        $this->authorization->portfolioId = $authorization['portfolioid'];
        $this->authorization->password = $authorization['password'];
    }

    public function getWsdl()
    {
        return $this->wsdl;
    }
}