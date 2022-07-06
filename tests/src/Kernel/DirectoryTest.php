<?php

namespace Drupal\Tests\omdb_api\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Core\File\FileSystemInterface;

/**
 * OMDB API Module Directory Test.
 *
 * @group omdb_api
 */
class DirectoryTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'user',
    'system',
    'node',
    'block',
    'path_alias',
    'options',
    'taxonomy',
    'omdb_api',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {

    parent::setUp();

    $this->installConfig(['user', 'omdb_api']);
    $this->installSchema('system', ['sequences']);
    $this->installSchema('omdb_api', []);
    $this->installEntitySchema('user');
  }

  /**
   * Test QR Codes Directory.
   *
   * @covers ::directory creation, permissions
   */
  public function testQrCodesDirectory() {

    /** @var \Drupal\Core\File\FileSystemInterface $fileSystem */
    $fileSystem = $this->container->get('file_system');
    // Set up the required directories and files.
    $directory = 'public://omdb-api/qrcodes';
    $this->assertDirectoryDoesNotExist($directory);
    $fileSystem->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS);
    $fileSystem->saveData('File unrelated to OMDB API', 'public://omdb-api/file.txt');
    $fileSystem->saveData('File unrelated to OMDB API', 'public://file.txt');
    $fileSystem->saveData('Test contents for OMDB API', 'public://omdb-api/qrcodes/test-img.png');

    $this->assertDirectoryDoesNotExist('public://omdb-api/test');
    $this->assertDirectoryExists('public://omdb-api');
    $this->assertDirectoryExists('public://omdb-api/qrcodes');
    $this->assertFileExists('public://file.txt');
  }

}
