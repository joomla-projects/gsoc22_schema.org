<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Filesystem
 *
 * @copyright   (C) 2013 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Test class for JFilesystemHelper.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Event
 * @since       1.7.0
 */
class JFilesystemHelperTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * @var JFilesystemHelper
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->object = new JFilesystemHelper;
	}


	/**
	 * Overrides the parent tearDown method.
	 *
	 * @return  void
	 *
	 * @see     \PHPUnit\Framework\TestCase::tearDown()
	 * @since   3.6
	 */
	protected function tearDown()
	{
		unset($this->object);
		parent::tearDown();
	}

	/**
	 * Test...
	 *
	 * @covers  JFilesystemHelper::getJStreams
	 *
	 * @return void
	 */
	public function testGetJStreams()
	{
		$streams = JFilesystemHelper::getJStreams();

		$this->assertEquals(
			array('string'),
			$streams
		);
	}

	/**
	 * Test...
	 *
	 * @covers  JFilesystemHelper::isJoomlaStream
	 *
	 * @return void
	 */
	public function testIsJoomlaStream()
	{
		$this->assertTrue(
			JFilesystemHelper::isJoomlaStream('string')
		);

		$this->assertFalse(
			JFilesystemHelper::isJoomlaStream('unknown')
		);
	}
}
