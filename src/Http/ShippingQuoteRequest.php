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
        $data['clientSystemId'] = 88888888889001;

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
        if (!empty($receiver_address->getOffice())) {
            $data['recipient']['dropoffOfficeId'] = $receiver_address->getOffice()->getId();
        } else {
            $data['recipient']['addressLocation']['countryId'] = $receiver_address->getCountry()->getId();
            if (!empty($receiver_address->getState())) {
                $data['recipient']['addressLocation']['stateId'] = $receiver_address->getState()->getId();
            }
            if (!empty($receiver_address->getCity()->getId())) {
                $data['recipient']['addressLocation']['siteId'] = $receiver_address->getCity()->getId();
            } else {
                $data['recipient']['addressLocation']['siteName'] = $receiver_address->getCity()->getName();
            }
            $data['recipient']['addressLocation']['postCode'] = $receiver_address->getPostcode();
        }
        $data['service'] = $this->getOtherParameters('service');
        $data['content']['parcelsCount'] = $this->getItems()->count();
        $data['content']['totalWeight'] = $this->getItems()->sum('weight');
        $bank_account = $this->getOtherParameters('senderBankAccount');
        $data['payment']['courierServicePayer'] = $this->getPayer();
        if (!empty($bank_account)) {
            $data['payment']['senderBankAccount'] = $bank_account;
        }

// dump($data);
        return $data;
    }

    public
    function sendData($data)
    {
        $request = $this->getClient()->SendRequest('POST', 'validation/address', $data);
        return $this->createResponse($request);
    }

    protected
    function createResponse($data)
    {
        // dd($data);
        return $this->response = new ShippingQuoteResponse($this, $data);
    }
}
