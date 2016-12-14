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
      $reqSubtotal = Mage::getStoreConfig('cmallory_lifx/general/subtotal',Mage::app()->getStore());
      if ($subtotal > $reqSubtotal) {
          // Send request to LIFX API
          $link = "https://api.lifx.com/v1/lights/all/effects/breathe";
          $authToken = Mage::getStoreConfig('cmallory_lifx/general/api_token',Mage::app()->getStore());
          $headers = array('Authorization: Bearer ' . $authToken);
          $data = 'period=' . Mage::getStoreConfig('cmallory_lifx/general/period',Mage::app()->getStore()) . '&cycles=' . Mage::getStoreConfig('cmallory_lifx/general/flash',Mage::app()->getStore()) . '&color=' . Mage::getStoreConfig('cmallory_lifx/general/color',Mage::app()->getStore());
          $ch = curl_init($link);
          curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
          curl_setopt($ch, CURLOPT_POST, true);
          $response = curl_exec($ch);
          $error = curl_error($ch);
          // Log responses
          if (empty($error)){
            Mage::log("Order ID: " . print_r($orderNo, true), null, 'cmallory_lifx.log');
            Mage::log("Subtotal Amount: " . print_r($subtotal, true), null, 'cmallory_lifx.log');
            Mage::log("Notification Sent Successfully", null, 'cmallory_lifx.log');
          } else {
            Mage::log("Order ID: " . print_r($orderNo, true), null, 'cmallory_lifx.log');
            Mage::log("Subtotal Amount: " . print_r($subtotal, true), null, 'cmallory_lifx.log');
            Mage::log("Error Sending Notification: " . print_r($error, true), null, 'cmallory_lifx.log');
          }
          curl_close($ch);
      } else {
        Mage::log("Order ID: " . print_r($orderNo, true), null, 'cmallory_lifx.log');
        Mage::log("Subtotal Amount: " . print_r($subtotal, true), null, 'cmallory_lifx.log');
        Mage::log("Notification Not Sent: Subtotal below required value", null, 'cmallory_lifx.log');
      }
    }
  }
