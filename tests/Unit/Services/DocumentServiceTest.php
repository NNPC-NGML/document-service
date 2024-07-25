<?php

namespace Tests\Unit\Services;

use App\Services\DocumentService;
use App\Models\FileObject;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Faker\Factory as Faker;

/**
 * Class DocumentServiceTest
 *
 * Unit tests for the DocumentService class.
 *
 * @package Tests\Unit\Services
 */
class DocumentServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @var DocumentService
     */
    protected $documentService;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->documentService = new DocumentService();
    }

    /**
     * Test successful creation of a document.
     *
     * @return void
     */
    public function testCreateSuccess()
    {
        $request = [
            'entity_id' => 1,
            'entity_name' => 'TestEntity',
            'tag' => 'TestTag',
            'file_name' => 'testfile.txt',
            'file_content' => base64_encode('Test file content')
        ];

        Storage::shouldReceive('disk->put')->andReturn('documents/testfile.txt');

        $this->documentService->create($request);

        $this->assertDatabaseHas('documents', [
            'entity_id' => 1,
            'entity_name' => 'TestEntity',
            'tag' => 'TestTag'
        ]);
    }

    /**
     * Test that validation fails during document creation.
     *
     * @return void
     */
    public function testCreateValidationFails()
    {
        $this->expectException(ValidationException::class);

        $request = [
            'entity_id' => null,
            'entity_name' => '',
            'tag' => '',
            'file_name' => 'testfile.txt',
            'file_content' => base64_encode('Test file content')
        ];

        $this->documentService->create($request);
    }

    /**
     * Test uploading a base64 encoded file.
     *
     * @return void
     */
    public function testUploadBase64()
    {
        $faker = Faker::create();
        $content = $faker->text;
        $base64File = base64_encode($content);
        $fileName = 'testfile.txt';

        // Mock the storage
        Storage::shouldReceive('disk->put')
            ->once()
            ->with('documents/testfile.txt', $content)
            ->andReturn('documents/testfile.txt');

        $fileObject = $this->documentService->uploadBase64($base64File, $fileName);

        $this->assertInstanceOf(FileObject::class, $fileObject);
        $this->assertEquals($fileName, $fileObject->name);
        $this->assertEquals('txt', $fileObject->extension);
        $this->assertEquals(strlen($content), $fileObject->size);
        $this->assertEquals('text/plain', $fileObject->mimetype);
    }

    /**
     * Test uploading an actual file.
     *
     * @return void
     */
    public function testUploadFile()
    {
        $faker = Faker::create();
        $fileName = 'testfile.txt';
        $fileContent = $faker->text;

        // Create a fake uploaded file
        $file = UploadedFile::fake()->createWithContent($fileName, $fileContent);

        // Define the path where the file will be stored
        $path = "documents/{$fileName}";

        // Store the file on the fake disk
        Storage::disk(config('filesystems.default'))->put($path, $fileContent);

        // Call the method being tested
        $fileObject = $this->documentService->uploadFile($file);

        // Assertions
        $this->assertInstanceOf(FileObject::class, $fileObject);
        $this->assertEquals($fileName, $fileObject->name);
        $this->assertEquals('txt', $fileObject->extension);
        $this->assertEquals(strlen($fileContent), $fileObject->size);
        $this->assertEquals($file->getMimeType(), $fileObject->mimetype);
    }

    /**
     * Test successful validation of document data.
     *
     * @return void
     */
    public function testValidateSuccess()
    {
        $data = [
            'entity_id' => 1,
            'entity_name' => 'TestEntity',
            'tag' => 'TestTag',
            'file_name' => 'testfile.txt',
            'file_content' => base64_encode('Test file content')
        ];

        $validatedData = $this->documentService->validate($data);

        $this->assertEquals($data, $validatedData);
    }

    /**
     * Test validation failure of document data.
     *
     * @return void
     */
    public function testValidateFails()
    {
        $this->expectException(ValidationException::class);

        $data = [
            'entity_id' => null,
            'entity_name' => '',
            'tag' => '',
            'file_name' => 'testfile.txt',
            'file_content' => base64_encode('Test file content')
        ];

        $this->documentService->validate($data);
    }
}
