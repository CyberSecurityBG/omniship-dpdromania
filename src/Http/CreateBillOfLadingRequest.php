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
        $receiver_address = $this->getReceiverAddress();
        $data['userName'] = $this->getUsername();
        $data['password'] = $this->getPassword();
        $data['clientSystemId'] = $this->getClientId();

        if (!empty($this->getOtherParameters('address_id'))) {
            $data['sender']['clientId'] = $this->getOtherParameters('address_id');
            if (!empty($sender_address->getOffice())) {
                $data['sender']['dropoffOfficeId'] = $sender_address->getOffice()->getId();
            }
        } else {
            if (!empty($sender_address->getOffice())) {
                $data['sender']['dropoffOfficeId'] = $sender_address->getOffice()->getId();
            } else {
                $data['sender']['privatePerson'] = true;
                $data['sender']['addressLocation']['countryId'] = $sender_address->getCountry()->getId();
                if (!empty($sender_address->getState())) {
                    $data['sender']['addressLocation']['stateId'] = $sender_address->getState()->getId();
                }
                if (!empty($sender_address->getCity()->getId())) {
                    $data['sender']['addressLocation']['siteId'] = $sender_address->getCity()->getId();
                } else {
                    $data['sender']['addressLocation']['siteName'] = $sender_address->getCity()->getName();
                }
                $data['sender']['addressLocation']['postCode'] = $sender_address->getPostcode();
            }
        }
        $data['recipient']['privatePerson'] = true;
        $data['recipient']['phone1']['number'] = $receiver_address->getPhone();
        $data['recipient']['email'] = $this->getReceiverEmail();
        $data['recipient']['shipmentNote'] = $this->getClientNote();
        $data['recipient']['clientName'] = $receiver_address->getFullName();
        $data['recipient']['contactName'] = $receiver_address->getFullName();
        if (!empty($receiver_address->getOffice())) {
            $data['recipient']['dropoffOfficeId'] = $receiver_address->getOffice()->getId();
        } else {
            $data['recipient']['address']['countryId'] = $receiver_address->getCountry()->getId();
            if (!empty($receiver_address->getState())) {
                $data['recipient']['address']['stateId'] = $receiver_address->getState()->getId();
            }
            if (!empty($receiver_address->getCity()->getId())) {
                $data['recipient']['address']['siteId'] = $receiver_address->getCity()->getId();
            } else {
                $data['recipient']['address']['siteName'] = $receiver_address->getCity()->getName();
            }
            $data['recipient']['address']['postCode'] = $receiver_address->getPostcode();
        }
        $content_info = [];
        foreach ($this->getItems() as $item) {
            $content_info[] = $item->getName();
        }
        $data['service']['serviceId'] = $this->getServiceId();
        $data['content']['parcelsCount'] = $this->getItems()->count();
        $data['content']['totalWeight'] = $this->getItems()->sum('weight');
        $data['content']['contents'] = implode(', ', $content_info);
        $data['content']['package'] = 'BOX';
        $data['payment']['courierServicePayer'] = $this->getPayer();
        $bank_account = $this->getOtherParameters('senderBankAccount');
        $data['payment']['courierServicePayer'] = $this->getPayer();
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
