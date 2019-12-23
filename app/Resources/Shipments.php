<?php


namespace Shiprocket\Resources;


trait Shipments
{
    /**
     * create, ship and generate label and menifest for order
     *
     * @param array $attributes
     * @return stdClass
     */
    public function createForwardShipment(array $attributes)
    {
        return $this->request('post', 'shipments/create/forward-shipment', $attributes);
    }

    /**
     * geneate menifest file
     *
     * @param $shipmentId
     * @return stdClass
     */
    public function getMenifest($shipmentId)
    {
        return $this->request('post', 'manifests/generate', ['shipment_id' => $shipmentId]);
    }

    /**
     * print menifest file
     *
     * @param array $order_ids
     * @return stdClass
     */
    public function printMenifest(array $order_ids)
    {
        return $this->request('post', 'manifests/print', ['order_ids' => $order_ids]);
    }



    /**
     * Makes a request to the Shiprocket API and returns the response.
     *
     * @param string $verb The Http verb to use
     * @param string $path The path of the APi after the domain
     * @param array $parameters Parameters
     *
     * @return   stdClass The JSON response from the request
     * @throws   Exception
     */
    abstract protected function request($verb, $path, $parameters = []);
}
