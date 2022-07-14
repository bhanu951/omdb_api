<?php

namespace Drupal\omdb_api\Entity\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for the omdb api entity edit forms.
 */
class OmdbApiEntityForm extends ContentEntityForm implements ContainerInjectionInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The time system.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {

    $instance = parent::create($container);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->currentUser = $container->get('current_user');
    $instance->time = $container->get('datetime.time');
    return $instance;

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {

    /** @var \Drupal\omdb_api\Entity\OmdbApiEntityInterface $entity */
    $entity = $this->getEntity();
    // Save as a new revision if requested to do so.
    if (!$form_state->isValueEmpty('new_revision') && $form_state->getValue('new_revision') != FALSE) {

      $request_time = $this->time->getCurrentTime();
      $current_user = $this->currentUser->id();
      $entity->setNewRevision(TRUE);
      $entity->isDefaultRevision(TRUE);
      // If a new revision is created, save the current user as revision author.
      $entity->setRevisionCreationTime($request_time);
      $entity->setRevisionUserId($current_user);

    }
    else {
      $entity->setNewRevision(FALSE);
    }

    $result = parent::save($form, $form_state);

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
