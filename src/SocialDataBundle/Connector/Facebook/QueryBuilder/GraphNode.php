<?php

namespace SocialDataBundle\Connector\Facebook\QueryBuilder;

class GraphNode
{
    /**
     * @const string
     */
    const PARAM_FIELDS = 'fields';

    /**
     * @const string
     */
    const PARAM_LIMIT = 'limit';

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $modifiers = [];

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var array
     */
    protected $compiledValues = [];

    /**
     * @param string $name
     * @param array  $fields
     * @param int    $limit
     */
    public function __construct($name, $fields = [], $limit = 0)
    {
        $this->name = $name;

        $this->fields($fields);

        if ($limit > 0) {
            $this->limit($limit);
        }
    }

    /**
     * @param array $data
     *
     * @return GraphNode
     */
    public function modifiers(array $data)
    {
        $this->modifiers = array_merge($this->modifiers, $data);

        return $this;
    }

    /**
     * @return array
     */
    public function getModifiers()
    {
        return $this->modifiers;
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public function getModifier($key)
    {
        return isset($this->modifiers[$key]) ? $this->modifiers[$key] : null;
    }

    /**
     * @param int $limit
     *
     * @return GraphNode
     */
    public function limit($limit)
    {
        return $this->modifiers([
            static::PARAM_LIMIT => $limit,
        ]);
    }

    /**
     * @return int|null
     */
    public function getLimit()
    {
        return $this->getModifier(static::PARAM_LIMIT);
    }

    /**
     * @param mixed $fields
     *
     * @return GraphNode
     */
    public function fields($fields)
    {
        if (!is_array($fields)) {
            $fields = func_get_args();
        }

        $this->fields = array_merge($this->fields, $fields);

        return $this;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    public function resetCompiledValues()
    {
        $this->compiledValues = [];
    }

    public function compileModifiers()
    {
        if (count($this->modifiers) === 0) {
            return;
        }

        $this->compiledValues[] = http_build_query($this->modifiers, '', '&');
    }

    public function compileFields()
    {
        if (count($this->fields) === 0) {
            return;
        }

        $this->compiledValues[] = sprintf('%s=%s', static::PARAM_FIELDS, implode(',', $this->fields));
    }

    /**
     * @return string
     */
    public function compileUrl()
    {
        $append = '';
        if (count($this->compiledValues) > 0) {
            $append = sprintf('?%s', implode('&', $this->compiledValues));
        }

        return sprintf('/%s%s', $this->name, $append);
    }

    /**
     * @return string
     */
    public function asUrl()
    {
        $this->resetCompiledValues();
        $this->compileModifiers();
        $this->compileFields();

        return $this->compileUrl();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->asUrl();
    }
}
