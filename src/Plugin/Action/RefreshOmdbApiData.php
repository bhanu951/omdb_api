<?php

namespace Drupal\omdb_api\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a Refresh OMDB API Data Action.
 *
 * @Action(
 *   id = "refresh_omdb_api_data",
 *   label = @Translation("Refresh OMDB API Data"),
 *   type = "omdb_api",
 *   category = @Translation("Custom")
 * )
 */
class RefreshOmdbApiData extends ActionBase {

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
    $omdb_api_entity->setRefreshData(TRUE);
    $omdb_api_entity->setNewRevision(TRUE);
    $omdb_api_entity->setRevisionCreationTime(time());
    $omdb_api_entity->save();

  }

}
