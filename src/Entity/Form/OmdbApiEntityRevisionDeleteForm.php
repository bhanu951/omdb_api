<?php

namespace Drupal\omdb_api\Entity\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a Omdb API Entity revision.
 *
 * @ingroup omdb_api
 */
class OmdbApiEntityRevisionDeleteForm extends ConfirmFormBase {

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
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {

    $instance = parent::create($container);
    $instance->omdbApiEntityStorage = $container->get('entity_type.manager')->getStorage('omdb_api');
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->connection = $container->get('database');
    return $instance;

  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'omdb_api_entity_revision_delete';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the revision from %revision-date?', [
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
    return $this->t('Delete');
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

    $this->omdbApiEntityStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('omdb_api')->notice($this->t('Omdb API Entity: deleted %title revision %revision.', [
      '%title' => $this->revision->label(),
      '%revision' => $this->revision->getRevisionId(),
    ]));
    $revision_date = $this->dateFormatter->format($this->revision->getRevisionCreationTime());
    $this->messenger()->addMessage($this->t('Revision from %revision-date of Omdb API Entity %title has been deleted.', [
      '%revision-date' => $revision_date,
      '%title' => $this->revision->label(),
    ]));
    $form_state->setRedirect(
      'entity.omdb_api.canonical',
      ['omdb_api' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT revision_id) FROM {omdb_api_field_revision} WHERE oid = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.omdb_api.version_history',
        ['omdb_api' => $this->revision->id()]
      );
    }

  }

}
