<?php

namespace Drupal\omdb_api\Entity\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Form controller for the omdb api entity delete forms.
 */
class OmdbApiEntityDeleteForm extends ContentEntityConfirmFormBase {

  /**
   * Returns the question to ask the user.
   *
   * @return string
   *   The form question. The page title will be set to this value.
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete %name?', ['%name' => $this->entity->label()]);
  }

  /**
   * Returns the route to go to if the user cancels the action.
   *
   * @return \Drupal\Core\Url
   *   A URL object.
   */
  public function getCancelUrl() {
    return new Url('entity.omdb_api.collection');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   *
   * Delete the entity and log the event.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $entity = $this->getEntity();
    $entity->delete();

    $this->logger('omdb_api')->notice($this->t('The omdb api %title has been deleted.', ['%title' => $this->entity->label()]));

    // Redirect to term list after delete.
    $form_state->setRedirect('entity.omdb_api.collection');

    $this->messenger()->addStatus($this->t('The omdb api %title has been deleted.', ['%title' => $this->entity->label()]));

  }

}
