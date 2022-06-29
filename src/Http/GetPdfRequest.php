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
        foreach(explode('-', $this->getBolId()) as $bol){
            $set[] = ['parcel' => ['id' => $bol]];
        }

        return [
            'userName' => $this->getUsername(),
            'password' => $this->getPassword(),
            'clientSystemId' => $this->getClientId(),
            'format' => 'pdf',
            'paperSize' => $this->getOtherParameters('printer_type'),
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
