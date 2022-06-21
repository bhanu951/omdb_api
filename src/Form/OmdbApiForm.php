<?php

namespace Drupal\omdb_api\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the omdb api entity edit forms.
 */
class OmdbApiForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $result = parent::save($form, $form_state);

    $entity = $this->getEntity();

    $message_arguments = ['%label' => $entity->toLink()->toString()];
    $logger_arguments = [
      '%label' => $entity->label(),
      'link' => $entity->toLink($this->t('View'))->toString(),
    ];

    switch ($result) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('New omdb api %label has been created.', $message_arguments));
        $this->logger('omdb_api')->notice('Created new omdb api %label', $logger_arguments);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The omdb api %label has been updated.', $message_arguments));
        $this->logger('omdb_api')->notice('Updated omdb api %label.', $logger_arguments);
        break;
    }

    $form_state->setRedirect('entity.omdb_api.canonical', ['omdb_api' => $entity->id()]);

    return $result;
  }

}
