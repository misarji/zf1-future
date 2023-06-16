<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestSuite;
use PHPUnit\TextUI\TestRunner;

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
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_View_Helper_PartialTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_PartialTest::main");
}

/** Zend_View_Helper_Partial */
require_once 'Zend/View/Helper/Partial.php';

/** Zend_View */
require_once 'Zend/View.php';

/** Zend_Controller_Front */
require_once 'Zend/Controller/Front.php';

/**
 * Test class for Zend_View_Helper_Partial.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class Zend_View_Helper_PartialTest extends TestCase
{
    /**
     * @var Zend_View_Helper_Partial
     */
    public $helper;

    /**
     * @var string
     */
    public $basePath;

    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite = new TestSuite("Zend_View_Helper_PartialTest");
        $result = (new TestRunner())->run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->basePath = dirname(__FILE__) . '/_files/modules';
        $this->helper = new Zend_View_Helper_Partial();
        Zend_Controller_Front::getInstance()->resetInstance();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->helper);
    }

    /**
     * @return void
     */
    public function testPartialRendersScript()
    {
        $view = new Zend_View([
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ]);
        $this->helper->setView($view);
        $return = $this->helper->partial('partialOne.phtml');
        $this->assertStringContainsString('This is the first test partial', $return);
    }

    /**
     * @return void
     */
    public function testPartialRendersScriptWithVars()
    {
        $view = new Zend_View([
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ]);
        $view->message = 'This should never be read';
        $this->helper->setView($view);
        $return = $this->helper->partial('partialThree.phtml', ['message' => 'This message should be read']);
        $this->assertStringNotContainsString($view->message, $return);
        $this->assertStringContainsString('This message should be read', $return, $return);
    }

    /**
     * @return void
     */
    public function testPartialRendersScriptInDifferentModuleWhenRequested()
    {
        Zend_Controller_Front::getInstance()->addModuleDirectory($this->basePath);
        $view = new Zend_View([
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ]);
        $this->helper->setView($view);
        $return = $this->helper->partial('partialTwo.phtml', 'foo');
        $this->assertStringContainsString('This is the second partial', $return, $return);
    }

    /**
     * @return void
     * @doesNotPerformAssertions
     */
    public function testPartialThrowsExceptionWithInvalidModule()
    {
        Zend_Controller_Front::getInstance()->addModuleDirectory($this->basePath);
        $view = new Zend_View([
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ]);
        $this->helper->setView($view);

        try {
            $return = $this->helper->partial('partialTwo.phtml', 'barbazbat');
            $this->fail('Partial should throw exception if module does not exist');
        } catch (Exception $e) {
        }
    }

    /**
     * @return void
     */
    public function testSetViewSetsViewProperty()
    {
        $view = new Zend_View();
        $this->helper->setView($view);
        $this->assertSame($view, $this->helper->view);
    }

    /**
     * @return void
     */
    public function testCloneViewReturnsDifferentViewInstance()
    {
        $view = new Zend_View();
        $this->helper->setView($view);
        $clone = $this->helper->cloneView();
        $this->assertNotSame($view, $clone);
        $this->assertTrue($clone instanceof Zend_View);
    }

    /**
     * @return void
     */
    public function testCloneViewClearsViewVariables()
    {
        $view = new Zend_View();
        $view->foo = 'bar';
        $this->helper->setView($view);

        $clone = $this->helper->cloneView();
        $clonedVars = $clone->getVars();

        $this->assertTrue(empty($clonedVars));
        $this->assertNull($clone->foo);
    }

    public function testObjectModelWithPublicPropertiesSetsViewVariables()
    {
        $model = new stdClass();
        $model->foo = 'bar';
        $model->bar = 'baz';

        $view = new Zend_View([
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ]);
        $this->helper->setView($view);
        $return = $this->helper->partial('partialVars.phtml', $model);

        foreach (get_object_vars($model) as $key => $value) {
            $string = sprintf('%s: %s', $key, $value);
            $this->assertStringContainsString($string, $return);
        }
    }

    public function testObjectModelWithToArraySetsViewVariables()
    {
        $model = new Zend_View_Helper_PartialTest_Aggregate();

        $view = new Zend_View([
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ]);
        $this->helper->setView($view);
        $return = $this->helper->partial('partialVars.phtml', $model);

        foreach ($model->toArray() as $key => $value) {
            $string = sprintf('%s: %s', $key, $value);
            $this->assertStringContainsString($string, $return);
        }
    }

    public function testObjectModelSetInObjectKeyWhenKeyPresent()
    {
        $this->helper->setObjectKey('foo');
        $model = new stdClass();
        $model->footest = 'bar';
        $model->bartest = 'baz';

        $view = new Zend_View([
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ]);
        $this->helper->setView($view);
        $return = $this->helper->partial('partialObj.phtml', $model);

        $this->assertStringNotContainsString('No object model passed', $return);

        foreach (get_object_vars($model) as $key => $value) {
            $string = sprintf('%s: %s', $key, $value);
            $this->assertStringContainsString($string, $return, "Checking for '$return' containing '$string'");
        }
    }

    public function testPassingNoArgsReturnsHelperInstance()
    {
        $test = $this->helper->partial();
        $this->assertSame($this->helper, $test);
    }

    public function testObjectKeyIsNullByDefault()
    {
        $this->assertNull($this->helper->getObjectKey());
    }

    public function testCanSetObjectKey()
    {
        $this->testObjectKeyIsNullByDefault();
        $this->helper->setObjectKey('foo');
        $this->assertEquals('foo', $this->helper->getObjectKey());
    }

    public function testCanSetObjectKeyToNullValue()
    {
        $this->testCanSetObjectKey();
        $this->helper->setObjectKey(null);
        $this->assertNull($this->helper->getObjectKey());
    }

    public function testSetObjectKeyImplementsFluentInterface()
    {
        $test = $this->helper->setObjectKey('foo');
        $this->assertSame($this->helper, $test);
    }

    public function testWhenSecondParamsIsViewModelThenPartialUseGetVarsToSetsViewVariables()
    {
        $view = new Zend_View([
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ]);
        $this->helper->setView($view);
        $view->foo = 'partial script can access first';
        $view->bar = 'partial script can access second';
        $return = $this->helper->partial('partialVars.phtml', $view);
        $this->assertStringContainsString(sprintf('%s: %s', 'foo', 'partial script can access first'), $return);
        $this->assertStringContainsString(sprintf('%s: %s', 'bar', 'partial script can access second'), $return);
    }
}

class Zend_View_Helper_PartialTest_Aggregate
{
    public $vars = [
        'foo' => 'bar',
        'bar' => 'baz'
    ];

    public function toArray()
    {
        return $this->vars;
    }
}

// Call Zend_View_Helper_PartialTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_PartialTest::main") {
    Zend_View_Helper_PartialTest::main();
}
