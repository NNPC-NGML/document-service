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
        $base64File = base64_encode('Test file content');
        $fileName = 'testfile.txt';

        Storage::shouldReceive('disk->put')->andReturn('documents/testfile.txt');

        $fileObject = $this->documentService->uploadBase64($base64File, $fileName);

        $this->assertInstanceOf(FileObject::class, $fileObject);
        $this->assertEquals('testfile.txt', $fileObject->name);
        $this->assertEquals('txt', $fileObject->extension);
        $this->assertEquals('text/plain', $fileObject->mimetype);
    }

    /**
     * Test uploading an actual file.
     *
     * @return void
     */
    public function testUploadFile()
    {
        $file = UploadedFile::fake()->create('testfile.txt', 1, 'text/plain');

        Storage::shouldReceive('disk->putFileAs')->andReturn('documents/testfile.txt');

        $fileObject = $this->documentService->uploadFile($file);

        $this->assertInstanceOf(FileObject::class, $fileObject);
        $this->assertEquals('testfile.txt', $fileObject->name);
        $this->assertEquals('txt', $fileObject->extension);
        $this->assertEquals('text/plain', $fileObject->mimetype);
        $this->assertEquals(1024, $fileObject->size);
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
