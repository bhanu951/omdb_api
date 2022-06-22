<?php

namespace Drupal\omdb_api\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining an omdb api entity type.
 */
interface OmdbApiEntityInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
