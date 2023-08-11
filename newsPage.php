<?php

// 資料庫連線設定
$host = '127.0.0.1'; // 資料庫主機
$dbname = 'test1'; // 資料庫名稱
$username = 'root'; // 資料庫使用者名稱
$password = ''; // 資料庫密碼

// 建立資料庫連線
try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("資料庫連線失敗: " . $e->getMessage());
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS'); // 可以添加其他支援的請求方法，例如 PUT、DELETE 等
header('Access-Control-Allow-Headers: Content-Type'); // 如果您使用了自定義的 Header，請將它們添加到這裡

/// 處理 HTTP 請求
$method = $_SERVER['REQUEST_METHOD'];

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($method === 'POST') {
  $action = $_POST['action'] ?? '';

  try {
    switch ($action) {
      case 0:
        // 查詢操作
        $stmt = $pdo->prepare("SELECT * FROM NewsPage");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['code' => 0, 'data' => $result]);
        break;
      case 1:
        // 修改操作
        $id = $_POST['id'];
        $news = $_POST['news'];

        $stmt = $pdo->prepare("UPDATE NewsPage SET news = :news WHERE id = :id");
        $stmt->bindParam(':news', $news);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // 重新獲取資料並回傳
        $stmt = $pdo->prepare("SELECT * FROM NewsPage");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['code' => 0, 'message' => 'Update successful', 'data' => $result]);
        break;
      case 2:
        // 刪除操作
        $id = $_POST['id'];

        $stmt = $pdo->prepare("DELETE FROM NewsPage WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // 重新獲取資料並回傳
        $stmt = $pdo->prepare("SELECT * FROM NewsPage");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['code' => 0, 'message' => 'Delete successful', 'data' => $result]);
        break;
      default:
        echo json_encode(['code' => 1, 'message' => '不支援的操作']);
        break;
    }
  } catch (PDOException $e) {
    // 資料庫錯誤處理
    echo json_encode(['code' => 1, 'message' => $e->getMessage()]);
  } catch (Exception $e) {
    // 其他錯誤處理
    echo json_encode(['code' => 1, 'message' => '發生其他錯誤']);
  }
} else {
  echo json_encode(['code' => 1, 'message' => '不支援的請求方法']);
}
?>