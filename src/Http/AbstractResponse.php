<?php
/**
 * Created by PhpStorm.
 * User: joro
 * Date: 23.5.2017 г.
 * Time: 09:35 ч.
 */

namespace Omniship\Dpdromania\Http;

use Omniship\Dpdromania\Client;
use Omniship\Message\AbstractResponse AS BaseAbstractResponse;

class AbstractResponse extends BaseAbstractResponse
{

    protected $error;

    protected $errorCode;

    protected $client;


    /**
     * Get the initiating request object.
     *
     * @return AbstractRequest
     */
    public function getRequest()
    {
       return  $this->request;
    }

    /**
     * @return null|string
     */
    public function getMessage()
    {
        if(!empty($this->data->error)) {
            return $this->data->error->message;
        }
        elseif(!empty($this->getClient()->getError())){
            $decode = json_decode($this->getClient()->getError()['error']);
            return isset($decode->message) ? $decode->message : $this->getCode().' - '.$this->getClient()->getError()['error'];
        }
        return null;
    }

    /**
     * @return null|string
     */
    public function getCode()
    {
        if(!empty($this->getClient()->getError())) {
            return $this->getClient()->getError()['code'];
        }
        return null;
    }

    /**
     * @return null|Client
     */
    public function getClient()
    {
        return $this->getRequest()->getClient();
    }

    /**
     * @param mixed $client
     * @return AbstractResponse
     */


}
