# AFD Postcode Evolution PHP Wrapper
A PHP Wrapper to retrieve the information given by the AFD Postcode Evolution Server software (http://www.afd.co.uk)

## Installation

Installation is available via [Composer/Packagist](https://packagist.org/packages/adamb/database), you can add the following line to your `composer.json` file:

```json
"adamb/afd": "^1.0"
```

or

```sh
composer require adamb/afd
```

## Usage

Example of usage can be found below:

### 1. Connect
```php
<?php

$afd = new AFD\AFD();
// Default port is 81 and no need to call setPort() method unless running as another port number
$afd->setHost('http://127.0.0.1')->setPort(81);
$afd->programActive();
// Will return true of false dependant on if connection can be made to the AFD Postcode Evolution software

```

### 2. List addresses for a given postcode
```php
<?php

$postcode = 'LN1 1YA';

$afd = new AFD\AFD();
$afd->setHost('http://myhost.co.uk');
$addresses = $afd->findAddresses($postcode);

print_r($addresses);

// Returns
Array (
    [0] => Array ( [address] => Lahore, 94-96 Newland, Lincoln [key] => LN1 1YA1001~20161213 )
    [1] => Array ( [address] => C A D Associates Ltd, 102-104 Newland, Lincoln [key] => LN1 1YA1002~20161213 )
    [2] => Array ( [address] => Europcar Van Rentals, 70-76 Newland, Lincoln [key] => LN1 1YA1003~20161213 )
    [3] => Array ( [address] => 90-92 Newland, Lincoln [key] => LN1 1YA1004~20161213 )
    [4] => Array ( [address] => N D F C, Lincoln House, 37-39 Newland, Lincoln [key] => LN1 1YA1005~20161213 )
    [5] => Array ( [address] => Flat 1, Brayford Quay, Newland, Lincoln [key] => LN1 1YA1006~20161213 )
    [6] => Array ( [address] => Flat 10, Brayford Quay, Newland, Lincoln [key] => LN1 1YA1007~20161213 )
    [7] => Array ( [address] => Flat 11, Brayford Quay, Newland, Lincoln [key] => LN1 1YA1008~20161213 )
    [8] => Array ( [address] => Flat 12, Brayford Quay, Newland, Lincoln [key] => LN1 1YA1009~20161213 )
    [9] => Array ( [address] => Flat 13, Brayford Quay, Newland, Lincoln [key] => LN1 1YA1010~20161213 )
    [10] => Array ( [address] => Flat 14, Brayford Quay, Newland, Lincoln [key] => LN1 1YA1011~20161213 )
    [11] => Array ( [address] => Flat 15, Brayford Quay, Newland, Lincoln [key] => LN1 1YA1012~20161213 )
    [12] => Array ( [address] => Flat 16, Brayford Quay, Newland, Lincoln [key] => LN1 1YA1013~20161213 )
    [13] => Array ( [address] => Flat 17, Brayford Quay, Newland, Lincoln [key] => LN1 1YA1014~20161213 )
    [14] => Array ( [address] => Flat 18, Brayford Quay, Newland, Lincoln [key] => LN1 1YA1015~20161213 )
    [15] => Array ( [address] => Flat 19, Brayford Quay, Newland, Lincoln [key] => LN1 1YA1016~20161213 )
    [16] => Array ( [address] => Flat 2, Brayford Quay, Newland, Lincoln [key] => LN1 1YA1017~20161213 )
    [17] => Array ( [address] => Flat 20, Brayford Quay, Newland, Lincoln [key] => LN1 1YA1018~20161213 )
    [18] => Array ( [address] => Flat 21, Brayford Quay, Newland, Lincoln [key] => LN1 1YA1019~20161213 )
    [19] => Array ( [address] => Flat 22, Brayford Quay, Newland, Lincoln [key] => LN1 1YA1020~20161213 )
    [20] => Array ( [address] => Flat 23, Brayford Quay, Newland, Lincoln [key] => LN1 1YA1021~20161213 )
    ...
    ...
    ...
    etc, etc, etc
    [69] => Array ( [address] => Jobcentre Plus, Viking House, 98 Newland, Lincoln [key] => LN1 1YA1070~20161213 )
    [70] => Array ( [address] => Pyne & Co, 100 Newland, Lincoln [key] => LN1 1YA1071~20161213 ) ) 

```

### 3. Find postcode area details
```php

$postcode = 'LN1 1YA';

$afd = new AFD\AFD();
$afd->setHost('http://myhost.co.uk');
$details = $afd->postcodeDetails($postcode);

print_r($details);

// Returns
Array
(
    [Street] => Newland
    [Town] => Lincoln
    [Postcode] => LN1 1YA
    [Mailsort] => 33749
    [PostcodeType] => Small User
    [HouseholdCount] => 1
    [AuthorityCode] => E07000138
    [Authority] => Lincoln
    [Constituency] => Lincoln
    [TVRegion] => Yorkshire
    [GridEast] => 49719
    [GridNorth] => 37125
    [Latitude] => 53.2295
    [Longitude] => -0.5454
    [STDCode] => 01522
    [WardCode] => E05010787
    [WardName] => Carholme
    [NHSCode] => Q33
    [NHSName] => East Midlands
    [NHSRegion] => Midlands and Eastern
    [NHSRegionCode] => Y22
    [Changed] => False
    [CensusCode] => BY12
    [Affluence] => Prosperous
    [LifeStage] => Empty nests and seniors
    [AdditionalCensusInfo] => Established focus on buying home, DIY, home improvements and the garden
    [Occupancy] => 5
    [OccupancyDescription] => Mostly Residential
    [AddressType] => 3
    [AddressTypeDescription] => Numbered and Named, Likelihood of Multiple Occupancy
    [PCTCode] => E38000100
    [PCTName] => Lincolnshire West
    [EERCode] => E15000004
    [EERName] => East Midlands
    [UrbanRuralCode] => C1
    [UrbanRuralName] => Urban City and Town
    [LEACode] => 925
    [LEAName] => Lincolnshire

)

```