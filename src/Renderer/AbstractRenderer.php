<?php

namespace PE\Component\Asset\Renderer;

abstract class AbstractRenderer implements RendererInterface
{
    /**
     * Render attributes array to usable in html string
     *
     * @param array $attributes
     *
     * @return string
     */
    protected function renderAttributes(array $attributes)
    {
        $result = '';

        foreach ($attributes as $name => $value) {
            if ($value === true) {
                $result .= " {$name}=\"{$name}\"";
            } else if ($value !== false) {
                $result .= " {$name}=\"{$value}\"";
            }
        }

        return trim($result);
    }
}