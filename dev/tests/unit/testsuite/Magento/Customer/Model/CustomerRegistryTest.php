<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Customer\Model;

/**
 * Test for CustomerRegistry
 *
 * @package Magento\Customer\Model
 */
class CustomerRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var \Magento\Customer\Model\CustomerFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerFactory;

    /**
     * @var \Magento\Customer\Model\Customer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customer;

    /**#@+
     * Sample customer data
     */
    const CUSTOMER_ID = 1;
    const CUSTOMER_EMAIL = 'customer@example.com';
    const WEBSITE_ID = 1;

    public function setUp()
    {
        $this->customerFactory = $this->getMockBuilder('\Magento\Customer\Model\CustomerFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->customerRegistry = new CustomerRegistry($this->customerFactory);
        $this->customer = $this->getMockBuilder('\Magento\Customer\Model\Customer')
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'load',
                    'getId',
                    'getEmail',
                    'getWebsiteId',
                    '__wakeup',
                    'setEmail',
                    'setWebsiteId',
                    'loadByEmail'
                ]
            )
            ->getMock();
    }

    public function testRetrieve()
    {
        $this->customer->expects($this->once())
            ->method('load')
            ->with(self::CUSTOMER_ID)
            ->will($this->returnValue($this->customer));
        $this->customer->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::CUSTOMER_ID));
        $this->customerFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->customer));
        $actual = $this->customerRegistry->retrieve(self::CUSTOMER_ID);
        $this->assertEquals($this->customer, $actual);
        $actualCached = $this->customerRegistry->retrieve(self::CUSTOMER_ID);
        $this->assertEquals($this->customer, $actualCached);
    }

    public function testRetrieveByEmail()
    {
        $this->customer->expects($this->once())
            ->method('loadByEmail')
            ->with(self::CUSTOMER_EMAIL)
            ->will($this->returnValue($this->customer));
        $this->customer->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::CUSTOMER_ID));
        $this->customer->expects($this->any())
            ->method('getEmail')
            ->will($this->returnValue(self::CUSTOMER_EMAIL));
        $this->customer->expects($this->any())
            ->method('getWebsiteId')
            ->will($this->returnValue(self::WEBSITE_ID));
        $this->customer->expects($this->any())
            ->method('setEmail')
            ->will($this->returnValue($this->customer));
        $this->customer->expects($this->any())
            ->method('setWebsiteId')
            ->will($this->returnValue($this->customer));
        $this->customerFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->customer));
        $actual = $this->customerRegistry->retrieveByEmail(self::CUSTOMER_EMAIL, self::WEBSITE_ID);
        $this->assertEquals($this->customer, $actual);
        $actualCached = $this->customerRegistry->retrieveByEmail(self::CUSTOMER_EMAIL, self::WEBSITE_ID);
        $this->assertEquals($this->customer, $actualCached);
    }

    /**
     * @expectedException \Magento\Exception\NoSuchEntityException
     */
    public function testRetrieveException()
    {
        $this->customer->expects($this->once())
            ->method('load')
            ->with(self::CUSTOMER_ID)
            ->will($this->returnValue($this->customer));
        $this->customer->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(null));
        $this->customerFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->customer));
        $this->customerRegistry->retrieve(self::CUSTOMER_ID);
    }

    /**
     * @expectedException \Magento\Exception\NoSuchEntityException
     */
    public function testRetrieveByEmailException()
    {
        $this->customer->expects($this->once())
            ->method('loadByEmail')
            ->with(self::CUSTOMER_EMAIL)
            ->will($this->returnValue($this->customer));
        $this->customer->expects($this->any())
            ->method('getEmail')
            ->will($this->returnValue(null));
        $this->customer->expects($this->any())
            ->method('getWebsiteId')
            ->will($this->returnValue(null));
        $this->customer->expects($this->any())
            ->method('setEmail')
            ->will($this->returnValue($this->customer));
        $this->customer->expects($this->any())
            ->method('setWebsiteId')
            ->will($this->returnValue($this->customer));
        $this->customerFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->customer));
        $this->customerRegistry->retrieveByEmail(self::CUSTOMER_EMAIL, self::WEBSITE_ID);
    }

    public function testRemove()
    {
        $this->customer->expects($this->exactly(2))
            ->method('load')
            ->with(self::CUSTOMER_ID)
            ->will($this->returnValue($this->customer));
        $this->customer->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::CUSTOMER_ID));
        $this->customerFactory->expects($this->exactly(2))
            ->method('create')
            ->will($this->returnValue($this->customer));
        $actual = $this->customerRegistry->retrieve(self::CUSTOMER_ID);
        $this->assertEquals($this->customer, $actual);
        $this->customerRegistry->remove(self::CUSTOMER_ID);
        $actual = $this->customerRegistry->retrieve(self::CUSTOMER_ID);
        $this->assertEquals($this->customer, $actual);
    }

    public function testRemoveByEmail()
    {
        $this->customer->expects($this->exactly(2))
            ->method('loadByEmail')
            ->with(self::CUSTOMER_EMAIL)
            ->will($this->returnValue($this->customer));
        $this->customer->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::CUSTOMER_ID));
        $this->customer->expects($this->any())
            ->method('getEmail')
            ->will($this->returnValue(self::CUSTOMER_EMAIL));
        $this->customer->expects($this->any())
            ->method('getWebsiteId')
            ->will($this->returnValue(self::WEBSITE_ID));
        $this->customer->expects($this->any())
            ->method('setEmail')
            ->will($this->returnValue($this->customer));
        $this->customer->expects($this->any())
            ->method('setWebsiteId')
            ->will($this->returnValue($this->customer));
        $this->customerFactory->expects($this->exactly(2))
            ->method('create')
            ->will($this->returnValue($this->customer));
        $actual = $this->customerRegistry->retrieveByEmail(self::CUSTOMER_EMAIL, self::WEBSITE_ID);
        $this->assertEquals($this->customer, $actual);
        $this->customerRegistry->removeByEmail(self::CUSTOMER_EMAIL, self::WEBSITE_ID);
        $actual = $this->customerRegistry->retrieveByEmail(self::CUSTOMER_EMAIL, self::WEBSITE_ID);
        $this->assertEquals($this->customer, $actual);
    }
}