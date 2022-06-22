<?php

namespace Drupal\omdb_api\Entity\Storage;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\omdb_api\Entity\OmdbApiEntityInterface;

/**
 * Defines the storage handler class for OMDB API entities.
 *
 * This extends the base storage class, adding required special handling for
 * OMDB API entities.
 *
 * @ingroup omdb_api
 */
class OmdbApiEntityStorage extends SqlContentEntityStorage implements OmdbApiEntityStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(OmdbApiEntityInterface $entity) {

    // Entity Revision Data Table name : omdb_api_revision.
    return $this->database->query(
      'SELECT [revision_id] FROM {' . $this->getRevisionTable() . '} WHERE [oid] = :id ORDER BY [revision_id]',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {

    // Field Revision Data Table name : omdb_api_field_revision.
    return $this->database->query(
      'SELECT [revision_id] FROM {' . $this->getRevisionDataTable() . '} WHERE [uid] = :uid ORDER BY [revision_id]',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(OmdbApiEntityInterface $entity) {

    // Field Revision Data Table name : omdb_api_field_revision.
    return $this->database->query('SELECT COUNT(*) FROM {' . $this->getRevisionDataTable() . '} WHERE [oid] = :id AND [default_langcode] = 1', [':id' => $entity->id()])->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {

    return $this->database->update($this->getRevisionTable())
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }
}
