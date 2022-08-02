# getaddress
A PHP library for the [getaddress.io](http://getaddress.io) postcode lookup service

# Pre-requisites
You will require a [getaddress.io](http://getaddress.io) API key.  For low use applications (fewer than 20 lookups/day) this is free.

# Usage
    $client = new \WombatInvest\GetAddress\GetAddressClient('YOUR-GETADDRESS.IO-KEY');
    $result = $client->lookup('NR10 4JJ');
    $address0 = $result->getAddresses()[0];
    echo $address0->getTown();

# Tests
    GETADDRESSKEY=YOUR-GETADDRESS.IO-KEY vendor/bin/phpunit tests/
