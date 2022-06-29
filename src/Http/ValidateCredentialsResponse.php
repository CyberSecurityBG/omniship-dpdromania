<?php

namespace Omniship\Dpdromania\Http;

class ValidateCredentialsResponse extends AbstractResponse
{

    /**
     * @return bool
     */
    public function getData()
    {
        if(!empty($this->getMessage())){
            return null;
        }

        return $this->data->clientId;
    }

}
