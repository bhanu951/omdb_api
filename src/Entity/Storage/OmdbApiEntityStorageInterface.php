<?php

namespace Drupal\omdb_api\Entity\Storage;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface OmdbApiEntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of OMDB API revision IDs for a specific OMDB API.
   *
   * @param \Drupal\omdb_api\Entity\OmdbApiEntityInterface $entity
   *   The OMDB API entity.
   *
   * @return int[]
   *   OMDB API revision IDs (in ascending order).
   */
  public function revisionIds(OmdbApiEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as OMDB API author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   OMDB API revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\omdb_api\Entity\OmdbApiEntityInterface $entity
   *   The OMDB API entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(OmdbApiEntityInterface $entity);

  /**
   * Unsets the language for all OMDB API with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
