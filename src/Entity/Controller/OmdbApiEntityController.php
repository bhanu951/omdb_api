<?php

namespace Drupal\omdb_api\Entity\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\omdb_api\Entity\OmdbApiEntityInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Response;

/**
 * Returns responses for OMDB API Entity routes.
 *
 * @ingroup omdb_api
 */
class OmdbApiEntityController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * The extension path resolver.
   *
   * @var \Drupal\Core\Extension\ExtensionPathResolver
   */
  protected $extensionPathResolver;

  /**
   * Protected requestStack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Options for dompdf.
   *
   * @var \Dompdf\Dompdf
   */
  protected $options;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {

    $instance = parent::create($container);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->renderer = $container->get('renderer');
    $instance->extensionPathResolver = $container->get('extension.path.resolver');
    $instance->requestStack = $container->get('request_stack');
    $instance->options = new Options();
    $instance->options->set('enable_css_float', TRUE);
    $instance->options->set('enable_html5_parser', TRUE);
    $instance->options->set('enable_remote', TRUE);
    $instance->options->set('defaultFont', 'Times');

    return $instance;

  }

  /**
   * Displays a OMDB API Entity revision.
   *
   * @param int $omdb_api_revision
   *   The OMDB API Entity revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($omdb_api_revision) {

    /** @var \Drupal\omdb_api\Entity\OmdbApiEntityInterface $omdb_entity */
    $omdb_entity = $this->entityTypeManager()->getStorage('omdb_api')
      ->loadRevision($omdb_api_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('omdb_api');

    return $view_builder->view($omdb_entity);

  }

  /**
   * Page title callback for a OMDB API Entity revision.
   *
   * @param int $omdb_api_revision
   *   The OMDB API Entity revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($omdb_api_revision) {

    /** @var \Drupal\omdb_api\Entity\OmdbApiEntityInterface $omdb_entity */
    $omdb_entity = $this->entityTypeManager()->getStorage('omdb_api')
      ->loadRevision($omdb_api_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $omdb_entity->label(),
      '%date' => $this->dateFormatter->format($omdb_entity->getRevisionCreationTime()),
    ]);

  }

  /**
   * Generates an overview table of older revisions of a OMDB API Entity.
   *
   * @param \Drupal\omdb_api\Entity\OmdbApiEntityInterface $omdb_api
   *   A OMDB API Entity object.
   *
   * @return array
   *   An array as expected by \Drupal\Core\Render\RendererInterface::render().
   */
  public function revisionOverview(OmdbApiEntityInterface $omdb_api) {

    $account = $this->currentUser();
    /** @var \Drupal\omdb_api\Entity\Storage\OmdbApiEntityStorageInterface $omdb_entity_storage */
    $omdb_entity_storage = $this->entityTypeManager()->getStorage('omdb_api');

    $langcode = $omdb_api->language()->getId();
    $langname = $omdb_api->language()->getName();
    $languages = $omdb_api->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', [
      '@langname' => $langname,
      '%title' => $omdb_api->label(),
    ]) : $this->t('Revisions for %title', ['%title' => $omdb_api->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all omdb api entities revisions") || $account->hasPermission('administer omdb api entities')));
    $delete_permission = (($account->hasPermission("delete all omdb api entities revisions") || $account->hasPermission('administer omdb api entities')));

    $rows = [];
    $current_revision_displayed = FALSE;
    $default_revision = $omdb_api->getRevisionId();

    $revision_ids = $omdb_entity_storage->revisionIds($omdb_api);

    foreach (array_reverse($revision_ids) as $revision_id) {

      /** @var \Drupal\omdb_api\Entity\OmdbApiEntityInterface $revision */
      $revision = $omdb_entity_storage->loadRevision($revision_id);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $revision_date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        // We treat also the latest translation-affecting revision as current
        // revision, if it was the default revision, as its values for the
        // current language will be the same of the current default revision in
        // this case.
        $is_current_revision = $revision_id == $default_revision || (!$current_revision_displayed && $revision->wasDefaultRevision());

        if (!$is_current_revision) {
          $link = Link::fromTextAndUrl($revision_date, new Url('entity.omdb_api.revision', [
            'omdb_api' => $omdb_api->id(),
            'omdb_api_revision' => $revision_id,
          ]))->toString();
        }
        else {
          $link = $omdb_api->toLink($revision_date)->toString();
          $current_revision_displayed = TRUE;
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderPlain($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
            ],
          ],
        ];

        // @todo Simplify once https://www.drupal.org/node/2334319 lands.
        $this->renderer->addCacheableDependency($column['data'], $username);
        $row[] = $column;

        if ($is_current_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.omdb_api.revision_revert_translation', [
                'omdb_api' => $omdb_api->id(),
                'omdb_api_revision' => $revision_id,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.omdb_api.revision_revert', [
                'omdb_api' => $omdb_api->id(),
                'omdb_api_revision' => $revision_id,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.omdb_api.revision_delete', [
                'omdb_api' => $omdb_api->id(),
                'omdb_api_revision' => $revision_id,
              ]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;

      }

    }

    $build['omdb_api_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;

  }

  /**
   * Callback for a OMDB API Entity export.
   *
   * @param object $omdb_api
   *   The OMDB API Entity Object.
   *
   * @return string
   *   The page title.
   */
  public function entityExport($omdb_api) {

    $url_options = ['absolute' => TRUE];
    $url = Url::fromRoute('entity.omdb_api.canonical', ['omdb_api' => $omdb_api->id()], $url_options)->toString();

    $args = [
      "ssl"  => [
        "verify_peer" => FALSE,
        "verify_peer_name" => FALSE,
      ],
      "http" => [
        'timeout' => 60,
        'user_agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.9) Gecko/20071025 Firefox/3.0.0.1',
        'follow_location' => FALSE,
      ],
    ];

    $title = $omdb_api->label();
    $filename = strtolower(trim(preg_replace('#\W+#', '_', $title), '_'));

    // Get the Module path to get logo path.
    $module_path = $this->extensionPathResolver->getPath('module', 'omdb_api');
    $css = file_get_contents($module_path . '/libraries/css/pdf.css');
    $html = file_get_contents($url, FALSE, stream_context_create($args));

    $dompdf = new Dompdf();

    // $dompdf->loadHtml(Markup::create($pdf_html));
    $dompdf->loadHtml($html);

    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper('A4', 'landscape');

    // Render the HTML as PDF.
    $dompdf->render();
    ob_end_clean();

    $response = new Response();
    $response->setContent($dompdf->output());
    $response->headers->set('Content-Type', 'application/pdf');
    $response->headers->set('Content-Disposition', "attachment; filename={$filename}.pdf");

    return $response;

  }

}
