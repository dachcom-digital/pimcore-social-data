<?php

namespace SocialDataBundle\Connector\Facebook\QueryBuilder;

class FacebookQueryBuilder
{
    /**
     * @var GraphNode
     */
    protected $graphNode;

    /**
     * @param string|null $graphEndpoint
     */
    public function __construct($graphEndpoint = '')
    {
        if (isset($graphEndpoint)) {
            $this->graphNode = new GraphNode($graphEndpoint);
        }
    }

    /**
     * @param string $graphNodeName
     *
     * @return FacebookQueryBuilder
     */
    public function node($graphNodeName)
    {
        return new static($graphNodeName);
    }

    /**
     * @param string $edgeName
     * @param array  $fields
     *
     * @return GraphEdge
     */
    public function edge($edgeName, array $fields = [])
    {
        return new GraphEdge($edgeName, $fields);
    }

    /**
     * @param array|string $fields
     *
     * @return FacebookQueryBuilder
     */
    public function fields($fields)
    {
        if (!is_array($fields)) {
            $fields = func_get_args();
        }

        $this->graphNode->fields($fields);

        return $this;
    }

    /**
     * @param int $limit
     *
     * @return FacebookQueryBuilder
     */
    public function limit($limit)
    {
        $this->graphNode->limit($limit);

        return $this;
    }

    /**
     * @param array $data
     *
     * @return FacebookQueryBuilder
     */
    public function modifiers(array $data)
    {
        $this->graphNode->modifiers($data);

        return $this;
    }

    /**
     * @return string
     */
    public function asEndpoint()
    {
        return $this->graphNode->asUrl();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->asEndpoint();
    }
}
