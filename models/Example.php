<?php
class Example
{
    public $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }
}


?>