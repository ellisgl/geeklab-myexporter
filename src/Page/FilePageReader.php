<?php
declare(strict_types=1);

namespace App\Page;


class FilePageReader implements PageReader
{
    private string $pageFolder;

    public function __construct(string $pageFolder)
    {
        $this->pageFolder = $pageFolder;
    }

    /**
     * @param string $slug
     *
     * @return string
     * @throws InvalidPageException
     */
    public function readBySlug(string $slug): string
    {
        $path = "$this->pageFolder/$slug.md";

        if (!file_exists($path)) {
            throw new InvalidPageException($slug);
        }

        return file_get_contents($path);
    }
}
