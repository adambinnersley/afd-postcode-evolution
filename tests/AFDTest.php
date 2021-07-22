<?php

namespace AFD\Tests;

use AFD\AFD;
use PHPUnit\Framework\TestCase;

class AFDTest extends TestCase
{
    
    public $afd;
    
    public function setUp(): void
    {
        $this->afd = new AFD();
    }
    
    public function tearDown(): void
    {
        $this->afd = null;
    }
    
    /**
     * @covers AFD\AFD::setHost
     */
    public function testSetHost()
    {
        $this->assertObjectHasAttribute('address1', $this->afd->setHost('https://testdomain.co.uk'));
        $this->assertObjectNotHasAttribute('address10', $this->afd->setHost('https://testdomain.co.uk'));
    }
    
    /**
     * @covers AFD\AFD::getHost
     * @covers AFD\AFD::setHost
     */
    public function testGetHost()
    {
        $this->assertEquals('https://testdomain.co.uk', $this->afd->getHost());
        $this->afd->setHost('http://pce.afd.co.uk');
        $this->assertEquals('http://pce.afd.co.uk', $this->afd->getHost());
    }
    
    /**
     * @covers AFD\AFD::getPort
     * @covers AFD\AFD::setPort
     */
    public function testPortMethods()
    {
        $this->afd->setPort(125);
        $this->assertEquals(125, $this->afd->getPort());
        $this->afd->setPort(-1);
        $this->assertEquals(125, $this->afd->getPort());
        $this->afd->setPort('not_a_num');
        $this->assertEquals(125, $this->afd->getPort());
        $this->afd->setPort(81);
        $this->assertEquals(81, $this->afd->getPort());
    }
    
    /**
     * @covers AFD\AFD::programActive
     * @covers AFD\AFD::getData
     * @covers AFD\AFD::getHost
     * @covers AFD\AFD::setHost
     * @covers AFD\AFD::getPort
     * @covers AFD\AFD::setPort
     */
    public function testProgramActive()
    {
        $response = $this->getData('tests\Responces\getStatus.xml');
        
        $mock = $this->getMockBuilder(AFD::class)->setMethods(['getData'])->getMock();
        $mock->method('getData')->willReturn($response);
        
        $this->afd->setHost('http://www.nohost.co.uk');
        $this->assertFalse($this->afd->programActive(), 'Incorrect host test');
        $this->afd->setHost('http://pce.afd.co.uk')->setPort(125);
        $this->assertFalse($this->afd->programActive(), 'Incorrect port test');
        $this->afd->setHost('http://pce.afd.co.uk')->setPort(80);
        $this->assertTrue($mock->programActive(), 'Correct host and port test');
    }
    
    /**
     * @covers AFD\AFD::findAddresses
     * @covers AFD\AFD::getData
     * @covers AFD\AFD::getHost
     * @covers AFD\AFD::getPort
     */
    public function testFindAddress()
    {
        $response = $this->getData('tests\Responces\findAddresses.xml');
        
        $mock = $this->getMockBuilder(AFD::class)->setMethods(['getData'])->getMock();
        $mock->method('getData')->willReturn($response);
        
        $address = $mock->findAddresses('LN1 1YA');
        $this->assertArrayHasKey(10, $address);
        $this->assertArrayHasKey('address', $address[0]);
        $this->assertArrayHasKey('key', $address[5]);
        
        $responseNF = $this->getData('tests\Responces\postcodeNotFound.xml');
        
        $mockNF = $this->getMockBuilder(AFD::class)->setMethods(['getData'])->getMock();
        $mockNF->method('getData')->willReturn($responseNF);
        
        $noneExistingAddress = $mockNF->findAddresses('LN86W 1YA');
        $this->assertFalse($noneExistingAddress);
    }
    
    /**
     * @covers AFD\AFD::postcodeDetails
     * @covers AFD\AFD::getData
     * @covers AFD\AFD::getHost
     * @covers AFD\AFD::getPort
     */
    public function testGetPostcodeDetails()
    {
        $response = $this->getData('tests\Responces\getAddressDetails.xml');
        
        $mock = $this->getMockBuilder(AFD::class)->setMethods(['getData'])->getMock();
        $mock->method('getData')->willReturn($response);
        
        $address = $mock->postcodeDetails('LN1 1YA');
        $this->assertArrayHasKey('Street', $address);
        $this->assertArrayHasKey('Town', $address);
        $this->assertContains('Lincoln', $address);
        $this->assertContains('LN1 1YA', $address);
        $this->assertNotContains('Postcode not found', $address);
    }
    
    /**
     * @covers AFD\AFD::setAddress
     * @covers AFD\AFD::buildHouseAddress
     * @covers AFD\AFD::getAddressInfo
     */
    public function testSetAddress()
    {
        $response = $this->getData('tests\Responces\setAddress.xml');
        
        $mock = $this->getMockBuilder(AFD::class)->setMethods(['getData'])->getMock();
        $mock->method('getData')->willReturn($response);
        $mock->setAddress('LN1 1YA1006~20210622');
        
        $this->assertArrayHasKey('property', $mock->getAddressInfo());
    }
    
    /**
     * @covers AFD\AFD::getLongitude
     * @covers AFD\AFD::setAddress
     * @covers AFD\AFD::buildHouseAddress
     */
    public function testGetLongitude()
    {
        $this->assertFalse($this->afd->getLongitude());
        
        $response = $this->getData('tests\Responces\setAddress.xml');
        
        $mock = $this->getMockBuilder(AFD::class)->setMethods(['getData'])->getMock();
        $mock->method('getData')->willReturn($response);
        $mock->setAddress('LN1 1YA1006~20210622');
        
        $this->assertEquals('-0.5454', $mock->getLongitude());
    }
    
    /**
     * @covers AFD\AFD::getLatitude
     * @covers AFD\AFD::setAddress
     * @covers AFD\AFD::buildHouseAddress
     */
    public function testGetLatitude()
    {
        $this->assertFalse($this->afd->getLatitude());
        $response = $this->getData('tests\Responces\setAddress.xml');
        
        $mock = $this->getMockBuilder(AFD::class)->setMethods(['getData'])->getMock();
        $mock->method('getData')->willReturn($response);
        $mock->setAddress('LN1 1YA1006~20210622');
        
        $this->assertEquals('53.2295', $mock->getLatitude());
    }
    
    protected function getData($url)
    {
        $data = file_get_contents($url);
        return simplexml_load_string($data);
    }
}
