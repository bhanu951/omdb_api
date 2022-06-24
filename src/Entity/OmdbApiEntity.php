<?php

declare(strict_types=1);

namespace Drupal\omdb_api\Entity;

use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\user\EntityOwnerTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Defines the omdb api entity class.
 *
 * @ContentEntityType(
 *   id = "omdb_api",
 *   label = @Translation("OMDB API"),
 *   label_collection = @Translation("OMDB APIs"),
 *   label_singular = @Translation("omdb api"),
 *   label_plural = @Translation("omdb apis"),
 *   label_count = @PluralTranslation(
 *     singular = "@count omdb apis",
 *     plural = "@count omdb apis",
 *   ),
 *   bundle_label = @Translation("OMDB API type"),
 *   handlers = {
 *     "storage" = "Drupal\omdb_api\Entity\Storage\OmdbApiEntityStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\omdb_api\Entity\OmdbApiEntityListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "translation" = "Drupal\omdb_api\Entity\OmdbApiEntityTranslationHandler",
 *     "access" = "Drupal\omdb_api\Entity\Access\OmdbApiEntityAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\omdb_api\Entity\Form\OmdbApiEntityForm",
 *       "edit" = "Drupal\omdb_api\Entity\Form\OmdbApiEntityForm",
 *       "delete" = "Drupal\omdb_api\Entity\Form\OmdbApiEntityDeleteForm",
 *       "delete-multiple-confirm" = "Drupal\omdb_api\Entity\Form\OmdbApiEntityMultipleDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\omdb_api\Entity\Routing\OmdbApiEntityHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "omdb_api",
 *   data_table = "omdb_api_field_data",
 *   revision_table = "omdb_api_revision",
 *   revision_data_table = "omdb_api_field_revision",
 *   show_revision_ui = TRUE,
 *   translatable = TRUE,
 *   admin_permission = "administer omdb api types",
 *   entity_keys = {
 *     "id" = "oid",
 *     "revision" = "revision_id",
 *     "langcode" = "langcode",
 *     "bundle" = "bundle",
 *     "label" = "imdb_title",
 *     "uuid" = "uuid",
 *     "owner" = "uid",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_uid",
 *     "revision_created" = "revision_timestamp",
 *     "revision_log_message" = "revision_log",
 *   },
 *   links = {
 *     "collection" = "/admin/content/omdb-api/list",
 *     "add-form" = "/omdb-api/add/{omdb_api_type}",
 *     "add-page" = "/omdb-api/add",
 *     "canonical" = "/omdb-api/{omdb_api}",
 *     "edit-form" = "/omdb-api/{omdb_api}/edit",
 *     "delete-form" = "/omdb-api/{omdb_api}/delete",
 *     "delete-multiple-form" = "/content/omdb-api/delete",
 *     "version-history" = "/content/omdb-api/{omdb_api}/revisions",
 *     "revision" = "/content/omdb-api/{omdb_api}/revisions/{omdb_api_revision}/view",
 *     "revision_revert" = "/content/omdb-api/{omdb_api}/revisions/{omdb_api_revision}/revert",
 *     "revision_delete" = "/content/omdb-api/{omdb_api}/revisions/{omdb_api_revision}/delete",
 *     "revision_revert_translation" = "/content/omdb-api/{omdb_api}/revisions/{omdb_api_revision}/revert/{langcode}",
 *   },
 *   bundle_entity_type = "omdb_api_type",
 *   field_ui_base_route = "entity.omdb_api_type.edit_form",
 * )
 */
class OmdbApiEntity extends RevisionableContentEntityBase implements OmdbApiEntityInterface {

  use EntityChangedTrait;
  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'uid' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);

    if ($rel === 'revision_revert' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel === 'revision_delete' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }

    return $uri_route_parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly,
    // make the omdb api owner the revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function isRefreshData() {
    return $this->get('refresh_data')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setRefreshData($refreshed = NULL): OmdbApiEntityInterface {
    $this->set('refresh_data', $refreshed);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getImdbTitle(): string {
    return $this->get('imdb_title')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setImdbTitle($imdb_title): OmdbApiEntityInterface {
    $this->set('imdb_title', $imdb_title);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getImdbId(): string {
    return $this->get('imdb_id')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setImdbId($imdb_id): OmdbApiEntityInterface {
    $this->set('imdb_id', $imdb_id);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime(): string {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp): OmdbApiEntityInterface {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId(): int {
    return (int) $this->get('uid')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid): OmdbApiEntityInterface {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account): OmdbApiEntityInterface {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished(): bool {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published = NULL): OmdbApiEntityInterface {

    if ($published == 'True') {
      $published = TRUE;
    }
    else {
      $published = FALSE;
    }

    $this->set('status', $published);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setUnpublished(): OmdbApiEntityInterface {
    $this->set('status', FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getViewerRating(): string {
    return $this->get('viewer_rating')->value ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function setViewerRating($viewer_rating): OmdbApiEntityInterface {
    $this->set('viewer_rating', $viewer_rating);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getReleasedYear(): string {
    return $this->get('released_year')->value ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function setReleasedYear($released_year): OmdbApiEntityInterface {
    $this->set('released_year', $released_year);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRuntime(): string {
    return $this->get('runtime')->value ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function setRuntime($released_year): OmdbApiEntityInterface {
    $this->set('runtime', $released_year);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getGenre(): string {
    return $this->get('genre')->value ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function setGenre($genre): OmdbApiEntityInterface {
    $this->set('genre', $genre);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getWriter(): string {
    return $this->get('writer')->value ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function setDirector($director): OmdbApiEntityInterface {
    $this->set('director', $director);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDirector(): string {
    return $this->get('director')->value ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function setWriter($writer): OmdbApiEntityInterface {
    $this->set('writer', $writer);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getActors(): string {
    return $this->get('actors')->value ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function setActors($actors): OmdbApiEntityInterface {
    $this->set('actors', $actors);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPlot(): string {
    return $this->get('plot')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setPlot($plot): OmdbApiEntityInterface {
    $this->set('plot', $plot);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getLanguage(): string {
    return $this->get('language')->value ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function setLanguage($language): OmdbApiEntityInterface {
    $this->set('language', $language);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCountry(): string {
    return $this->get('country')->value ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function setCountry($language): OmdbApiEntityInterface {
    $this->set('country', $language);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getAwards(): string {
    return $this->get('awards')->value ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function setAwards($awards): OmdbApiEntityInterface {

    if (is_array($awards)) {
      $awards = json_encode($awards);
    }
    $this->set('awards', $awards);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPoster(): string {
    return $this->get('poster')->value ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function setPoster($poster): OmdbApiEntityInterface {
    $this->set('poster', $poster);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRatings(): string {
    return $this->get('ratings')->value ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function setRatings($ratings): OmdbApiEntityInterface {

    if (is_array($ratings)) {
      $ratings = json_encode($ratings);
    }
    $this->set('ratings', $ratings);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getMetascore(): string {
    return $this->get('metascore')->value ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function setMetascore($metascore): OmdbApiEntityInterface {
    $this->set('metascore', $metascore);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getimdbRating(): string {
    return $this->get('imdb_rating')->value ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function setimdbRating($imdb_rating): OmdbApiEntityInterface {
    $this->set('imdb_rating', $imdb_rating);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getimdbVotes(): string {
    return $this->get('imdb_votes')->value ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function setimdbVotes($imdb_votes): OmdbApiEntityInterface {
    $this->set('imdb_votes', $imdb_votes);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDvdReleasedYear(): string {

    $dvd_released_year = $this->get('dvd_released_year')->value;
    if (($dvd_released_year != 'N/A') && isset($dvd_released_year)) {
      $formatted_date = \Drupal::service('date.formatter')->format(
        $dvd_released_year,
        'custom',
        'd M Y'
      );
      return $formatted_date;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function setDvdReleasedYear($dvd_released_year): OmdbApiEntityInterface {

    if ($dvd_released_year != 'N/A') {
      $new_datetime = DrupalDateTime::createFromFormat("d M Y", $dvd_released_year);
      $timestamp = $new_datetime->format('U');
      $this->set('dvd_released_year', $timestamp);
      return $this;
    }
    else {
      $this->set('dvd_released_year', NULL);
      return $this;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getBoxOfficeCollections(): string {
    return $this->get('box_office_collections')->value ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function setBoxOfficeCollections($box_office_collections): OmdbApiEntityInterface {
    $this->set('box_office_collections', $box_office_collections);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getProductionHouse(): string {
    return $this->get('box_office_collections')->value ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function setProductionHouse($production): OmdbApiEntityInterface {
    $this->set('production', $production);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getWebsite(): string {
    return $this->get('website')->value ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function setWebsite($website): OmdbApiEntityInterface {
    $this->set('website', $website);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getApiResponseStatus(): string {
    return $this->get('api_response')->value ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function setApiResponseStatus($api_response): OmdbApiEntityInterface {
    $this->set('api_response', $api_response);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getReleasedDate(): string {

    $released_date = $this->get('released_date')->value;
    if (($released_date != 'N/A') && isset($released_date)) {
      $formatted_date = \Drupal::service('date.formatter')->format(
        $released_date,
        'custom',
        'd M Y'
      );
      return $formatted_date;
    }

    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function setReleasedDate($released_date): OmdbApiEntityInterface {

    if ($released_date != 'N/A') {
      $new_datetime = DrupalDateTime::createFromFormat("d M Y", $released_date);
      $timestamp = $new_datetime->format('U');
      $this->set('released_date', $timestamp);
      return $this;
    }
    else {
      $this->set('released_date', NULL);
      return $this;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getRevisionCreationTime(): string {
    return (string) $this->get('revision_timestamp')->value ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function setRevisionCreationTime($revision_timestamp): OmdbApiEntityInterface {

    if (!$revision_timestamp) {
      $revision_timestamp = time();
    }

    $this->set('revision_timestamp', $revision_timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    // The OMDB Id is an integer, using the IntegerItem field item class.
    $fields['oid'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('omdb ID'))
      ->setDescription(t('The OMDB ID.'))
      ->setReadOnly(TRUE);

    // The UUID field uses the uuid_field type which ensures that
    // a new UUID will automatically be generated when an entity is created.
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The OMDB UUID.'))
      ->setReadOnly(TRUE);

    $fields['imdb_title'] = BaseFieldDefinition::create('string')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(new TranslatableMarkup('IMDB Title'))
      ->setDescription(new TranslatableMarkup('The IMDB Title of the Movie or Series.'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['imdb_id'] = BaseFieldDefinition::create('string')
      ->setRequired(TRUE)
      ->setConstraints([
        'OmdbApiImdbIdConstraint' => [],
        'OmdbApiImdbIdUniqueConstraint' => [],
      ])
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(new TranslatableMarkup('IMDB Id'))
      ->setDescription(new TranslatableMarkup('The IMDB Id of the Movie or Series.'))
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['refresh_data'] = BaseFieldDefinition::create('boolean')
      ->setLabel(new TranslatableMarkup('Refresh Data'))
      ->setDescription(new TranslatableMarkup('Select to get Entity Data Refreshed from API.'))
      ->setDefaultValue(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -2,
      ]);

    $fields['released_year'] = BaseFieldDefinition::create('string')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(new TranslatableMarkup('Released Year'))
      ->setDescription(new TranslatableMarkup('The Year that the Movie or Series was Released.'))
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 11,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 11,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['viewer_rating'] = BaseFieldDefinition::create('string')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(new TranslatableMarkup('Viewer Rating'))
      ->setDescription(new TranslatableMarkup('The ratings of the Movie or Series.'))
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 12,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 12,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['released_date'] = BaseFieldDefinition::create('timestamp')
      ->setRevisionable(TRUE)
      ->setLabel(new TranslatableMarkup('Released Date'))
      ->setDescription(new TranslatableMarkup('The Date that the Movie or Series was Released.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'datetime',
        'weight' => 13,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime',
        'weight' => 13,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['runtime'] = BaseFieldDefinition::create('string')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(new TranslatableMarkup('Runtime'))
      ->setDescription(new TranslatableMarkup('The Runtime of the Movie or Series.'))
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 14,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 14,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['genre'] = BaseFieldDefinition::create('string')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(new TranslatableMarkup('Genre'))
      ->setDescription(new TranslatableMarkup('The Genre of the Movie or Series.'))
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 15,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 15,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['director'] = BaseFieldDefinition::create('string')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(new TranslatableMarkup('Director'))
      ->setDescription(new TranslatableMarkup('The Director of the Movie or Series.'))
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 16,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 16,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['writer'] = BaseFieldDefinition::create('string')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(new TranslatableMarkup('Writer'))
      ->setDescription(new TranslatableMarkup('The Writer of the Movie or Series.'))
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 17,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 17,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['actors'] = BaseFieldDefinition::create('string')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(new TranslatableMarkup('Actors'))
      ->setDescription(new TranslatableMarkup('The Actors of the Movie or Series.'))
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 18,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 18,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['plot'] = BaseFieldDefinition::create('text_long')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(new TranslatableMarkup('Plot'))
      ->setDescription(new TranslatableMarkup('The Plot of the Movie or Series.'))
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 19,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 19,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['language'] = BaseFieldDefinition::create('string')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(new TranslatableMarkup('Language'))
      ->setDescription(new TranslatableMarkup('The Language of the Movie or Series.'))
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['country'] = BaseFieldDefinition::create('string')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(new TranslatableMarkup('Country'))
      ->setDescription(new TranslatableMarkup('The Country of the Movie or Series.'))
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 21,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 21,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['awards'] = BaseFieldDefinition::create('string')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(new TranslatableMarkup('Awards'))
      ->setDescription(new TranslatableMarkup('The Awards of the Movie or Series.'))
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 22,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 22,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['poster'] = BaseFieldDefinition::create('string')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(new TranslatableMarkup('Poster'))
      ->setDescription(new TranslatableMarkup('The Poster of the Movie or Series.'))
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 23,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 23,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['ratings'] = BaseFieldDefinition::create('string')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(new TranslatableMarkup('Ratings'))
      ->setDescription(new TranslatableMarkup('The Ratings of the Movie or Series.'))
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 24,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 24,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['metascore'] = BaseFieldDefinition::create('string')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(new TranslatableMarkup('Metascore'))
      ->setDescription(new TranslatableMarkup('The Metascore of the Movie or Series.'))
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 25,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 25,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['imdb_rating'] = BaseFieldDefinition::create('string')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(new TranslatableMarkup('IMDB Rating'))
      ->setDescription(new TranslatableMarkup('The IMDB Rating of the Movie or Series.'))
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 26,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 26,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['imdb_votes'] = BaseFieldDefinition::create('string')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(new TranslatableMarkup('IMDB Votes'))
      ->setDescription(new TranslatableMarkup('The IMDB Votes of the Movie or Series.'))
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 27,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 27,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['dvd_released_year'] = BaseFieldDefinition::create('timestamp')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(new TranslatableMarkup('DVD Release Date'))
      ->setDescription(new TranslatableMarkup('The DVD Release date of the Movie or Series.'))
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 29,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'datetime',
        'label' => 'above',
        'weight' => 29,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['box_office_collections'] = BaseFieldDefinition::create('string')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(new TranslatableMarkup('BoxOffice Collections'))
      ->setDescription(new TranslatableMarkup('The BoxOffice Collections of the Movie or Series.'))
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 30,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 30,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['production'] = BaseFieldDefinition::create('string')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(new TranslatableMarkup('Production'))
      ->setDescription(new TranslatableMarkup('The Production of the Movie or Series.'))
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 31,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 31,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['website'] = BaseFieldDefinition::create('string')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(new TranslatableMarkup('Website'))
      ->setDescription(new TranslatableMarkup('The Website of the Movie or Series.'))
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 32,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 32,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['api_response'] = BaseFieldDefinition::create('string')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(new TranslatableMarkup('OMDB API Response'))
      ->setDescription(new TranslatableMarkup('The OMDB API Response of the Movie or Series.'))
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 33,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 33,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setRevisionable(TRUE)
      ->setLabel(new TranslatableMarkup('Published'))
      ->setDescription(new TranslatableMarkup('Select to get Entity Published.'))
      ->setDefaultValue(FALSE)
      ->setSetting('on_label', 'Published')
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => FALSE,
        ],
        'weight' => 35,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'boolean',
        'label' => 'above',
        'weight' => 35,
        'settings' => [
          'format' => 'enabled-disabled',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(new TranslatableMarkup('Author'))
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback(static::class . '::getDefaultEntityOwner')
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ],
        'weight' => 36,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'author',
        'weight' => 36,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(new TranslatableMarkup('Authored on'))
      ->setTranslatable(TRUE)
      ->setDescription(new TranslatableMarkup('The time that the omdb api was created.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 37,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 37,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(new TranslatableMarkup('Changed'))
      ->setTranslatable(TRUE)
      ->setDescription(new TranslatableMarkup('The time that the omdb api was last edited.'));

    $fields['path'] = BaseFieldDefinition::create('path')
      ->setLabel(new TranslatableMarkup('URL alias'))
      ->setDescription(new TranslatableMarkup('Set the URL Alias to the OMDB API Entity.'))
      ->setTranslatable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'path',
        'weight' => 40,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setComputed(TRUE);

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 41,
      ]);

    return $fields;
  }

}
