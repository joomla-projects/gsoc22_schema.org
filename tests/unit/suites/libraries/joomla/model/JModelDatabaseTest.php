<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Model
 *
 * @copyright   (C) 2013 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

JLoader::register('DatabaseModel', __DIR__ . '/stubs/tdatabase.php');

/**
 * Tests for the JViewBase class.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Mapper
 * @since       3.0.0
 */
class JModelDatabaseTest extends TestCase
{
	/**
	 * @var    DatabaseModel
	 * @since  3.0.0
	 */
	private $_instance;

	/**
	 * Tests the __construct method.
	 *
	 * @return  void
	 *
	 * @since   3.4
	 */
	public function testChecksDefaultDatabaseDriver()
	{
		$this->assertSame(JFactory::getDbo(), $this->_instance->getDb());
	}

	/**
	 * Tests the __construct method.
	 *
	 * @return  void
	 *
	 * @since   34
	 */
	public function testChecksInjectedDatabaseDriver()
	{
		// Create a new database mock for injection.
		$db = $this->getMockDatabase();
		$class = new DatabaseModel(null, $db);
		$this->assertSame($db, $class->getDb());
	}

	/**
	 * Tests the getDb method.
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 */
	public function testGetDb()
	{
		// Reset the db property to a known value.
		TestReflection::setValue($this->_instance, 'db', 'foo');

		$this->assertEquals('foo', $this->_instance->getDb());
	}

	/**
	 * Tests the setDb method.
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 */
	public function testSetDb()
	{
		$db = TestMockDatabaseDriver::create($this);
		$this->_instance->setDb($db);

		$this->assertAttributeSame($db, 'db', $this->_instance);
	}

	/**
	 * Tests the loadDb method.
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 */
	public function testLoadDb()
	{
		JFactory::$database = 'database';
		$this->assertEquals('database', TestReflection::invoke($this->_instance, 'loadDb'));
	}

	/**
	 * Setup the tests.
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->saveFactoryState();

		JFactory::$database = $this->getMockDatabase();

		$this->_instance = new DatabaseModel;
	}

	/**
	 * Method to tear down whatever was set up before the test.
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 */
	protected function tearDown()
	{
		$this->restoreFactoryState();
		unset($this->_instance);
		parent::tearDown();
	}
}
