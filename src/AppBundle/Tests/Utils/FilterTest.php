<?php

/**
 * Created by PhpStorm.
 * User: krasai
 * Date: 13.11.15
 * Time: 14:55
 */
namespace AppBundle\Tests\Utils;

use AppBundle\Utils\Filter;

class FilterTest extends \PHPUnit_Framework_TestCase
{

    public function testFilterData()
    {
        // given
        $connector = $this->getMock('Tickets\Connectors\AllegroConnector');
        $connector->expects($this->any())->method('getItems')->will($this->returnValue(['error'=> 1]));
        $instance = new AllegroBridge($connector);

        // when
        $result = $instance->getSportTickets();

        // then
        $this->assertEquals(['error'=> 1], $result);
    }
}
