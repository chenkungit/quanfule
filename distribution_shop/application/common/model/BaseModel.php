<?php

namespace app\common\model;


use app\common\Utils\Request;
use think\Model;

class BaseModel extends Model
{
    public static function getInfo(string $index, $value, $fields = '')
    {
        if (!$fields) {
            $fields = '*';
        }
        return static::field($fields)
            ->where($index, $value)
            ->find();
    }




    public static function getPageList(array $pageData = [])
    {

        $page = Request::getPage();
        $pageSize = Request::getLimit();

        $result['count'] = self::count('id');
        $result['pagecount'] = ceil(($result['count'] / $pageSize));
        $result['list'] = self::page($page, $pageSize)->select();
        return $result;
    }

    public static function batchUpdate($argument, $key_name = 'id')
    {
        $sql = "UPDATE  " . static::getTable() . " SET ";

        foreach (current($argument) as $key => $value) {
            $sql .= "{$key} = CASE {$key_name} ";
            foreach ($argument as $id => $item) {
                $sql .= sprintf("WHEN %s THEN %s ", $id, $item[$key]);
            }
            $sql .= "END, ";
        }
        $sql = rtrim(trim($sql), ',');
        $ids = implode(',', array_keys($argument));
        $sql .= " WHERE {$key_name} IN ({$ids})";

        return static::execute($sql);
    }
}