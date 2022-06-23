<?php

namespace Drupal\omdb_api\Entity\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\omdb_api\Entity\OmdbApiEntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for reverting a OMDB API revision for a single trans.
 *
 * @ingroup omdb_api
 */
class OmdbApiEntityRevisionRevertTranslationForm extends OmdbApiEntityRevisionRevertForm {

  /**
   * The language to be reverted.
   *
   * @var string
   */
  protected $langcode;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {

    $instance = parent::create($container);
    $instance->languageManager = $container->get('language_manager');
    return $instance;

  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'omdb_api_entity_revision_revert_translation';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to revert @language translation to the revision from %revision-date?', [
      '@language' => $this->languageManager->getLanguageName($this->langcode),
      '%revision-date' => $this->dateFormatter->format($this->revision->getRevisionCreationTime()),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $omdb_api_revision = NULL, $langcode = NULL) {
    $this->langcode = $langcode;
    $form = parent::buildForm($form, $form_state, $omdb_api_revision);

    $form['revert_untranslated_fields'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Revert content shared among translations'),
      '#default_value' => FALSE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function prepareRevertedRevision(OmdbApiEntityInterface $revision, FormStateInterface $form_state) {

    $revert_untranslated_fields = $form_state->getValue('revert_untranslated_fields');

    /** @var \Drupal\omdb_api\Entity\Storage\OmdbApiEntityInterface $latest_revision */
    $latest_revision = $this->omdbApiEntityStorage->load($revision->id());
    $latest_revision_translation = $latest_revision->getTranslation($this->langcode);

    $revision_translation = $revision->getTranslation($this->langcode);

    foreach ($latest_revision_translation->getFieldDefinitions() as $field_name => $definition) {
      if ($definition->isTranslatable() || $revert_untranslated_fields) {
        $latest_revision_translation->set($field_name, $revision_translation->get($field_name)->getValue());
      }
    }

    $request_time = \Drupal::time()->getCurrentTime();
    $latest_revision_translation->setNewRevision();
    $latest_revision_translation->isDefaultRevision(TRUE);
    $revision->setRevisionCreationTime($request_time);

    return $latest_revision_translation;

  }

}
