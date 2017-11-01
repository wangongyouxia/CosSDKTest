<?php

namespace Qcloud\Cos\Tests;

use Qcloud\Cos\Client;
use Qcloud\Cos\Exception\CosException;
use Qcloud\Cos\Exception\ServiceResponseException;

class ObjectTest extends \PHPUnit_Framework_TestCase {
    private $cosClient;
	private $default_bucket_name = 'testbucket';
    protected function setUp() {
        TestHelper::nuke($this->default_bucket_name);

        $this->cosClient = new Client(array('region' => getenv('COS_REGION'),
                    'credentials'=> array(
                        'appId' => getenv('COS_APPID'),
                    'secretId'    => getenv('COS_KEY'),
                    'secretKey' => getenv('COS_SECRET'))));
    }



    protected function tearDown() {
        TestHelper::nuke($this->default_bucket_name);
    }

    public function testPutObject() {
        try {
//			$this->cosClient->DeleteBucket(array('Bucket' => $this->default_bucket_name));
            $this->cosClient->CreateBucket(array('Bucket' => $this->default_bucket_name));
#		sleep(10);
		$this->cosClient->putObject(array(
                        'Bucket' => $this->default_bucket_name, 'Key' => 'hello.txt', 'Body' => 'Hello World'));
            $this->cosClient->deleteObject(array(
                        'Bucket' => $this->default_bucket_name, 'Key' => 'hello.txt', 'Body' => 'Hello World'));
//		$this->cosClient->DeleteBucket(array('Bucket' => $this->default_bucket_name));
        } catch (\Exception $e) {
            $this->assertFalse(true, $e);
        }
    }

    public function testPutObjectIntoNonexistedBucket() {
		$this->expectException(ServiceResponseException::class);
		$this->expectExceptionMessage("The specified bucket does not exist");
            $this->cosClient->putObject(array(
                        'Bucket' => '000testbucket', 'Key' => 'hello.txt', 'Body' => 'Hello World'));
            $this->cosClient->deleteObject(array(
                        'Bucket' => '000testbucket', 'Key' => 'hello.txt', 'Body' => 'Hello World'));

    }

    public function testUploadSmallObject() {
        try {
            $result = $this->cosClient->CreateBucket(array('Bucket' => $this->default_bucket_name));
            var_dump($result);
    
	    sleep(10);
            $this->cosClient->putObject(array(
                        'Bucket' => $this->default_bucket_name, 'Key' => 'hello.txt', 'Body' => 'Hello World'));
            $this->cosClient->deleteObject(array(
                        'Bucket' => $this->default_bucket_name, 'Key' => 'hello.txt', 'Body' => 'Hello World'));
			$this->cosClient->DeleteBucket(array('Bucket' => $this->default_bucket_name));
        } catch (\Exception $e) {
            $this->assertFalse(true, $e);
        }
    }

    public function testUploadLargeObject() {
        try {
            $this->cosClient->CreateBucket(array('Bucket' => $this->default_bucket_name));
            $this->cosClient->putObject(array(
                        'Bucket' => $this->default_bucket_name, 'Key' => 'hello.txt', 'Body' => str_repeat('a', 20 * 1024 * 1024)));
            $this->cosClient->deleteObject(array(
                        'Bucket' => $this->default_bucket_name, 'Key' => 'hello.txt', 'Body' => 'Hello World'));
			$this->cosClient->DeleteBucket(array('Bucket' => $this->default_bucket_name));
        } catch (\Exception $e) {
            $this->assertFalse(true, $e);
        }
    }

    public function testGetObjectUrl() {
        try{
            $this->cosClient->CreateBucket(array('Bucket' => $this->default_bucket_name));
            $this->cosClient->putObject(array(
                        'Bucket' => $this->default_bucket_name, 'Key' => 'hello.txt', 'Body' => str_repeat('a', 20 * 1024 * 1024)));
            $this->cosClient->deleteObject(array(
                        'Bucket' => $this->default_bucket_name, 'Key' => 'hello.txt', 'Body' => 'Hello World'));
            $this->cosClient->getObjectUrl($this->default_bucket_name, 'hello.txt', '+10 minutes');
			$this->cosClient->DeleteBucket(array('Bucket' => $this->default_bucket_name));
        } catch (\Exception $e) {
            $this->assertFalse(true, $e);
        }
    }
}
