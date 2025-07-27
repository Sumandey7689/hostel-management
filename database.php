<?php
$sessionLifetime = 30 * 24 * 60 * 60;
ini_set('session.gc_maxlifetime', $sessionLifetime);
session_set_cookie_params($sessionLifetime);
session_write_close();
session_start();

class Database
{
    private $host;
    private $dbusername;
    private $dbpassword;
    private $dbname;
    private $con;

    public function __construct()
    {
        $this->host = 'localhost';
        $this->dbusername = 'root';
        $this->dbpassword = '';
        $this->dbname = 'shanti_hostel';
        $this->con = new mysqli($this->host, $this->dbusername, $this->dbpassword, $this->dbname);
        if ($this->con->connect_error) {
            die("Connection failed: " . $this->con->connect_error);
        }
    }

    public function getConnection()
    {
        return $this->con;
    }

    public function getData($table, $field = '*', $condition_arr = [], $order_by_field = '', $order_by_type = 'desc', $limit = '')
    {
        $sql = "SELECT $field FROM $table";
        if (!empty($condition_arr)) {
            $sql .= ' WHERE ' . $this->buildCondition($condition_arr);
        }
        if ($order_by_field != '') {
            $sql .= " ORDER BY $order_by_field $order_by_type";
        }
        if ($limit != '') {
            $sql .= " LIMIT $limit";
        }

        $result = $this->con->query($sql);
        if ($result) {
            $arr = $result->fetch_all(MYSQLI_ASSOC);
            return $arr;
        } else {
            return [];
        }
    }

    public function joinTables($table1, $table2, $table1joinColumn, $table2joinColumn, $conditions, $orderByColumn = null, $orderByDirection = 'ASC')
    {
        $selectColumns = '*';
        $whereConditions = $this->buildCondition($conditions);
        $orderByClause = '';

        if ($orderByColumn) {
            $orderByClause = "ORDER BY $orderByColumn $orderByDirection";
        }

        $query = "SELECT $selectColumns FROM $table1
              JOIN $table2 ON $table1joinColumn = $table2joinColumn
              WHERE $whereConditions
              $orderByClause";

        $result = $this->con->query($query);
        if ($result) {
            $arr = $result->fetch_all(MYSQLI_ASSOC);
            return $arr;
        } else {
            return [];
        }
    }

    public function refreshRoomData()
    {
        $sql = "UPDATE tbl_rooms_data SET room_filled = COALESCE((SELECT COUNT(*) FROM tbl_users_room WHERE tbl_users_room.room_id = tbl_rooms_data.room_id AND tbl_users_room.active = 1), 0)";
        return $this->con->query($sql);
    }

    public function conditionGetData($table, $field = '*', $condition)
    {
        $sql = "SELECT $field FROM $table WHERE $condition";

        $result = $this->con->query($sql);
        if ($result) {
            $arr = $result->fetch_all(MYSQLI_ASSOC);
            return $arr;
        } else {
            return [];
        }
    }

    public function joinSQLDataWithConditions($table, $fields = '*', $joinTable, $onCondition, $condition_arr = [])
    {
        $sql = "SELECT $fields FROM $table";
        $sql .= " JOIN $joinTable ON $onCondition";
        $sql .= ' WHERE ' . $this->buildCondition($condition_arr);

        $result = $this->con->query($sql);

        if ($result) {
            $arr = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            return $arr;
        } else {
            return [];
        }
    }


    public function insertData($table, $data)
    {
        $fields = implode(',', array_keys($data));
        $values = "'" . implode("','", array_values($data)) . "'";
        $sql = "INSERT INTO $table ($fields) VALUES ($values)";
        return $this->con->query($sql);
    }

    public function insertDataGetId($table, $data)
    {
        $fields = implode(',', array_keys($data));
        $values = "'" . implode("','", array_values($data)) . "'";
        $sql = "INSERT INTO $table ($fields) VALUES ($values)";

        if ($this->con->query($sql)) {
            return $this->con->insert_id;
        } else {
            return ['error' => $this->con->error];
        }
    }

    public function deleteData($table, $condition_arr)
    {
        $sql = "DELETE FROM $table WHERE " . $this->buildCondition($condition_arr);
        return $this->con->query($sql);
    }

    public function updateData($table, $data, $condition_arr)
    {
        $setClause = $this->buildSetClause($data);
        $whereClause = $this->buildCondition($condition_arr);
        $sql = "UPDATE $table SET $setClause WHERE $whereClause";
        return $this->con->query($sql);
    }

    public function resetData($table, $data)
    {
        $setClause = $this->buildSetClause($data);
        $sql = "UPDATE $table SET $setClause";
        return $this->con->query($sql);
    }

    public function get_safe_str($str)
    {
        return $this->con->real_escape_string($str);
    }

    public function joinTablesInCondition($table1, $table2, $table1joinColumn, $table2joinColumn, $conditions, $orderByColumn = null, $orderByDirection = 'ASC')
    {
        $selectColumns = '*';
        $whereClause = '';

        if (!empty($conditions)) {
            $whereParts = [];
            foreach ($conditions as $key => $value) {
                if (is_array($value) && isset($value[0]) && $value[0] === 'IN' && is_array($value[1])) {
                    $inValues = array_map(function ($v) {
                        return $this->get_safe_str($v);
                    }, $value[1]);
                    $whereParts[] = "$key IN ('" . implode("','", $inValues) . "')";
                } else {
                    $whereParts[] = "$key='" . $this->get_safe_str($value) . "'";
                }
            }
            $whereClause = "WHERE " . implode(' AND ', $whereParts);
        }

        $orderByClause = $orderByColumn ? "ORDER BY $orderByColumn $orderByDirection" : '';

        $query = "SELECT $selectColumns 
              FROM $table1
              JOIN $table2 ON $table1joinColumn = $table2joinColumn
              $whereClause
              $orderByClause";

        $result = $this->con->query($query);
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    private function buildCondition($condition_arr)
    {
        $conditions = [];
        foreach ($condition_arr as $key => $value) {
            $conditions[] = "$key='" . $this->get_safe_str($value) . "'";
        }
        return implode(' AND ', $conditions);
    }

    private function buildSetClause($data)
    {
        $setValues = [];
        foreach ($data as $key => $value) {
            $setValues[] = "$key='" . $this->get_safe_str($value) . "'";
        }
        return implode(', ', $setValues);
    }
}
