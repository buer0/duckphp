# DB\DBAdvanceTrait

## 简介
DBAdvanceTrait 这个 trait 给 DB类提供了常用的 高级的 DB 方法
## 选项

## 方法

    public function quoteIn($array)
    public function quoteSetArray($array)
    public function qouteInsertArray($array)
    public function findData($table_name, $id, $key = 'id')
    public function insertData($table_name, $data, $return_last_id = true)
    public function deleteData($table_name, $id, $key = 'id', $key_delete = 'is_deleted')
    public function updateData($table_name, $id, $data, $key = 'id')
## 详解

