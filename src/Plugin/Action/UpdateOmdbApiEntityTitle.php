<?php

namespace Drupal\omdb_api\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a an Update OMDB API Entity Title action.
 *
 * @Action(
 *   id = "update_omdb_api_entity_title",
 *   label = @Translation("Update OMDB API Entity Title"),
 *   type = "omdb_api",
 *   category = @Translation("Custom")
 * )
 */
class UpdateOmdbApiEntityTitle extends ActionBase {

  /**
   * {@inheritdoc}
   */
  public function access($omdb_api_entity, AccountInterface $account = NULL, $return_as_object = FALSE) {
    /** @var \Drupal\omdb_api\Entity\OmdbApiEntityInterface $omdb_api_entity */
    $access = $omdb_api_entity->access('update', $account, TRUE)
      ->andIf($omdb_api_entity->imdb_title->access('edit', $account, TRUE));
    return $return_as_object ? $access : $access->isAllowed();
  }

  /**
   * {@inheritdoc}
   */
  public function execute($omdb_api_entity = NULL) {

    /** @var \Drupal\omdb_api\Entity\OmdbApiEntityInterface $omdb_api_entity */
    $imdb_title = $omdb_api_entity->getImdbTitle();
    $omdb_api_entity->setImdbTitle($this->t('Updated : @imdb_title', ['@imdb_title' => $imdb_title]));
    $omdb_api_entity->setRevisionCreationTime(time());
    $omdb_api_entity->setNewRevision(TRUE);
    $omdb_api_entity->save();

  }

}
