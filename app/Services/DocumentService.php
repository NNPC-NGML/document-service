<?php

namespace App\Services;

use App\Http\Resources\DocumentResource;
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
        $data = $this->validate($request);
        $fileObject = $this->uploadBase64($data['file_content'], $data['file_name']);

        // Save document metadata in the database
        $document = Document::create([
            "entity_id" => $request['entity_id'],
            "entity_name" => $request['entity_name'],
            "tag" => $request['tag'],
            "file_object" => $fileObject->toArray(),
            "file_content" => $request['file_content'],
        ]);
        $data = new DocumentResource($document);

        // TODO
        // dispatch a job to send a response with their respective tage on ($data)
    }

    /**
     * Update a document.
     *
     * @param array $request The request data containing document details and file content.
     * @throws ValidationException If the validation of request data fails.
     */
    public function update(array $request): void
    {
        // TODO
    }

    /**
     * Delete a document.
     *
     * @param int $id The request id of the document.
     * @throws ValidationException If the validation of request data fails.
     */
    public function delete(int $id): void
    {
        // TODO
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

        // Store the file on S3
        $path = Storage::disk('s3')->put('documents', $tmpFilePath);

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
        // Store the file on S3
        $path = $file->store('documents', 's3');

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
