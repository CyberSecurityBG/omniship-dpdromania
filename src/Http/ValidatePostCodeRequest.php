<?php
/**
 * Created by PhpStorm.
 * User: joro
 * Date: 10.5.2017 г.
 * Time: 16:55 ч.
 */

namespace Omniship\Dpdromania\Http;

use Omniship\Common\Address;

class ValidatePostCodeRequest extends AbstractRequest
{
    public function getData() {
         return [
             'userName' => $this->getUsername(),
             'password' => $this->getPassword(),
             'countryId' => $this->getAddress()->getCountry()->getId(),
             'siteId' => $this->getAddress()->getCity()->getId(),
             'postCode' => $this->getAddress()->getPostCode(),
         ];
    }

    /**
     * @param mixed $data
     * @return ValidateAddressResponse
     */
    public function sendData($data) {
        $request = $this->getClient()->SendRequest('POST', 'validation/postcode', $data);
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
