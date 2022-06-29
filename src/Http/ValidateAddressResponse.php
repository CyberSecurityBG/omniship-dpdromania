<?php
/**
 * Created by PhpStorm.
 * User: joro
 * Date: 10.5.2017 г.
 * Time: 17:22 ч.
 */

namespace Omniship\Dpdromania\Http;

use Omniship\Common\ServiceBag;

class ValidateAddressResponse extends AbstractResponse
{

    /**
     * @return bool
     */
    public function getData()
    {
        $result = new ServiceBag();
        if (!empty($this->getMessage()) || $this->data->valid == false) {
            return null;
        }
        return $this->data->valid;
    }

}
