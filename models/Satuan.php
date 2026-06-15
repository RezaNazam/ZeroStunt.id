<?php

class Satuan
{
    private $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    public function all()
    {
        $query = "SELECT * FROM satuan ORDER BY nama_satuan ASC";
        $result = mysqli_query($this->db, $query);

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        return $data;
    }
}

