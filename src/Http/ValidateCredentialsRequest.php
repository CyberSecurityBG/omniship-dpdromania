<?php

namespace Omniship\Dpdromania\Http;
use Omniship\Dpdromania\Client;
class ValidateCredentialsRequest extends AbstractRequest
{


    public function getData()
    {
       return [
           'userName' => $this->getUsername(),
           'password' => $this->getPassword()
       ];
    }

    public function sendData($data)
    {
        $services = $this->getClient()->SendRequest('POST', 'client', $data);
        return $this->createResponse($services);
    }

    protected function createResponse($data)
    {
        return $this->response = new ValidateCredentialsResponse($this, $data);
    }

}
