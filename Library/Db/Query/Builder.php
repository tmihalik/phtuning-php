<?php

namespace Phalcon\Db\Query;

use Phalcon\Mvc\Model\Query\Builder as ModelQueryBuilder;

/**
 * Class Builder
 *
 * @package Phalcon\Db\Query
 */
class Builder extends ModelQueryBuilder
{
    protected $_columns = '*';

    /**
     * @param array $placeholders
     * @return \Phalcon\Db\ResultInterface
     */
    public function execute(array $placeholders = null)
    {
        $sqlString = str_replace(['[', ']'], '', $this->getPhql());

        $sqlString = preg_replace('/(:[\w]*)(:)/m', '$1', $sqlString);

        $params = (array) $placeholders + (array) $this->_bindParams;

        return $this->getDI()->get('db')->query($sqlString, $params);
    }
}
