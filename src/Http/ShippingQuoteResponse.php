<?php

namespace Omniship\Dpdromania\Http;
use Carbon\Carbon;
use Omniship\Common\ShippingQuoteBag;

class ShippingQuoteResponse extends AbstractResponse
{
    public function getData()
    {
        if (!empty($this->getMessage())) {
            return null;
        }

        $result = new ShippingQuoteBag();
        $getRequest = $this->getRequest()->getData();
        $services = collect($this->getClient()->SendRequest('GET', 'services', ['userName' => $getRequest['userName'], 'password' => $getRequest['password']])->services);

        foreach ($this->data->calculations as $data){
            if(empty($data->error)) {
                $result->push([
                    'id' => $data->serviceId,
                    'name' => $services->where('id', $data->serviceId)->first()->name,
//                    'description' => null,
                    'price' => $data->price->total,
                    'pickup_date' => Carbon::createFromFormat('Y-m-d', $data->pickupDate),
//                    'pickup_time' => null,
//                    'delivery_date' => null,
//                    'delivery_time' => null,
                    'currency' => $data->price->currency,
                    'tax' => null,
                    'insurance' => 0,
//                    'exchange_rate' => null,
//                    'payer' => null,
                    'allowance_fixed_time_delivery' => false,
                    'allowance_cash_on_delivery' => true,
                    'allowance_insurance' => true,
                ]);
            }
        }
        return $result;
    }
}
