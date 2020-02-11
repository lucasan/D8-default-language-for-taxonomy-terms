<?php

namespace Drupal\prevent_translation\Entity;


use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityRepository;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Plugin\Context\ContextRepositoryInterface;
use Drupal\Core\Routing\AdminContext;

/**
 * Class DecorateEntityRepository
 * @package Drupal\prevent_translation\Entity
 * @author Luke Torres <mail@lucastorres.co>
 */
class DecorateEntityRepository extends EntityRepository {
  /**
   * The admin context.
   *
   * @var AdminContext
   */
  protected $adminContext;

  /**
   * @var LanguageManagerInterface
   */
  protected $language_manager;

  /**
   * DecorateEntityRepository constructor.
   *
   * @param AdminContext $adminContext
   * @param EntityTypeManagerInterface $entity_type_manager
   * @param LanguageManagerInterface $language_manager
   * @param ContextRepositoryInterface|NULL $context_repository
   */
  public function __construct(AdminContext $adminContext,
                              EntityTypeManagerInterface $entity_type_manager,
                              LanguageManagerInterface $language_manager,
                              ContextRepositoryInterface $context_repository = NULL) {
    $this->adminContext = $adminContext;
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
    // Inject the default language only for Taxonomy terms in Admin pages
    if ($entity instanceof Term && $this->adminContext->isAdminRoute()) {
      $langcode = $this->language_manager->getDefaultLanguage()->getId();
    }

    return parent::getTranslationFromContext($entity, $langcode, $context);
  }
}
