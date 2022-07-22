<?php

namespace Drupal\proedm\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Noticia entities.
 *
 * @ingroup proedm
 */
interface NoticiaInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Noticia name.
   *
   * @return string
   *   Name of the Noticia.
   */
  public function getName();

  /**
   * Sets the Noticia name.
   *
   * @param string $name
   *   The Noticia name.
   *
   * @return \Drupal\proedm\Entity\NoticiaInterface
   *   The called Noticia entity.
   */
  public function setName($name);

  /**
   * Gets the Noticia creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Noticia.
   */
  public function getCreatedTime();

  /**
   * Sets the Noticia creation timestamp.
   *
   * @param int $timestamp
   *   The Noticia creation timestamp.
   *
   * @return \Drupal\proedm\Entity\NoticiaInterface
   *   The called Noticia entity.
   */
  public function setCreatedTime($timestamp);

}
