<?php

namespace WombatInvest\GetAddress\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use WombatInvest\GetAddress\GetAddressAuthenticationException;
use WombatInvest\GetAddress\GetAddressClient;
use WombatInvest\GetAddress\GetAddressLookupException;
use WombatInvest\GetAddress\GetAddressResponse;

class GetAddressClientTest extends TestCase
{
    /**
     * Tests that attempting to instantiate GetAddressClient with an empty apiKey throws an error
     */
    public function testWithoutApikey()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No apiKey provided');

        new GetAddressClient('');
    }


    /**
     * Tests the lookup function with an invalid apiKey
     */
    public function testLookupWithInvalidApikey()
    {
        $this->expectException(GetAddressAuthenticationException::class);

        $client = new GetAddressClient('fooo');
        $client->lookup('NR10 4JJ');
    }


    public function testParseResponse()
    {
        $client = new GetAddressClient('fooo');
        $response = $this->getSampleResponse();

        $result = $client->parseResponse($response);

        $this->checkResultObject($result);
    }


    /**
     * Tests the lookup function with just a postcode
     *
     * @depends testParseResponse
     */
    public function testLookup()
    {
        $result = $this->getAuthenicatedClient()->lookup('NR10 4JJ');

        $this->checkResultObject($result);
    }


    /**
     * Tests the lookup function with a postcode and house name
     *
     * @depends testParseResponse
     */
    public function testLookupWithHouseName()
    {
        $result = $this->getAuthenicatedClient()->lookup('NR10 4JJ', 'Bank');

        $this->assertEquals(1, sizeof($result->getAddresses()));

        //Check that the correct property was returned
        $address0 = $result->getAddresses()[0];
        $this->assertEquals('Bank House', $address0->getLine1());
    }


    /**
     * Tests the lookup function with an invalid postcode
     */
    public function testInvalidLookup()
    {
        $this->expectException(GetAddressLookupException::class);
        $this->getAuthenicatedClient()->lookup('XX10 4JJ');
    }



    private function getSampleResponse()
    {
        //Example JSON for a lookup of 'NR10 4JJ'
        return '{
            "Latitude":52.76197,
            "Longitude":1.109534,
            "Addresses": [
                "7 Market Place, , , , Reepham, Norwich, Norfolk",
                "Bank House, Market Place, , , Reepham, Norwich, Norfolk",
                "Bircham Centre, Market Place, , , Reepham, Norwich, Norfolk",
                "Bonhams Auctioneers & Valuers, Market Place, , , Reepham, Norwich, Norfolk",
                "Breese House, Market Place, , , Reepham, Norwich, Norfolk",
                "Browns the Butchers, Sun House, Market Place, , Reepham, Norwich, Norfolk",
                "Butler Castell, Market Place, , , Reepham, Norwich, Norfolk",
                "Carlton House, Market Place, , , Reepham, Norwich, Norfolk",
                "Diannes Pantry, 8 Market Place, , , Reepham, Norwich, Norfolk",
                "Finchley Cottage, Market Place, , , Reepham, Norwich, Norfolk",
                "Flat, 8 Market Place, , , Reepham, Norwich, Norfolk",
                "Flat, The Chimes, Market Place, , Reepham, Norwich, Norfolk",
                "Flat 1, Sun House, Market Place, , Reepham, Norwich, Norfolk",
                "Flat 2, Sun House, Market Place, , Reepham, Norwich, Norfolk",
                "Flat 3, Sun House, Market Place, , Reepham, Norwich, Norfolk",
                "Flat 4, Sun House, Market Place, , Reepham, Norwich, Norfolk",
                "Flat 5, Sun House, Market Place, , Reepham, Norwich, Norfolk",
                "H S B C, Market Place, , , Reepham, Norwich, Norfolk",
                "Hewkes House, Market Place, , , Reepham, Norwich, Norfolk",
                "Hideaway Cottage, Market Place, , , Reepham, Norwich, Norfolk",
                "Ivy House, Market Place, , , Reepham, Norwich, Norfolk",
                "Kings Arms, Market Place, , , Reepham, Norwich, Norfolk",
                "Melton House, Market Place, , , Reepham, Norwich, Norfolk",
                "Middle Flat, Iona, Market Place, , Reepham, Norwich, Norfolk",
                "Old Telephone Exchange, Market Place, , , Reepham, Norwich, Norfolk",
                "One Stop, Reepham Library, Market Place, , Reepham, Norwich, Norfolk",
                "Queen Cottage, Market Place, , , Reepham, Norwich, Norfolk",
                "Quidsin, St. Johns Alley, Market Place, , Reepham, Norwich, Norfolk",
                "Reepham Beauty Therapy, Market Place, , , Reepham, Norwich, Norfolk",
                "Riches House, Market Place, , , Reepham, Norwich, Norfolk",
                "Robertsons, Market Place, , , Reepham, Norwich, Norfolk",
                "St. Johns Alley, Market Place, , , Reepham, Norwich, Norfolk",
                "St. Michaels House, Market Place, , , Reepham, Norwich, Norfolk",
                "The Chimes, Market Place, , , Reepham, Norwich, Norfolk",
                "The Dial House, Market Place, , , Reepham, Norwich, Norfolk",
                "The Old Bakery, Market Place, , , Reepham, Norwich, Norfolk",
                "Top Flat, Iona, Market Place, , Reepham, Norwich, Norfolk",
                "Tops Group, Market Place, , , Reepham, Norwich, Norfolk",
                "Tops Office, Market Place, , , Reepham, Norwich, Norfolk",
                "Very Nice Things, Market Place, , , Reepham, Norwich, Norfolk"
            ]
        }';
    }

    private function checkResultObject($result)
    {
        $this->assertInstanceOf(GetAddressResponse::class, $result);
        $this->assertEquals('52.76197', $result->getLatitude());
        $this->assertEquals('1.109534', $result->getLongitude());
        $this->assertTrue(is_array($result->getAddresses()));

        //Check that the address fields have been correctly set
        $address0 = $result->getAddresses()[0];
        $this->assertEquals('7 Market Place', $address0->getLine1());
        $this->assertEquals('', $address0->getLine2());
        $this->assertEquals('', $address0->getLine3());
        $this->assertEquals('', $address0->getLine4());
        $this->assertEquals('Reepham', $address0->getTown());
        $this->assertEquals('Norwich', $address0->getPostalTown());
        $this->assertEquals('Norfolk', $address0->getCounty());
    }


    /**
     * Returns a GetAddressClient with a valid key (assuming a valid one has been supplied)
     *
     * @return [type] [description]
     */
    private function getAuthenicatedClient()
    {
        $apiKey = getenv('GETADDRESSKEY');
        if (!$apiKey) {
            $this->markTestIncomplete('No api key has been set, so unable to test against getaddress.io');
            return;
        }

        return new GetAddressClient($apiKey);
    }
}
