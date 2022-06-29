<?php
/**
 * Created by PhpStorm.
 * User: joro
 * Date: 12.5.2017 г.
 * Time: 18:03 ч.
 */

namespace Omniship\Dpdromania\Http;

class ServicesRequest extends AbstractRequest
{
    /**
     * @return array
     */
    public function getData() {
        return [];
    }

    /**
     * @param mixed $data
     * @return ServicesResponse
     */
    public function sendData($data) {
        $services = $this->getClient()->SendRequest('GET', 'services', ['userName' => $this->getUsername(), 'password' => $this->getPassword()]);

        return $this->createResponse($services);
    }

    /**
     * @param $data
     * @return ServicesResponse
     */
    protected function createResponse($data)
    {
        return $this->response = new ServicesResponse($this, $data);
    }
}
