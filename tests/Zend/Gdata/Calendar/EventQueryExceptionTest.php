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
 * @package    Zend_Gdata_Calendar
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

require_once 'Zend/Gdata/Calendar.php';
require_once 'Zend/Gdata/Calendar/EventQuery.php';
require_once 'Zend/Http/Client.php';

/**
 * @category   Zend
 * @package    Zend_Gdata_Calendar
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Gdata
 * @group      Zend_Gdata_Calendar
 */
class Zend_Gdata_Calendar_EventQueryExceptionTest extends TestCase
{
    /**
     * @var \Zend_Gdata_Calendar_EventQuery|mixed
     */
    protected $query;

    public const GOOGLE_DEVELOPER_CALENDAR = 'developer-calendar@google.com';

    protected function setUp(): void
    {
        $this->query = new Zend_Gdata_Calendar_EventQuery();
    }

    public function testSingleEventsThrowsExceptionOnSetInvalidValue()
    {
        $this->expectException(Zend_Gdata_App_Exception::class);
        $this->query->resetParameters();
        $singleEvents = 'puppy';
        $this->query->setUser(self::GOOGLE_DEVELOPER_CALENDAR);
        $this->query->setSingleEvents($singleEvents);
    }

    public function testFutureEventsThrowsExceptionOnSetInvalidValue()
    {
        $this->expectException(Zend_Gdata_App_Exception::class);
        $this->query->resetParameters();
        $futureEvents = 'puppy';
        $this->query->setUser(self::GOOGLE_DEVELOPER_CALENDAR);
        $this->query->setFutureEvents($futureEvents);
    }
}
