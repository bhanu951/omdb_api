<?php

namespace Drupal\omdb_api\Entity\Logger;

use Drupal\Core\Entity\EntityInterface;

/**
 * Provides and interface for OMDB API Entity Operations.
 */
interface OmdbApiEntityLoggerInterface {

  /**
   * Log the performed operation on an entity.
   *
   * @param string $operation
   *   The operation which is performed on $entity_id.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The manipulated entity.
   */
  public function entityOperationLog(string $operation, EntityInterface $entity);

}
