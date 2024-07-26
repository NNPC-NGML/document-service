<?php

namespace App\Models;

/**
 * Class FileObject
 *
 * Represents a file object with properties such as name, extension, size, mimetype, location, and URL.
 */
class FileObject {
    /**
     * @var string The name of the file.
     */
    public string $name;

    /**
     * @var string The extension of the file.
     */
    public string $extension;

    /**
     * @var int The size of the file in bytes.
     */
    public int $size;

    /**
     * @var string The MIME type of the file.
     */
    public string $mimetype;

    /**
     * @var string The location where the file is stored.
     */
    public string $location;

    /**
     * @var string|null The URL to access the file, if available.
     */
    public string|null $url;

    /**
     * FileObject constructor.
     *
     * @param string $name The name of the file.
     * @param string $extension The extension of the file.
     * @param int $size The size of the file in bytes.
     * @param string $mimetype The MIME type of the file.
     * @param string $location The location where the file is stored.
     * @param string|null $url The URL to access the file, if available.
     */
    public function __construct(string $name, string $extension, int $size, string $mimetype, string $location, string|null $url = null) {
        $this->name = $name;
        $this->extension = $extension;
        $this->size = $size;
        $this->mimetype = $mimetype;
        $this->url = $url;
        $this->location = $location;
    }

    /**
     * Creates a new FileObject with the specified properties, retaining the original values for properties that are not provided.
     *
     * @param string|null $name The name of the file.
     * @param string|null $extension The extension of the file.
     * @param int|null $size The size of the file in bytes.
     * @param string|null $mimetype The MIME type of the file.
     * @param string|null $location The location where the file is stored.
     * @param string|null $url The URL to access the file, if available.
     * @return FileObject A new instance of the FileObject class.
     */
    public function copyWith(
        ?string $name = null,
        ?string $extension = null,
        ?int $size = null,
        ?string $mimetype = null,
        ?string $location = null,
        ?string $url = null
    ): FileObject {
        return new FileObject(
            $name ?? $this->name,
            $extension ?? $this->extension,
            $size ?? $this->size,
            $mimetype ?? $this->mimetype,
            $location ?? $this->location,
            $url ?? $this->url
        );
    }

    /**
     * Converts the FileObject properties to an associative array.
     *
     * @return array An associative array containing the file properties.
     */
    public function toArray(): array {
        return [
            "name" => $this->name,
            "extension" => $this->extension,
            "size" => $this->size,
            "mimetype" => $this->mimetype,
            "location" => $this->location,
            "url" => $this->url,
        ];
    }
}
