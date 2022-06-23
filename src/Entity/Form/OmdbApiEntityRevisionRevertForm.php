<?php

namespace Drupal\omdb_api\Entity\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\omdb_api\Entity\OmdbApiEntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for reverting a Omdb API Entity revision.
 *
 * @ingroup omdb_api
 */
class OmdbApiEntityRevisionRevertForm extends ConfirmFormBase {

  /**
   * The Omdb API Entity revision.
   *
   * @var \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   */
  protected $revision;

  /**
   * The Omdb API Entity storage.
   *
   * @var \Drupal\Core\Entity\Storage\EntityStorageInterface
   */
  protected $omdbApiEntityStorage;

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->omdbApiEntityStorage = $container->get('entity_type.manager')->getStorage('omdb_api');
    $instance->dateFormatter = $container->get('date.formatter');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'omdb_api_revision_revert_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to revert to the revision from %revision-date?', [
      '%revision-date' => $this->dateFormatter->format($this->revision->getRevisionCreationTime()),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.omdb_api.version_history', ['omdb_api' => $this->revision->id()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Revert');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $omdb_api_revision = NULL) {
    $this->revision = $this->omdbApiEntityStorage->loadRevision($omdb_api_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // The revision timestamp will be updated when the revision is saved. Keep
    // the original one for the confirmation message.
    $original_revision_timestamp = $this->revision->getRevisionCreationTime();

    $this->revision = $this->prepareRevertedRevision($this->revision, $form_state);
    $this->revision->revision_log = $this->t('Copy of the revision from %date.', [
      '%date' => $this->dateFormatter->format($original_revision_timestamp),
    ]);
    $this->revision->save();

    $this->logger('content')->notice($this->t('OmdbApi: reverted %title revision %revision.', [
      '%title' => $this->revision->label(),
      '%revision' => $this->revision->getRevisionId(),
    ]));
    $this->messenger()->addMessage($this->t('OmdbApi %title has been reverted to the revision from %revision-date.', [
      '%title' => $this->revision->label(),
      '%revision-date' => $this->dateFormatter->format($original_revision_timestamp),
    ]));
    $form_state->setRedirect(
      'entity.omdb_api.version_history',
      ['omdb_api' => $this->revision->id()]
    );

  }

  /**
   * Prepares a revision to be reverted.
   *
   * @param \Drupal\omdb_api\Entity\OmdbApiEntityInterface $revision
   *   The revision to be reverted.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return \Drupal\omdb_api\Entity\OmdbApiEntityInterface
   *   The prepared revision ready to be stored.
   */
  protected function prepareRevertedRevision(OmdbApiEntityInterface $revision, FormStateInterface $form_state) {

    $request_time = \Drupal::time()->getCurrentTime();
    $revision->setNewRevision();
    $revision->isDefaultRevision(TRUE);
    $revision->setRevisionCreationTime($request_time);

    return $revision;
  }

}
