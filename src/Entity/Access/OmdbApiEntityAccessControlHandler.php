<?php

namespace Drupal\omdb_api\Entity\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the omdb api entity type.
 */
class OmdbApiEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {

    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view omdb api entities');

      case 'update':
        return AccessResult::allowedIfHasPermissions(
          $account,
          ['edit omdb api entities', 'administer omdb api entities'],
          'OR',
        );

      case 'delete':
        return AccessResult::allowedIfHasPermissions(
          $account,
          ['delete omdb api entities', 'administer omdb api entities'],
          'OR',
        );

      default:
        // No opinion.
        return AccessResult::neutral();
    }

  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermissions(
      $account,
      ['create omdb api entities', 'administer omdb api entities'],
      'OR',
    );
  }

}
