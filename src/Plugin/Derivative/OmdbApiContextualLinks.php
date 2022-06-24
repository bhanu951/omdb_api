<?php

namespace Drupal\omdb_api\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides contextual links for OMDB API Entities.
 *
 * @see https://git.drupalcode.org/project/oh/-/blob/2.x/modules/oh_review/src/Plugin/Derivative/OhReviewContextualLinks.php
 */
class OmdbApiContextualLinks extends DeriverBase implements ContainerDeriverInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Construct OMDB API Entity Contextual Links.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->omdbApiEntityStorage = $this->entityTypeManager->getStorage('omdb_api');

  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition): array {

    // Get OMDB API Entity Bundle Types.
    $omdb_api_entity_types = $this->omdbApiEntityStorage->loadMultiple();

    foreach ($omdb_api_entity_types as $omdb_api_entity_type) {
      // $options[$omdb_api_entity_type->id()] = $omdb_api_entity_type->label();
      $route_name = sprintf('entity.omdb_api.%s', $omdb_api_entity_type);
      $this->derivatives[$omdb_api_entity_type->id()] = $base_plugin_definition;
      $this->derivatives[$omdb_api_entity_type->id()]['route_name'] = $route_name;
      $this->derivatives[$omdb_api_entity_type->id()]['group'] = 'omdb_api';
      $this->derivatives[$omdb_api_entity_type->id()]['title'] = $omdb_api_entity_type->label();
    }

    return parent::getDerivativeDefinitions($base_plugin_definition);
  }

}
