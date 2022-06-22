<?php

namespace Drupal\omdb_api\Plugin\Validation\Constraint;

use Drupal\Core\Validation\Plugin\Validation\Constraint\UniqueFieldConstraint;

/**
 * Provides an Omdb Api Imdb Id Unique constraint.
 *
 * @Constraint(
 *   id = "OmdbApiImdbIdUniqueConstraint",
 *   label = @Translation("Omdb Api ImdbId Unique Constraint", context = "Validation"),
 * )
 */
class OmdbApiImdbIdUniqueConstraint extends UniqueFieldConstraint {

  /**
   * The Error Message for duplicate Imdb Id.
   *
   * @var string
   */
  public $message = 'The entered IMDB Id %value already exists.';

}
