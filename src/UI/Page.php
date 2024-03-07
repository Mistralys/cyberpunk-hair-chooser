<?php

declare(strict_types=1);

namespace Mistralys\CPHairChooser\UI;

use AppUtils\Interfaces\RenderableInterface;
use AppUtils\Interfaces\StringableInterface;
use AppUtils\Traits\RenderableBufferedTrait;

class Page implements RenderableInterface
{
    use RenderableBufferedTrait;

    private string $navLabel;
    private string $includeFile;
    private string $abstract = '';
    private string $title = '';
    private bool $inNav = true;
    private string $id;

    public function __construct(string $id, string $navLabel, string $includeFile)
    {
        $this->id = $id;
        $this->navLabel = $navLabel;
        $this->includeFile = $includeFile;
    }

    public function getID(): string
    {
        return $this->id;
    }

    public function getNavLabel(): string
    {
        return $this->navLabel;
    }

    public function setAbstract(string|int|float|StringableInterface|NULL $abstract): self
    {
        $this->abstract = (string)$abstract;
        return $this;
    }

    public function setTitle(string|int|float|StringableInterface|NULL $title): self
    {
        $this->title = (string)$title;
        return $this;
    }

    public function getAbstract(): string
    {
        return $this->abstract;
    }

    public function setInNav(bool $inNav): self
    {
        $this->inNav = $inNav;
        return $this;
    }

    public function isInNav() : bool
    {
        return $this->inNav;
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    protected function generateOutput(): void
    {
        include $this->includeFile;
    }

    public function getAdminURL(array $params=array()) : string
    {
        $params['page'] = $this->getID();

        return '?'.http_build_query($params);
    }
}