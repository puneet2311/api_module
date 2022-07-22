<?php

/**
 * @file
 * Contains mensaje.page.inc.
 *
 * Page callback for Mensaje entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Mensaje templates.
 *
 * Default template: mensaje.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_mensaje(array &$variables) {
  // Fetch Mensaje Entity Object.
  $mensaje = $variables['elements']['#mensaje'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
