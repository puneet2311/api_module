<?php

/**
 * @file
 * Contains mesa.page.inc.
 *
 * Page callback for Mesa entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Mesa templates.
 *
 * Default template: mesa.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_mesa(array &$variables) {
  // Fetch Mesa Entity Object.
  $mesa = $variables['elements']['#mesa'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
