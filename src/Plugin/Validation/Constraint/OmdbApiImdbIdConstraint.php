<?php

namespace Drupal\omdb_api\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Provides an Omdb Api Imdb Id constraint.
 *
 * @Constraint(
 *   id = "OmdbApiImdbIdConstraint",
 *   label = @Translation("OmdbApiImdbId", context = "Validation"),
 * )
 */
class OmdbApiImdbIdConstraint extends Constraint {

  /**
   * The Error Message for Invalid Imdb Id.
   *
   * @var string
   */
  public $invalidImdbIdErrorMessage = 'The entered Imdb Id is invalid. It should be either 7 or 8 digit integer or in the format tt0123456';

}
