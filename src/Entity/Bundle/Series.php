<?php

namespace Drupal\omdb_api\Entity\Bundle;

use Drupal\omdb_api\Entity\OmdbApiEntity;
use Drupal\omdb_api\Entity\OmdbApiEntityInterface;

/**
 * A base bundle class for omdb_api entities.
 *
 * @see https://www.drupal.org/node/3191609 for CR.
 */
abstract class Series extends OmdbApiEntity implements OmdbApiEntityInterface {

}
