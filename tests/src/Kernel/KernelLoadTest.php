<?php

namespace Drupal\Tests\omdb_api\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\user\RoleInterface;

/**
 * Test description.
 *
 * @group omdb_api
 */
class KernelLoadTest extends KernelTestBase {


  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'user',
    'system',
    'node',
    'block',
    'path_alias',
    'options',
    'taxonomy',
    'omdb_api',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig(['user', 'omdb_api']);
    $this->installSchema('system', ['sequences']);
    $this->installSchema('omdb_api', []);
    $this->installEntitySchema('user');
    // Create anonymous user.
    $anonymous = $this->container->get('entity_type.manager')
      ->getStorage('user')
      ->create([
        'uid' => 0,
        'status' => 0,
        'name' => '',
      ]);
    $anonymous->save();
    /** @var \Drupal\user\RoleInterface $anonymous_role */
    $anonymous_role = $this->container->get('entity_type.manager')
      ->getStorage('user_role')
      ->load(RoleInterface::ANONYMOUS_ID);
    $anonymous_role->grantPermission('access content');
    $anonymous_role->save();

  }

  /**
   * Tests that OMDB API Module can be Installed.
   */
  public function testModulesCanBeInstalled() {

    $module = $this->container->get('module_handler')->moduleExists('omdb_api');

    $this->assertTrue($module);
  }

}
