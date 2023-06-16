<?php

use PHPUnit\Extensions\Database\DataSet\FlatXmlDataSet;
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

require_once "Zend/Test/DbAdapter.php";

require_once "Zend/Test/PHPUnit/Db/Operation/DeleteAll.php";

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Test
 */
class Zend_Test_PHPUnit_Db_Operation_DeleteAllTest extends TestCase
{
    private $operation = null;

    protected function setUp(): void
    {
        $this->operation = new Zend_Test_PHPUnit_Db_Operation_DeleteAll();
    }

    public function testDeleteAll()
    {
        $dataSet = new FlatXmlDataSet(dirname(__FILE__) . "/_files/truncateFixture.xml");

        $testAdapter = $this->createMock('Zend_Test_DbAdapter');
        $testAdapter->expects($this->at(0))
                    ->method('delete')
                    ->with('foo');
        $testAdapter->expects($this->at(1))
                    ->method('delete')
                    ->with('bar');

        $connection = new Zend_Test_PHPUnit_Db_Connection($testAdapter, "schema");

        $this->operation->execute($connection, $dataSet);
    }

    public function testDeleteQueryErrorTransformsException()
    {
        $this->expectException('PHPUnit_Extensions_Database_Operation_Exception');

        $dataSet = new FlatXmlDataSet(dirname(__FILE__) . "/_files/truncateFixture.xml");

        $testAdapter = $this->createMock('Zend_Test_DbAdapter');
        $testAdapter->expects($this->any())
                    ->method('delete')
                    ->will($this->throwException(new Exception()));

        $connection = new Zend_Test_PHPUnit_Db_Connection($testAdapter, "schema");

        $this->operation->execute($connection, $dataSet);
    }

    public function testInvalidConnectionGivenThrowsException()
    {
        $this->expectException("Zend_Test_PHPUnit_Db_Exception");

        $dataSet = $this->createMock('PHPUnit_Extensions_Database_DataSet_IDataSet');
        $connection = $this->createMock('PHPUnit_Extensions_Database_DB_IDatabaseConnection');

        $this->operation->execute($connection, $dataSet);
    }
}
