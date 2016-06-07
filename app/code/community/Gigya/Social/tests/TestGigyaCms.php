<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 6/6/16
 * Time: 9:04 AM
 */
include_once "../sdk/gigyaCMS.php";
class TestGigyaCms extends PHPUnit_Framework_TestCase
{

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $gigyaCms;

    public function testIsRaasFalse()
    {
        $this->gigyaCms->expects($this->once())->method('call')->with($this->equalTo("accounts.getSchema"), $this->equalTo(array()))->willReturn(403036);
        $this->assertFalse($this->gigyaCms->isRaaS());
        
    }
    public function testIsRaasTrue()
    {
        $this->gigyaCms->expects($this->once())->method('call')->with($this->equalTo("accounts.getSchema"), $this->equalTo(array()))->willReturn(array());
        $this->assertTrue($this->gigyaCms->isRaaS());

    }

    public function testExchangeUidSigRaas()
    {
        $uid = "123456";
        $timestamp = time();
        $sig = sha1($timestamp . $uid);
        $this->gigyaCms->expects($this->once())->method('call')->with($this->equalTo("accounts.exchangeUIDSignature"), $this->equalTo(array(
            "UID" => $uid,
            "signatureTimestamp" => $timestamp,
            "UIDSignature" => $sig
        )));
        $this->gigyaCms->exchangeUidSignature($uid, $sig, $timestamp, "raas");
    }

    public function testtestExchangeUidSigSocial()
    {
        $uid = "123456";
        $timestamp = time();
        $sig = sha1($timestamp . $uid);
        $this->gigyaCms->expects($this->once())->method('call')->with($this->equalTo("socialize.exchangeUIDSignature"), $this->equalTo(array(
            "UID" => $uid,
            "signatureTimestamp" => $timestamp,
            "UIDSignature" => $sig
        )));
        $this->gigyaCms->exchangeUidSignature($uid, $sig, $timestamp, "socialize");
    }

    protected function setUp()
    {
        $this->gigyaCms = $this->getMockBuilder("GigyaCMS")
            ->setMethods(["call"])
            ->getMock();

    }

}
