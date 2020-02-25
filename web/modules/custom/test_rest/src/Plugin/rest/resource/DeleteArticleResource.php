<?php

namespace Drupal\test_rest\Plugin\rest\resource;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\node\Entity\Node;

/**
 * Provides a resource to create new article.
 *
 * @RestResource(
 *   id = "delete_article_resource",
 *   label = @Translation("Delete article resource"),
 *   uri_paths = {
 *     "create" = "/api/v1/delete-article",
 *   }
 * )
 */
class DeleteArticleResource extends ResourceBase {

  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a new CreateArticleResource object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   A current user instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);

    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('test_rest'),
      $container->get('current_user')
    );
  }

  /**
   * Responds to POST requests.
   */
  public function post($entity_data) {
    if (!$this->currentUser->hasPermission('delete article content')) {
      throw new AccessDeniedHttpException();
    }

    try {
      $entity_data['type'] = 'article';
      \Drupal::entityTypeManager()
        ->getStorage('node')
        ->delete($entity_data);
    }
    catch (\Exception $e) {
      return new ResourceResponse('Something went wrong during entity creation. Check your data.', 400);
    }
  }

}
