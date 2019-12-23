<?php

namespace Shiprocket\Resources;

use Exception;
use stdClass;

trait Couriers
{
    /**
     * Get a list of serviceable couriers between two pincodes
     *
     * @param string $pickup_postcode
     * @param string $delivery_postcode
     * @param int $is_cod
     * @param int $weight
     * @param int $shiprocket_order_id
     *
     * @return stdClass
     * @throws Exception
     */
    public function CheckServiceAvailability(
        $pickup_postcode,
        $delivery_postcode,
        $weight = 0,
        $is_cod = 0,
        $shiprocket_order_id = 0
    ) {
        return $this->request(
            'get',
            'courier/serviceability?' .
            'pickup_postcode=' . $pickup_postcode . '&' .
            'delivery_postcode=' . $delivery_postcode . '&' .
            'weight=' . $weight . '&' .
            'cod=' . $is_cod . '&' .
            'order_id=' . $shiprocket_order_id
        );
    }

    /*
     * assign awb to shipment
     */
    public function assignAWBs(
        $shipment_ids,
        $courier_id,
        $weight
    ) {
        if (count($shipment_ids) > 1) {
            return $this->request(
                'post',
                'courier/assign/awb',
                [
                    'shipment_id' => $shipment_ids
                ]
            );
        }

        return $this->request(
            'post',
            'courier/assign/awb',
            [
                'shipment_id' => $shipment_ids,
                'courier_id' => $courier_id,
                'weight' => $weight
            ]
        );
    }

    /**
     * generate label
     *
     * @param array $shipment_ids
     *
     * @return stdClass
     * @throws Exception
     */
    public function getLabel(array $shipment_ids)
    {
        return $this->request('post', 'courier/generate/label', ['shipment_id' => $shipment_ids]);
    }

    public function getInvoice(array $shipment_ids)
    {
        return $this->request('post', 'orders/print/invoice', ['ids' => $shipment_ids]);
    }

    /**
     * Makes a request to the Shiprocket API and returns the response.
     *
     * @param    string $verb       The Http verb to use
     * @param    string $path       The path of the APi after the domain
     * @param    array  $parameters Parameters
     *
     * @return   stdClass The JSON response from the request
     * @throws   Exception
     */
    abstract protected function request($verb, $path, $parameters = []);
}
