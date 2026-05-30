<?php 
    $host = "localhost";
    $user = "root";
    $pass = "";
    $dbname = "quan_ly_phong_tro";
    $con = mysqli_connect($host,$user,$pass,$dbname);
    if(!$con){
        die("Lỗi kết nối cơ sở dữ liệu ".mysqli_connect_error());
    }
    mysqli_set_charset($con, "utf8");
?>
<?php
class database{
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $dbname = "quan_ly_phong_tro";
    private $con;
    public function connect(){
        $this->con= null;
        try {
                $this->con = new mysqli(
                $this->host,
                $this->user,
                $this->pass,
                $this->dbname
            );
                return $this->con;
            $this->con->set_charset("utf8mb4");
        } catch (Exception $e) {
            die("Lỗi kết nối cơ sở dữ liệu ".$e->getMessage());
        }
    }
}
?>