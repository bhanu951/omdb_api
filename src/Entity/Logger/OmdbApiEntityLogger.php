<?php

namespace Drupal\omdb_api\Entity\Logger;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Logger\LogMessageParserInterface;
use Drupal\Component\Uuid\Php;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Database\Database;
use Drupal\omdb_api\Entity\Exception\OmdbApiEntityException;

// http://grep.xnddx.ru/node/30960912
/**
 * OMDB API Entity Operations Logger Class.
 */
class OmdbApiEntityLogger implements OmdbApiEntityLoggerInterface {

  use StringTranslationTrait;

  /**
   * A configuration object containing system.file settings.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * The logger channel factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $logger;

  /**
   * The message's placeholders parser.
   *
   * @var \Drupal\Core\Logger\LogMessageParserInterface
   */
  protected $parser;

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * File system service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  public $fileSystem;

  /**
   * Request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Uuid service.
   *
   * @var \Drupal\Component\Uuid\Php
   */
  protected $uuid;

  /**
   * The time system.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected TimeInterface $time;

  /**
   * Constructs OMDB API Logger Object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory object.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger channel factory.
   * @param \Drupal\Core\Logger\LogMessageParserInterface $parser
   *   The parser to use when extracting message variables.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   Object of file_system service.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request
   *   Request object to get request params.
   * @param \Drupal\Component\Uuid\Php $uuid
   *   The UUID service.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The datetime.time service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, LoggerChannelFactoryInterface $logger_factory, LogMessageParserInterface $parser, DateFormatterInterface $date_formatter, AccountInterface $current_user, FileSystemInterface $file_system, RequestStack $request, Php $uuid, TimeInterface $time) {
    $this->config = $config_factory->get('omdb_api_entity_logger.settings');
    $this->logger = $logger_factory->get('omdb_api');
    $this->parser = $parser;
    $this->dateFormatter = $date_formatter;
    $this->currentUser = $current_user;
    $this->fileSystem = $file_system;
    $this->requestStack = $request;
    $this->uuid = $uuid;
    $this->time = $time;
  }

  /**
   * Method to Log OMDB API Entity Operations.
   */
  public function entityOperationLog(string $operation, EntityInterface $entity) {

    $args = [];
    $link = '';
    if ($entity->hasLinkTemplate('canonical')) {
      $link = $entity->toLink($this->t('View'), 'canonical')->toString();
    }
    elseif ($entity->getEntityTypeId() === 'omdb_api') {
      $link = Url::fromUri('<current>')->toString();
    }
    else {
      $link = '';
    }

    $time = $this->time->getRequestTime();
    $level = 'notice';
    if ($request = ($this->requestStack->getCurrentRequest() ?? NULL)) {
      $args = [
        'uuid' => $this->uuid->generate(),
        'request_uri' => $request->getUri(),
        'user_agent' => $request->headers->get('user-agent'),
        'ip' => $request->getClientIP(),
        'protocol' => $request->getProtocolVersion(),
        'referer' => $request->headers->get('Referer', '') ?? NULL,
        '@operation' => ucfirst($operation),
        '@type' => $entity->getEntityTypeId(),
        '@title' => $entity->label(),
        '@id' => $entity->id(),
        '@langcode' => $entity->language()->getId(),
        '@user' => $this->currentUser->id() . (!empty($this->currentUser->getDisplayName()) ? ' (' . $this->currentUser->getDisplayName() . ')' : ''),
        'date' => $this->dateFormatter->format($time),
        'link' => $link,
        'uid' => $this->currentUser->id(),
        'severity' => $level,
      ];
    }

    $log_message = $this->t('@operation operation performed on entity of type: @type, ID: @id, Title : @title , for language @langcode performed by user @user.');

    // Populate the message placeholders and then replace them in the message.
    $message_placeholders = $this->parser->parseMessagePlaceholders($log_message, $args);
    $message = empty($message_placeholders) ? $log_message : strtr($log_message, $message_placeholders);
    $args['message'] = $message;
    $log_file_path = $this->config->get('log_file_path');
    $log_file_name = $this->config->get('log_file_name');
    $log_file_header = $this->config->get('log_file_header');

    $log_file = $log_file_path . DIRECTORY_SEPARATOR . $log_file_name ?? tempnam(sys_get_temp_dir(), $log_file_name);

    $destination = $this->fileSystem->realpath($log_file);
    $this->fileSystem->prepareDirectory($destination, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS);

    if (!file_exists($log_file)) {
      file_put_contents($log_file, $log_file_header . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    file_put_contents($log_file, implode("|", $args) . PHP_EOL, FILE_APPEND | LOCK_EX);
    $this->logger->notice($log_message, $args);

    // Calling  Method to Log OMDB API Entity Operations in SQLite Database.
    $this->insertToSqliteDb($args);

  }

  /**
   * Method to Log OMDB API Entity Operations in SQLite Database.
   *
   * @param array $args
   *   The array of data to insert into SQLite Database.
   */
  protected function insertToSqliteDb(array $args) {

    $keys = str_replace('@', '', array_keys($args));
    $results = array_combine($keys, array_values($args));

    $spec = [
      'description' => 'OMDB API Entity Log Data',
      'fields' => [
        'uuid' => [
          'type' => 'varchar',
          'not null' => TRUE,
        ],
        'request_uri' => [
          'type' => 'varchar',
          'length' => 255,
        ],
        'user_agent' => [
          'type' => 'varchar',
          'length' => 255,
        ],
        'ip' => [
          'type' => 'varchar',
          'length' => 255,
        ],
        'protocol' => [
          'type' => 'varchar',
          'length' => 255,
        ],
        'referer' => [
          'type' => 'varchar',
          'length' => 255,
        ],
        'operation' => [
          'type' => 'varchar',
          'length' => 255,
        ],
        'type' => [
          'type' => 'varchar',
          'length' => 255,
        ],
        'title' => [
          'type' => 'varchar',
          'length' => 255,
        ],
        'id' => [
          'type' => 'varchar',
          'length' => 15,
        ],
        'langcode' => [
          'type' => 'varchar',
          'length' => 20,
        ],
        'user' => [
          'type' => 'varchar',
          'length' => 255,
        ],
        'date' => [
          'type' => 'varchar',
          'length' => 50,
        ],
        'link' => [
          'type' => 'varchar',
          'length' => 255,
        ],
        'uid' => [
          'type' => 'varchar',
          'length' => 5,
        ],
        'severity' => [
          'type' => 'varchar',
          'length' => 255,
        ],
        'message' => [
          'type' => 'varchar',
          'length' => 255,
        ],
      ],
      'primary key' => ['uuid'],
    ];

    try {
      $connection = Database::getConnection('default', 'sqlite');
      $schema = $connection->schema();
      if (!$schema->tableExists('omdb_api_log_data')) {
        $schema->createTable('omdb_api_log_data', $spec);
      }

      $query = $connection->insert('omdb_api_log_data');
      $query->fields([
        'uuid',
        'request_uri',
        'user_agent',
        'ip',
        'protocol',
        'referer',
        'operation',
        'type',
        'title',
        'id',
        'langcode',
        'user',
        'date',
        'link',
        'uid',
        'severity',
        'message',
      ]);
      $query->values($results);
      $query->execute();

    }
    catch (OmdbApiEntityException $e) {

      $this->logger->error('SQLite Database configurtion is not setup. Error occurred establishing connection : %error', [
        '%error' => $e->getMessage(),
      ]);
      return new OmdbApiEntityException("SQLite Database configurtion is not setup.", 1);

    }
  }

}
