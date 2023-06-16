<?php

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
 * @package    Zend_Cloud_DocumentService
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Cloud_DocumentService_Adapter_AllTests::main');
}

/**
 * @see Zend_Cloud_DocumentService_Adapter_SimpleDbTest
 */
require_once 'Zend/Cloud/DocumentService/Adapter/SimpleDbTest.php';

/**
 * @see Zend_Cloud_DocumentService_Adapter_WindowsAzureTest
 */
require_once 'Zend/Cloud/DocumentService/Adapter/WindowsAzureTest.php';

/**
 * @category   Zend
 * @package    Zend_Cloud_DocumentService_Adapter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Cloud_DocumentService_Adapter_AllTests
{
    /**
     * Runs this test suite
     *
     * @return void
     */
    public static function main()
    {
        (new TestRunner())->run(self::suite());
    }

    /**
     * Creates and returns this test suite
     *
     * @return TestSuite
     */
    public static function suite()
    {
        $suite = new TestSuite('Zend Framework - Zend_Cloud');

        $suite->addTestSuite('Zend_Cloud_DocumentService_Adapter_SimpleDbTest');
        $suite->addTestSuite('Zend_Cloud_DocumentService_Adapter_WindowsAzureTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Cloud_DocumentService_Adapter_AllTests::main') {
    Zend_Cloud_DocumentService_Adapter_AllTests::main();
}