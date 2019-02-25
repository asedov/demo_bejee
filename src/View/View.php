<?php
declare(strict_types=1);

namespace App\View;

/**
 * Class View
 *
 * @package App\View
 */
final class View implements ViewInterface
{
    /** @var array */
    private $content = [];

    /** @var string */
    private $html = '';

    /**
     * @inheritDoc
     */
    public function set(string $name, $value): ViewInterface
    {
        $this->content[$name] = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(string $template): string
    {
        extract($this->content);

        ob_start();
        include __DIR__ . '/../Template/' . $template . '.phtml';
        $this->html = ob_get_clean();

        return $this->renderLayout();
    }

    /**
     * @return string
     */
    private function renderLayout(): string
    {
        extract($this->content);

        ob_start();
        include __DIR__ . '/../Template/_layout.phtml';

        return ob_get_clean();
    }
}
