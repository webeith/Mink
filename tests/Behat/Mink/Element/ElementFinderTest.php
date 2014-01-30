<?php

namespace Test\Behat\Mink\Element;

use Behat\Mink\Element\ElementFinder;

class ElementFinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $driver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $selectorsHandler;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $manipulator;

    /**
     * @var ElementFinder
     */
    private $finder;

    protected function setUp()
    {
        $this->driver = $this->getMock('Behat\Mink\Driver\DriverInterface');
        $this->selectorsHandler = $this->getMockBuilder('Behat\Mink\Selector\SelectorsHandler')
            ->disableOriginalConstructor()
            ->getMock();
        $this->manipulator = $this->getMockBuilder('Behat\Mink\Selector\Xpath\Manipulator')->getMock();

        $this->finder = new ElementFinder($this->driver, $this->selectorsHandler, $this->manipulator);
    }

    public function testNotFound()
    {
        $this->selectorsHandler->expects($this->once())
            ->method('selectorToXpath')
            ->with('css', 'h3 > a')
            ->will($this->returnValue('css_xpath'));

        $this->manipulator->expects($this->once())
            ->method('prepend')
            ->with('css_xpath', 'parent_xpath')
            ->will($this->returnValue('full_xpath'));

        $this->driver->expects($this->once())
            ->method('find')
            ->with('full_xpath')
            ->will($this->returnValue(array()));

        $this->assertEquals(array(), $this->finder->findAll('css', 'h3 > a', 'parent_xpath'));
    }

    public function testFound()
    {
        $this->selectorsHandler->expects($this->once())
            ->method('selectorToXpath')
            ->with('css', 'h3 > a')
            ->will($this->returnValue('css_xpath'));

        $this->manipulator->expects($this->once())
            ->method('prepend')
            ->with('css_xpath', 'parent_xpath')
            ->will($this->returnValue('full_xpath'));

        $this->driver->expects($this->once())
            ->method('find')
            ->with('full_xpath')
            ->will($this->returnValue(array('element1', 'element2')));

        $results = $this->finder->findAll('css', 'h3 > a', 'parent_xpath');

        $this->assertCount(2, $results);
        $this->assertContainsOnly('Behat\Mink\Element\NodeElement', $results);
        $this->assertEquals('element1', $results[0]->getXpath());
        $this->assertEquals('element2', $results[1]->getXpath());
    }
}