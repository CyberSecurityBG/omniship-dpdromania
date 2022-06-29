<?php
/**
 * Created by PhpStorm.
 * User: joro
 * Date: 10.5.2017 г.
 * Time: 17:22 ч.
 */

namespace Omniship\Dpdromania\Http;

use Omniship\Common\ServiceBag;

class ServicesResponse extends AbstractResponse
{
    /**
     * @return ServiceBag
     */
    public function getData()
    {
        $result = new ServiceBag();
        if (!empty($this->getMessage())) {
            return null;
        }

        /** @var \ResultCourierService $service */
        foreach ($this->data->services as $service) {
            $result->push([
                'id' => $service->id,
                'name' => $service->name,
            ]);
        }
        return $result;
    }
}
