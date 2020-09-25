<?php

namespace SocialDataBundle\Connector\Facebook\QueryBuilder;

class GraphEdge extends GraphNode
{
    /**
     * @return array
     */
    public function toEndpoints()
    {
        $endpoints = [];

        $children = $this->getChildEdges();
        foreach ($children as $child) {
            $endpoints[] = sprintf('/%s', implode('/', $child));
        }

        return $endpoints;
    }

    /**
     * @return array
     */
    public function getChildEdges()
    {
        $edges = [];
        $hasChildren = false;

        foreach ($this->fields as $v) {

            if ($v instanceof GraphEdge) {
                $hasChildren = true;

                $children = $v->getChildEdges();
                foreach ($children as $childEdges) {
                    $edges[] = array_merge([$this->name], $childEdges);
                }
            }
        }

        if (!$hasChildren) {
            $edges[] = [$this->name];
        }

        return $edges;
    }

    public function compileModifiers()
    {
        if (count($this->modifiers) === 0) {
            return;
        }

        $processed_modifiers = [];

        foreach ($this->modifiers as $k => $v) {
            $processed_modifiers[] = sprintf('%s(%s)', urlencode($k), urlencode($v));
        }

        $this->compiledValues[] = sprintf('.%s', implode('.', $processed_modifiers));
    }

    public function compileFields()
    {
        if (count($this->fields) === 0) {
            return;
        }

        $processed_fields = [];

        foreach ($this->fields as $v) {
            $processed_fields[] = $v instanceof GraphEdge ? $v->asUrl() : urlencode($v);
        }

        $this->compiledValues[] = sprintf('{%s}', implode(',', $processed_fields));
    }

    /**
     * @return string
     */
    public function compileUrl()
    {
        $append = '';

        if (count($this->compiledValues) > 0) {
            $append = implode('', $this->compiledValues);
        }

        return sprintf('%s%s', $this->name, $append);
    }
}
