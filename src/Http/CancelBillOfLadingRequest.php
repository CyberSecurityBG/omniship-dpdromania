<?php
/**
 * Created by PhpStorm.
 * User: joro
 * Date: 10.5.2017 г.
 * Time: 16:55 ч.
 */

namespace Omniship\Dpdromania\Http;

class CancelBillOfLadingRequest extends AbstractRequest
{

    /**
     * @return array
     */
    public function getData() {
        return [
            'userName' => $this->getUsername(),
            'password' => $this->getPassword(),
            'shipmentId' => $this->getBolId(),
            'comment' => 'Cancelled',
        ];
    }

    /**
     * @param mixed $data
     * @return CancelBillOfLadingResponse
     */
    public function sendData($data) {
        $request = $this->getClient()->SendRequest('POST', 'shipment/cancel', $data);
        return $this->createResponse($request);
    }

    /**
     * @param $data
     * @return CancelBillOfLadingResponse
     */
    protected function createResponse($data)
    {
        return $this->response = new CancelBillOfLadingResponse($this, $data);
    }

    /**
     * @param $bol_id
     * @return GetPdfRequest
     */
    public function getPdf($bol_id)
    {
        return $this->createRequest(GetPdfRequest::class, $this->setBolId($bol_id)->getParameters());
    }

}
