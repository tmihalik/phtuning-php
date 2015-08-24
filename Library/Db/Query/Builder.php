<?php

namespace Phalcon\Db\Query;

/**
 * Class Builder
 *
 * @package Phalcon\Db\Query
 */
class Builder extends \Phalcon\Mvc\Model\Query\Builder
{
    protected $_columns = '*';

    /**
     * @param array $placeholders
     * @return \Phalcon\Db\ResultInterface
     */
    public function execute(array $placeholders = null)
    {
        $sqlString = $this->getPhql();

        $bindParams = (array) $placeholders + (array) $this->_bindParams;

        $di = $this->getDI();

        /**
         * @var $mm \Phalcon\Mvc\Model\Manager
         */
        $mm = $di->get('modelsManager');

        /**
         * @var $db \Phalcon\Db\Adapter\Pdo\Postgresql
         */
        $db = $di->get('db');

        /**
         * Replace model names to table names
         * [App\Models\Hirdetes\Hirdetesek] -> hirdetes.hirdetesek
         */
        $sqlString = preg_replace_callback('/\[([^\]]*)\]/m', function (array $matches) use ($mm) {
            if (strpos($matches[1], '\\') !== false) {
                $model = $mm->load($matches[1]);

                $schema = $model->getSchema();
                $table = $model->getSource();

                return $schema ? "$schema.$table" : $table;
            }

            return $matches[1];

        }, $sqlString);

        /**
         * Replace PHQL placeholders to PDO placeholders
         * :name: -> :name
         */
        $sqlString = preg_replace('/(:[\w]*)(:)/m', '$1', $sqlString);

        /**
         * Replace new PHQL placeholders to PDO placeholders
         * {nev} -> :nev
         * {id:int} -> :id
         * {ids:array} -> :ids0, :ids1
         */
        $sqlString = preg_replace_callback('/\{([^\}]*)\}/m', function (array $matches) use (&$bindParams) {
            if (strpos($matches[1], ':') !== false) {
                list($key, $type) = explode(':', $matches[1]);

                if ($type == 'array') {
                    $result = [];

                    foreach ($bindParams[$key] as $k => $bindParam) {
                        $newkey = $key.'_'.$k;
                        $bindParams[$newkey] = $bindParam;
                        $result[] = ':'.$newkey;
                    }

                    unset($bindParams[$key]);

                    return implode(', ', $result);

                } elseif ('int') {
                    $bindParams[$key] = intval($bindParams[$key]);
                }

                return ':'.$key;
            }

            return ':'.$matches[1];

        }, $sqlString);

        return $db->query($sqlString, $bindParams);
    }
}
