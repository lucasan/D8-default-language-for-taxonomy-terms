<?php

namespace Drupal\prevent_translation\Entity;


use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityRepository;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Plugin\Context\ContextRepositoryInterface;

/**
 * Class DecorateEntityRepository
 * @package Drupal\prevent_translation\Entity
 * @author Luke Torres <mail@lucastorres.co>
 */
class DecorateEntityRepository extends EntityRepository {
  /**
   * @var CurrentRouteMatch
   */
  protected $route_match;

  /**
   * @var LanguageManagerInterface
   */
  protected $language_manager;

  /**
   * DecorateEntityRepository constructor.
   *
   * @param CurrentRouteMatch $route_match
   * @param EntityTypeManagerInterface $entity_type_manager
   * @param LanguageManagerInterface $language_manager
   * @param ContextRepositoryInterface|NULL $context_repository
   */
  public function __construct(CurrentRouteMatch $route_match,
                              EntityTypeManagerInterface $entity_type_manager,
                              LanguageManagerInterface $language_manager,
                              ContextRepositoryInterface $context_repository = NULL) {
    $this->route_match = $route_match;
    $this->language_manager = $language_manager;

    parent::__construct($entity_type_manager, $language_manager, $context_repository);
  }

  /**
   * Decorates EntityRepository::getTranslationFromContext to inject the default language in admin pages for Taxonomy terms
   *
   * @param EntityInterface $entity
   * @param null $langcode
   * @param array $context
   * @return EntityInterface|\Drupal\Core\TypedData\TranslatableInterface
   */
  public function getTranslationFromContext(EntityInterface $entity, $langcode = NULL, $context = []) {
    $route_name = $this->route_match->getCurrentRouteMatch()->getRouteName();

    // Inject the default language only for Taxonomy terms in Node Edit form
    if ($entity instanceof Term && 'entity.node.edit_form' === $route_name) {
      $langcode = $this->language_manager->getDefaultLanguage()->getId();
    }

    return parent::getTranslationFromContext($entity, $langcode, $context);
  }
}
