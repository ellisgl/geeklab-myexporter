<?php

declare(strict_types = 1);

namespace App\Page;

interface PageReader
{
    /**
     * @param string $slug
     *
     * @return string
     * @throws InvalidPageException
     */
    public function readBySlug(string $slug) : string;
}
