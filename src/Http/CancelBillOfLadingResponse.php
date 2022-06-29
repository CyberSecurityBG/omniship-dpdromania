<?php
/**
 * Created by PhpStorm.
 * User: joro
 * Date: 10.5.2017 г.
 * Time: 17:22 ч.
 */

namespace Omniship\Dpdromania\Http;

class CancelBillOfLadingResponse extends AbstractResponse
{

    /**
     * @return bool
     */
    public function getData()
    {
        if (!empty($this->getMessage())) {
            return false;
        }
        return true;
    }

}
