<?php

use PHPUnit\Framework\TestCase;

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'Zend/Feed/Pubsubhubbub/Subscriber.php';
require_once 'Zend/Feed/Pubsubhubbub/Model/Subscription.php';
require_once 'Zend/Db/Table/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @group      Zend_Feed
 * @group      Zend_Feed_Subsubhubbub
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Feed_Pubsubhubbub_SubscriberTest extends TestCase
{
    protected $_subscriber = null;

    protected $_adapter = null;

    protected $_tableGateway = null;

    protected function setUp(): void
    {
        $client = new Zend_Http_Client();
        Zend_Feed_Pubsubhubbub::setHttpClient($client);
        $this->_subscriber = new Zend_Feed_Pubsubhubbub_Subscriber();
        $this->_adapter = $this->_getCleanMock(
            'Zend_Db_Adapter_Abstract'
        );
        $this->_tableGateway = $this->_getCleanMock(
            'Zend_Db_Table_Abstract'
        );
        $this->_tableGateway->expects($this->any())->method('getAdapter')
            ->will($this->returnValue($this->_adapter));
    }


    public function testAddsHubServerUrl()
    {
        $this->_subscriber->addHubUrl('http://www.example.com/hub');
        $this->assertEquals(['http://www.example.com/hub'], $this->_subscriber->getHubUrls());
    }

    public function testAddsHubServerUrlsFromArray()
    {
        $this->_subscriber->addHubUrls([
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        ]);
        $this->assertEquals([
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        ], $this->_subscriber->getHubUrls());
    }

    public function testAddsHubServerUrlsFromArrayUsingSetConfig()
    {
        $this->_subscriber->setConfig(['hubUrls' => [
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        ]]);
        $this->assertEquals([
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        ], $this->_subscriber->getHubUrls());
    }

    public function testRemovesHubServerUrl()
    {
        $this->_subscriber->addHubUrls([
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        ]);
        $this->_subscriber->removeHubUrl('http://www.example.com/hub');
        $this->assertEquals([
            1 => 'http://www.example.com/hub2'
        ], $this->_subscriber->getHubUrls());
    }

    public function testRetrievesUniqueHubServerUrlsOnly()
    {
        $this->_subscriber->addHubUrls([
            'http://www.example.com/hub', 'http://www.example.com/hub2',
            'http://www.example.com/hub'
        ]);
        $this->assertEquals([
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        ], $this->_subscriber->getHubUrls());
    }

    public function testThrowsExceptionOnSettingEmptyHubServerUrl()
    {
        $this->expectException(Zend_Feed_Pubsubhubbub_Exception::class);
        $this->_subscriber->addHubUrl('');
    }

    public function testThrowsExceptionOnSettingNonStringHubServerUrl()
    {
        $this->expectException(Zend_Feed_Pubsubhubbub_Exception::class);
        $this->_subscriber->addHubUrl(123);
    }

    public function testThrowsExceptionOnSettingInvalidHubServerUrl()
    {
        $this->expectException(Zend_Feed_Pubsubhubbub_Exception::class);
        $this->_subscriber->addHubUrl('http://');
    }

    public function testAddsParameter()
    {
        $this->_subscriber->setParameter('foo', 'bar');
        $this->assertEquals(['foo' => 'bar'], $this->_subscriber->getParameters());
    }

    public function testAddsParametersFromArray()
    {
        $this->_subscriber->setParameters([
            'foo' => 'bar', 'boo' => 'baz'
        ]);
        $this->assertEquals([
            'foo' => 'bar', 'boo' => 'baz'
        ], $this->_subscriber->getParameters());
    }

    public function testAddsParametersFromArrayInSingleMethod()
    {
        $this->_subscriber->setParameter([
            'foo' => 'bar', 'boo' => 'baz'
        ]);
        $this->assertEquals([
            'foo' => 'bar', 'boo' => 'baz'
        ], $this->_subscriber->getParameters());
    }

    public function testAddsParametersFromArrayUsingSetConfig()
    {
        $this->_subscriber->setConfig(['parameters' => [
            'foo' => 'bar', 'boo' => 'baz'
        ]]);
        $this->assertEquals([
            'foo' => 'bar', 'boo' => 'baz'
        ], $this->_subscriber->getParameters());
    }

    public function testRemovesParameter()
    {
        $this->_subscriber->setParameters([
            'foo' => 'bar', 'boo' => 'baz'
        ]);
        $this->_subscriber->removeParameter('boo');
        $this->assertEquals([
            'foo' => 'bar'
        ], $this->_subscriber->getParameters());
    }

    public function testRemovesParameterIfSetToNull()
    {
        $this->_subscriber->setParameters([
            'foo' => 'bar', 'boo' => 'baz'
        ]);
        $this->_subscriber->setParameter('boo', null);
        $this->assertEquals([
            'foo' => 'bar'
        ], $this->_subscriber->getParameters());
    }

    public function testCanSetTopicUrl()
    {
        $this->_subscriber->setTopicUrl('http://www.example.com/topic');
        $this->assertEquals('http://www.example.com/topic', $this->_subscriber->getTopicUrl());
    }

    public function testThrowsExceptionOnSettingEmptyTopicUrl()
    {
        $this->expectException(Zend_Feed_Pubsubhubbub_Exception::class);
        $this->_subscriber->setTopicUrl('');
    }


    public function testThrowsExceptionOnSettingNonStringTopicUrl()
    {
        $this->expectException(Zend_Feed_Pubsubhubbub_Exception::class);
        $this->_subscriber->setTopicUrl(123);
    }


    public function testThrowsExceptionOnSettingInvalidTopicUrl()
    {
        $this->expectException(Zend_Feed_Pubsubhubbub_Exception::class);
        $this->_subscriber->setTopicUrl('http://');
    }

    public function testThrowsExceptionOnMissingTopicUrl()
    {
        $this->expectException(Zend_Feed_Pubsubhubbub_Exception::class);
        $this->_subscriber->getTopicUrl();
    }

    public function testCanSetCallbackUrl()
    {
        $this->_subscriber->setCallbackUrl('http://www.example.com/callback');
        $this->assertEquals('http://www.example.com/callback', $this->_subscriber->getCallbackUrl());
    }

    public function testThrowsExceptionOnSettingEmptyCallbackUrl()
    {
        $this->expectException(Zend_Feed_Pubsubhubbub_Exception::class);
        $this->_subscriber->setCallbackUrl('');
    }


    public function testThrowsExceptionOnSettingNonStringCallbackUrl()
    {
        $this->expectException(Zend_Feed_Pubsubhubbub_Exception::class);
        $this->_subscriber->setCallbackUrl(123);
    }


    public function testThrowsExceptionOnSettingInvalidCallbackUrl()
    {
        $this->expectException(Zend_Feed_Pubsubhubbub_Exception::class);
        $this->_subscriber->setCallbackUrl('http://');
    }

    public function testThrowsExceptionOnMissingCallbackUrl()
    {
        $this->expectException(Zend_Feed_Pubsubhubbub_Exception::class);
        $this->_subscriber->getCallbackUrl();
    }

    public function testCanSetLeaseSeconds()
    {
        $this->_subscriber->setLeaseSeconds('10000');
        $this->assertEquals(10000, $this->_subscriber->getLeaseSeconds());
    }

    public function testThrowsExceptionOnSettingZeroAsLeaseSeconds()
    {
        $this->expectException(Zend_Feed_Pubsubhubbub_Exception::class);
        $this->_subscriber->setLeaseSeconds(0);
    }

    public function testThrowsExceptionOnSettingLessThanZeroAsLeaseSeconds()
    {
        $this->expectException(Zend_Feed_Pubsubhubbub_Exception::class);
        $this->_subscriber->setLeaseSeconds(-1);
    }

    public function testThrowsExceptionOnSettingAnyScalarTypeCastToAZeroOrLessIntegerAsLeaseSeconds()
    {
        $this->expectException(Zend_Feed_Pubsubhubbub_Exception::class);
        $this->_subscriber->setLeaseSeconds('0aa');
    }

    public function testCanSetPreferredVerificationMode()
    {
        $this->_subscriber->setPreferredVerificationMode(Zend_Feed_Pubsubhubbub::VERIFICATION_MODE_ASYNC);
        $this->assertEquals(Zend_Feed_Pubsubhubbub::VERIFICATION_MODE_ASYNC, $this->_subscriber->getPreferredVerificationMode());
    }

    public function testSetsPreferredVerificationModeThrowsExceptionOnSettingBadMode()
    {
        $this->expectException(Zend_Feed_Pubsubhubbub_Exception::class);
        $this->_subscriber->setPreferredVerificationMode('abc');
    }

    public function testPreferredVerificationModeDefaultsToSync()
    {
        $this->assertEquals(Zend_Feed_Pubsubhubbub::VERIFICATION_MODE_SYNC, $this->_subscriber->getPreferredVerificationMode());
    }

    public function testCanSetStorageImplementation()
    {
        $storage = new Zend_Feed_Pubsubhubbub_Model_Subscription($this->_tableGateway);
        $this->_subscriber->setStorage($storage);
        $this->assertThat($this->_subscriber->getStorage(), $this->identicalTo($storage));
    }

    public function testGetStorageThrowsExceptionIfNoneSet()
    {
        $this->expectException(Zend_Feed_Pubsubhubbub_Exception::class);
        $this->_subscriber->getStorage();
    }

    protected function _getCleanMock($className)
    {
        $class = new ReflectionClass($className);
        $methods = $class->getMethods();
        $stubMethods = [];
        foreach ($methods as $method) {
            if ($method->isPublic() || ($method->isProtected()
            && $method->isAbstract())) {
                $stubMethods[] = $method->getName();
            }
        }
        $mocked = $this->createMock(
            $className
        );
        return $mocked;
    }
}
