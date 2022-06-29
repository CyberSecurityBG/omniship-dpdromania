<?php

namespace Omniship\Dpdromania;

use Omniship\Common\AbstractGateway;
use Omniship\Common\Address;
use Omniship\Dpdromania\Http\CreateBillOfLadingRequest;
use Omniship\Dpdromania\Http\GetPdfRequest;
use Omniship\Dpdromania\Http\ServicesRequest;
use Omniship\Dpdromania\Http\ShippingQuoteRequest;
use Omniship\Dpdromania\Http\ValidateAddressRequest;
use Omniship\Dpdromania\Http\ValidateCredentialsRequest;
use Omniship\Dpdromania\Http\ValidatePostCodeRequest;
use Omniship\Dpdromania\Http\ValidatePostCodeResponse;
use Omniship\Dpdromania\Http\CancelBillOfLadingRequest;

class Gateway extends AbstractGateway
{

    private $name = 'dpdromania';
    const TRACKING_URL = 'https://berry.bg/bg/t/';
    /**
     * @return stringc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array
     */
    public function getDefaultParameters()
    {
        return array(
            'key' => ''
        );
    }

    public function getUsername() {
        return $this->getParameter('username');
    }

    /**
     * @param $value
     * @return $this
     */
    public function setUsername($value) {
        return $this->setParameter('username', $value);
    }

    /**
     * @return mixed
     */
    public function getPassword() {
        return $this->getParameter('password');
    }

    /**
     * @param $value
     * @return $this
     */
    public function setPassword($value) {
        return $this->setParameter('password', $value);
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->getParameter('country');
    }

    /**
     * @param $value
     * @return $this
     */
    public function setCountry($value)
    {
        return $this->setParameter('country', $value);
    }

    /**
     * @return mixed
     */
    public function getClientId(){
        return $this->getParameter('client_id');
    }

    /**
     * @param $value
     * @return $this
     */
    public function setClientId($value){
        return $this->setParameter('client_id', $value);
    }

    /**
     * @return bool
     */
    public function supportsValidateCredentials(){
        return true;
    }

    /**
     * @param array $parameters
     * @param $test_mode
     * @return \Omniship\Interfaces\RequestInterface|\Omniship\Message\AbstractRequest
     */
    public function validateCredentials(array $parameters = [], $test_mode = null)
    {
        return $this->createRequest(ValidateCredentialsRequest::class, $parameters);
    }

    /**
     * @return bool
     */
    public function supportsValidateAddress()
    {
        return true;
    }

    public function supportsGetClient()
    {
        return true;
    }

    public function getClient()
    {
       return new Client($this->getUsername(), $this->getPassword(), $this->getCountry());
    }

    /**
     * @param $address
     * @return \Omniship\Interfaces\RequestInterface|\Omniship\Message\AbstractRequest
     */
    public function validateAddress($address = [])
    {
        return $this->createRequest(ValidateAddressRequest::class, $this->setAddress($address)->getParameters());

    }

    /**
     * @param array $parameters
     * @return ServicesRequest
     */
    public function getServices(array $parameters = [])
    {
        return $this->createRequest(ServicesRequest::class, $this->getParameters() + $parameters);
    }

    /**
     * @param Address $address
     * @return \Omniship\Interfaces\RequestInterface|\Omniship\Message\AbstractRequest
     */
    public function validatePostCode(Address $address)
    {
        return $this->createRequest(ValidatePostCodeRequest::class, $this->setAddress($address)->getParameters());
    }

    /**
     * @param $parameters
     * @return ShippingQuoteRequest|\Omniship\Interfaces\RequestInterface|\Omniship\Message\AbstractRequest
     */
    public function getQuotes($parameters = [])
    {
        if($parameters instanceof ShippingQuoteRequest) {
            return $parameters;
        }
        if(!is_array($parameters)) {
            $parameters = [];
        }
        return $this->createRequest(ShippingQuoteRequest::class, $this->getParameters() + $parameters);
    }

    /**
     * @param array|CreateBillOfLadingRequest $parameters
     * @return CreateBillOfLadingRequest
     */
    public function createBillOfLading($parameters = [])
    {
        if ($parameters instanceof CreateBillOfLadingRequest) {
            return $parameters;
        }
        if (!is_array($parameters)) {
            $parameters = [];
        }
        return $this->createRequest(CreateBillOfLadingRequest::class, $this->getParameters() + $parameters);
    }

    /**
     * @param $bol_id
     * @return \Omniship\Interfaces\RequestInterface|\Omniship\Message\AbstractRequest
     */
    public function cancelBillOfLading($bol_id)
    {
        $this->setBolId($bol_id);
        return $this->createRequest(CancelBillOfLadingRequest::class, $this->getParameters());
    }

    /**
     * @param $bol_id
     * @return GetPdfRequest
     */
    public function getPdf($bol_id)
    {
        return $this->createRequest(GetPdfRequest::class, $this->setBolId($bol_id)->getParameters());
    }

    /**
     * @return bool
     */
    public function supportsCashOnDelivery()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function supportsGetPdf()
    {
        return true;
    }

}
