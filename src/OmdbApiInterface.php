<?php

namespace Drupal\omdb_api;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining an omdb api entity type.
 */
interface OmdbApiInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
