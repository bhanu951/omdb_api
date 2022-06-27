<?php

namespace Drupal\omdb_api\Plugin\DevelGenerate;

use Drupal\Core\Form\FormStateInterface;
use Drupal\devel_generate\DevelGenerateBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ExtensionPathResolver;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;

/**
 * Provides a Omdb Api Entity Devel Generate plugin.
 *
 * @DevelGenerate(
 *   id = "omdb_api_entity_devel_generate",
 *   label = @Translation("Omdb Api Entities"),
 *   description = @Translation("Omdb Api Entity Devel Generate Plugin"),
 *   url = "omdb-api-entities",
 *   permission = "administer devel_generate",
 *   settings = {
 *     "num" = 50,
 *     "kill" = FALSE,
 *     "name_length" = 4,
 *   },
 *   dependencies = {
 *     "omdb_api",
 *   },
 * )
 */
class OmdbApiEntityDevelGenerate extends DevelGenerateBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Provides system time.
   *
   * @var \Drupal\Core\Datetime\TimeInterface
   */
  protected $time;

  /**
   * The language manager service.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The extension path resolver.
   *
   * @var \Drupal\Core\Extension\ExtensionPathResolver
   */
  protected $extensionPathResolver;

  /**
   * The url generator service.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * The omdb api entity storage.
   *
   * @var \Drupal\omdb_api\Entity\Storage\OmdbApiEntityInterface
   */
  protected $omdbApiEntityStorage;

  /**
   * The user entity storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * The alias storage.
   *
   * @var \Drupal\path_alias\PathAliasStorage
   */
  protected $aliasStorage;

  /**
   * The Drush batch flag.
   *
   * @var bool
   */
  protected $drushBatch;

  /**
   * The entity bundle info.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

  /**
   * Constructs a new Omdb Api Entity Devel Generate object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity storage.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager service.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Core\Extension\ExtensionPathResolver $extension_path_resolver
   *   The extension path resolver.
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $url_generator
   *   The url generator service.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   * @param \Drupal\Core\Datetime\TimeInterface $time
   *   Provides system time.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle info.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, LanguageManagerInterface $language_manager, ModuleHandlerInterface $module_handler, ExtensionPathResolver $extension_path_resolver, UrlGeneratorInterface $url_generator, DateFormatterInterface $date_formatter, TimeInterface $time, EntityTypeBundleInfoInterface $entity_type_bundle_info) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->dateFormatter = $date_formatter;
    $this->languageManager = $language_manager;
    $this->moduleHandler = $module_handler;
    $this->extensionPathResolver = $extension_path_resolver;
    $this->urlGenerator = $url_generator;
    $this->time = $time;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
    $this->omdbApiEntityStorage = $this->entityTypeManager->getStorage('omdb_api');
    $this->userStorage = $this->entityTypeManager->getStorage('user');
    $this->aliasStorage = $this->entityTypeManager->getStorage('path_alias');

  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {

    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('language_manager'),
      $container->get('module_handler'),
      $container->get('extension.path.resolver'),
      $container->get('url_generator'),
      $container->get('date.formatter'),
      $container->get('datetime.time'),
      $container->get('entity_type.bundle.info')
    );

  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    $form['num'] = [
      '#type' => 'textfield',
      '#title' => $this->t('How many Omdb Api Entities would you like to generate?'),
      '#default_value' => $this->getSetting('num'),
      '#size' => 10,
    ];

    $form['kill'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Delete all Omdb Api Entities before generating new Omdb Api Entities.'),
      '#default_value' => $this->getSetting('kill'),
    ];

    $header = [
      'type' => $this->t('OMDB API Entity type'),
    ];
    // Get OMDB API Entity Bundle Information.
    $bundles = $this->entityTypeBundleInfo->getBundleInfo('omdb_api') ?? [];
    $bundle_options = [];
    if (!empty($bundles)) {
      foreach ($bundles as $key => $bundle) {
        $bundle_options[$key] = $bundle['label'];
      }

      $form['omdb_api_types'] = [
        '#type' => 'select',
        '#header' => $header,
        '#options' => $bundle_options,
      ];
    }

    $options = [1 => $this->t('Now')];
    foreach ([3600, 86400, 604800, 2592000, 31536000] as $interval) {
      $options[$interval] = $this->dateFormatter->formatInterval($interval, 1) . ' ' . $this->t('ago');
    }

    $form['time_range'] = [
      '#type' => 'select',
      '#title' => $this->t('How far back in time should the omdb api entity be dated?'),
      '#description' => $this->t('Omdb Api Entities creation dates will be distributed randomly from the current time, back to the selected time.'),
      '#options' => $options,
      '#default_value' => 604800,
    ];

    $form['name_length'] = [
      '#type' => 'number',
      '#title' => $this->t('Maximum number of words in names'),
      '#default_value' => $this->getSetting('name_length'),
      '#required' => TRUE,
      '#min' => 1,
      '#max' => 255,
    ];

    $form['add_alias'] = [
      '#type' => 'checkbox',
      '#disabled' => !$this->moduleHandler->moduleExists('path'),
      '#description' => $this->t('Requires path.module'),
      '#title' => $this->t('Add an url alias for each omdb api entity.'),
      '#default_value' => FALSE,
    ];

    $options = [];
    // We always need a language.
    $languages = $this->languageManager->getLanguages(LanguageInterface::STATE_ALL);
    foreach ($languages as $langcode => $language) {
      $options[$langcode] = $language->getName();
    }

    $form['add_language'] = [
      '#type' => 'select',
      '#title' => $this->t('Set language on omdb api entity'),
      '#multiple' => TRUE,
      '#description' => $this->t('Requires locale.module'),
      '#options' => $options,
      '#default_value' => [
        $this->languageManager->getDefaultLanguage()->getId(),
      ],
    ];

    // Add the user selection checkboxes.
    $author_header = [
      'id' => $this->t('User ID'),
      'user' => $this->t('Name'),
      'role' => $this->t('Role(s)'),
    ];

    $author_rows = [];
    /** @var \Drupal\user\UserInterface $user */
    foreach ($this->userStorage->loadMultiple() as $user) {
      $author_rows[$user->id()] = [
        'id' => ['#markup' => $user->id()],
        'user' => ['#markup' => $user->getAccountName()],
        'role' => ['#markup' => implode(", ", $user->getRoles())],
      ];
    }

    $form['authors-wrap'] = [
      '#type' => 'details',
      '#title' => $this->t('Users'),
      '#open' => FALSE,
      '#description' => $this->t('Select users for randomly assigning as authors of the generated content. Leave all unchecked to use a random selection of up to 50 users.'),
    ];

    $form['authors-wrap']['authors'] = [
      '#type' => 'tableselect',
      '#header' => $author_header,
      '#options' => $author_rows,
    ];

    $form['#redirect'] = FALSE;

    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function settingsFormValidate(array $form, FormStateInterface $form_state) {
    // Add Form Validations.
  }

  /**
   * {@inheritdoc}
   */
  protected function generateElements(array $values) {

    if ($this->isBatch($values['num'])) {
      $this->generateBatchOmdbApiEntities($values);
    }
    else {
      $this->generateOmdbApiEntities($values);
    }

  }

  /**
   * Batch Method for creating omdb api entity.
   *
   * When number of items are greater than 50.
   *
   * @param array $values
   *   The input values from the settings form.
   */
  protected function generateBatchOmdbApiEntities(array $values) {

    $operations = [];

    // Setup the batch operations and save the variables.
    $operations[] = [
      'devel_generate_operation',
      [$this, 'batchOmdbApiEntityPreGenerate', $values],
    ];

    // Add the kill operation.
    if ($values['kill']) {
      $operations[] = [
        'devel_generate_operation',
        [$this, 'batchOmdbApiEntityKill', $values],
      ];
    }

    // Add the operations to create the omdb api entity.
    for ($num = 0; $num < $values['num']; $num++) {
      $operations[] = [
        'devel_generate_operation',
        [$this, 'batchCreateOmdbApiEntityItem', $values],
      ];
    }

    $module_path = $this->extensionPathResolver->getPath('module', 'devel_generate');

    // Start the batch.
    $batch = [
      'title' => $this->t('Generating omdb api entity items'),
      'operations' => $operations,
      'finished' => 'devel_generate_batch_finished',
      'file' => $module_path . '/devel_generate.batch.inc',
    ];
    batch_set($batch);

    if ($this->drushBatch) {
      drush_backend_batch_process();
    }

  }

  /**
   * Provides a batch version of preOmdbApiEntityGenerate().
   *
   * @param array $vars
   *   The input values from the settings form.
   * @param iterable $context
   *   Batch job context.
   *
   * @see self::preOmdbApiEntityGenerate()
   */
  public function batchOmdbApiEntityPreGenerate(array $vars, iterable &$context) {
    $context['results'] = $vars;
    $context['results']['num'] = 0;
    $this->preOmdbApiEntityGenerate($context['results']);
  }

  /**
   * Provides a batch version of createOmdbApiEntityItem().
   *
   * @param array $vars
   *   The input values from the settings form.
   * @param object $context
   *   Batch job context.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   *   Thrown if the storage handler couldn't be loaded.
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   *   Thrown if the entity type doesn't exist.
   * @throws \Drupal\Core\Entity\EntityStorageException
   *   Thrown if the bundle does not exist or was needed but not specified.
   *
   * @see self::createOmdbApiEntityItem()
   */
  public function batchCreateOmdbApiEntityItem(array $vars, &$context) {

    if ($this->drushBatch) {
      $this->createOmdbApiEntityItem($vars);
    }
    else {
      $this->createOmdbApiEntityItem($context['results']);
    }
    $context['results']['num']++;

  }

  /**
   * Provides a batch version of omdbApiEntityKill().
   *
   * @param array $vars
   *   The input values from the settings form.
   * @param object $context
   *   Batch job context.
   *
   * @see self::omdbApiEntityKill()
   */
  public function batchOmdbApiEntityKill(array $vars, object &$context) {

    if ($this->drushBatch) {
      $this->omdbApiEntityKill();
    }
    else {
      $this->omdbApiEntityKill();
    }

  }

  /**
   * Finds out if the omdb api entity item generation will run in batch process.
   *
   * @param int $omdb_api_items_count
   *   Number of omdb api entity items to be generated.
   *
   * @return bool
   *   If the process should be a batch process.
   */
  protected function isBatch($omdb_api_items_count) {
    return $omdb_api_items_count >= 50;
  }

  /**
   * Determine language based on $results.
   *
   * @param array $results
   *   The input values from the settings form.
   *
   * @return string
   *   The language code.
   */
  protected function getLangcode(array $results) {

    if (isset($results['add_language'])) {
      $langcodes = $results['add_language'];
      $langcode = $langcodes[array_rand($langcodes)];
    }
    else {
      $langcode = $this->languageManager->getDefaultLanguage()->getId();
    }
    return $langcode;

  }

  /**
   * Code to be run before generating items.
   *
   * Returns the same array passed in as parameter, but with an array of uids
   * for the key 'users'.
   *
   * @param array $results
   *   The input values from the settings form.
   */
  protected function preOmdbApiEntityGenerate(array &$results) {
    // Get user id.
    $users = array_values($this->userStorage->getQuery()
      ->range(0, 50)
      ->execute());
    $users = array_merge($users, ['0']);
    $results['users'] = $users;
  }

  /**
   * Deletes all omdb api entities.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *   Thrown if the omdb api entity does not exist.
   */
  protected function omdbApiEntityKill() {

    $omdb_api_ids = $this->entityTypeManager->getStorage('omdb_api')->getQuery()->execute();

    if (!empty($omdb_api_ids)) {
      $omdb_api_entity = $this->omdbApiEntityStorage->loadMultiple($omdb_api_ids);
      $this->omdbApiEntityStorage->delete($omdb_api_entity);
      $this->setMessage($this->t('Deleted %count omdb api entity items.', ['%count' => count($omdb_api_ids)]));
    }

  }

  /**
   * Method for creating omdb api entities.
   *
   * When number of elements is less than 50.
   *
   * @param array $values
   *   Array of values submitted through a form.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   *   Thrown if the storage handler couldn't be loaded.
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   *   Thrown if the entity type doesn't exist.
   * @throws \Drupal\Core\Entity\EntityStorageException
   *   Thrown if the bundle does not exist or was needed but not specified.
   */
  protected function generateOmdbApiEntities(array $values) {

    if (!empty($values['kill'])) {
      $this->omdbApiEntityKill();
    }

    // Generate omdb api entity items.
    $this->preOmdbApiEntityGenerate($values);
    $start = time();
    for ($i = 1; $i <= $values['num']; $i++) {
      $this->createOmdbApiEntityItem($values);
      if (isset($values['feedback']) && $i % $values['feedback'] == 0) {
        $now = time();
        $this->messenger()->addStatus(dt('Completed !feedback omdb api entity items (!rate omdb api entity/min)', [
          '!feedback' => $values['feedback'],
          '!rate' => ($values['feedback'] * 60) / ($now - $start),
        ]));
        $start = $now;
      }
    }

    $this->setMessage($this->formatPlural($values['num'], '1 omdb api entity items created.', 'Finished creating @count omdb api entity items .'));

  }

  /**
   * Create one Omdb Api Entity item.
   *
   * Used by both batch and non-batch code branches.
   *
   * @param array $results
   *   The input values from the settings form.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   *   Thrown if the storage handler couldn't be loaded.
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   *   Thrown if the entity type doesn't exist.
   * @throws \Drupal\Core\Entity\EntityStorageException
   *   Thrown if the bundle does not exist or was needed but not specified.
   */
  protected function createOmdbApiEntityItem(array &$results) {

    if (!isset($results['time_range'])) {
      $results['time_range'] = 0;
    }

    $uid = $results['users'][array_rand($results['users'])];

    // Set type as either Movies or Series.
    if (isset($results['omdb_api_types'])) {
      $type = $results['omdb_api_types'];
    }
    else {
      $types = ['series', 'movies'];
      $index = array_rand($types);
      $type = $types[$index];
    }

    $data = [
      'bundle' => $type,
      'imdb_title' => "OMDB API Entity Devel : " . $this->getRandom()->sentences(mt_rand(1, $results['name_length']), TRUE),
      'imdb_id' => 'tt' . mt_rand(1000000, 9999999),
      'released_year' => mt_rand(1900, 2022),
      'plot' => $this->getRandom()->sentences(mt_rand(1, (2 * $results['name_length'])), TRUE),
      'website' => 'http://www.example.com/',
      'ratings' => mt_rand(55, 95) / 10,
      'viewer_rating' => mt_rand(55, 95) / 10,
      'metascore' => mt_rand(55, 95) / 10,
      'released_date' => strtotime('today') - (mt_rand(10000, 99999) * 1000),
      'dvd_released_year' => strtotime('today') - (mt_rand(10000, 99999) * 1000),
      'uid' => $uid,
      'revision' => mt_rand(0, 1),
      'status' => TRUE,
      'moderation_state' => 'published',
      'created' => $this->time->getRequestTime() - mt_rand(0, $results['time_range']),
      'langcode' => $this->getLangcode($results),
    ];

    $omdb_api_entity = $this->omdbApiEntityStorage->create($data);

    // A flag to let hook implementations know that this is a generated item.
    $omdb_api_entity->devel_generate = $results;

    // Populate all fields with sample values.
    $this->populateFields($omdb_api_entity);

    $omdb_api_entity->save();

    // Add url alias if required.
    if (!empty($results['add_alias'])) {
      $path_alias = $this->aliasStorage->create([
        'path' => '/content/omdb-api/' . $omdb_api_entity->id(),
        'alias' => '/path-alias/content/omdb-api/' . $type . $omdb_api_entity->id(),
        'langcode' => $values['langcode'] ?? LanguageInterface::LANGCODE_NOT_SPECIFIED,
      ]);
      $path_alias->save();
    }

  }

  /**
   * {@inheritdoc}
   */
  public function validateDrushParams(array $args, array $options = []) {

    $add_language = $options['languages'];
    if (!empty($add_language)) {
      $add_language = explode(',', str_replace(' ', '', $add_language));
      // Intersect with the enabled languages to make sure the language args
      // passed are actually enabled.
      $values['values']['add_language'] = array_intersect($add_language, array_keys($this->languageManager->getLanguages(LanguageInterface::STATE_ALL)));
    }

    $values['kill'] = $options['kill'];
    $values['feedback'] = $options['feedback'];
    $values['name_length'] = 6;
    $values['num'] = $options['num'];

    if ($this->isBatch($values['num'])) {
      $this->drushBatch = TRUE;
      $this->preOmdbApiEntityGenerate($values);
    }

    return $values;

  }

}
