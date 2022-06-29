<?php
/**
 * Created by PhpStorm.
 * User: joro
 * Date: 10.5.2017 г.
 * Time: 17:22 ч.
 */

namespace Omniship\Dpdromania\Http;

use Carbon\Carbon;
use Omniship\Common\Bill\Create;
use Omniship\Speedy\Client;
use ResultBOL;
use ResultAmounts;
use ResultParcelInfo;

class CreateBillOfLadingResponse extends AbstractResponse
{

    /**
     * @return Create
     */
    public function getData()
    {
        if (!empty($this->getMessage())) {
            return null;
        }
        $result = new Create();
        $result->setServiceId(strtolower($this->getRequest()->getServiceId()));
        $result->setBolId($this->data->id);
        $result->setBillOfLadingSource(base64_encode($this->getRequest()->getClient()->createPDF([$this->data->id])));
        $result->setBillOfLadingType($result::PDF);
        $result->setEstimatedDeliveryDate(Carbon::createFromFormat('Y-m-d\TH:i:sP', $this->data->deliveryDeadline));
        $result->setPickupDate(Carbon::createFromFormat('Y-m-d', $this->data->pickupDate));
        $result->setTotal($this->data->price->total);
        $result->setCurrency('RON');
        $result->setCurrency($this->data->price->currency);

        return $result;
    }

}
