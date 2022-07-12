<?php

namespace Drupal\Tests\omdb_api\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * OMDB API Entity Module Browser Test Base Class.
 *
 * @group omdb_api
 */
abstract class OmdbApiEntityBrowserTestBase extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';
  /**
   * {@inheritdoc}
   */
  protected static $modules = ['node', 'block', 'omdb_api'];

}
