<?php

/**
 * @file
 * Contains \Drupal\html5_audio\Plugin\Field\FieldFormatter\Html5AudioFieldFormatter.
 */

namespace Drupal\html5_audio\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'html5audio_field_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "html5audio_field_formatter",
 *   label = @Translation("HTML5 Audio"),
 *   field_types = {
 *     "link"
 *   }
 * )
 */
class Html5AudioFieldFormatter extends FormatterBase {
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      // Implement default settings.
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return array(
      // Implement settings form.
    ) + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Implement settings summary.

    return $summary;
  }

/**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    // Render all field values as part of a single <audio> tag.
    $sources = array();
    foreach ($items as $delta => $item) {
      // Get the mime type.
      $mimetype = \Drupal::service('file.mime_type.guesser')->guess($item->uri);
      $sources[] = array(
        'src' => $item->uri,
        'mimetype' => $mimetype,
      );
   }

   // Put everything in an array for theming.
    $elements[] = array(
      '#theme' => 'html5_audio',
      '#sources' => $sources,
    );

   return $elements;
  }

}
