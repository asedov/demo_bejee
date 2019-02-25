<?php
declare(strict_types=1);

namespace App\View;

/**
 * Interface ViewInterface
 *
 * @package App\View
 */
interface ViewInterface
{
    /**
     * @param string $name
     * @param mixed  $value
     * @return ViewInterface
     */
    public function set(string $name, $value): ViewInterface;

    /**
     * @param string $template
     * @return string
     */
    public function render(string $template): string;
}
