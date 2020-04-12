<?php declare(strict_types = 1);

namespace Drupal\html5_audio\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'HTML5 Audio' formatter.
 *
 * @FieldFormatter(
 *   id = "html5_audio_formatter",
 *   label = @Translation("HTML5 Audio"),
 *   field_types = {
 *     "link"
 *   }
 * )
 */
final class Html5AudioFieldFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      // Implement default settings.
      'autoplay' => '0',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $elements['autoplay'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Autoplay enabled'),
      '#default_value' => $this->getSetting('autoplay'),
    );

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Implement settings summary.
    $settings = $this->getSettings();
    if ($settings['autoplay']) {
      $summary[] = t('Autoplay is enabled.');
    }
    else {
      $summary[] = t('Autoplay is not enabled.');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      // Get the mime type. This method for calling a service is **not** using
      // dependency injection.
      $mimetype = \Drupal::service('file.mime_type.guesser')->guess($item->uri);

      $sources[] = array(
        'src' => $item->uri,
        'mimetype' => $mimetype,
      );
    }

    // Configuration
    $autoplay = '';
    if ($this->getSetting('autoplay')) {
      $autoplay = 'autoplay';
    }

    // Create render array for theming.
    $elements[] = array(
      '#theme' => 'audio_tag',
      '#sources' => $sources,
      '#autoplay' => $autoplay,
    );

    return $elements;
  }

}
