<?php

namespace App\Services;

use App\Jobs\Document\DocumentCreated;
use App\Jobs\Document\DocumentUpdated;
use App\Models\Document;
use App\Models\FileObject;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class DocumentService
 *
 * Provides services for creating and uploading documents.
 */
class DocumentService
{
    /**
     * Create a new document.
     *
     * @param array $request The request data containing document details and file content.
     * @throws ValidationException If the validation of request data fails.
     */
    public function create(array $request): void
    {
        $validated = $this->validate($request);
        $fileObject = $this->uploadBase64($validated['file_content'], $validated['file_name']);

        // Save document metadata in the database
        $document = Document::create([
            "entity_id" => $validated['entity_id'],
            "entity_name" => $validated['entity_name'],
            "tag" => $validated['tag'],
            "file_object" => $fileObject->toArray(),
            "file_content" => $validated['file_content'],
        ]);
        $data = array_merge($request, $document->toArray());
        DocumentCreated::dispatch($data)->onQueue(config("nnpcreusable.DOCUMENT_TASK_CREATED"));
    }

    /**
     * Update a document.
     *
     * @param array $request The request data containing document details and file content.
     * @throws ValidationException If the validation of request data fails.
     */
    public function update(array $request): void
    {
        $validated = $this->validate($request);
        $fileObject = $this->uploadBase64($validated['file_content'], $validated['file_name']);

        // Save document metadata in the database
        $document = Document::create([
            "entity_id" => $validated['entity_id'],
            "entity_name" => $validated['entity_name'],
            "tag" => $validated['tag'],
            "file_object" => $fileObject->toArray(),
            "file_content" => $validated['file_content'],
        ]);
        $data = array_merge($request, $document->toArray());
        DocumentUpdated::dispatch($data)->onQueue(config("nnpcreusable.DOCUMENT_TASK_UPDATED"));
    }

    /**
     * Upload a base64 encoded file.
     *
     * @param string $base64File The base64 encoded file content.
     * @param string $fileName The name of the file.
     * @return FileObject The file object containing file details.
     */
    public function uploadBase64(string $base64File, string $fileName): FileObject
    {
        // extract file extension
        $exp = explode(".", $fileName);
        $extension = end($exp);

        // Decode the base64 string
        $fileData = base64_decode($base64File);

        // Generate a unique file name
        $tmpFileName = Str::random(10) . '.tmp';

        // Create a temporary file
        $tmpFilePath = sys_get_temp_dir() . '/' . $tmpFileName;
        file_put_contents($tmpFilePath, $fileData);

        // Get the file's mime type and size
        $fileMimeType = mime_content_type($tmpFilePath);
        $fileSize = filesize($tmpFilePath);

        // Determine the storage disk
        $disk = config('filesystems.default');

        // Generate the full path for storing the file
        $path = "documents/{$fileName}";

        // Store the file on the determined disk
        Storage::disk($disk)->put($path, file_get_contents($tmpFilePath));

        // Remove the temporary file
        unlink($tmpFilePath);

        // Create a FileObject instance
        $fileObject = new FileObject(
            $fileName,
            $extension,
            $fileSize,
            $fileMimeType,
            $path
        );

        // return FileObject
        return $fileObject;
    }

    /**
     * Upload a file.
     *
     * @param UploadedFile $file The uploaded file.
     * @return FileObject The file object containing file details.
     */
    public function uploadFile(UploadedFile $file): FileObject
    {
        // Determine the storage disk
        $disk = config('filesystems.default');

        // Store the file on the determined disk
        $path = $file->store('documents', $disk);

        // Create a FileObject instance
        $fileObject = new FileObject(
            $file->getClientOriginalName(),
            $file->getClientOriginalExtension(),
            $file->getSize(),
            $file->getClientMimeType(),
            $path
        );

        // return FileObject
        return $fileObject;
    }

    /**
     * Validate the document data.
     *
     * @param array $data The data to be validated.
     * @return array The validated data.
     * @throws ValidationException If the validation fails.
     */
    public function validate(array $data): array
    {
        $validator = Validator::make($data, [
            "entity_id" => "required|integer|max:255",
            "entity_name" => "required|string|max:255",
            "tag" => "required|string|max:255",
            "file_name" => "required|string",
            "file_content" => "required|string",
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $data;
    }
}
