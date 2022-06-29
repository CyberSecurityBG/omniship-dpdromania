<?php
/**
 * Created by PhpStorm.
 * User: joro
 * Date: 10.5.2017 г.
 * Time: 16:55 ч.
 */

namespace Omniship\Dpdromania\Http;

use Omniship\Common\Address;

class ValidateAddressRequest extends AbstractRequest
{

    /**
     * @return ParamAddress
     */
    public function getData() {

        $address = $this->getAddress();
        if(!$address) {
            return false;
        }
        $address = new Address($address);
        $data['userName'] = $this->getUsername();
        $data['password'] = $this->getPassword();
        $data['countryId'] = $address->getCountry()->getId();
        $data['siteId'] = $address->getCity()->getId();
        $data['siteName'] = $address->getCity()->getName();
        $data['postCode'] = $address->getPostCode();
        //$data['streetId'] = $address->getStreet()->getId();
        $data['streetName'] = $address->getStreet()->getName();
        $data['streetNo'] = $address->getStreetNumber();
        //$data['addressLine1'] = $address->getCountry()->getName().', '.$address->getCity()->getName().', '.$address->getStreet()->getName().' '.$address->getStreetNumber();
        $data['address'] = $data;
        return $data;
    }

    /**
     * @param mixed $data
     * @return ValidateAddressResponse
     */
    public function sendData($data) {
        $request = $this->getClient()->SendRequest('POST', 'validation/address', $data);
        return $this->createResponse($request);
    }

    /**
     * @param $data
     * @return ValidateAddressResponse
     */
    protected function createResponse($data)
    {
        return $this->response = new ValidateAddressResponse($this, $data);
    }

}
