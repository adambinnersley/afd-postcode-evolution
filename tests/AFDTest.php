<?php
namespace AFD\Tests;

use AFD\AFD;
use PHPUnit\Framework\TestCase;

class AFDTest extends TestCase{
    
    public $afd;
    
    public function setUp() {
        $this->afd = new AFD();
    }
    
    public function tearDown() {
        $this->afd = null;
    }
    
    /**
     * @covers AFD\AFD::setHost
     */
    public function testSetHost() {
        $this->assertObjectHasAttribute('address1', $this->afd->setHost('https://testdomain.co.uk'));
        $this->assertObjectNotHasAttribute('address10', $this->afd->setHost('https://testdomain.co.uk'));
    }
    
    /**
     * @covers AFD\AFD::getHost
     * @covers AFD\AFD::setHost
     */
    public function testGetHost() {
        $this->assertEquals('https://testdomain.co.uk', $this->afd->getHost());
        $this->afd->setHost('http://pce.afd.co.uk');
        $this->assertEquals('http://pce.afd.co.uk', $this->afd->getHost());
    }
    
    /**
     * @covers AFD\AFD::getPort
     * @covers AFD\AFD::setPort
     */
    public function testPortMethods() {
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
    public function testProgramActive() {        
        $this->afd->setHost('http://www.nohost.co.uk');
        $this->assertFalse($this->afd->programActive(), 'Incorrect host test');
        $this->afd->setHost('http://pce.afd.co.uk')->setPort(125);
        $this->assertFalse($this->afd->programActive(), 'Incorrect port test');
        $this->afd->setHost('http://pce.afd.co.uk')->setPort(80);
        $this->assertTrue($this->afd->programActive(), 'Correct host and port test');
    }
    
    /**
     * @covers AFD\AFD::findAddress
     * @covers AFD\AFD::getData
     * @covers AFD\AFD::getHost
     * @covers AFD\AFD::getPort
     */
    public function testFindAddress() {
        $address = $this->afd->findAddresses('LN1 1YA');
        $this->assertArrayHasKey(10, $address);
        $this->assertContains('Flat 13', $address);
        $this->assertContains('address', $address);
        $noneExistingAddress = $this->afd->findAddresses('LN86W 1YA');
        $this->assertArrayNotHasKey(0, $noneExistingAddress);
        $this->assertNotContains('address', $noneExistingAddress);
    }
    
    /**
     * @covers AFD\AFD::getPostcodeDetails
     * @covers AFD\AFD::getData
     * @covers AFD\AFD::getHost
     * @covers AFD\AFD::getPort
     */
    public function testGetPostcodeDetails() {
        $address = $this->afd->postcodeDetails('LN1 1YA');
        $this->assertArrayHasKey('Street', $address);
        $this->assertArrayHasKey('Town', $address);
        $this->assertContains('Lincoln', $address);
        $this->assertContains('LN1 1YA', $address);
        $this->assertNotContains('Postcode not found', $address);
    }
    
    /**
     * @covers AFD\AFD::setAddress
     * @covers AFD\AFD::getData
     * @covers AFD\AFD::getHost
     * @covers AFD\AFD::getPort
     */
    public function testSetAddress() {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers AFD\AFD::getLongitude
     */
    public function testGetLongitude() {
        $this->assertEquals('-0.5454', $this->afd->getLongitude());
    }
    
    /**
     * @covers AFD\AFD::getLatitude
     */
    public function testGetLatitude() {
        $this->assertEquals('53.2295', $this->afd->getLatitude());
    }
}