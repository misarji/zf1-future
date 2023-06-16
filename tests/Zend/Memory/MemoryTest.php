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
 * @package    Zend_Memory
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Memory_MemoryTest::main');
}

/** Zend_Memory */
require_once 'Zend/Memory.php';

/**
 * @category   Zend
 * @package    Zend_Memory
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Memory
 */
class Zend_Memory_MemoryTest extends TestCase
{
    /**
     * @var string
     */
    protected $cacheDir;

    public static function main()
    {
        $suite = new TestSuite(__CLASS__);
        $result = (new TestRunner())->run($suite);
    }

    protected function setUp(): void
    {
        $tmpDir = sys_get_temp_dir() . '/zend_memory';
        $this->_removeCacheDir($tmpDir);
        mkdir($tmpDir);
        $this->cacheDir = $tmpDir;
    }

    protected function _removeCacheDir($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir) || is_link($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            $this->_removeCacheDir($dir . '/' . $item);
        }

        return rmdir($dir);
    }

    /**
     * tests the Memory Manager creation
     *
     */
    public function testCreation()
    {
        /** 'None' backend */
        $memoryManager = Zend_Memory::factory('None');
        $this->assertTrue($memoryManager instanceof Zend_Memory_Manager);
        unset($memoryManager);

        /** 'File' backend */
        $backendOptions = ['cache_dir' => $this->cacheDir]; // Directory where to put the cache files
        $memoryManager = Zend_Memory::factory('File', $backendOptions);
        $this->assertTrue($memoryManager instanceof Zend_Memory_Manager);
        unset($memoryManager);
    }

    /**
     * @group ZF-9883
     * @dataProvider Zend_Memory_MemoryTest::providerCacheBackend
     */
    public function testFactoryCacheBackendStandards($backend)
    {
        try {
            $memoryManager = Zend_Memory::factory($backend);
        } catch (Zend_Cache_Exception $exception) {
            $this->markTestSkipped($exception->getMessage());
        }
        $this->assertTrue($memoryManager instanceof Zend_Memory_Manager);
    }

    /**
     * @group ZF-9883
     */
    public function providerCacheBackend()
    {
        return [
            'Zend_Cache_Backend_Apc' => ['Apc'],
            'Zend_Cache_Backend_File' => ['File'],
            'Zend_Cache_Backend_Libmemcached' => ['Libmemcached'],
            'Zend_Cache_Backend_Memcached' => ['Memcached'],
            'Zend_Cache_Backend_Sqlite' => ['Sqlite'],
            'Zend_Cache_Backend_TwoLevels' => ['TwoLevels'],
            'Zend_Cache_Backend_TwoLevels' => ['Xcache'],
            'Zend_Cache_Backend_ZendPlatform' => ['ZendPlatform'],
            'Zend_Cache_Backend_ZendServer_Disk' => ['ZendServer_Disk'],
            'Zend_Cache_Backend_ZendServer_ShMem    ' => ['ZendServer_ShMem']
        ];
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Memory_MemoryTest::main') {
    Zend_Memory_MemoryTest::main();
}
