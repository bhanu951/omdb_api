<?php

namespace Drupal\Tests\omdb_api\Functional;

use Drupal\Tests\BrowserTestBase;
use Drush\TestTraits\DrushTestTrait;

/**
 * Test class for the Devel Generate drush commands.
 *
 * @group omdb_api
 */
class OmdbApiEntityDevelGenerateCommandsTest extends BrowserTestBase {
  use DrushTestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'content_translation',
    'devel',
    'devel_generate',
    'language',
    'block',
    'views',
    'menu_ui',
    'node',
    'path',
    'taxonomy',
    'omdb_api',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'bartik';

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

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
   * Tests generating omdb api entities via drush.
   */
  public function testDrushGenerateOmdbApiEntities() {

    // Creates omdb api entities With out Kill Option.
    $this->drush('devel-generate:omdb-api', [], ['num' => 55, 'bundles' => 'movie,series']);
    $omdb_api_entities = \Drupal::entityQuery('omdb_api')->accessCheck(TRUE)->execute();
    $this->assertCount(55, $omdb_api_entities);
    $messages = $this->getErrorOutput();
    $this->assertStringContainsStringIgnoringCase('Finished 55 elements created successfully.', $messages, 'devel-generate-omdb-api batch ending message not found');

    // Creates omdb api entities With Kill Option.
    $this->drush('devel-generate:omdb-api', [], ['num' => 1, 'kill' => NULL]);
    $omdb_api_entities1 = \Drupal::entityQuery('omdb_api')->accessCheck(TRUE)->execute();
    $this->assertCount(1, $omdb_api_entities1);

  }

  /**
   * Tests generating omdb api entities via drush command alias.
   */
  public function testDrushCommandAliasGenerateOmdbApiEntities() {

    // Creates omdb api entities With out Kill Option.
    $this->drush('dgenom', [], ['num' => 120]);
    $omdb_api_entities2 = \Drupal::entityQuery('omdb_api')->accessCheck(TRUE)->execute();
    $this->assertCount(120, $omdb_api_entities2);
    $messages2 = $this->getErrorOutput();
    $this->assertStringContainsStringIgnoringCase('Finished 120 elements created successfully.', $messages2, 'dgenom batch ending message not found');

    // Creates omdb api entities With Kill Option.
    $this->drush('dgen:omdb-api', [], ['num' => 10, 'kill' => NULL]);
    $omdb_api_entities3 = \Drupal::entityQuery('omdb_api')->accessCheck(TRUE)->execute();
    $this->assertCount(10, $omdb_api_entities3);

  }

}
