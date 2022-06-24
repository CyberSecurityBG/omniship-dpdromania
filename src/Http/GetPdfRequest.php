<?php
/**
 * Created by PhpStorm.
 * User: joro
 * Date: 12.5.2017 г.
 * Time: 18:03 ч.
 */
namespace Omniship\Dpdromania\Http;

use Infifni\FanCourierApiClient\Client;
use Infifni\FanCourierApiClient\Request\GetAwb;
use Omniship\Fancourier\FanClient;

class GetPdfRequest extends AbstractRequest
{
    /**
     * @return integer
     */
    public function getData() {
        $set = [];
        $set[] = ['parcel' => ['id' => $this->getBolId()]];
        return [
            'userName' => $this->getUsername(),
            'password' => $this->getPassword(),
            'clientSystemId' => $this->getClientId(),
            'format' => 'pdf',
            'paperSize' => 'A4', //$this->getOtherParameters('format'),
            'parcels' => $set,
        ];
    }

    /**
     * @param mixed $data
     * @return GetPdfResponse
     */
    public function sendData($data) {
        $request = $this->getClient()->SendRequest('POST', 'print', $data);
        return $this->createResponse($request);
    }

    /**
     * @param $data
     * @return GetPdfResponse
     */
    protected function createResponse($data)
    {
        return $this->response = new GetPdfResponse($this, $data);
    }
}
