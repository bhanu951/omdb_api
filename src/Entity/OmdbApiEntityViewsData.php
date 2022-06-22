<?php

declare(strict_types=1);

namespace Drupal\omdb_api\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for OMDB API Entities.
 */
class OmdbApiEntityViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {

    $data = parent::getViewsData();

    // Additional information for Views integration,
    // Such as table joins, can be put here.
    $data['omdb_api']['omdb_api_bulk_form'] = [
      'title' => $this->t('OMDB API Entity Bulk Operations Form'),
      'help' => $this->t('Add a form element that lets you run operations on multiple OMDB API  Entity items.'),
      'field' => [
        'id' => 'omdb_api_bulk_form',
      ],
    ];

    return $data;

  }

}
