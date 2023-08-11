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
        // 新增操作
        $news = $_POST['news'];
        
        $stmt = $pdo->prepare("INSERT INTO NewsPage (date, news) VALUES (NOW(), :news)");
        $stmt->bindParam(':news', $news);
        $stmt->execute();
        
        // 重新獲取資料並回傳
        $stmt = $pdo->prepare("SELECT * FROM NewsPage");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['code' => 0, 'message' => 'Insert successful', 'data' => $result]);
        break;
      case 2:
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
      case 3:
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
      case 4:
        // 交換操作
        $id1 = $_POST['id1'];
        $id2 = $_POST['id2'];

        // 獲取兩個 id 對應的資料
        $stmt1 = $pdo->prepare("SELECT * FROM NewsPage WHERE id = :id");
        $stmt1->bindParam(':id', $id1);
        $stmt1->execute();
        $data1 = $stmt1->fetch(PDO::FETCH_ASSOC);

        $stmt2 = $pdo->prepare("SELECT * FROM NewsPage WHERE id = :id");
        $stmt2->bindParam(':id', $id2);
        $stmt2->execute();
        $data2 = $stmt2->fetch(PDO::FETCH_ASSOC);

        // 交換資料
        if ($data1 && $data2) {
            $stmtUpdate1 = $pdo->prepare("UPDATE NewsPage SET news = :news WHERE id = :id");
            $stmtUpdate1->bindParam(':news', $data2['news']);
            $stmtUpdate1->bindParam(':id', $id1);
            $stmtUpdate1->execute();

            $stmtUpdate2 = $pdo->prepare("UPDATE NewsPage SET news = :news WHERE id = :id");
            $stmtUpdate2->bindParam(':news', $data1['news']);
            $stmtUpdate2->bindParam(':id', $id2);
            $stmtUpdate2->execute();

            // 重新獲取資料並回傳
            $stmt = $pdo->prepare("SELECT * FROM NewsPage");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['code' => 0, 'message' => 'Swap successful', 'data' => $result]);
        } else {
            echo json_encode(['code' => 1, 'message' => 'One or both of the IDs do not exist']);
        }
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