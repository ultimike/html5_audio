<?php

namespace Drupal\Tests\html5_audio\Functional;

use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\link\LinkItemInterface;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests Html5 Audio functionality.
 *
 * @group html5_audio
 */
class Html5AudioTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'entity_test',
    'link',
    'html5_audio',
  ];

  /**
   * A test user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $testUser;

  /**
   * The test link field machine name.
   *
   * @var string
   */
  protected $testFieldName;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Create and log in an administrative user.
    $this->testUser = $this->drupalCreateUser([
      'view test entity',
      'administer entity_test content',
    ]);

    // Create a machine name for the link field.
    $this->testFieldName = mb_strtolower($this->randomMachineName());
    // Create a link field for entity_test entities.
    $fieldStorage = FieldStorageConfig::create([
      'field_name' => $this->testFieldName,
      'entity_type' => 'entity_test',
      'type' => 'link',
      'cardinality' => 2,
    ]);
    $fieldStorage->save();

    // Associate the link field with an entity type bundle.
    $field = FieldConfig::create([
      'field_storage' => $fieldStorage,
      'bundle' => 'entity_test',
      'settings' => [
        'title' => DRUPAL_DISABLED,
        'link_type' => LinkItemInterface::LINK_GENERIC,
      ],
    ]);
    $field->save();

    // Set the form and view displays.
    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $display_repository */
    $display_repository = \Drupal::service('entity_display.repository');
    // Set the link field widget.
    $display_repository->getFormDisplay('entity_test', 'entity_test')
      ->setComponent($this->testFieldName, [
        'type' => 'link_default',
      ])
      ->save();
    // Set the link field formatter.
    $display_repository->getViewDisplay('entity_test', 'entity_test', 'full')
      ->setComponent($this->testFieldName, [
        'type' => 'html5_audio_formatter',
      ])
      ->save();
  }

  /**
   * Tests Html5 Audio functionality.
   */
  public function testHtml5Audio() {
    $this->drupalLogin($this->testUser);

    // Set up handy variables for our test page and user session.
    $page = $this->getSession()->getPage();
    $assert_session = $this->assertSession();

    // Load, populate, and submit the entity creation page.
    $this->drupalGet('entity_test/add');
    $page->fillField("{$this->testFieldName}[0][uri]", 'http://podcasts.drupaleasy.com/DrupalEasy_ep175_20160510.ogg');
    $page->fillField("{$this->testFieldName}[1][uri]", 'https://drupaleasy.podbean.com/mf/play/g7em5f/DrupalEasy_ep175_20160510.mp3');
    $page->pressButton('Save');

    // Use entity query to get the ID of the just created entity.
    $entity_id = $this->container
      ->get('entity_type.manager')
      ->getStorage('entity_test')
      ->getQuery()
      ->accessCheck(FALSE)
      ->sort('id', 'DESC')
      ->execute();
    $entity_id = reset($entity_id);

    // Load the created entity.
    $this->drupalGet('/entity_test/' . $entity_id);

    // Check to make sure there are two "source" HTML elements within the
    // "audio" element.
    $assert_session->elementsCount('css', 'audio source', 2);

    // Ensure the "source" element "src" values are correct.
    $assert_session->elementExists('css', 'audio source[src="http://podcasts.drupaleasy.com/DrupalEasy_ep175_20160510.ogg"]');
    $assert_session->elementExists('css', 'audio source[src="https://drupaleasy.podbean.com/mf/play/g7em5f/DrupalEasy_ep175_20160510.mp3"]');
  }

}
