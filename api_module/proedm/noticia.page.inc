<?php

/**
 * @file
 * Contains noticia.page.inc.
 *
 * Page callback for Noticia entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Noticia templates.
 *
 * Default template: noticia.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_noticia(array &$variables) {
  // Fetch Noticia Entity Object.
  $noticia = $variables['elements']['#noticia'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
