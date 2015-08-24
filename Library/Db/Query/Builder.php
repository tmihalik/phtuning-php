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
        $sqlString = $this->getPhql();

        $di = $this->getDI();

        /**
         * @var $mm \Phalcon\Mvc\Model\Manager
         */
        $mm = $di->get('modelsManager');

        /**
         * @var $db \Phalcon\Db\Adapter\Pdo\Postgresql
         */
        $db = $di->get('db');

        $sqlString = preg_replace_callback('/\[([^\]]*)\]/m', function (array $matches) use ($mm) {
            if (strpos($matches[1], '\\') !== false) {
                $model = $mm->load($matches[1]);

                $schema = $model->getSchema();
                $table = $model->getSource();

                return $schema ? "$schema.$table" : $table;
            }

            return $matches[1];

        }, $sqlString);

        $sqlString = preg_replace('/(:[\w]*)(:)/m', '$1', $sqlString);

        $bindParams = (array) $placeholders + (array) $this->_bindParams;

        return $db->query($sqlString, $bindParams);
    }
}
