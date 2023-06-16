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
 * @package    Zend_Service_StrikeIron
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Service_StrikeIron
 */
require_once 'Zend/Service/StrikeIron.php';

/**
 * @see Zend_Service_StrikeIron_ZipCodeInfo
 */
require_once 'Zend/Service/StrikeIron/ZipCodeInfo.php';


/**
 * @category   Zend
 * @package    Zend_Service_StrikeIron
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_StrikeIron
 */
class Zend_Service_StrikeIron_ZipCodeInfoTest extends TestCase
{
    /**
     * @var \stdclass|mixed
     */
    protected $soapClient;

    /**
     * @var \Zend_Service_StrikeIron_ZipCodeInfo|mixed
     */
    protected $service;

    protected function setUp(): void
    {
        $this->soapClient = new stdclass();
        $this->service = new Zend_Service_StrikeIron_ZipCodeInfo(['client' => $this->soapClient]);
    }

    public function testInheritsFromBase()
    {
        $this->assertTrue($this->service instanceof Zend_Service_StrikeIron_Base);
    }

    public function testHasCorrectWsdl()
    {
        $wsdl = 'http://sdpws.strikeiron.com/zf1.StrikeIron/sdpZIPCodeInfo?WSDL';
        $this->assertEquals($wsdl, $this->service->getWsdl());
    }

    public function testInstantiationFromFactory()
    {
        $strikeIron = new Zend_Service_StrikeIron(['client' => $this->soapClient]);
        $client = $strikeIron->getService(['class' => 'ZipCodeInfo']);

        $this->assertTrue($client instanceof Zend_Service_StrikeIron_ZipCodeInfo);
    }
}