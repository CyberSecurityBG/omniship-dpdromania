<?php

namespace Omniship\DpdRomania;

use GuzzleHttp\Client as HttpClient;
use http\Client\Response;
use Omniship\Address\City;
use Omniship\Address\Office;
use Omniship\Helper\Collection;

class Client
{

    protected $username;
    protected $password;
    protected $country;
    protected $error;
    const ENDPOINTS = ['RO' => 'https://api.dpd.ro/v1/', 'BG' => 'https://api.speedy.bg/v1/'];

    public function __construct($username, $password, $country)
    {
        $this->username = $username;
        $this->password = $password;
        $this->country = $country;
    }


    public function getError()
    {
        return $this->error;
    }


    public function SendRequest($method = 'GET', $endpoint = '', $data = [])
    {
        try {
            $client = new HttpClient(['base_uri' => self::ENDPOINTS[$this->country]]);
            if($method == 'POST') {
                $response = $client->request($method, $endpoint, [
                    'json' => $data,
                    'headers' => [
                        'Content-type' => 'application/json',
                    ],
                ]);
            } elseif($method == 'GET'){
                $response = $client->request('GET', $endpoint, [
                    'query' => $data
                ]);
            }
            return json_decode($response->getBody()->getContents());
        } catch (\Exception $e) {
            return $this->error = [
                'code' => $e->getCode(),
                'error' => $e->getResponse()->getBody()->getContents()
            ];
        }
    }

    /**
     * $type - name / isoAlpha2 / isoAlpha3
     * @param $data
     */
    public function getCountries($type, $value)
    {
        $countrues = $this->SendRequest('GET', 'location/country', ['userName' => $this->username, 'password' => $this->password, $type => $value]);
        if(empty($countrues->error)) {
            return $countrues->countries;
        }
        return false;

    }

    /**
     * @param $country_id
     * @param $name
     * @return false
     */
    public function getState($country_id, $name){
        $state = $this->SendRequest('GET', 'location/state', [
            'userName' => $this->username,
            'password' => $this->password,
            'countryId' => $country_id,
            'name' => $name
        ]);
        if(empty($state->error)) {
            return $state->states;
        }
        return false;
    }

    /**
     * @param $country_id
     * @param $name
     * @param $postcode
     * @return false
     */
    public function getCities($country_id, $name = null, $postcode = null){
        $city = $this->SendRequest('GET', 'location/site', [
            'userName' => $this->username,
            'password' => $this->password,
            'countryId' => $country_id,
            'name' => $name,
            'postCode' => $postcode
        ]);
        if(empty($city->error)) {
            return $city->sites;
        }
        return false;
    }

    /**
     * @param $city_id
     * @param $name
     * @return false
     */
    public function GetStreet($city_id, $name = null){
        $street = $this->SendRequest('GET', 'location/street', [
            'userName' => $this->username,
            'password' => $this->password,
            'siteId' => $city_id,
            'name' => $name,
        ]);
        if(empty($street->error)) {
            return $street->streets;
        }
        return false;
    }

    /**
     * @param $country_id
     * @param $city_id
     * @param $name
     * @param $limit
     * @return false
     */
    public function getOffices($country_id, $city_id, $name = null, $limit = null){
        $offices = $this->SendRequest('GET', 'location/office', [
            'userName' => $this->username,
            'password' => $this->password,
            'countryId' => $country_id,
            'siteId' => $city_id,
            'name' => $name,
            'limit' => $limit
        ]);
        if(empty($offices->error)) {
            return $offices->offices;
        }
        return false;
    }

    /**
     * @param $id
     * @return false
     */
    public function getOfficeById($id){
        $office = $this->SendRequest('GET', 'location/office/'.$id, [
            'userName' => $this->username,
            'password' => $this->password,
        ]);
        if(empty($office->error)) {
            return $office->office;
        }
        return false;
    }

    /**
     * @param $client_id
     * @return false
     */
    public function getClienAdrresses($client_id){
        $client = $this->SendRequest('GET', 'client/contract', [
            'userName' => $this->username,
            'password' => $this->password,
            'clientSystemId' => $client_id
        ]);
        if(empty($client->error)) {
            return $client->clients;
        }
        return false;
    }
}
