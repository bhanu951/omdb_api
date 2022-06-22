<?php

namespace Drupal\omdb_api\Entity\Form;

use Drupal\Core\Entity\Form\DeleteMultipleForm as EntityDeleteMultipleForm;
use Drupal\Core\Url;

/**
 * Form controller for the omdb api entity delete forms.
 */
class OmdbApiEntityMultipleDeleteForm extends EntityDeleteMultipleForm {

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.omdb_api.collection');
  }

  /**
   * {@inheritdoc}
   */
  protected function getDeletedMessage($count) {
    return $this->formatPlural(
      $count,
      'Deleted @count OMDB API Entity item.',
      'Deleted @count OMDB API Entity items.'
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getInaccessibleMessage($count) {
    return $this->formatPlural(
      $count,
      "@count OMDB API Entity item has not been deleted because you do not have the necessary permissions.",
      "@count OMDB API Entity items have not been deleted because you do not have the necessary permissions."
    );
  }

}
