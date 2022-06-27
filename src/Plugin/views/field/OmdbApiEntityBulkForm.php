<?php

namespace Drupal\omdb_api\Plugin\views\field;

use Drupal\views\Plugin\views\field\BulkForm;

/**
 * Defines a omdb_api bulk operations form element.
 *
 * @ViewsField("omdb_api_bulk_form")
 */
class OmdbApiEntityBulkForm extends BulkForm {

  /**
   * {@inheritdoc}
   */
  protected function emptySelectedMessage() {
    return $this->t('No Omdb Api Entity items selected.');
  }

}
