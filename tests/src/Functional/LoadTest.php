<?php

namespace Drupal\Tests\omdb_api\Functional;

use Drupal\Tests\BrowserTestBase;
use DrupalFinder\DrupalFinder;
use Symfony\Component\Filesystem\Filesystem;

/**
 * OMDB API Module Load Test.
 *
 * @group omdb_api
 */
class LoadTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stable';
  /**
   * {@inheritdoc}
   */
  protected static $modules = ['node', 'block', 'omdb_api'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {

    parent::setUp();

    $fs = new Filesystem();
    $drupalFinder = new DrupalFinder();
    $drupalFinder->locateRoot(getcwd());
    $drupalRoot = $_ENV['DRUPAL_ROOT'] ?? $drupalFinder->getDrupalRoot();

    // Create the public directory with chmod 0777.
    if (!$fs->exists($drupalRoot . '/sites/default/files/public/omdb-api/qrcodes')) {
      $oldmask = umask(0);
      $fs->mkdir($drupalRoot . '/sites/default/files/public/omdb-api/qrcodes', 0777, TRUE);
      umask($oldmask);
    }
    else {
      $fs->chmod($drupalRoot . '/sites/default/files/public/omdb-api/qrcodes', 0777);
    }

    $this->drupalPlaceBlock('page_title_block');
  }

  /**
   * Tests Homepage after enabling OMDB API Module.
   */
  public function testHomepage() {
    // Test homepage.
    $this->drupalGet('<front>');
    $this->assertSession()->statusCodeEquals(200);

    // Minimal homepage title.
    $this->assertSession()->pageTextContains('Log in');
  }

  /**
   * Tests the OMDB API Entity Structure Page.
   */
  public function testOmdbApiEntityStructurePage() {

    $omdb_api_entity_content_types = $this->drupalCreateUser([
      'administer omdb api types',
    ]);
    $this->drupalLogin($omdb_api_entity_content_types);
    // Visit the OMDB API Type Page.
    $this->drupalGet('/admin/structure/omdb_api_types');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('OMDB API Type');
    $this->assertSession()->pageTextNotContains('No omdb api types available.');

  }

  /**
   * Tests the OMDB API module unistall.
   */
  public function testModuleUninstall() {

    $admin_user = $this->drupalCreateUser([
      'access administration pages',
      'administer site configuration',
      'administer modules',
    ]);

    // Uninstall the module.
    $this->drupalLogin($admin_user);
    $this->drupalGet('/admin/modules/uninstall');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('OMDB API');
    $this->submitForm(['uninstall[omdb_api]' => TRUE], 'Uninstall');
    $this->submitForm([], 'Uninstall');
    $this->assertSession()->pageTextContains('The selected modules have been uninstalled.');
    $this->assertSession()->pageTextNotContains('OMDB API');

    // Visit the frontpage.
    $this->drupalGet('');
    $this->assertSession()->statusCodeEquals(200);

  }

  /**
   * Tests the OMDB API module reinstalling after being uninstalled.
   */
  // Public function testReinstallAfterUninstall() {
  // $admin_user = $this->drupalCreateUser([
  //     'access administration pages',
  //     'administer site configuration',
  //     'administer modules',
  //   ]);
  // $drupalFinder = new DrupalFinder();
  //   $drupalFinder->locateRoot(getcwd());
  //   $drupalRoot = $_ENV['DRUPAL_ROOT'] ?? $drupalFinder->getDrupalRoot();
  // // Uninstall the module.
  //   $this->drupalLogin($admin_user);
  //   $this->assertDirectoryExists($drupalRoot . '/sites/default/files/public/omdb-api/qrcodes');
  //   $assert_session = $this->assertSession();
  //   $page = $this->getSession()->getPage();
  // // Uninstall the OMDB API module.
  //   $this->container->get('module_installer')->uninstall(['omdb_api'], FALSE);
  // $this->drupalGet('/admin/modules');
  //   $page->checkField('modules[omdb_api][enable]');
  //   $page->pressButton('Install');
  //   $assert_session->pageTextNotContains('Unable to install OMDB API');
  //   $assert_session->pageTextContains('Module OMDB API has been enabled');
  // }.
}
