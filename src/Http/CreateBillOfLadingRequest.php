<?php
/**
 * Created by PhpStorm.
 * User: joro
 * Date: 10.5.2017 г.
 * Time: 16:55 ч.
 */

namespace Omniship\Dpdromania\Http;

use Omniship\Common\Address;
use Omniship\Common\PieceBag;
use Omniship\Consts;
use Omniship\Speedy\Helper\Convert;
use ParamCalculation;
use ParamClientData;
use ParamPhoneNumber;
use ParamOptionsBeforePayment;
use ParamAddress;
use Carbon\Carbon;
use ParamPicking;

class CreateBillOfLadingRequest extends AbstractRequest
{
    /**
     * @return ParamCalculation
     */
    public function getData()
    {

        $sender_address = $this->getSenderAddress();
        //dd($sender_address);
        $receiver_address = $this->getReceiverAddress();
        $data['userName'] = $this->getUsername();
        $data['password'] = $this->getPassword();
        $data['clientSystemId'] = $sender_address->getId();

        if (!empty($this->getOtherParameters('address_id'))) {
            $data['sender']['clientId'] = $sender_address->getId();
            if (!empty($sender_address->getOffice())) {
                $data['sender']['dropoffOfficeId'] = $sender_address->getOffice()->getId();
            }
        } else {
            if (!empty($sender_address->getOffice())) {
                $data['sender']['dropoffOfficeId'] = $sender_address->getOffice()->getId();
            } else {
                $data['sender']['clientId'] = $sender_address->getId();
              //  $data['sender']['privatePerson'] = true;
//                $data['sender']['addressLocation']['countryId'] = $sender_address->getCountry()->getId();
//                if (!empty($sender_address->getState())) {
//                    $data['sender']['addressLocation']['stateId'] = $sender_address->getState()->getId();
//                }
//                if (!empty($sender_address->getCity()->getId())) {
//                    $data['sender']['addressLocation']['siteId'] = $sender_address->getCity()->getId();
//                } else {
//                    $data['sender']['addressLocation']['siteName'] = $sender_address->getCity()->getName();
//                }
//                $data['sender']['addressLocation']['postCode'] = $sender_address->getPostcode();
            }
        }
        $data['recipient']['privatePerson'] = true;
        $data['recipient']['phone1']['number'] = $receiver_address->getPhone();
        $data['recipient']['email'] = $this->getReceiverEmail();
        $data['recipient']['shipmentNote'] = mb_strimwidth( $this->getClientNote() ,0,99,'...','utf-8');;
        $data['recipient']['clientName'] = $receiver_address->getFullName();
        $data['recipient']['contactName'] = $receiver_address->getFullName();
        if (!empty($receiver_address->getOffice())) {
            $data['recipient']['pickupOfficeId'] = $receiver_address->getOffice()->getId();
        } else {
            $data['recipient']['address']['countryId'] = $receiver_address->getCountry()->getId();
            if (!empty($receiver_address->getState())) {
                $data['recipient']['address']['stateId'] = $receiver_address->getState()->getId();
            }
            if (!empty($receiver_address->getCity())) {
                $data['recipient']['address']['siteId'] = $receiver_address->getCity()->getId();
            } else {
                $data['recipient']['address']['siteName'] = $receiver_address->getCity()->getName();
            }
            $data['recipient']['address']['streetName'] = $receiver_address->getStreet()->getName();
            $data['recipient']['address']['streetNo'] = $receiver_address->getStreetNumber();
            $data['recipient']['address']['postCode'] = $receiver_address->getPostcode();
        }
        $content_info = [];
        foreach ($this->getItems() as $item) {
            $content_info[] = $item->getName();
        }
        $data['service']['serviceId'] = $this->getServiceId();
        if (!empty($this->getCashOnDeliveryAmount())) {
            $data['service']['additionalServices']['cod']['amount'] = $this->getCashOnDeliveryAmount();
            // $data['service']['additionalServices']['cod']['amount'] = $this->getCurrency();
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
        switch ($this->getPayer()){
            case '1': $payer =  'SENDER'; break;
            case '2': $payer =  'RECIPIENT'; break;
            case 'RECEIVER': $payer =  'RECIPIENT'; break;
            case '3': $payer =  'THIRD_PARTY'; break;
            default: $payer =  $this->getPayer(); break;
        }
        $contnt_info = implode(', ', $content_info);
        $data['payment']['courierServicePayer'] = $payer;
        $data['content']['parcelsCount'] = $this->getItems()->count();
        $data['content']['totalWeight'] = $this->getWeight();
        $data['content']['contents'] = mb_strimwidth($contnt_info,0,99,'...','utf-8');
        $data['content']['package'] = 'BOX';
        $bank_account = $this->getOtherParameters('senderBankAccount');
        if (!empty($bank_account)) {
            $data['payment']['senderBankAccount'] = $bank_account;
        }
        return $data;
    }

    /**
     * @param mixed $data
     * @return CreateBillOfLadingResponse
     */
    public function sendData($data)
    {
        $request = $this->getClient()->SendRequest('POST', 'shipment', $data);
        return $this->createResponse($request);
    }

    /**
     * @param $data
     * @return CreateBillOfLadingResponse
     */
    protected function createResponse($data)
    {
        return $this->response = new CreateBillOfLadingResponse($this, $data);
    }
}
