<?php

namespace Qcloud\Cos\Tests;

use Qcloud\Cos\Client;
use Qcloud\Cos\Exception\CosException;

class BucketTest extends \PHPUnit_Framework_TestCase
{
    private $cosClient;

    protected function setUp()
    {
        TestHelper::nuke('zuhaotestphpbucket-'.getenv('COS_APPID'));

        $this->cosClient = new Client(array('region' => getenv('COS_REGION'),
            'credentials' => array(
                'appId' => getenv('COS_APPID'),
                'secretId' => getenv('COS_KEY'),
                'secretKey' => getenv('COS_SECRET'))));
    }

    protected function tearDown()
    {
        TestHelper::nuke('zuhaotestphpbucket-'.getenv('COS_APPID'));
        sleep(2);
    }

    public function testCreateBucket()
    {
        try {
            $result = $this->cosClient->createBucket(array('Bucket' => 'zuhaotestphpbucket-'.getenv('COS_APPID')));
            sleep(2);
        } catch (\Exception $e) {
            $this->assertFalse(true, $e);
        }
    }


    public function testDeleteBucket()
    {
        try {
            $result = $this->cosClient->createBucket(array('Bucket' => 'zuhaotestphpbucket-'.getenv('COS_APPID')));
            sleep(2);
            $result = $this->cosClient->deleteBucket(array('Bucket' => 'zuhaotestphpbucket-'.getenv('COS_APPID')));
        } catch (\Exception $e) {
            $this->assertFalse(true, $e);
        }
    }

    public function testDeleteNonexistedBucket()
    {
        try {
            $result = $this->cosClient->deleteBucket(array('Bucket' => 'zuhaotestphpbucket-'.getenv('COS_APPID')));
            sleep(2);
        } catch (CosException $e) {
            $this->assertTrue($e->getExceptionCode() === 'NoSuchBucket');
            $this->assertTrue($e->getStatusCode() === 404);
        }
    }

    public function testDeleteNonemptyBucket()
    {
        try {
            $result = $this->cosClient->createBucket(array('Bucket' => 'zuhaotestphpbucket-'.getenv('COS_APPID')));
            sleep(2);
            $result = $this->cosClient->putObject(array(
                'Bucket' => 'zuhaotestphpbucket-'.getenv('COS_APPID'), 'Key' => 'hello.txt', 'Body' => 'Hello World!'));
            $result = $this->cosClient->deleteBucket(array('Bucket' => 'zuhaotestphpbucket-'.getenv('COS_APPID')));
        } catch (CosException $e) {
            echo "$e\n";
            echo $e->getExceptionCode();
            $this->assertTrue($e->getExceptionCode() === 'BucketNotEmpty');
            $this->assertTrue($e->getStatusCode() === 409);
        }
    }

    public function testPutBucketACL()
    {
        try {
            $this->cosClient->createBucket(array('Bucket' => 'zuhaotestphpbucket-'.getenv('COS_APPID')));
            sleep(5);
            $this->cosClient->PutBucketAcl(array(
                'Bucket' => 'zuhaotestphpbucket-'.getenv('COS_APPID'),
                'Grants' => array(
                    array(
                        'Grantee' => array(
    'DisplayName' => 'qcs::cam::uin/123456789:uin/123456789',
    'ID' => 'qcs::cam::uin/123456789:uin/123456789',
    'Type' => 'CanonicalUser',
),
                        'Permission' => 'FULL_CONTROL',
                    ),
                    // ... repeated
                ),
                'Owner' => array(
    'DisplayName' => 'qcs::cam::uin/'.getenv('COS_UIN').':uin/'.getenv('COS_UIN').'',
    'ID' => 'qcs::cam::uin/'.getenv('COS_UIN').':uin/'.getenv('COS_UIN').'',
),));
        } catch (\Exception $e) {
    $this->assertFalse(true, $e);
}

    }

    public function testGetBucketACL()
{
    try {
        $this->cosClient->createBucket(array('Bucket' => 'zuhaotestphpbucket-'.getenv('COS_APPID')));
        sleep(5);
        $this->cosClient->PutBucketAcl(array(
            'Bucket' => 'zuhaotestphpbucket-'.getenv('COS_APPID'),
            'Grants' => array(
                array(
                    'Grantee' => array(
                        'DisplayName' => 'qcs::cam::uin/123456789:uin/123456789',
                        'ID' => 'qcs::cam::uin/123456789:uin/123456789',
                        'Type' => 'CanonicalUser',
                    ),
                    'Permission' => 'FULL_CONTROL',
                ),
                // ... repeated
            ),
            'Owner' => array(
                'DisplayName' => 'qcs::cam::uin/'.getenv('COS_UIN').':uin/'.getenv('COS_UIN').'',
                'ID' => 'qcs::cam::uin/'.getenv('COS_UIN').':uin/'.getenv('COS_UIN').'',
            ),));

    } catch (\Exception $e) {
        $this->assertFalse(true, $e);
    }
}

    public function testPutBucketLifecycle()
{
    try {
        $result = $this->cosClient->createBucket(array('Bucket' => 'zuhaotestphpbucket-'.getenv('COS_APPID')));
        sleep(2);
        $result = $this->cosClient->putBucketLifecycle(array(
            // Bucket is required
            'Bucket' => 'zuhaotestphpbucket-'.getenv('COS_APPID'),
            // Rules is required
            'Rules' => array(
                array(
                    'Expiration' => array(
                        'Days' => 1000,
                    ),
                    'ID' => 'id1',
                    'Filter' => array(
                        'Prefix' => 'documents/'
                    ),
                    // Status is required
                    'Status' => 'Enabled',
                    'Transitions' => array(
                        array(
                            'Days' => 100,
                            'StorageClass' => 'Standard_IA'),
                    ),
                    // ... repeated
                ),
            )));
    } catch (\Exception $e) {
        $this->assertFalse(true, $e);
    }
}

    public function testGetBucketLifecycle()
{
    try {
        $result = $this->cosClient->createBucket(array('Bucket' => 'zuhaotestphpbucket-'.getenv('COS_APPID')));
        sleep(2);
        $result = $this->cosClient->putBucketLifecycle(array(
            // Bucket is required
            'Bucket' => 'zuhaotestphpbucket-'.getenv('COS_APPID'),
            // Rules is required
            'Rules' => array(
                array(
                    'Expiration' => array(
                        'Days' => 1000,
                    ),
                    'ID' => 'id1',
                    'Filter' => array(
                        'Prefix' => 'documents/'
                    ),
                    // Status is required
                    'Status' => 'Enabled',
                    'Transitions' => array(
                        array(
                            'Days' => 100,
                            'StorageClass' => 'Standard_IA'),
                    ),
                    // ... repeated
                ),
            )));
        sleep(5);
        $result = $this->cosClient->getBucketLifecycle(array(
            // Bucket is required
            'Bucket' => 'zuhaotestphpbucket-'.getenv('COS_APPID'),
        ));
    } catch (\Exception $e) {
        $this->assertFalse(true, $e);
    }
}

    public function testDeleteBucketLifecycle()
{
    try {
        $result = $this->cosClient->createBucket(array('Bucket' => 'zuhaotestphpbucket-'.getenv('COS_APPID')));
        sleep(2);
        $result = $this->cosClient->putBucketLifecycle(array(
            // Bucket is required
            'Bucket' => 'zuhaotestphpbucket-'.getenv('COS_APPID'),
            // Rules is required
            'Rules' => array(
                array(
                    'Expiration' => array(
                        'Days' => 1000,
                    ),
                    'ID' => 'id1',
                    'Filter' => array(
                        'Prefix' => 'documents/'
                    ),
                    // Status is required
                    'Status' => 'Enabled',
                    'Transitions' => array(
                        array(
                            'Days' => 100,
                            'StorageClass' => 'Standard_IA'),
                    ),
                    // ... repeated
                ),
            )));
        sleep(5);
        $result = $this->cosClient->deleteBucketLifecycle(array(
            // Bucket is required
            'Bucket' => 'zuhaotestphpbucket-'.getenv('COS_APPID'),
        ));
    } catch (\Exception $e) {
        $this->assertFalse(true, $e);
    }
}

    public function testPutBucketCors()
{
    try {
        $result = $this->cosClient->createBucket(array('Bucket' => 'zuhaotestphpbucket-'.getenv('COS_APPID')));
        sleep(2);
        $result = $this->cosClient->putBucketCors(array(
            // Bucket is required
            'Bucket' => 'zuhaotestphpbucket-'.getenv('COS_APPID'),
            // CORSRules is required
            'CORSRules' => array(
                array(
                    'ID' => '1234',
                    'AllowedHeaders' => array('*',),
                    // AllowedMethods is required
                    'AllowedMethods' => array('PUT',),
                    // AllowedOrigins is required
                    'AllowedOrigins' => array('*',),
                    'ExposeHeaders' => array('*',),
                    'MaxAgeSeconds' => 1,
                ),
                // ... repeated
            ),
        ));
    } catch (\Exception $e) {
        $this->assertFalse(true, $e);
    }
}
    public function testGetBucketCors() {
    try {
        $result = $this->cosClient->createBucket(array('Bucket' => 'zuhaotestphpbucket-'.getenv('COS_APPID')));
        sleep(2);
        $result = $this->cosClient->putBucketCors(array(
            // Bucket is required
            'Bucket' => 'zuhaotestphpbucket-'.getenv('COS_APPID'),
            // CORSRules is required
            'CORSRules' => array(
                array(
                    'ID' => '1234',
                    'AllowedHeaders' => array('*',),
                    // AllowedMethods is required
                    'AllowedMethods' => array('PUT', ),
                    // AllowedOrigins is required
                    'AllowedOrigins' => array('*', ),
                    'ExposeHeaders' => array('*', ),
                    'MaxAgeSeconds' => 1,
                ),
                // ... repeated
            ),
        ));
        $result = $this->cosClient->getBucketCors(array(
            'Bucket' => 'zuhaotestphpbucket-'.getenv('COS_APPID'),
        ));
    } catch (\Exception $e) {
        $this->assertFalse(true, $e);
    }}
    public function testDeleteBucketCors() {
    try {
        $result = $this->cosClient->createBucket(array('Bucket' => 'zuhaotestphpbucket-'.getenv('COS_APPID')));
        sleep(2);
        $result = $this->cosClient->putBucketCors(array(
            // Bucket is required
            'Bucket' => 'zuhaotestphpbucket-'.getenv('COS_APPID'),
            // CORSRules is required
            'CORSRules' => array(
                array(
                    'ID' => '1234',
                    'AllowedHeaders' => array('*',),
                    // AllowedMethods is required
                    'AllowedMethods' => array('PUT', ),
                    // AllowedOrigins is required
                    'AllowedOrigins' => array('*', ),
                    'ExposeHeaders' => array('*', ),
                    'MaxAgeSeconds' => 1,
                ),
                // ... repeated
            ),
        ));
        $result = $this->cosClient->deleteBucketCors(array(
            'Bucket' => 'zuhaotestphpbucket-'.getenv('COS_APPID'),
        ));
    } catch (\Exception $e) {
        $this->assertFalse(true, $e);
    }}
}
