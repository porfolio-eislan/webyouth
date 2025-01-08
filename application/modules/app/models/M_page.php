<?php

Class M_page extends CI_Model {

    public function get_identitas()
    {
        $sql = "SELECT * FROM master_identitas";
        $query = DB::raw('row_array', $sql);
        return $query;
    }

    public function get_perangkat_desa()
    {
        $sql = "SELECT a.*, b.jabatan_nm 
                FROM data_perangkat_desa a
                LEFT JOIN master_jabatan b ON a.jabatan_id = b.jabatan_id
                LIMIT 4";
        $query = DB::raw('result_array', $sql);
        return $query;
    }

    public function get_perangkat_desa_all()
    {
        $sql = "SELECT a.*, b.jabatan_nm 
                FROM data_perangkat_desa a
                LEFT JOIN master_jabatan b ON a.jabatan_id = b.jabatan_id";
        $query = DB::raw('result_array', $sql);
        return $query;
    }
}