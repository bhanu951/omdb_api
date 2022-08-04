<?php

namespace Drupal\omdb_api\Entity;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleUninstallValidatorInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Url;

/**
 * Prevents uninstalling of module having content.
 */
class OmdbApiEntityUninstallValidator implements ModuleUninstallValidatorInterface {

  use StringTranslationTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new FilterUninstallValidator.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_manager
   *   The entity manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_manager) {
    $this->entityTypeManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function validate($module) {

    $reasons = [];

    if ($module != 'omdb_api') {
      return [];
    }

    $entity_types = $this->entityTypeManager->getDefinitions();
    $reasons = [];
    foreach ($entity_types as $entity_type) {
      if ($module == $entity_type->getProvider() && $entity_type instanceof ContentEntityTypeInterface) {
        $reasons[] = $this->t('You need delete the entity type config first: @entity_type. <a href=":url">Remove @entity_type</a>.', [
          '@entity_type' => $entity_type->getLabel(),
          ':url' => Url::fromRoute('entity.content_type.collection')->toString(),
        ]);
      }
    }
    return $reasons;

  }

}
