<?php

namespace Drupal\proedm\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Mensaje entities.
 *
 * @ingroup proedm
 */
interface MensajeInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Mensaje name.
   *
   * @return string
   *   Name of the Mensaje.
   */
  public function getName();

  /**
   * Sets the Mensaje name.
   *
   * @param string $name
   *   The Mensaje name.
   *
   * @return \Drupal\proedm\Entity\MensajeInterface
   *   The called Mensaje entity.
   */
  public function setName($name);

  /**
   * Gets the Mensaje creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Mensaje.
   */
  public function getCreatedTime();

  /**
   * Sets the Mensaje creation timestamp.
   *
   * @param int $timestamp
   *   The Mensaje creation timestamp.
   *
   * @return \Drupal\proedm\Entity\MensajeInterface
   *   The called Mensaje entity.
   */
  public function setCreatedTime($timestamp);

}
