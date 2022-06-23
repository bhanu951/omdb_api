<?php

namespace Drupal\omdb_api\Entity\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for OMDB API Log View.
 */
class OmdbApiLogViewController extends ControllerBase {

  /**
   * The file handler.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Configuration Factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * The file URL generator.
   *
   * @var \Drupal\Core\File\FileUrlGeneratorInterface|null
   */
  protected $fileUrlGenerator;

  /**
   * The controller constructor.
   *
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The file handler.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \Drupal\Core\File\FileUrlGeneratorInterface $file_url_generator
   *   The file URL generator service.
   */
  public function __construct(FileSystemInterface $file_system, EntityTypeManagerInterface $entity_type_manager, ConfigFactoryInterface $config_factory, FileUrlGeneratorInterface $file_url_generator) {
    $this->fileSystem = $file_system;
    $this->entityTypeManager = $entity_type_manager;
    $this->configFactory = $config_factory->get('omdb_api_entity_logger.settings');
    $this->fileUrlGenerator = $file_url_generator;

  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('file_system'),
      $container->get('entity_type.manager'),
      $container->get('config.factory'),
      $container->get('file_url_generator')
    );
  }

  /**
   * Builds the Log view.
   */
  public function build() {

    $log_file_path = $this->configFactory->get('log_file_path');
    $log_file_name = $this->configFactory->get('log_file_name');
    $log_file_header = $this->configFactory->get('log_file_header');
    $log_file = $log_file_path . DIRECTORY_SEPARATOR . $log_file_name;

    $count = 0;
    $lines = $rows = [];
    if (($handle = fopen($log_file, 'r')) !== FALSE) {
      while (($data = fgetcsv($handle, 1000, '|')) !== FALSE) {
        $count++;
        if ($count !== 1) {
          $lines[] = $data;
        }
      }
    }
    fclose($handle);

    $lines_reverse = array_reverse($lines);
    $size = count($lines_reverse);
    if ($size > 50) {
      $size = 20;
      $rows_slice = array_chunk($lines_reverse, $size, TRUE);
      $rows = $rows_slice[0];
    }
    else {
      $rows = $lines_reverse;
    }

    $build['table'] = [
      '#type' => 'table',
      '#header' => explode("|", (string) $log_file_header),
      '#rows' => $rows,
      '#empty' => $this->t('No records found'),
    ];

    $build['pager'] = [
      '#type' => 'pager',
    ];

    return $build;
  }

}
