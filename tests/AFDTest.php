<?php
namespace AFD\Tests;

use AFD\AFD;
use PHPUnit\Framework\TestCase;

class AFDTest extends TestCase{
    
    public $afd;
    
    public function setUp(){
        $this->afd = new AFD();
    }
    
    public function tearDown(){
        $this->afd = null;
    }
    
    public function testSetHost(){
        $this->assertObjectHasAttribute('address1', $this->afd->setHost('https://ldctestadam.co.uk'));
    }
}