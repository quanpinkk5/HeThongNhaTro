<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "quan_ly_phong_tro";
$port = 3307;

$con = mysqli_connect($host, $user, $pass, $dbname, $port);

if (!$con) {
    die("Lỗi kết nối cơ sở dữ liệu: " . mysqli_connect_error());
}

mysqli_set_charset($con, "utf8");
?>

<?php
class database
{
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $dbname = "quan_ly_phong_tro";
    private $port = 3307;      // Thêm port
    private $con;

    public function connect()
    {
        try {
            $this->con = new mysqli(
                $this->host,
                $this->user,
                $this->pass,
                $this->dbname,
                $this->port          // Truyền port
            );

            $this->con->set_charset("utf8mb4"); // Đặt trước return

            return $this->con;
        } catch (Exception $e) {
            die("Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage());
        }
    }
}
?>