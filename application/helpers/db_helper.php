<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class DB
{

    // QUERY
    public static function all($table, $params = null, $order = null)
    {
        $CI = &get_instance();
        if ($order != null) $CI->db->order_by($order[0], $order[1]);
        if (is_array($params)) $CI->db->where($params);
        $CI->db->where(['active_st' => '1']);
        return $CI->db->get($table)->result_array();
    }

    public static function all_like($table, $params = null, $params_where = null, $order = null, $side = 'both')
    {
        $CI = &get_instance();
        if ($order != null) $CI->db->order_by($order[0], $order[1]);

        $par = [];
        if (is_array($params)) {
            foreach ($params as $k => $v) {
                $par['LOWER(' . $k . ')'] = strtolower($v);
            }
        }
        $CI->db->or_like($par, '', $side);
        if (is_array($params_where)) $CI->db->where($params_where);
        $CI->db->where(['active_st' => '1']);
        return $CI->db->get($table)->result_array();
    }

    public static function all_in($table, $params = null, $params_where = null, $order = null)
    {
        $CI = &get_instance();
        if ($order != null) $CI->db->order_by($order[0], $order[1]);

        if (is_array($params)) {
            foreach ($params as $k => $v) {
                $CI->db->where_in('LOWER(' . $k . ')', array_map('strtolower', $v));
            }
        }

        if (is_array($params_where)) $CI->db->where($params_where);
        $CI->db->where(['active_st' => '1']);
        return $CI->db->get($table)->result_array();
    }

    // QUERY
    public static function get($table, $params = array())
    {
        $CI = &get_instance();
        $CI->db->where($params);
        return $CI->db->get($table)->row_array();
    }

    // QUERY
    public static function query($query, $where = null, $order = null, $return = 'result')
    {
        $CI = &get_instance();
        $fwhere = '';
        if ($where != null) {
            $fwhere = 'WHERE ';
            $setWhere = array();
            foreach ($where as $key => $value) {
                $setWhere[] = $key . "='" . $value . "'";
            }
            $fwhere .= implode(' AND ', $setWhere);
        }
        $forder = '';
        if ($order != null) {
            $forder = 'ORDER BY ';
            $setOrder = array();
            foreach ($order as $key => $value) {
                $setOrder[] = $key . " " . $value . "";
            }
            $forder .= implode(', ', $setOrder);
        }
        if ($return == 'result') {
            return $CI->db->query($query . " " . $fwhere . " " . $forder)->result_array();
        } elseif ($return == 'row') {
            return $CI->db->query($query . " " . $fwhere . " " . $forder)->row_array();
        }
    }

    // QUERY
    public static function get_return($table, $params = array(), $return = false)
    {
        $CI = &get_instance();
        $CI->db->where($params);
        $query = $CI->db->get($table);
        if ($query->num_rows() > 0) {
            return $query->row_array();
        } else {
            return $return;
        }
    }

    // QUERY
    public static function last_id()
    {
        $CI = &get_instance();
        return $CI->db->insert_id();
    }

    // QUERY
    public static function valid_id($table, $field, $value)
    {
        $return = DB::get_return($table, [$field => $value]);
        if ($return != false) {
            return true;
        } else {
            return false;
        }
    }

    // QUERY
    public static function raw($init, $sql, $params = false)
    {
        $CI = &get_instance();
        switch ($init) {
            case 'result_array':
                return $CI->db->query($sql, $params)->result_array();
                break;
            case 'row_array':
                return $CI->db->query($sql, $params)->row_array();
                break;
            case 'num_rows':
                return $CI->db->query($sql, $params)->num_rows();
                break;
            default:
                return $CI->db->query($sql, $params);
                break;
        }
    }

    public static function raw_json($init, $sql, $params = false)
    {
        $CI = &get_instance();
        switch ($init) {
            case 'result_array':
                _json($CI->db->query($sql, $params)->result_array());
                break;
            case 'row_array':
                _json($CI->db->query($sql, $params)->row_array());
                break;
            case 'num_rows':
                _json($CI->db->query($sql, $params)->num_rows());
                break;
            default:
                return $CI->db->query($sql, $params);
                break;
        }
    }

    // INSERT
    public static function insert($table, $data)
    {
        $CI = &get_instance();
        // @validate token
        _validate_token();
        // @main process
        $data['created_at'] = _now();
        $data['created_by'] = _ses_get('user_realname');

        // Log Data
        DB::log_db('insert', $table, $data);
        // End Log Data

        return array(
            'data' => $CI->db->insert($table, $data),
        );
    }

    // UPDATE
    public static function update($table, $data, $where)
    {
        $CI = &get_instance();
        // @validate token
        _validate_token();

        // Log Data
        DB::log_db('update', $table, $data, $where);
        // End Log Data

        // @main process
        $data['updated_at'] = _now();
        $data['updated_by'] = _ses_get('user_realname');
        $CI->db->where($where);
        return $CI->db->update($table, $data);
    }

    // SAVE
    public static function save($table, $data, $where = null)
    {
        $CI = &get_instance();
        // @validate token
        _validate_token();
        // @ternary process

        if (is_array($where)) {
            // @main process
            $data['updated_at'] = _now();
            $data['updated_by'] = _ses_get('user_realname');
            $result = DB::update($table, $data, $where);
        } else {
            $data['created_at'] = _now();
            $data['created_by'] = _ses_get('user_realname');
            $result = DB::insert($table, $data);
        }
        return $result;
    }

    // DELETE
    public static function delete($table, $where = null)
    {
        // @log 
        DB::log_db('delete', $table, null, $where);

        // @main process
        $CI = &get_instance();
        if (is_array($where)) {
            foreach ($where as $k => $v) {
                $CI->db->where($k, strval($v));
            }
        }
        return $CI->db->delete($table);
    }

    // LOG DB
    private static function log_db($method, $table, $data, $where = null)
    {
        if ($method == 'insert') {
            DBLog::insert($table, $data);
        }
        if ($method == 'update') {
            DBLog::update($table, $data, $where);
        }
        if ($method == 'delete') {
            DBLog::delete($table, $data, $where);
        }
    }

    // TRASH
    public static function trash($action = 'delete', $table, $data, $where = null)
    {
        $CI = &get_instance();
        if (get_parameter('db_trash')['parameter_cd'] == 'true') {
            if ($action == 'delete') {
                if (in_array($table, array('dat_resep', 'dat_resep_item', 'mst_pasien', 'dat_cppt'))) { // Hanya table disini yang menggunakan trash
                    // @update main
                    $data = ['deleted_st' => 1, 'deleted_at' => _now(), 'deleted_by' => _ses_get('user_realname')];
                    DB::update($table, $data, $where);
                    // @insert trash
                    $result = DB::all($table, $where);
                    // if (get_parameter('db_trash_type')['parameter_cd'] == 'mongodb') {
                    //     $CI->load->library('mongo_db');
                    //     $CI->mongo_db->connect();
                    //     foreach ($result as $val) {
                    //         $val['updated_at'] = _now();
                    //         $val['updated_by'] = _ses_get('user_realname');
                    //         $result = $CI->mongo_db->insert($table, $val);
                    //     }
                    // } else {
                    $db_trash = $CI->load->database('trash', TRUE);
                    foreach ($result as $val) {
                        $db_trash->insert($table, $val);
                    }
                    // }
                }
            }
            if ($action == 'update') {
                // @insert trash
                if (in_array($table, array('dat_resep', 'dat_resep_item', 'mst_pasien', 'dat_cppt'))) { // Hanya table disini yang menggunakan trash
                    $result = DB::all($table, $where);
                    // if (get_parameter('db_trash_type')['parameter_cd'] == 'mongodb') {
                    //     $CI->load->library('mongo_db');
                    //     $CI->mongo_db->connect();
                    //     foreach ($result as $val) {
                    //         $val['deleted_at'] = _now();
                    //         $val['deleted_by'] = _ses_get('user_realname');
                    //         $result = $CI->mongo_db->insert($table, $val);
                    //     }
                    // } else {
                    $db_trash = $CI->load->database('trash', TRUE);
                    foreach ($result as $val) {
                        $db_trash->insert($table, $val);
                    }
                    // }
                }
            }
        }
    }

    // GET ID
    public static function get_id($modul = null, $type = 1, $tanggal = null)
    {
        if ($tanggal == null) {
            $tanggal = date('Y-m-d');
        }
        $table = 'tmp_id';
        // $pk = DB::raw('row_array', "SHOW KEYS FROM $modul WHERE Key_name = 'PRIMARY'"); // Mysql
        $pk = DB::raw(
            'row_array',
            "SELECT
                kcu.column_name as column_name
            FROM
                information_schema.table_constraints tco
            JOIN information_schema.key_column_usage kcu on
                kcu.constraint_name = tco.constraint_name
                and kcu.constraint_schema = tco.constraint_schema
                and kcu.constraint_name = tco.constraint_name 
            WHERE
                tco.constraint_type = 'PRIMARY KEY' 
                and tco.table_name = '$modul'"
        ); // Postgresql
        if ($type == 1) {
            $id = DB::get($table, ['modul' => $modul, 'tgl_id' => $tanggal]);
            if (@$id == '' || @$id['modul'] == '') {
                // $result = date('ymd') . '000001';
                $result = date('ymd', strtotime($tanggal)) . '000001';
            } else {
                $result = $id['no_id'] + 1;
            }

            $last = DB::get($modul, [@$pk['column_name'] => strval($result)]);
            while ($last != null) {
                $result = $last[@$pk['column_name']] + 1;
                $last = DB::get($modul, [@$pk['column_name'] => strval($result)]);
            }
        } else if ($type == 2) {
            $id = DB::get($table, ['modul' => $modul, 'tgl_id' => $tanggal]);
            if (@$id == '' || @$id['modul'] == '') {
                $result = str_pad('1', 12, '0', STR_PAD_LEFT);
            } else {
                $result = str_pad(intval($id['no_id']) + 1, 12, '0', STR_PAD_LEFT);
            }

            $last = DB::get($modul, [@$pk['column_name'] => strval($result)]);
            while ($last != null) {
                $result = str_pad(intval($last[@$pk['column_name']]) + 1, 12, '0', STR_PAD_LEFT);
                $last = DB::get($modul, [@$pk['column_name'] => strval($result)]);
            }
        } else {
            $id = DB::get($table, ['modul' => $modul, 'tgl_id' => $tanggal]);
            if (@$id == '' || @$id['modul'] == '') {
                // $result = date('ymd') . '000001';
                $result = date('ymd', strtotime($tanggal)) . '000001';
            } else {
                $last = substr($id['no_id'], 8, 99);
                $result = date('ymd', strtotime($tanggal)) . str_pad(intval($last) + 1, 4, '0', STR_PAD_LEFT);
            }
        }
        // return strval($result); // @disabled
        // 
        // @auto update id
        $result_id = strval($result);
        DB::update_id($modul, $result_id, $tanggal, true);
        // 
        return $result_id;
    }

    // GET ID CUSTOM
    public static function get_id_custom($modul = null, $type = 1, $length_id = '12', $tanggal = null)
    {
        if ($tanggal == null) {
            $tanggal = date('Y-m-d');
        }

        $table = 'tmp_id';
        $pk = DB::raw(
            'row_array',
            "SELECT
                kcu.column_name as column_name
            FROM
                information_schema.table_constraints tco
            JOIN information_schema.key_column_usage kcu on
                kcu.constraint_name = tco.constraint_name
                and kcu.constraint_schema = tco.constraint_schema
                and kcu.constraint_name = tco.constraint_name 
            WHERE
                tco.constraint_type = 'PRIMARY KEY' 
                and tco.table_name = '$modul'"
        ); // Postgresql
        if ($type == 1) {
            $id = DB::get($table, ['modul' => $modul, 'tgl_id' => $tanggal]);
            if (@$id == '' || @$id['modul'] == '') {
                $result = date('ymd', strtotime($tanggal)) . str_pad('1', ($length_id - 6), '0', STR_PAD_LEFT);
            } else {
                $result = $id['no_id'] + 1;
                if (strlen($result) != $length_id) {
                    $prefix = substr($result, 0, 6);
                    $sufix  = str_pad(abs(substr($result, 7, 99)), ($length_id - 6), '0', STR_PAD_LEFT);
                    $result = $prefix . $sufix;
                }
            }

            $last = DB::get($modul, [@$pk['column_name'] => strval($result)]);
            while ($last != null) {
                $result = $last[@$pk['column_name']] + 1;
                $last = DB::get($modul, [@$pk['column_name'] => strval($result)]);
            }
        }
        // @auto update id
        // @auto update id
        $result_id = strval($result);
        DB::update_id($modul, $result_id, $tanggal, true);
        // 
        return $result_id;
    }

    // UPDATE ID
    public static function update_id($modul = null, $no_id = null, $tanggal = null, $status = false)
    {
        if ($status) {
            if ($tanggal == null) {
                $tanggal = date("Y-m-d");
            }
            $table = 'tmp_id';
            $check = DB::get($table, ['modul' => $modul, 'tgl_id' => $tanggal]);
            if (@$check['no_id'] == '') {
                $result = DB::insert($table, ['modul' => $modul, 'tgl_id' => $tanggal, 'no_id' => $no_id]);
            } else {
                $result = DB::update($table, ['tgl_id' => $tanggal, 'no_id' => $no_id], ['modul' => $modul, 'tgl_id' => $tanggal]);
            }
            return $result;
        }
    }

    // DATATABLES
    public static function datatables_query($query, $keyword, $where, $iswhere = null)
    {
        $CI = &get_instance();
        // Params
        $_search_value = @$_POST['search']['value'];
        $_length = @$_POST['length'];
        $_start = @$_POST['start'];
        $_order_field = @$_POST['order'][0]['column'];
        $_order_ascdesc = @$_POST['order'][0]['dir'];
        // 
        // Ambil data yang di ketik user pada textbox pencarian
        $search = htmlspecialchars($_search_value);
        $search = strtolower($search);
        // 
        // Ambil data limit per page
        $limit = preg_replace("/[^a-zA-Z0-9.]/", '', "{$_length}");
        // 
        // Ambil data start
        $start = preg_replace("/[^a-zA-Z0-9.]/", '', "{$_start}");
        //
        // Lower Keywoard
        if (is_array($keyword)) {
            foreach ($keyword as $k => $v) {
                $keyword[$k] = "LOWER(" . $v . ")";
            }
        }

        $strWhere = " WHERE ";

        if ($iswhere != null) {
            if (strtolower(substr(@$iswhere, 0, 3)) == "and" || @$iswhere == "") {
                $strWhere .= '1 = 1 ';
            } else {
                $strWhere .= ' ';
            }

            $strWhere .= $iswhere;
        } else {
            $strWhere .= '1 = 1 ';
        }

        if ($where != null) {
            $setWhere = array();
            foreach ($where as $key => $value) {
                $setWhere[] = $key . "='" . $value . "'";
            }
            $fwhere = implode(' AND ', $setWhere);
            $strWhere .= " AND " . $fwhere;
        }

        // Untuk mengambil nama field yg menjadi acuan untuk sorting
        if (@$_POST['columns'][$_order_field]['data']) {
            $strOrder = " ORDER BY " . @$_POST['columns'][$_order_field]['data'] . " " . $_order_ascdesc;
        } else {
            $strOrder = '';
        }

        $queryData = $query . $strWhere;
        $queryAllRecords = str_replace_between($queryData, 'SELECT', 'FROM', ' COUNT(1) AS count ');

        // Searching by keyword
        if ($keyword != null && @count($keyword) > 0) {
            $strWhereKeyword = $strWhere;
            $keyword = implode(" LIKE '%" . $search . "%' OR ", $keyword) . " LIKE '%" . $search . "%'";
            $strWhereKeyword .= " AND (" . $keyword . ") ";

            $queryData = $query . $strWhereKeyword . $strOrder;
            $queryFiltered = $query . $strWhereKeyword;
        } else {
            $queryData = $query . $strWhere . $strOrder;
            $queryFiltered = $query . $strWhere;
        }

        if ($CI->db->dbdriver == 'sqlsrv') {
            $queryData .= " OFFSET " . $start . " ROW FETCH NEXT " . $limit . " ROWS ONLY";
        } else {
            $queryData .= " LIMIT " . $limit . " OFFSET " . $start;
        }

        $data = DB::raw('result_array', $queryData);
        $recordsTotal = DB::raw('row_array', $queryAllRecords)['count'];

        if ($keyword != null && @count($keyword) > 0) {
            $queryRecordsFiltered = str_replace_between($queryFiltered, 'SELECT', 'FROM', ' COUNT(1) AS count ');
            $recordsFiltered = DB::raw('row_array', $queryRecordsFiltered)['count'];
        } else {
            $recordsFiltered = $recordsTotal;
        }

        $callback = array(
            'draw' => $_POST['draw'], // Ini dari datatablenya    
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        );

        _json($callback);
    }

    public static function where_in_str($field, $string, $separator = '#')
    {
        $res = $field . " IN (";
        $arr = explode($separator, $string);
        foreach ($arr as $k => $v) {
            if ($k + 1 == count($arr)) {
                $res .= "'" . $v . "' ";
            } else {
                $res .= "'" . $v . "', ";
            }
        }
        $res .= ") ";
        return $res;
    }

    public static function like_in_str($field, $string, $separator = '#')
    {
        $res = "( ";
        $arr = explode($separator, $string);
        foreach ($arr as $k => $v) {
            if ($k + 1 == count($arr)) {
                $res .= $field . " LIKE '" . $v . "%' ";
            } else {
                $res .= $field . " LIKE '" . $v . "%' OR ";
            }
        }
        $res .= ") ";
        return $res;
    }

    public static function datatables_query_different_count_query($query, $keyword, $where, $iswhere = null, $queryCount)
    {
        $CI = &get_instance();
        // Params
        $_search_value = @$_POST['search']['value'];
        $_length = @$_POST['length'];
        $_start = @$_POST['start'];
        $_order_field = @$_POST['order'][0]['column'];
        $_order_ascdesc = @$_POST['order'][0]['dir'];
        // 
        // Ambil data yang di ketik user pada textbox pencarian
        $search = htmlspecialchars($_search_value);
        $search = strtolower($search);
        // 
        // Ambil data limit per page
        $limit = preg_replace("/[^a-zA-Z0-9.]/", '', "{$_length}");
        // 
        // Ambil data start
        $start = preg_replace("/[^a-zA-Z0-9.]/", '', "{$_start}");
        //
        // Lower Keywoard
        if (is_array($keyword)) {
            foreach ($keyword as $k => $v) {
                $keyword[$k] = "LOWER(" . $v . ")";
            }
        }

        $strWhere = " WHERE ";

        if ($iswhere != null) {
            if (strtolower(substr(@$iswhere, 0, 3)) == "and" || @$iswhere == "") {
                $strWhere .= '1 = 1 ';
            } else {
                $strWhere .= ' ';
            }

            $strWhere .= $iswhere;
        } else {
            $strWhere .= '1 = 1 ';
        }

        if ($where != null) {
            $setWhere = array();
            foreach ($where as $key => $value) {
                $setWhere[] = $key . "='" . $value . "'";
            }
            $fwhere = implode(' AND ', $setWhere);
            $strWhere .= " AND " . $fwhere;
        }

        // Untuk mengambil nama field yg menjadi acuan untuk sorting
        if (@$_POST['columns'][$_order_field]['data']) {
            $strOrder = " ORDER BY " . @$_POST['columns'][$_order_field]['data'] . " " . $_order_ascdesc;
        } else {
            $strOrder = '';
        }

        $queryData = $query . $strWhere;
        $queryAllRecords = str_replace_between($queryCount, 'SELECT', 'FROM', ' COUNT(1) AS count ');

        // Searching by keyword
        if ($keyword != null && @count($keyword) > 0) {
            $strWhereKeyword = $strWhere;
            $keyword = implode(" LIKE '%" . $search . "%' OR ", $keyword) . " LIKE '%" . $search . "%'";
            $strWhereKeyword .= " AND (" . $keyword . ") ";

            $queryData = $query . $strWhereKeyword . $strOrder;
            $queryFiltered = $query . $strWhereKeyword;
            $queryFilteredCount = $queryCount . $strWhereKeyword;
        } else {
            $queryData = $query . $strWhere . $strOrder;
            $queryFiltered = $query . $strWhere;
            $queryFilteredCount = $queryCount . $strWhere;
        }

        if ($CI->db->dbdriver == 'sqlsrv') {
            $queryData .= " OFFSET " . $start . " ROW FETCH NEXT " . $limit . " ROWS ONLY";
        } else {
            $queryData .= " LIMIT " . $limit . " OFFSET " . $start;
        }

        $data = DB::raw('result_array', $queryData);
        $recordsTotal = DB::raw('row_array', $queryAllRecords)['count'];

        if ($keyword != null && @count($keyword) > 0) {
            $queryRecordsFiltered = str_replace_between($queryFilteredCount, 'SELECT', 'FROM', ' COUNT(1) AS count ');
            $recordsFiltered = DB::raw('row_array', $queryRecordsFiltered)['count'];
        } else {
            $recordsFiltered = $recordsTotal;
        }

        $callback = array(
            'draw' => $_POST['draw'], // Ini dari datatablenya    
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        );

        _json($callback);
    }
}
