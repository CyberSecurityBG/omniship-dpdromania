<?php
/**
 * Created by PhpStorm.
 * User: joro
 * Date: 10.5.2017 г.
 * Time: 16:55 ч.
 */

namespace Omniship\Dpdromania\Http;

use Omniship\Dpdromania\Client as DpdClient;

use Omniship\Message\AbstractRequest as BaseAbstractRequest;

abstract class AbstractRequest extends BaseAbstractRequest
{
    protected $client;

    /**
     * @return mixed
     */
    public function getUsername(){

        return $this->getParameter('username');
    }

    /**
     * @param $value
     * @return AbstractRequest
     */
    public function setUsername($value){
        return $this->setParameter('username', $value);
    }

    /**
     * @return mixed
     */
    public function getPassword(){

        return $this->getParameter('password');
    }

    /**
     * @param $value
     * @return AbstractRequest
     */
    public function setPassword($value){
        return $this->setParameter('password', $value);
    }

    /**
     * @return mixed
     */
    public function getCountry(){

        return $this->getParameter('country');
    }

    /**
     * @param $value
     * @return AbstractRequest
     */
    public function setCountry($value){
        return $this->setParameter('country', $value);
    }

    /**
     * @return mixed
     */
    public function getClientId(){
        return $this->getParameter('client_id');
    }

    /**
     * @param $value
     * @return AbstractRequest
     */
    public function setClientId($value){
        return $this->setParameter('client_id', $value);
    }

    public function getClient()
    {
        if(is_null($this->client)) {
            $this->client = new DpdClient($this->getUsername(), $this->getPassword(), $this->getCountry());
        }

        return $this->client;
    }

    abstract protected function createResponse($data);

}
