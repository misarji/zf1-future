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
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once "Zend/Test/PHPUnit/Db/SimpleTester.php";
require_once "Zend/Test/PHPUnit/Db/Connection.php";
require_once "Zend/Test/DbAdapter.php";
require_once "Zend/Test/PHPUnit/Db/Exception.php";

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Test
 */
class Zend_Test_PHPUnit_Db_SimpleTesterTest extends TestCase
{
    public function testGetConnection()
    {
        $testAdapter = $this->createMock('Zend_Test_DbAdapter');
        $testAdapter->expects($this->any())
                    ->method('delete')
                    ->will($this->throwException(new Exception()));

        $connection = new Zend_Test_PHPUnit_Db_Connection($testAdapter, "schema");

        $databaseTester = new Zend_Test_PHPUnit_Db_SimpleTester($connection);

        $this->assertSame($connection, $databaseTester->getConnection());
    }

    public function testSetupDatabase()
    {
        $testAdapter = $this->createMock('Zend_Test_DbAdapter');
        $testAdapter->expects($this->any())
                    ->method('delete')
                    ->will($this->throwException(new Exception()));

        $connection = new Zend_Test_PHPUnit_Db_Connection($testAdapter, "schema");

        $databaseTester = new Zend_Test_PHPUnit_Db_SimpleTester($connection);

        $dataSet = $this->createMock('PHPUnit_Extensions_Database_DataSet_IDataSet');
        $dataSet->expects($this->any())
                ->method('getIterator')
                ->will($this->returnValue($this->createMock('Iterator')));
        $dataSet->expects($this->any())
                ->method('getReverseIterator')
                ->will($this->returnValue($this->createMock('Iterator')));
        $databaseTester->setUpDatabase($dataSet);
    }

    public function testInvalidConnectionGivenThrowsException()
    {
        $this->expectException("Zend_Test_PHPUnit_Db_Exception");

        $connection = $this->createMock('PHPUnit_Extensions_Database_DB_IDatabaseConnection');

        $databaseTester = new Zend_Test_PHPUnit_Db_SimpleTester($connection);
    }
}
