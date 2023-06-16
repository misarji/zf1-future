<?php

use MyApp\Controller\Plugin\ThrowingPlugin;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestSuite;
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
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Controller_FrontTest::main() if this source file is executed directly.
use PHPUnit\TextUI\TestRunner;

if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Controller_FrontTest::main");

    $basePath = realpath(dirname(__FILE__) . str_repeat(DIRECTORY_SEPARATOR . '..', 3));

    set_include_path(
        $basePath . DIRECTORY_SEPARATOR . 'tests'
        . PATH_SEPARATOR . $basePath . DIRECTORY_SEPARATOR . 'library'
        . PATH_SEPARATOR . get_include_path()
    );
}


require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Request/Http.php';
require_once 'Zend/Controller/Response/Cli.php';
require_once 'Zend/Controller/Dispatcher/Standard.php';
require_once 'Zend/Controller/Router/Rewrite.php';
require_once 'Zend/Controller/Action/HelperBroker.php';
require_once 'Zend/Controller/Action/Helper/Url.php';
require_once 'Zend/Controller/Action/Helper/ViewRenderer.php';

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Front
 */
class Zend_Controller_FrontTest extends TestCase
{
    protected $_controller = null;

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        $suite = new TestSuite("Zend_Controller_FrontTest");
        $result = (new TestRunner())->run($suite);
    }

    protected function setUp(): void
    {
        $this->_controller = Zend_Controller_Front::getInstance();
        $this->_controller->resetInstance();
        $this->_controller->setControllerDirectory(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files')
                          ->setParam('noErrorHandler', true)
                          ->setParam('noViewRenderer', true)
                          ->returnResponse(true)
                          ->throwExceptions(false);
        Zend_Controller_Action_HelperBroker::resetHelpers();
    }

    protected function tearDown(): void
    {
        unset($this->_controller);
    }

    public function testResetInstance()
    {
        $this->_controller->setParam('foo', 'bar');
        $this->assertEquals('bar', $this->_controller->getParam('foo'));

        $this->_controller->resetInstance();
        $this->assertNull($this->_controller->getParam('bar'));
        $this->assertSame([], $this->_controller->getParams());
        $this->assertSame([], $this->_controller->getControllerDirectory());
    }

    /**
     * @see ZF-3145
     */
    public function testResetInstanceShouldResetHelperBroker()
    {
        Zend_Controller_Action_HelperBroker::addHelper(new Zend_Controller_Action_Helper_ViewRenderer());
        Zend_Controller_Action_HelperBroker::addHelper(new Zend_Controller_Action_Helper_Url());
        $helpers = Zend_Controller_Action_HelperBroker::getExistingHelpers();
        $this->assertTrue(is_array($helpers));
        $this->assertFalse(empty($helpers));

        $this->_controller->resetInstance();
        $helpers = Zend_Controller_Action_HelperBroker::getExistingHelpers();
        $this->assertTrue(is_array($helpers));
        $this->assertTrue(empty($helpers));
    }

    public function testSetGetRequest()
    {
        $request = new Zend_Controller_Request_Http();
        $this->_controller->setRequest($request);
        $this->assertTrue($request === $this->_controller->getRequest());

        $this->_controller->resetInstance();
        $this->_controller->setRequest('Zend_Controller_Request_Http');
        $request = $this->_controller->getRequest();
        $this->assertTrue($request instanceof Zend_Controller_Request_Http);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testSetRequestThrowsExceptionWithBadRequest()
    {
        try {
            $this->_controller->setRequest('Zend_Controller_Response_Cli');
            $this->fail('Should not be able to set invalid request class');
        } catch (Exception $e) {
            // success
        }
    }

    public function testSetGetResponse()
    {
        $response = new Zend_Controller_Response_Cli();
        $this->_controller->setResponse($response);
        $this->assertTrue($response === $this->_controller->getResponse());

        $this->_controller->resetInstance();
        $this->_controller->setResponse('Zend_Controller_Response_Cli');
        $response = $this->_controller->getResponse();
        $this->assertTrue($response instanceof Zend_Controller_Response_Cli);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testSetResponseThrowsExceptionWithBadResponse()
    {
        try {
            $this->_controller->setResponse('Zend_Controller_Request_Http');
            $this->fail('Should not be able to set invalid response class');
        } catch (Exception $e) {
            // success
        }
    }

    public function testSetGetRouter()
    {
        $router = new Zend_Controller_Router_Rewrite();
        $this->_controller->setRouter($router);
        $this->assertTrue($router === $this->_controller->getRouter());

        $this->_controller->resetInstance();
        $this->_controller->setRouter('Zend_Controller_Router_Rewrite');
        $router = $this->_controller->getRouter();
        $this->assertTrue($router instanceof Zend_Controller_Router_Rewrite);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testSetRouterThrowsExceptionWithBadRouter()
    {
        try {
            $this->_controller->setRouter('Zend_Controller_Request_Http');
            $this->fail('Should not be able to set invalid router class');
        } catch (Exception $e) {
            // success
        }
    }

    public function testSetGetDispatcher()
    {
        $dispatcher = new Zend_Controller_Dispatcher_Standard();
        $this->_controller->setDispatcher($dispatcher);

        $this->assertTrue($dispatcher === $this->_controller->getDispatcher());
    }

    public function testSetGetControllerDirectory()
    {
        $test = $this->_controller->getControllerDirectory();
        $expected = ['default' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files'];
        $this->assertSame($expected, $test);
    }

    public function testGetSetParam()
    {
        $this->_controller->setParam('foo', 'bar');
        $this->assertEquals('bar', $this->_controller->getParam('foo'));

        $this->_controller->setParam('bar', 'baz');
        $this->assertEquals('baz', $this->_controller->getParam('bar'));
    }

    public function testGetSetParams()
    {
        $this->_controller->setParams(['foo' => 'bar']);
        $params = $this->_controller->getParams();
        $this->assertTrue(isset($params['foo']));
        $this->assertEquals('bar', $params['foo']);

        $this->_controller->setParam('baz', 'bat');
        $params = $this->_controller->getParams();
        $this->assertTrue(isset($params['foo']));
        $this->assertEquals('bar', $params['foo']);
        $this->assertTrue(isset($params['baz']));
        $this->assertEquals('bat', $params['baz']);

        $this->_controller->setParams(['foo' => 'bug']);
        $params = $this->_controller->getParams();
        $this->assertTrue(isset($params['foo']));
        $this->assertEquals('bug', $params['foo']);
        $this->assertTrue(isset($params['baz']));
        $this->assertEquals('bat', $params['baz']);
    }

    public function testClearParams()
    {
        $this->_controller->setParams(['foo' => 'bar', 'baz' => 'bat']);
        $params = $this->_controller->getParams();
        $this->assertTrue(isset($params['foo']));
        $this->assertTrue(isset($params['baz']));

        $this->_controller->clearParams('foo');
        $params = $this->_controller->getParams();
        $this->assertFalse(isset($params['foo']));
        $this->assertTrue(isset($params['baz']));

        $this->_controller->clearParams();
        $this->assertSame([], $this->_controller->getParams());

        $this->_controller->setParams(['foo' => 'bar', 'bar' => 'baz', 'baz' => 'bat']);
        $this->assertSame(['foo' => 'bar', 'bar' => 'baz', 'baz' => 'bat'], $this->_controller->getParams());
        $this->_controller->clearParams(['foo', 'baz']);
        $this->assertSame(['bar' => 'baz'], $this->_controller->getParams());
    }

    public function testSetGetDefaultControllerName()
    {
        $this->assertEquals('index', $this->_controller->getDefaultControllerName());

        $this->_controller->setDefaultControllerName('foo');
        $this->assertEquals('foo', $this->_controller->getDefaultControllerName());
    }

    public function testSetGetDefaultAction()
    {
        $this->assertEquals('index', $this->_controller->getDefaultAction());

        $this->_controller->setDefaultAction('bar');
        $this->assertEquals('bar', $this->_controller->getDefaultAction());
    }

    /**
     * Test default action on valid controller
     */
    public function testDispatch()
    {
        $request = new Zend_Controller_Request_Http('http://example.com/index');
        $this->_controller->setResponse(new Zend_Controller_Response_Cli());
        $response = $this->_controller->dispatch($request);

        $this->assertStringContainsString('Index action called', $response->getBody());
    }

    /**
     * Test valid action on valid controller
     */
    public function testDispatch1()
    {
        $request = new Zend_Controller_Request_Http('http://example.com/index/index');
        $this->_controller->setResponse(new Zend_Controller_Response_Cli());
        $response = $this->_controller->dispatch($request);

        $this->assertStringContainsString('Index action called', $response->getBody());
    }

    /**
     * Test invalid action on valid controller
     */
    /*
    public function testDispatch2()
    {
        $request = new Zend_Controller_Request_Http('http://example.com/index/foo');

        try {
            $this->_controller->dispatch($request);
            $this->fail('Exception should be raised by __call');
        } catch (Exception $e) {
            // success
        }
    }
     */

    /**
     * Test invalid controller
     */
    /*
    public function testDispatch3()
    {
        $request = new Zend_Controller_Request_Http('http://example.com/baz');

        try {
            $this->_controller->dispatch($request);
            $this->fail('Exception should be raised; no such controller');
        } catch (Exception $e) {
            // success
        }
    }
     */

    /**
     * Test valid action on valid controller; test pre/postDispatch
     */
    public function testDispatch4()
    {
        $request = new Zend_Controller_Request_Http('http://example.com/foo/bar');
        $this->_controller->setResponse(new Zend_Controller_Response_Cli());
        $response = $this->_controller->dispatch($request);

        $body = $response->getBody();
        $this->assertStringContainsString('Bar action called', $body, $body);
        $this->assertStringContainsString('preDispatch called', $body, $body);
        $this->assertStringContainsString('postDispatch called', $body, $body);
    }

    /**
     * Test that extra arguments get passed
     */
    public function testDispatch5()
    {
        $request = new Zend_Controller_Request_Http('http://example.com/index/args');
        $this->_controller->setResponse(new Zend_Controller_Response_Cli());
        $this->_controller->setParam('foo', 'bar');
        $this->_controller->setParam('baz', 'bat');
        $response = $this->_controller->dispatch($request);

        $body = $response->getBody();
        $this->assertStringContainsString('foo: bar', $body, $body);
        $this->assertStringContainsString('baz: bat', $body);
    }

    /**
     * Test using router
     */
    public function testDispatch6()
    {
        $request = new Zend_Controller_Request_Http('http://framework.zend.com/foo/bar/var1/baz');
        $this->_controller->setResponse(new Zend_Controller_Response_Cli());
        $this->_controller->setRouter(new Zend_Controller_Router_Rewrite());
        $response = $this->_controller->dispatch($request);

        $body = $response->getBody();
        $this->assertStringContainsString('Bar action called', $body);
        $params = $request->getParams();
        $this->assertTrue(isset($params['var1']));
        $this->assertEquals('baz', $params['var1']);
    }

    /**
     * Test without router, using GET params
     */
    public function testDispatch7()
    {
        if ('cli' == strtolower(php_sapi_name())) {
            $this->markTestSkipped('Issues with $_GET in CLI interface prevents test from passing');
        }
        $request = new Zend_Controller_Request_Http('http://framework.zend.com/index.php?controller=foo&action=bar');

        $response = new Zend_Controller_Response_Cli();
        $response = $this->_controller->dispatch($request, $response);

        $body = $response->getBody();
        $this->assertStringContainsString('Bar action called', $body);
    }

    /**
     * Test that run() throws exception when called from object instance
     */
    public function _testRunThrowsException()
    {
        try {
            $this->_controller->run(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files');
            $this->fail('Should not be able to call run() from object instance');
        } catch (Exception $e) {
            // success
        }
    }

    /**
     * Test that set/getBaseUrl() functionality works
     */
    public function testSetGetBaseUrl()
    {
        $this->assertNull($this->_controller->getBaseUrl());
        $this->_controller->setBaseUrl('/index.php');
        $this->assertEquals('/index.php', $this->_controller->getBaseUrl());
    }

    public function testSetGetBaseUrlPopulatesRequest()
    {
        $request = new Zend_Controller_Request_Http();
        $this->_controller->setRequest($request);
        $this->_controller->setBaseUrl('/index.php');
        $this->assertEquals('/index.php', $request->getBaseUrl());

        $this->assertEquals($request->getBaseUrl(), $this->_controller->getBaseUrl());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testSetBaseUrlThrowsExceptionOnNonString()
    {
        try {
            $this->_controller->setBaseUrl([]);
            $this->fail('Should not be able to set non-string base URL');
        } catch (Exception $e) {
            // success
        }
    }

    /**
     * Test that a set base URL is pushed to the request during the dispatch
     * process
     */
    public function testBaseUrlPushedToRequest()
    {
        $this->_controller->setBaseUrl('/index.php');
        $request = new Zend_Controller_Request_Http('http://example.com/index');
        $response = new Zend_Controller_Response_Cli();
        $response = $this->_controller->dispatch($request, $response);

        $this->assertStringContainsString('index.php', $request->getBaseUrl());
    }

    /**
     * Test that throwExceptions() sets and returns value properly
     */
    public function testThrowExceptions()
    {
        $this->_controller->throwExceptions(true);
        $this->assertTrue($this->_controller->throwExceptions());
        $this->_controller->throwExceptions(false);
        $this->assertFalse($this->_controller->throwExceptions());
    }

    public function testThrowExceptionsFluentInterface()
    {
        $result = $this->_controller->throwExceptions(true);
        $this->assertSame($this->_controller, $result);
    }

    /**
     * Test that with throwExceptions() set, an exception is thrown
     * @doesNotPerformAssertions
     */
    public function testThrowExceptionsThrows()
    {
        $this->_controller->throwExceptions(true);
        $this->_controller->setControllerDirectory(dirname(__FILE__));
        $request = new Zend_Controller_Request_Http('http://framework.zend.com/bogus/baz');
        $this->_controller->setResponse(new Zend_Controller_Response_Cli());
        $this->_controller->setRouter(new Zend_Controller_Router_Rewrite());

        try {
            $response = $this->_controller->dispatch($request);
            $this->fail('Invalid controller should throw exception');
        } catch (Exception $e) {
            // success
        }
    }

    /**
     * Test that returnResponse() sets and returns value properly
     */
    public function testReturnResponse()
    {
        $this->_controller->returnResponse(true);
        $this->assertTrue($this->_controller->returnResponse());
        $this->_controller->returnResponse(false);
        $this->assertFalse($this->_controller->returnResponse());
    }

    public function testReturnResponseFluentInterface()
    {
        $result = $this->_controller->returnResponse(true);
        $this->assertSame($this->_controller, $result);
    }

    /**
     * Test that with returnResponse set to false, output is echoed and equals that in the response
     */
    public function testReturnResponseReturnsResponse()
    {
        $request = new Zend_Controller_Request_Http('http://framework.zend.com/foo/bar/var1/baz');
        $this->_controller->setResponse(new Zend_Controller_Response_Cli());
        $this->_controller->setRouter(new Zend_Controller_Router_Rewrite());
        $this->_controller->returnResponse(false);

        ob_start();
        $this->_controller->dispatch($request);
        $body = ob_get_clean();

        $actual = $this->_controller->getResponse()->getBody();
        $this->assertStringContainsString($actual, $body);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testRunStatically()
    {
        $request = new Zend_Controller_Request_Http('http://example.com/index/index');
        $this->_controller->setRequest($request);
        Zend_Controller_Front::run(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files');
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testRunDynamically()
    {
        $request = new Zend_Controller_Request_Http('http://example.com/index/index');
        $this->_controller->setRequest($request);
        $this->_controller->run(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files');
    }

    public function testModulePathDispatched()
    {
        $this->_controller->addControllerDirectory(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . '/Admin', 'admin');
        $request = new Zend_Controller_Request_Http('http://example.com/admin/foo/bar');
        $this->_controller->setResponse(new Zend_Controller_Response_Cli());
        $response = $this->_controller->dispatch($request);

        $body = $response->getBody();
        $this->assertStringContainsString('Admin_Foo::bar action called', $body, $body);
    }

    public function testModuleControllerDirectoryName()
    {
        $this->assertEquals('controllers', $this->_controller->getModuleControllerDirectoryName());
        $this->_controller->setModuleControllerDirectoryName('foobar');
        $this->assertEquals('foobar', $this->_controller->getModuleControllerDirectoryName());
    }

    public function testAddModuleDirectory()
    {
        $moduleDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'modules';
        $this->_controller->addModuleDirectory($moduleDir);
        $controllerDirs = $this->_controller->getControllerDirectory();
        $this->assertTrue(isset($controllerDirs['foo']));
        $this->assertTrue(isset($controllerDirs['bar']));
        $this->assertTrue(isset($controllerDirs['default']));
        $this->assertFalse(isset($controllerDirs['.svn']));

        $this->assertStringContainsString('modules' . DIRECTORY_SEPARATOR . 'foo', $controllerDirs['foo']);
        $this->assertStringContainsString('modules' . DIRECTORY_SEPARATOR . 'bar', $controllerDirs['bar']);
        $this->assertStringContainsString('modules' . DIRECTORY_SEPARATOR . 'default', $controllerDirs['default']);
    }

    /**#@+
     * @see ZF-2910
     */
    public function testShouldAllowRetrievingCurrentModuleDirectory()
    {
        $this->testAddModuleDirectory();
        $request = new Zend_Controller_Request_Http();
        $request->setModuleName('bar');
        $this->_controller->setRequest($request);
        $dir = $this->_controller->getModuleDirectory();
        $this->assertStringContainsString('modules' . DIRECTORY_SEPARATOR . 'bar', $dir);
        $this->assertStringNotContainsString('controllers', $dir);
    }

    public function testShouldAllowRetrievingSpecifiedModuleDirectory()
    {
        $this->testAddModuleDirectory();
        $dir = $this->_controller->getModuleDirectory('foo');
        $this->assertStringContainsString('modules' . DIRECTORY_SEPARATOR . 'foo', $dir);
        $this->assertStringNotContainsString('controllers', $dir);
    }

    public function testShouldReturnNullWhenRetrievingNonexistentModuleDirectory()
    {
        $this->testAddModuleDirectory();
        $this->assertNull($this->_controller->getModuleDirectory('bogus-foo-bar'));
    }
    /**#@-*/

    /**
     * ZF-2435
     */
    public function testCanRemoveIndividualModuleDirectory()
    {
        $moduleDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'modules';
        $this->_controller->addModuleDirectory($moduleDir);
        $controllerDirs = $this->_controller->getControllerDirectory();
        $this->_controller->removeControllerDirectory('foo');
        $test = $this->_controller->getControllerDirectory();
        $this->assertNotEquals($controllerDirs, $test);
        $this->assertFalse(array_key_exists('foo', $test));
    }

    public function testAddModuleDirectoryThrowsExceptionForInvalidDirectory()
    {
        $moduleDir = 'doesntexist';
        try {
            $this->_controller->addModuleDirectory($moduleDir);
            $this->fail('Exception expected but not thrown');
        } catch (Exception $e) {
            $this->assertTrue($e instanceof Zend_Exception);
            $this->assertMatchesRegularExpression(
                '/Directory \w+ not readable/',
                $e->getMessage()
            );
        }
    }

    public function testGetControllerDirectoryByModuleName()
    {
        $moduleDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'modules';
        $this->_controller->addModuleDirectory($moduleDir);
        $barDir = $this->_controller->getControllerDirectory('bar');
        $this->assertNotNull($barDir);
        $this->assertStringContainsString('modules' . DIRECTORY_SEPARATOR . 'bar', $barDir);
    }

    public function testGetControllerDirectoryByModuleNameReturnsNullOnBadModule()
    {
        $moduleDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'modules';
        $this->_controller->addModuleDirectory($moduleDir);
        $dir = $this->_controller->getControllerDirectory('_bazbat');
        $this->assertNull($dir);
    }

    public function testDefaultModule()
    {
        $dispatcher = $this->_controller->getDispatcher();
        $this->assertEquals($dispatcher->getDefaultModule(), $this->_controller->getDefaultModule());
        $this->_controller->setDefaultModule('foobar');
        $this->assertEquals('foobar', $this->_controller->getDefaultModule());
        $this->assertEquals($dispatcher->getDefaultModule(), $this->_controller->getDefaultModule());
    }

    public function testErrorHandlerPluginRegisteredWhenDispatched()
    {
        $this->assertFalse($this->_controller->hasPlugin('Zend_Controller_Plugin_ErrorHandler'));
        $request = new Zend_Controller_Request_Http('http://example.com/index/index');
        $this->_controller->setParam('noErrorHandler', false)
                          ->setResponse(new Zend_Controller_Response_Cli());
        $response = $this->_controller->dispatch($request);

        $this->assertTrue($this->_controller->hasPlugin('Zend_Controller_Plugin_ErrorHandler'));
    }

    public function testErrorHandlerPluginNotRegisteredIfNoErrorHandlerSet()
    {
        $this->assertFalse($this->_controller->hasPlugin('Zend_Controller_Plugin_ErrorHandler'));
        $request = new Zend_Controller_Request_Http('http://example.com/index/index');
        $this->_controller->setParam('noErrorHandler', true)
                          ->setResponse(new Zend_Controller_Response_Cli());
        $response = $this->_controller->dispatch($request);

        $this->assertFalse($this->_controller->hasPlugin('Zend_Controller_Plugin_ErrorHandler'));
    }

    public function testReplaceRequestAndResponseMidStream()
    {
        $request = new Zend_Controller_Request_Http('http://example.com/index/replace');
        $this->_controller->setResponse(new Zend_Controller_Response_Cli());
        $response = new Zend_Controller_Response_Http();
        $responsePost = $this->_controller->dispatch($request, $response);

        $requestPost = $this->_controller->getRequest();

        $this->assertNotSame($request, $requestPost);
        $this->assertNotSame($response, $responsePost);

        $this->assertStringContainsString('Reset action called', $responsePost->getBody());
        $this->assertStringNotContainsString('Reset action called', $response->getBody());
    }

    public function testViewRendererHelperRegisteredWhenDispatched()
    {
        $this->assertFalse(Zend_Controller_Action_HelperBroker::hasHelper('viewRenderer'));
        $this->_controller->setParam('noViewRenderer', false);

        $request = new Zend_Controller_Request_Http('http://example.com/index/index');
        $this->_controller->setResponse(new Zend_Controller_Response_Cli());
        $response = $this->_controller->dispatch($request);

        $this->assertTrue(Zend_Controller_Action_HelperBroker::hasHelper('viewRenderer'));
    }

    public function testViewRendererHelperNotRegisteredIfNoViewRendererSet()
    {
        $this->assertFalse(Zend_Controller_Action_HelperBroker::hasHelper('viewRenderer'));
        $this->_controller->setParam('noViewRenderer', true);

        $request = new Zend_Controller_Request_Http('http://example.com/index/index');
        $this->_controller->setResponse(new Zend_Controller_Response_Cli());
        $response = $this->_controller->dispatch($request);

        $this->assertFalse(Zend_Controller_Action_HelperBroker::hasHelper('viewRenderer'));
    }

    public function testDispatcherHandlesTypeError()
    {
        $request = new Zend_Controller_Request_Http('http://example.com/index/type-error');

        $this->_controller
            ->setParam('noErrorHandler', false)
            ->setRequest($request);
        $response = new Zend_Controller_Response_Cli();
        $this->_controller->throwExceptions(false);
        $this->_controller->dispatch($request, $response);

        $body = $this->_controller->getResponse()->getBody();
        $this->assertStringNotContainsString('Type error action called', $body);
        if (version_compare(PHP_VERSION, '8.0.0', '<')) {
            $this->assertEquals("EXCEPTION_OTHER\nReturn value of IndexController::produceTypeError() must be an instance of IndexController, instance of stdClass returned", $body);
        } else {
            $this->assertEquals("EXCEPTION_OTHER\nIndexController::produceTypeError(): Return value must be of type IndexController, stdClass returned", $body);
        }
        
        $this->assertSame(500, $this->_controller->getResponse()->getHttpResponseCode());
    }

    public function testDispatcherHandlesException()
    {
        $request = new Zend_Controller_Request_Http('http://example.com/index/exception');

        $this->_controller
            ->setParam('noErrorHandler', false)
            ->setRequest($request);
        $response = new Zend_Controller_Response_Cli();
        $this->_controller->throwExceptions(false);
        $this->_controller->dispatch($request, $response);

        $body = $this->_controller->getResponse()->getBody();
        $this->assertStringNotContainsString('Exception action called', $body);
        $this->assertStringContainsString('EXCEPTION_OTHER', $body);
        $this->assertStringContainsString('This is an exception message', $body);
        $this->assertSame(500, $this->_controller->getResponse()->getHttpResponseCode());
    }

    public function testDispatcherPassesTypeErrorThroughWhenThrowExceptions()
    {
        $request = new Zend_Controller_Request_Http('http://example.com/index/type-error');

        $this->_controller->setRequest($request);
        $response = new Zend_Controller_Response_Cli();
        $this->_controller->throwExceptions(true);

        try {
            $this->_controller->dispatch($request, $response);
            $this->fail('Should have thrown');
        } catch (Throwable $e) {
            if (version_compare(PHP_VERSION, '8.0.0', '<')) {
                $this->assertSame(
                    'Return value of IndexController::produceTypeError() must be an instance of IndexController, instance of stdClass returned',
                    $e->getMessage()
                );
            } else {
                $this->assertSame(
                    'IndexController::produceTypeError(): Return value must be of type IndexController, stdClass returned',
                    $e->getMessage()
                );
            }
        }
    }

    public function testDispatcherCatchesTypeExceptionFromPlugin()
    {
        $request = new Zend_Controller_Request_Http('http://example.com/index/index');
        require_once __DIR__ . '/_files/Plugins/ThrowingPlugin.php';
        $this->_controller
            ->setParam('noErrorHandler', false)
            ->setRequest($request)
            ->registerPlugin(new ThrowingPlugin());
        $response = new Zend_Controller_Response_Cli();
        $this->_controller->throwExceptions(false);
        $this->_controller->dispatch($request, $response);

        $body = $this->_controller->getResponse()->getBody();
        if (version_compare(PHP_VERSION, '8.0.0', '<')) {
            $this->assertEquals("EXCEPTION_OTHER\nReturn value of MyApp\Controller\Plugin\ThrowingPlugin::produceTypeError() must be an instance of MyApp\Controller\Plugin\ThrowingPlugin, instance of stdClass returned", $body);
        } else {
            $this->assertEquals("EXCEPTION_OTHER\nMyApp\Controller\Plugin\ThrowingPlugin::produceTypeError(): Return value must be of type MyApp\Controller\Plugin\ThrowingPlugin, stdClass returned", $body);
        }
        $this->assertSame(500, $this->_controller->getResponse()->getHttpResponseCode());
    }
}

// Call Zend_Controller_FrontTest::main() if this source file is executed directly.
// if (PHPUnit_MAIN_METHOD == "Zend_Controller_FrontTest::main") {
//     Zend_Controller_FrontTest::main();
// }
