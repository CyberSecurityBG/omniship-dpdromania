<?php

namespace Omniship\Dpdromania\Http;

use Doctrine\Common\Collections\ArrayCollection;

class ShippingQuoteRequest extends AbstractRequest
{

    public function getData()
    {

        $sender_address = $this->getSenderAddress();
        $receiver_address = $this->getReceiverAddress();
        $data['userName'] = $this->getUsername();
        $data['password'] = $this->getPassword();
        $data['clientSystemId'] =  $sender_address->getId();
       // $data['language'] = 'BG';

        if (!empty($sender_address->getOffice())) {
            $data['sender']['clientId'] = $sender_address->getId();
            if (!empty($sender_address->getOffice())) {
                $data['sender']['dropoffOfficeId'] = (int)$sender_address->getOffice()->getId();
            }
        } else {
            $data['sender']['clientId'] = $sender_address->getId();
//            $data['sender']['privatePerson'] = true;
//            $data['sender']['addressLocation']['countryId'] = $sender_address->getCountry()->getId();
//            if (!empty($sender_address->getState())) {
//                $data['sender']['addressLocation']['stateId'] = $sender_address->getState()->getId();
//            }
//            if (!empty($sender_address->getCity()->getId())) {
//                $data['sender']['addressLocation']['siteId'] = $sender_address->getCity()->getId();
//            } else {
//                $data['sender']['addressLocation']['siteName'] = $sender_address->getCity()->getName();
//            }
//            $data['sender']['addressLocation']['postCode'] = $sender_address->getPostcode();
        }

        $data['recipient']['privatePerson'] = true;
        if (!empty($receiver_address->getOffice())) {
            $data['recipient']['pickupOfficeId'] = (int)$receiver_address->getOffice()->getId();
        } else {
            $data['recipient']['addressLocation']['countryId'] = $receiver_address->getCountry()->getId();
            if (!empty( $receiver_address->getState())) {
                $data['recipient']['addressLocation']['stateId'] = $receiver_address->getState()->getId();
            }
            if (!empty($receiver_address->getCity())) {
                $data['recipient']['addressLocation']['siteId'] = $receiver_address->getCity()->getId();
            } else {
                $data['recipient']['addressLocation']['siteName'] = $receiver_address->getCity()->getName();
            }
            $data['recipient']['addressLocation']['postCode'] = $receiver_address->getPostcode();
        }

        $data['service']['autoAdjustPickupDate'] = true;
        $data['service']['serviceIds'] = $this->getOtherParameters('services');
        if (!empty($this->getCashOnDeliveryAmount())) {
            $data['service']['additionalServices']['cod']['amount'] = $this->getCashOnDeliveryAmount();
          //  $data['service']['additionalServices']['cod']['currencyCode'] = $this->getCurrency();
            if($this->getOtherParameters('pos_enabled') == 1){
                $data['service']['additionalServices']['cod']['cardPaymentForbidden'] = false;
            }
        }
        if(!empty($this->getOptionBeforePayment()) && $this->getOptionBeforePayment() != 'no_option') {
            $data['service']['additionalServices']['obpd']['option'] = str_upper($this->getOptionBeforePayment());
            $data['service']['additionalServices']['obpd']['returnShipmentPayer'] = $this->getOtherParameters('return_pay') == 1 || $this->getOtherParameters('return_pay') == 'return' ? 'SENDER' : 'RECIPIENT';
            $data['service']['additionalServices']['obpd']['returnShipmentServiceId'] = $this->getOtherParameters('return_service');
        }
        if(!empty($this->getDeclaredAmount())){
            $data['service']['additionalServices']['declaredValue']['amount'] = $this->getDeclaredAmount();
            if($this->getOtherParameters('fragile') == 1){
                $data['service']['additionalServices']['declaredValue']['fragile'] = true;
            }
        }

        if($this->getOtherParameters('documents') == true){
            $data['content']['documents'] = true;
        } else {
            if((int)$this->getOtherParameters('items_count') > 0){
                $data['content']['parcelsCount'] = $this->getOtherParameters('items_count');
            } else {
                $data['content']['parcelsCount'] = $this->getItems()->count();
            }
            if($this->getWeight() == 0) {
                $data['content']['totalWeight'] = $this->getItems()->sum('weight');
            } else {
                $data['content']['totalWeight'] = $this->getWeight();
            }
        }

        $bank_account = $this->getOtherParameters('senderBankAccount');
        switch ($this->getPayer()){
            case '1': $payer =  'SENDER'; break;
            case '2': $payer =  'RECIPIENT'; break;
            case 'RECEIVER': $payer =  'RECIPIENT'; break;
            case '3': $payer =  'THIRD_PARTY'; break;
            default: $payer =  $this->getPayer(); break;
        }
        $data['payment']['courierServicePayer'] = $payer;

        if (!empty($bank_account)) {
            $data['payment']['senderBankAccount'] = $bank_account;
        }
        return $data;
    }

    public function sendData($data)
    {
      //  dd($data);
        $request = $this->getClient()->SendRequest('POST', 'calculate', $data);
        return $this->createResponse($request);
    }

    protected function createResponse($data)
    {
      //  dd($data);
        return $this->response = new ShippingQuoteResponse($this, $data);
    }
}
