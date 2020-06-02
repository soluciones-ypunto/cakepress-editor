<?php
namespace CakepressEditor\Test\TestCase\View\Helper;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use CakepressEditor\View\Helper\EditorHelper;

/**
 * CakepressEditor\View\Helper\EditorHelper Test Case
 */
class EditorHelperTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \CakepressEditor\View\Helper\EditorHelper
     */
    public $Editor;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $view = new View();
        $this->Editor = new EditorHelper($view);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Editor);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
