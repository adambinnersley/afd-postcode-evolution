<?php

/**
 * The AFD class is made to be used with the AFD Postcode Evolution Server program to get the users postcode information
 * @author Adam Binnersley <abinnersley@gmail.com>
 */

namespace AFD;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;

error_reporting(0);

class AFD
{


    protected static $AFD_HOST = 'http://localhost';
    protected static $AFD_PORT = 81;
    public $addressInfo = [];
    public $address1;
    public $address2;
    public $address3;
    public $town;
    public $county;
    public $latitude;
    public $longitude;
    
    /**
     * Sets the host where the AFD Postcode Evolution is installed
     * @param string $host This should be a valid URL
     * @return void
     */
    public function setHost($host)
    {
        self::$AFD_HOST = $host;
        return $this;
    }
    
    /**
     * Gets he host where the AFD Postcode Evolution is installed
     * @return string
     */
    public function getHost()
    {
        return self::$AFD_HOST;
    }
    
    /**
     * Sets the port number to look for the AFD Postcode data
     * @param int $port This should be the Port number that the Postcode evolution is installed on
     * @return void
     */
    public function setPort($port)
    {
        if (is_int($port) && $port >= 1) {
            self::$AFD_PORT = $port;
        }
        return $this;
    }
    
    /**
     * Gets the port number to look for the AFD Postcode data
     * @return int
     */
    public function getPort()
    {
        return self::$AFD_PORT;
    }
    
    /**
     * Returns a list of all of the addresses with the given postcode
     * @param string $postcode Should be the postcode you wish to find the addresses for
     * @return array|boolean Returns a list of the addresses or returns false if program is not active
     */
    public function findAddresses($postcode)
    {
        $xml = $this->getData($this->getHost() . ':' . $this->getPort() . '/afddata.pce?Data=Address&Task=Lookup&Fields=List&Lookup=' . urlencode($postcode));
        if ($xml->Result == 1) {
            $addresses = [];
            $count = count($xml->Item);
            for ($i = 0; $i < $count; $i++) {
                $addresses[$i]['address'] = (string)trim(str_replace($postcode, '', $xml->Item[$i]->List));
                $addresses[$i]['key'] = (string)$xml->Item[$i]->Key;
            }
            return $addresses;
        }
        return false;
    }
    
    /**
     * Returns the details for any given postcode
     * @param string $postcode Should be the postcode you wish to find the information for
     * @return array|boolean Returns array if information exist else returns false
     */
    public function postcodeDetails($postcode)
    {
        $xml = $this->getData($this->getHost() . ':' . $this->getPort() . '/addresslookup.pce?postcode=' . urlencode($postcode));
        if ($xml->Address->Postcode != 'Error: Postcode Not Found') {
            return array_filter(get_object_vars($xml->Address));
        }
        return false;
    }
    
    /**
     * Returns the address details for a chosen address with the given key
     * @param string $key This should be the key from the address info previously retrieved
     * @return $this
     */
    public function setAddress($key)
    {
        $xml = $this->getData($this->getHost() . ':' . $this->getPort() . '/afddata.pce?Data=Address&Task=Retrieve&Fields=Standard&Key=' . urlencode($key));
        if ($xml->Result == 1) {
            $this->addressInfo = array_filter(array_change_key_case((array)$xml->Item, CASE_LOWER));
            $this->buildHouseAddress();
        }
        return $this;
    }
    
    /**
     * Returns the latitude of the last address location that was searched for
     * @return string|boolean
     */
    public function getLatitude()
    {
        if (!empty($this->latitude)) {
            return $this->latitude;
        }
        return false;
    }
    
    /**
     * Returns the longitude of the last address location that was searched for
     * @return string|boolean
     */
    public function getLongitude()
    {
        if (!empty($this->longitude)) {
            return $this->longitude;
        }
        return false;
    }
    
    /**
     * Checks to see if the program is active for the given location
     * @return boolean Returns true if program active else returns false
     */
    public function programActive()
    {
        $statusxml = $this->getData($this->getHost() . ':' . $this->getPort() . '/status.pce');
        return $statusxml->PCEStatus == 'OK' ? true : false;
    }
    
    /**
     * Sets the address information
     */
    protected function buildHouseAddress()
    {
        if (!empty($this->addressInfo)) {
            $this->latitude = $this->addressInfo['latitude'];
            $this->longitude = $this->addressInfo['longitude'];
            if (!empty($this->addressInfo['organisation'])) {
                $this->address1 = $this->addressInfo['organisation'] . ', ' . $this->addressInfo['property'];
                $this->address2 = $this->addressInfo['street'];
                $this->address3 = $this->addressInfo['locality'];
            } else {
                $this->address1 = (strlen($this->addressInfo['property']) >= 3 ? $this->addressInfo['property'] . ', ' . $this->addressInfo['street'] : $this->addressInfo['street']);
                $this->address2 = $this->addressInfo['locality'];
                $this->address3 = '';
            }
            $this->town = $this->addressInfo['town'];
            $this->county = $this->addressInfo['postalcounty'];
        }
        return $this;
    }
    
    /**
     * Returns an array containing the address information if it has been set
     * @return array This should contain any address information if it has been set
     */
    public function getAddressInfo()
    {
        return $this->addressInfo;
    }
    
    /**
     * Gets the information from the URL given in XML format and turns it to an array
     * @param string $url This should be the URL with the given information
     * @return array Returns the results from the URL given in an array format
     */
    protected function getData($url)
    {
        $client = new Client(['timeout'  => 2.0]);
        try {
            $response = $client->get($url);
            if ($response->getStatusCode() === 200) {
                return simplexml_load_string($response->getBody());
            }
        } catch (ConnectException $e) {
            new \Exception($e->getMessage());
        }
        return false;
    }
}
