<?php

namespace Drupal\omdb_api\Entity\Routing;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\AdminHtmlRouteProvider;
use Symfony\Component\Routing\Route;

/**
 * Provides routes for Omdb API Entities.
 *
 * @see Drupal\Core\Entity\Routing\AdminHtmlRouteProvider
 * @see Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider
 */
class OmdbApiEntityHtmlRouteProvider extends AdminHtmlRouteProvider {

  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {

    $collection = parent::getRoutes($entity_type);

    $entity_type_id = $entity_type->id();

    if ($history_route = $this->getHistoryRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.version_history", $history_route);
    }

    if ($revision_route = $this->getRevisionRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.revision", $revision_route);
    }

    if ($revert_route = $this->getRevisionRevertRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.revision_revert", $revert_route);
    }

    if ($delete_route = $this->getRevisionDeleteRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.revision_delete", $delete_route);
    }

    if ($translation_route = $this->getRevisionTranslationRevertRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.revision_revert_translation", $translation_route);
    }

    if ($export_route = $this->getEntityExportRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.export", $export_route);
    }

    // if ($settings_form_route = $this->getCredentialsSettingsFormRoute($entity_type)) {
    //   $collection->add("entity.{$entity_type_id}_credentials.settings_form", $settings_form_route);
    // }

    if ($logger_settings_form_route = $this->getOmdbApiLoggerSettingsFormRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}_logger.settings_form", $logger_settings_form_route);
    }

    // if ($logger_settings_form_route = $this->getOmdbApiLogViewControllerRoute($entity_type)) {
    //   $collection->add("entity.{$entity_type_id}.log_view", $logger_settings_form_route);
    // }

    return $collection;

  }

  /**
   * Gets the version history route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getHistoryRoute(EntityTypeInterface $entity_type) {

    if ($entity_type->hasLinkTemplate('version-history')) {
      $route = new Route($entity_type->getLinkTemplate('version-history'));
      $route
        ->setDefaults([
          '_title' => "{$entity_type->getLabel()} revisions",
          '_controller' => '\Drupal\omdb_api\Entity\Controller\OmdbApiEntityController::revisionOverview',
        ])
        ->setRequirement('_permission', 'view all omdb api entities revisions')
        ->setOption('_admin_route', TRUE);

      return $route;
    }

  }

  /**
   * Gets the revision route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getRevisionRoute(EntityTypeInterface $entity_type) {

    if ($entity_type->hasLinkTemplate('revision')) {
      $route = new Route($entity_type->getLinkTemplate('revision'));
      $route
        ->setDefaults([
          '_controller' => '\Drupal\omdb_api\Entity\Controller\OmdbApiEntityController::revisionShow',
          '_title_callback' => '\Drupal\omdb_api\Entity\Controller\OmdbApiEntityController::revisionPageTitle',
        ])
        ->setRequirement('_permission', 'view all omdb api entities revisions')
        ->setOption('_admin_route', TRUE);

      return $route;
    }

  }

  /**
   * Gets the revision revert route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getRevisionRevertRoute(EntityTypeInterface $entity_type) {

    if ($entity_type->hasLinkTemplate('revision_revert')) {
      $route = new Route($entity_type->getLinkTemplate('revision_revert'));
      $route
        ->setDefaults([
          '_form' => '\Drupal\omdb_api\Entity\Form\OmdbApiEntityRevisionRevertForm',
          '_title' => 'Revert to earlier revision',
        ])
        ->setRequirement('_permission', 'revert all omdb api entities revisions')
        ->setOption('_admin_route', TRUE);
      return $route;
    }

  }

  /**
   * Gets the revision delete route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getRevisionDeleteRoute(EntityTypeInterface $entity_type) {

    if ($entity_type->hasLinkTemplate('revision_delete')) {
      $route = new Route($entity_type->getLinkTemplate('revision_delete'));
      $route
        ->setDefaults([
          '_form' => '\Drupal\omdb_api\Entity\Form\OmdbApiEntityRevisionDeleteForm',
          '_title' => 'Delete earlier revision',
        ])
        ->setRequirement('_permission', 'delete all omdb api entities revisions')
        ->setOption('_admin_route', TRUE);
      return $route;
    }

  }

  /**
   * Gets the revision translation revert route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getRevisionTranslationRevertRoute(EntityTypeInterface $entity_type) {

    if ($entity_type->hasLinkTemplate('revision_revert_translation')) {
      $route = new Route($entity_type->getLinkTemplate('revision_revert_translation'));
      $route
        ->setDefaults([
          '_form' => '\Drupal\omdb_api\Entity\Form\OmdbApiEntityRevisionRevertTranslationForm',
          '_title' => 'Revert to earlier revision of a translation',
        ])
        ->setRequirement('_permission', 'revert all omdb api entities revisions')
        ->setOption('_admin_route', TRUE);
      return $route;
    }

  }

  /**
   * Gets the settings form route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getCredentialsSettingsFormRoute(EntityTypeInterface $entity_type) {

    if (!$entity_type->getBundleEntityType()) {
      $route = new Route("/admin/structure/omdb-api/credentials-settings");
      $route
        ->setDefaults([
          '_form' => 'Drupal\omdb_api\Form\OmdbApiCredentialsSettingsForm',
          '_title' => "{$entity_type->getLabel()} settings",
        ])
        ->setRequirement('_permission', $entity_type->getAdminPermission())
        ->setOption('_admin_route', TRUE);
      return $route;
    }

  }

  /**
   * Gets the OMDB API Entity Logger Settings route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getOmdbApiLoggerSettingsFormRoute(EntityTypeInterface $entity_type) {

    if (!$entity_type->getBundleEntityType()) {
      $route = new Route("/admin/structure/omdb-api/logger-settings");
      $route
        ->setDefaults([
          '_form' => 'Drupal\omdb_api\Entity\Form\OmdbApiEntityLoggerSettingsForm',
          '_title' => "{$entity_type->getLabel()} Logger Settings Form",
        ])
        ->setRequirement('_permission', $entity_type->getAdminPermission())
        ->setOption('_admin_route', TRUE);
      return $route;
    }
  }

  /**
   * Gets the OMDB API Entity Logger Settings route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getOmdbApiLogViewControllerRoute(EntityTypeInterface $entity_type) {

    if (!$entity_type->getBundleEntityType()) {
      $route = new Route("/admin/structure/omdb-api/view-logs");
      $route->setDefaults([
        '_title' => "OMDB API Entity Log View Controller",
        '_controller' => '\Drupal\omdb_api\Entity\Controller\OmdbApiLogViewController::build',
      ]);
      $route->setRequirement('_permission', $entity_type->getAdminPermission())
        ->setOption('_admin_route', TRUE);
      return $route;
    }
  }

  /**
   * Gets the  entity export route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getEntityExportRoute(EntityTypeInterface $entity_type) {

    if ($entity_type->hasLinkTemplate('export')) {
      $entity_type_id = $entity_type->id();
      $route = new Route($entity_type->getLinkTemplate('export'));
      $route->setDefaults([
        '_title' => "{$entity_type->getLabel()} export",
        '_controller' => '\Drupal\omdb_api\Entity\Controller\OmdbApiEntityController::entityExport',
      ]);
      $route->setRequirement('_permission', 'view all omdb api entities');
      $route->setOption('parameters', [
        $entity_type_id => ['type' => 'entity:' . $entity_type_id],
      ]);
      // Entity types with serial IDs can specify this in their route
      // requirements, improving the matching process.
      if ($this->getEntityTypeIdKeyType($entity_type) === 'integer') {
        $route->setRequirement($entity_type_id, '\d+');
      }
      return $route;
    }
  }

}
