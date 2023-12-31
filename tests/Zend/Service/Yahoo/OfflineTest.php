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
 * @package    Zend_Service_Yahoo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Service_Yahoo
 */
require_once 'Zend/Service/Yahoo.php';

/**
 * @see Zend_Service_Yahoo_ResultSet
 */
require_once 'Zend/Service/Yahoo/ResultSet.php';

/**
 * @see Zend_Http_Client_Adapter_Socket
 */
require_once 'Zend/Http/Client/Adapter/Socket.php';

/**
 * @see Zend_Http_Client_Adapter_Test
 */
require_once 'Zend/Http/Client/Adapter/Test.php';

/**
 * @see Zend_Service_Yahoo_WebResult
 */
require_once 'Zend/Service/Yahoo/WebResult.php';

/**
 * @category   Zend
 * @package    Zend_Service_Yahoo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Yahoo
 */
class Zend_Service_Yahoo_OfflineTest extends TestCase
{
    /**
     * Reference to Yahoo service consumer object
     *
     * @var Zend_Service_Yahoo
     */
    protected $_yahoo;

    /**
     * Socket based HTTP client adapter
     *
     * @var Zend_Http_Client_Adapter_Socket
     */
    protected $_httpClientAdapterSocket;

    /**
     * HTTP client adapter for testing
     *
     * @var Zend_Http_Client_Adapter_Test
     */
    protected $_httpClientAdapterTest;

    /**
     * Sets up this test case
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->_yahoo = new Zend_Service_Yahoo(constant('TESTS_ZEND_SERVICE_YAHOO_ONLINE_APPID'));

        $this->_httpClientAdapterSocket = new Zend_Http_Client_Adapter_Socket();

        $this->_httpClientAdapterTest = new Zend_Http_Client_Adapter_Test();
    }

    /**
     * Ensures that Zend_Service_Yahoo_ResultSet::current() throws an exception
     *
     * @return void
     */
    public function testResultSetCurrentException()
    {
        $domDocument = new DOMDocument();
        $domDocument->appendChild($domDocument->createElement('ResultSet'));

        $resultSet = new Zend_Service_Yahoo_OfflineTest_ResultSet($domDocument);

        try {
            $resultSet->current();
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString('implemented by child', $e->getMessage());
        }
    }

    /**
     * Ensures that inlinkDataSearch() throws an exception when the results option is invalid
     *
     * @return void
     */
    public function testInlinkDataSearchExceptionResultsInvalid()
    {
        try {
            $this->_yahoo->inlinkDataSearch('http://framework.zend.com/', ['results' => 101]);
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString("option 'results'", $e->getMessage());
        }
    }

    /**
     * Ensures that inlinkDataSearch() throws an exception when the start option is invalid
     *
     * @return void
     */
    public function testInlinkDataSearchExceptionStartInvalid()
    {
        try {
            $this->_yahoo->inlinkDataSearch('http://framework.zend.com/', ['start' => 1001]);
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString("option 'start'", $e->getMessage());
        }
    }

    /**
     * Ensures that inlinkDataSearch() throws an exception when the omit_inlinks option is invalid
     *
     * @return void
     */
    public function testInlinkDataSearchExceptionOmitLinksInvalid()
    {
        try {
            $this->_yahoo->inlinkDataSearch('http://framework.zend.com/', ['omit_inlinks' => 'oops']);
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString("option 'omit_inlinks'", $e->getMessage());
        }
    }

    /**
     * Ensures that imageSearch() throws an exception when the type option is invalid
     *
     * @return void
     */
    public function testImageSearchExceptionTypeInvalid()
    {
        try {
            $this->_yahoo->imageSearch('php', ['type' => 'oops']);
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString("option 'type'", $e->getMessage());
        }
    }

    /**
     * Ensures that imageSearch() throws an exception when the results option is invalid
     *
     * @return void
     */
    public function testImageSearchExceptionResultsInvalid()
    {
        try {
            $this->_yahoo->imageSearch('php', ['results' => 500]);
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString("option 'results'", $e->getMessage());
        }
    }

    /**
     * Ensures that imageSearch() throws an exception when the start option is invalid
     *
     * @return void
     */
    public function testImageSearchExceptionStartInvalid()
    {
        try {
            $this->_yahoo->imageSearch('php', ['start' => 1001]);
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString("option 'start'", $e->getMessage());
        }
    }

    /**
     * Ensures that imageSearch() throws an exception when the format option is invalid
     *
     * @return void
     */
    public function testImageSearchExceptionFormatInvalid()
    {
        try {
            $this->_yahoo->imageSearch('php', ['format' => 'oops']);
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString("option 'format'", $e->getMessage());
        }
    }

    /**
     * Ensures that imageSearch() throws an exception when the coloration option is invalid
     *
     * @return void
     */
    public function testImageSearchExceptionColorationInvalid()
    {
        try {
            $this->_yahoo->imageSearch('php', ['coloration' => 'oops']);
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString("option 'coloration'", $e->getMessage());
        }
    }

    /**
     * Ensures that localSearch() throws an exception when the results option is invalid
     *
     * @return void
     */
    public function testLocalSearchExceptionResultsInvalid()
    {
        try {
            $this->_yahoo->localSearch('php', ['results' => 'oops']);
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString("option 'results'", $e->getMessage());
        }
    }

    /**
     * Ensures that localSearch() throws an exception when the start option is invalid
     *
     * @return void
     */
    public function testLocalSearchExceptionStartInvalid()
    {
        try {
            $this->_yahoo->localSearch('php', ['start' => 'oops']);
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString("option 'start'", $e->getMessage());
        }
    }

    /**
     * Ensures that localSearch() throws an exception when the longitude option is invalid
     *
     * @return void
     */
    public function testLocalSearchExceptionLongitudeInvalid()
    {
        try {
            $this->_yahoo->localSearch('php', ['longitude' => -91]);
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString("option 'longitude'", $e->getMessage());
        }
    }

    /**
     * Ensures that localSearch() throws an exception when the latitude option is invalid
     *
     * @return void
     */
    public function testLocalSearchExceptionLatitudeInvalid()
    {
        try {
            $this->_yahoo->localSearch('php', ['latitude' => -181]);
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString("option 'latitude'", $e->getMessage());
        }
    }

    /**
     * Ensures that localSearch() throws an exception when the zip option is invalid
     *
     * @return void
     */
    public function testLocalSearchExceptionZipInvalid()
    {
        try {
            $this->_yahoo->localSearch('php', ['zip' => 'oops']);
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString("option 'zip'", $e->getMessage());
        }
    }

    /**
     * Ensures that localSearch() throws an exception when location data are missing
     *
     * @return void
     */
    public function testLocalSearchExceptionLocationMissing()
    {
        try {
            $this->_yahoo->localSearch('php');
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString('Location data', $e->getMessage());
        }
    }

    /**
     * Ensures that localSearch() throws an exception when the sort option is invalid
     *
     * @return void
     */
    public function testLocalSearchExceptionSortInvalid()
    {
        try {
            $this->_yahoo->localSearch('php', ['location' => '95014', 'sort' => 'oops']);
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString("option 'sort'", $e->getMessage());
        }
    }

    /**
     * Ensures that newsSearch() throws an exception when the results option is invalid
     *
     * @return void
     */
    public function testNewsSearchExceptionResultsInvalid()
    {
        try {
            $this->_yahoo->newsSearch('php', ['results' => 51]);
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString("option 'results'", $e->getMessage());
        }
    }

    /**
     * Ensures that newsSearch() throws an exception when the start option is invalid
     *
     * @return void
     */
    public function testNewsSearchExceptionStartInvalid()
    {
        try {
            $this->_yahoo->newsSearch('php', ['start' => 'oops']);
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString("option 'start'", $e->getMessage());
        }
    }

    /**
     * Ensures that newsSearch() throws an exception when the language option is invalid
     *
     * @return void
     */
    public function testNewsSearchExceptionLanguageInvalid()
    {
        try {
            $this->_yahoo->newsSearch('php', ['language' => 'oops']);
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString('selected language', $e->getMessage());
        }
    }

    /**
     * Ensures that pageDataSearch() throws an exception when the results option is invalid
     *
     * @return void
     */
    public function testPageDataSearchExceptionResultsInvalid()
    {
        try {
            $this->_yahoo->pageDataSearch('http://framework.zend.com/', ['results' => 101]);
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString("option 'results'", $e->getMessage());
        }
    }

    /**
     * Ensures that pageDataSearch() throws an exception when the start option is invalid
     *
     * @return void
     */
    public function testPageDataSearchExceptionStartInvalid()
    {
        try {
            $this->_yahoo->pageDataSearch('http://framework.zend.com/', ['start' => 1001]);
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString("option 'start'", $e->getMessage());
        }
    }

    /**
     * Ensures that videoSearch() throws an exception when the type option is invalid
     *
     * @return void
     */
    public function testVideoSearchExceptionTypeInvalid()
    {
        try {
            $this->_yahoo->videoSearch('php', ['type' => 'oops']);
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString("option 'type'", $e->getMessage());
        }
    }

    /**
     * Ensures that videoSearch() throws an exception when the results option is invalid
     *
     * @return void
     */
    public function testVideoSearchExceptionResultsInvalid()
    {
        try {
            $this->_yahoo->videoSearch('php', ['results' => 500]);
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString("option 'results'", $e->getMessage());
        }
    }

    /**
     * Ensures that videoSearch() throws an exception when the start option is invalid
     *
     * @return void
     */
    public function testVideoSearchExceptionStartInvalid()
    {
        try {
            $this->_yahoo->videoSearch('php', ['start' => 1001]);
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString("option 'start'", $e->getMessage());
        }
    }

    /**
     * Ensures that videoSearch() throws an exception when the format option is invalid
     *
     * @return void
     */
    public function testVideoSearchExceptionFormatInvalid()
    {
        try {
            $this->_yahoo->videoSearch('php', ['format' => 'oops']);
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString("option 'format'", $e->getMessage());
        }
    }

    /**
     * Ensures that webSearch() throws an exception when the results option is invalid
     *
     * @return void
     */
    public function testWebSearchExceptionResultsInvalid()
    {
        try {
            $this->_yahoo->webSearch('php', ['results' => 101]);
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString("option 'results'", $e->getMessage());
        }
    }

    /**
     * Ensures that webSearch() throws an exception when the start option is invalid
     *
     * @return void
     */
    public function testWebSearchExceptionStartInvalid()
    {
        try {
            $this->_yahoo->webSearch('php', ['start' => 'oops']);
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString("option 'start'", $e->getMessage());
        }
    }

    /**
     * Ensures that webSearch() throws an exception when the start option is invalid
     *
     * @return void
     */
    public function testWebSearchExceptionOptionInvalid()
    {
        try {
            $this->_yahoo->webSearch('php', ['oops' => 'oops']);
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString('parameters are invalid', $e->getMessage());
        }
    }

    /**
     * Ensures that webSearch() throws an exception when the type option is invalid
     *
     * @return void
     */
    public function testWebSearchExceptionTypeInvalid()
    {
        try {
            $this->_yahoo->webSearch('php', ['type' => 'oops']);
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertStringContainsString("option 'type'", $e->getMessage());
        }
    }

    /**
     * WebResult should check if the result has a Cache section or not
     *
     * @group ZF-3636
     * @doesNotPerformAssertions
     */
    public function testWebResultCache()
    {
        // create empty result eg. without cache section
        $domDoc = new DOMDocument();
        $element = $domDoc->createElement('Result');
        // this should not result in errors
        $webResult = new Zend_Service_Yahoo_WebResult($element);
    }
}


class Zend_Service_Yahoo_OfflineTest_ResultSet extends Zend_Service_Yahoo_ResultSet
{
    protected $_namespace = '';
}
