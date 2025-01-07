<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class DBLog
{

  private static function createTable($table)
  {
    $CI = &get_instance();
    $server_log = $CI->load->database('dblog', TRUE);

    $check_table_exists = $server_log->query(
      "SELECT 1 
      FROM information_schema.tables 
      WHERE table_schema = 'public' 
        AND table_name = '$table'"
    )->row_array();

    if ($check_table_exists == null) {
      $createSql = "CREATE TABLE " . $table . " (
                      log_id BIGSERIAL PRIMARY KEY,
                      created_at timestamp NULL,
                      created_by varchar(128) DEFAULT NULL::character varying NULL,
                      updated_at timestamp NULL,
                      updated_by varchar(128) DEFAULT NULL::character varying NULL,
                      deleted_at timestamp NULL,
                      deleted_by varchar(128) DEFAULT NULL::character varying NULL,
                      deleted_st int2 DEFAULT '0'::smallint NULL,
                      active_st int2 DEFAULT '1'::smallint NULL,
                      key_1_id varchar(36) NULL,
                      key_2_id varchar(36) NULL,
                      key_3_id varchar(36) NULL,
                      key_4_id varchar(36) NULL,
                      key_5_id varchar(36) NULL,
                      key_6_id varchar(36) NULL,
                      contents jsonb NULL
                    );
                    CREATE INDEX " . $table . "_key_1_id_idx ON public." . $table . " USING btree (key_1_id);
                    CREATE INDEX " . $table . "_key_2_id_idx ON public." . $table . " USING btree (key_2_id);
                    CREATE INDEX " . $table . "_key_3_id_idx ON public." . $table . " USING btree (key_3_id);
                    CREATE INDEX " . $table . "_key_4_id_idx ON public." . $table . " USING btree (key_4_id);
                    CREATE INDEX " . $table . "_key_5_id_idx ON public." . $table . " USING btree (key_5_id);
                    CREATE INDEX " . $table . "_key_6_id_idx ON public." . $table . " USING btree (key_6_id);";
      $server_log->query($createSql);
    }
  }

  public static function listTable()
  {
    $CI = &get_instance();
    $server_log = $CI->load->database('dblog', TRUE);

    $listTableSql = "SELECT table_name
                    FROM information_schema.tables
                    WHERE 
                      table_schema = 'public'
                      AND table_type = 'BASE TABLE'";
    return $server_log->query($listTableSql)->result_array();
  }

  private static function listPrimaryKey($table)
  {
    $getKeySql = DB::raw(
      'result_array',
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
        and tco.table_name = '$table'"
    );

    return $getKeySql;
  }

  // QUERY
  public static function get($table, $params = array())
  {
    $CI = &get_instance();
    $server_log = $CI->load->database('dblog', TRUE);

    $server_log->where($params);
    return $server_log->get($table)->row_array();
  }

  public static function insert($table, $data)
  {
    DBLog::createTable($table);
  }

  public static function update($table, $data, $where)
  {
    DBLog::createTable($table);

    $CI = &get_instance();
    $server_log = $CI->load->database('dblog', TRUE);

    $primaryKeyList = DBLog::listPrimaryKey($table);

    $oldList = DB::all($table, $where);
    foreach ($oldList as $oldRow) {
      $dLog = array(
        'created_at' => @$oldRow['created_at'],
        'created_by' => @$oldRow['created_by'],
        'updated_at' => @$oldRow['updated_at'],
        'updated_by' => @$oldRow['updated_by'],
        'deleted_at' => @$oldRow['deleted_at'],
        'deleted_by' => @$oldRow['deleted_by'],
        'deleted_st' => @$oldRow['deleted_st'],
        'active_st' => @$oldRow['active_st'],
        'contents' => json_encode($oldRow),
      );

      if ($primaryKeyList != null) {
        foreach ($primaryKeyList as $primaryKeyKey => $primaryKeyRow) {
          $dLog['key_' . ($primaryKeyKey + 1) . '_id'] = $oldRow[$primaryKeyRow['column_name']];
        }
      }

      $server_log->insert($table, $dLog);
    }
  }

  public static function delete($table, $data, $where)
  {
    DBLog::createTable($table);

    $CI = &get_instance();
    $server_log = $CI->load->database('dblog', TRUE);

    $primaryKeyList = DBLog::listPrimaryKey($table);

    $oldList = DB::all($table, $where);
    foreach ($oldList as $oldRow) {
      $dLog = array(
        'created_at' => @$oldRow['created_at'],
        'created_by' => @$oldRow['created_by'],
        'updated_at' => @$oldRow['updated_at'],
        'updated_by' => @$oldRow['updated_by'],
        'deleted_at' => date('Y-m-d H:i:s'),
        'deleted_by' => _ses_get('user_realname'),
        'deleted_st' => 1,
        'active_st' => 0,
        'contents' => json_encode($oldRow),
      );

      if ($primaryKeyList != null) {
        foreach ($primaryKeyList as $primaryKeyKey => $primaryKeyRow) {
          $dLog['key_' . ($primaryKeyKey + 1) . '_id'] = $oldRow[$primaryKeyRow['column_name']];
        }
      }

      $server_log->insert($table, $dLog);
    }
  }

  // QUERY
  public static function raw($init, $sql, $params = false)
  {
    $CI = &get_instance();
    $server_log = $CI->load->database('dblog', TRUE);
    switch ($init) {
      case 'result_array':
        return $server_log->query($sql, $params)->result_array();
        break;
      case 'row_array':
        return $server_log->query($sql, $params)->row_array();
        break;
      case 'num_rows':
        return $server_log->query($sql, $params)->num_rows();
        break;
      default:
        return $server_log->query($sql, $params);
        break;
    }
  }

  // DATATABLES
  public static function datatables_query($query, $keyword, $where, $iswhere = null)
  {
    $CI = &get_instance();
    $server_log = $CI->load->database('dblog', TRUE);
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

    if ($server_log->dbdriver == 'sqlsrv') {
      $queryData .= " OFFSET " . $start . " ROW FETCH NEXT " . $limit . " ROWS ONLY";
    } else {
      $queryData .= " LIMIT " . $limit . " OFFSET " . $start;
    }

    $data = DBLog::raw('result_array', $queryData);
    $recordsTotal = DBLog::raw('row_array', $queryAllRecords)['count'];

    if ($keyword != null && @count($keyword) > 0) {
      $queryRecordsFiltered = str_replace_between($queryFiltered, 'SELECT', 'FROM', ' COUNT(1) AS count ');
      $recordsFiltered = DBLog::raw('row_array', $queryRecordsFiltered)['count'];
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
