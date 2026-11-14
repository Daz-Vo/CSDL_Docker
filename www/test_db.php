<?php
echo "<h2>Kiểm tra kết nối Database (Bản cập nhật):</h2>";

$dev_password   = getenv('DEV_PASSWORD') ?: 'root'; // Fallback nếu không có
$mysql_password = getenv('MYSQL_ROOT_PASSWORD') ?: $dev_password;
$pgsql_password = getenv('POSTGRES_PASSWORD') ?: $dev_password;
$mssql_password = getenv('MSSQL_SA_PASSWORD') ?: $dev_password;
$mongo_password = getenv('MONGO_INITDB_ROOT_PASSWORD') ?: $dev_password;

// 1. Kiểm tra MySQL (Kết nối vào db 'mysql' có sẵn)
try {
    $mysql = new PDO("mysql:host=mysql;dbname=mysql", "root", $mysql_password);
    echo "✅ MySQL: Kết nối thành công!<br>";
} catch (Exception $e) { echo "❌ MySQL: Lỗi - " . $e->getMessage() . "<br>"; }

// 2. Kiểm tra PostgreSQL (Kết nối vào db 'postgres' có sẵn)
try {
    $pgsql = new PDO("pgsql:host=postgres;dbname=postgres", "postgres", $pgsql_password);
    echo "✅ PostgreSQL: Kết nối thành công!<br>";
} catch (Exception $e) { echo "❌ PostgreSQL: Lỗi - " . $e->getMessage() . "<br>"; }

// 3. Kiểm tra SQL Server (Thêm TrustServerCertificate để sửa lỗi SSL)
try {
    // Lưu ý: TrustServerCertificate=1 là bắt buộc cho môi trường Docker/Local
    $sqlsrv = new PDO("sqlsrv:Server=sqlserver_db;Database=master;TrustServerCertificate=1", "sa", $mssql_password);
    echo "✅ SQL Server: Kết nối thành công!<br>";
} catch (Exception $e) { echo "❌ SQL Server: Lỗi - " . $e->getMessage() . "<br>"; }

// 4. Kiểm tra MongoDB
if (extension_loaded("mongodb")) {
    try {
        // serverSelectionTimeoutMS=1000 để kiểm tra kết nối nhanh trong 1 giây mà không bị đơ trang
        $mongo = new MongoDB\Driver\Manager("mongodb://root:" . urlencode($mongo_password) . "@mongodb:27017", [], ["serverSelectionTimeoutMS" => 1000]);
        $command = new MongoDB\Driver\Command(['ping' => 1]);
        $mongo->executeCommand('admin', $command);
        echo "✅ MongoDB: Kết nối Database thành công!<br>";
    } catch (Exception $e) {
        echo "⚪ MongoDB: Driver PHP đã tích hợp sẵn, nhưng Container MongoDB đang tắt (hoặc chưa cài).<br>";
    }
} else {
    echo "❌ MongoDB: Driver PHP chưa được cài đặt.<br>";
}
?>