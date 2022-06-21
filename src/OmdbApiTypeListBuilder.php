<?php

namespace Drupal\omdb_api;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of omdb api type entities.
 *
 * @see \Drupal\omdb_api\Entity\OmdbApiType
 */
class OmdbApiTypeListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['title'] = $this->t('Label');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['title'] = [
      'data' => $entity->label(),
      'class' => ['menu-label'],
    ];

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();

    $build['table']['#empty'] = $this->t(
      'No omdb api types available. <a href=":link">Add omdb api type</a>.',
      [':link' => Url::fromRoute('entity.omdb_api_type.add_form')->toString()]
    );

    return $build;
  }

}
