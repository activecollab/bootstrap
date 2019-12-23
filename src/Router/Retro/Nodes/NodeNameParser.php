<?php

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router\Retro\Nodes;

class NodeNameParser implements NodeNameParserInterface
{
    private $node_name = '';
    private $extension = '';
    private $is_hidden = false;
    private $is_system = false;
    private $is_variable = false;

    public function __construct(string $basename)
    {
        $bits = explode('.', $basename);

        if (empty($bits[0])) {
            $this->is_hidden = true;

            unset($bits[0]);
            $bits = array_values($bits);
        }

        if (count($bits) > 1) {
            $this->extension = $bits[count($bits) - 1];
        }

        if (mb_substr($bits[0], 0, 2) === '__') {
            if (mb_substr($bits[0], -2) === '__') {
                $this->node_name = mb_substr($bits[0], 2, mb_strlen($bits[0]) - 4);
                $this->is_variable = true;
            } else {
                $this->node_name = mb_substr($bits[0], 2);
                $this->is_system = true;
            }
        } else {
            $this->node_name = $bits[0];
        }
    }

    public function getFileProperties(): array
    {
        return [
            $this->node_name,
            $this->extension,
            $this->is_hidden,
            $this->is_system,
            $this->is_variable,
        ];
    }

    public function getDirectoryProperties(): array
    {
        return [
            $this->node_name,
            $this->is_hidden,
            $this->is_system,
            $this->is_variable,
        ];
    }
}