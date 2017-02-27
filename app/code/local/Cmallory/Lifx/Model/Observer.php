<?php
class Cmallory_Lifx_Model_Observer
{
    public function sendSalesLight(Varien_Event_Observer $observer)
    {
        // Check if order is over $2500
      $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();
        $orderNo = $order->getIncrementID();
        $subtotal = $order->getSubtotal();
        $reqSubtotal = Mage::getStoreConfig('cmallory_lifx/general/subtotal', Mage::app()->getStore());
        if ($subtotal > $reqSubtotal) {
            // Send request to LIFX API
          $link = "https://api.lifx.com/v1/lights/all/effects/breathe";
            $authToken = Mage::getStoreConfig('cmallory_lifx/general/api_token', Mage::app()->getStore());
            $headers = array('Authorization: Bearer ' . $authToken);
            $data = 'period=' . Mage::getStoreConfig('cmallory_lifx/general/period', Mage::app()->getStore()) . '&cycles=' . Mage::getStoreConfig('cmallory_lifx/general/flash', Mage::app()->getStore()) . '&color=' . Mage::getStoreConfig('cmallory_lifx/general/color', Mage::app()->getStore());
            $ch = curl_init($link);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_POST, true);
            $response = curl_exec($ch);
            $error = curl_error($ch);
        }
    }
}
