<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Controller
 *
 * @copyright   (C) 2013 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

JLoader::register('BaseController', __DIR__ . '/stubs/tbase.php');

/**
 * Tests for the JController class.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Controller
 * @since       3.0.0
 */
class JControllerBaseTest extends TestCase
{
	/**
	 * @var    JControllerBase
	 * @since  3.0.0
	 */
	private $_instance;

	/**
	 * Tests the __construct method.
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 */
	public function test__construct()
	{
		// New controller with no dependencies.
		$this->assertEquals('default', TestReflection::getValue($this->_instance, 'app')->input, 'Checks the mock application came from the factory.');
		$this->assertAttributeEquals('default', 'input', $this->_instance, 'Checks the input came from the application.');

		// New controller with dependencies
		$app = TestMockApplicationWeb::create($this);
		$app->test = 'ok';

		$class = new BaseController(new JInputCookie, $app);
		$this->assertAttributeInstanceOf('JInputCookie', 'input', $class, 'Checks the type of the injected input.');
		$this->assertAttributeSame($app, 'app', $class, 'Checks the injected application.');
	}

	/**
	 * Tests the getApplication method.
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 */
	public function testGetApplication()
	{
		TestReflection::setValue($this->_instance, 'app', 'application');
		$this->assertEquals('application', $this->_instance->getApplication());
	}

	/**
	 * Tests the getInput method.
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 */
	public function testGetInput()
	{
		TestReflection::setValue($this->_instance, 'input', 'input');
		$this->assertEquals('input', $this->_instance->getInput());
	}

	/**
	 * Tests the serialize method.
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 */
	public function testSerialise()
	{
		$this->assertEquals('s:7:"default";', $this->_instance->serialize());
	}

	/**
	 * Tests the unserialize method.
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 */
	public function testUnserialise()
	{
		$input = serialize(new JInput);

		$this->assertSame($this->_instance, $this->_instance->unserialize($input), 'Checks chaining and target method.');
		$this->assertInstanceOf('JInput', $this->_instance->getInput());
	}

	/**
	 * Tests the unserialize method for an expected exception.
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 * @expectedException  UnexpectedValueException
	 */
	public function testUnserialise_exception()
	{
		$this->_instance->unserialize('s:7:"default";');
	}

	/**
	 * Tests the loadApplication method.
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 */
	public function testLoadApplication()
	{
		JFactory::$application = 'application';
		$this->assertEquals('application', TestReflection::invoke($this->_instance, 'loadApplication'));
	}

	/**
	 * Tests the loadInput method.
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 */
	public function testLoadInput()
	{
		// Reset the input property so we know it changes based on the mock application.
		TestReflection::setValue($this->_instance, 'input', null);

		$this->assertEquals('default', TestReflection::invoke($this->_instance, 'loadInput'));
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

		$app = TestMockApplicationWeb::create($this);
		$app->input = 'default';

		JFactory::$application = $app;

		$this->_instance = new BaseController;
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
