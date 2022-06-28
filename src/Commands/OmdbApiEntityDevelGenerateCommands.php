<?php

namespace Drupal\omdb_api\Commands;

use Drupal\devel_generate\Commands\DevelGenerateCommands;

/**
 * Class to create omdb api entity drush commands.
 */
class OmdbApiEntityDevelGenerateCommands extends DevelGenerateCommands {

  /**
   * Create omdb api entity items by drush command.
   *
   * @param int $num
   *   Number of omdb api entity items to generate.
   * @param array $options
   *   Array of options as described below.
   *
   * @command devel-generate:omdb-api
   * @aliases dgenom,dgen:omdb-api, devel-generate-omdb-api
   * @pluginId omdb_api_entity_devel_generate
   * @validate-module-enabled omdb_api
   *
   * @option kill Delete all omdb api entity items before generating new omdb api entity.
   * @option feedback An integer representing interval for insertion rate logging.
   * @option bundles A comma-delimited list of content types to create.
   * @option skip-fields A comma delimited list of fields to omit when generating random values.
   * @option languages A comma-separated list of language codes
   */
  public function omdbApiEntities(
    $num = 50,
    array $options = [
      'num' => 50,
      'kill' => FALSE,
      'bundles' => 'movie,series',
      'name_length' => 5,
      'time_range' => 604800,
      'feedback' => 100,

    ]
  ) {
    // Run the generate command.
    $this->generate();
  }

}
