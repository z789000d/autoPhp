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


if ($method === 'POST') {
  $action = $_POST['action'] ?? '';
  

  try {
    switch ($action) {
      case '0':
        // 查詢所有 HomePage 資料
        $stmt = $pdo->query('SELECT * FROM HomePage');
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['code' => 0, 'data' => $data]);
        break;
      case '1':
        // 新增 HomePage 資料
        $name = $_POST['name'];
        $category = $_POST['category'];
        $images = $_POST['images']; // 不使用 json_encode
        $description = $_POST['description'];
        $videoLink = $_POST['videoLink'];
        $type = $_POST['type'];
        
        $stmt = $pdo->prepare('INSERT INTO HomePage (name, category, images, description, videoLink, type) VALUES (?, ?, ?, ?, ?, ?)');
        $result = $stmt->execute([$name, $category, $images, $description, $videoLink, $type]);
        
        $stmtN = $pdo->query('SELECT * FROM HomePage');
        $dataN = $stmtN->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {
          echo json_encode(['code' => 0, 'message' => '新增成功','data' => $dataN]);
        } else {
          echo json_encode(['code' => 1, 'message' => '新增失敗','data' => $dataN]);
        }
        break;
      case '2':
        // 更改 HomePage 資料
        $id = $_POST['id'] ?? null;
        $name = $_POST['name'];
        $category = $_POST['category'];
        $images = $_POST['images']; // 不使用 json_encode
        $description = $_POST['description'];
        $videoLink = $_POST['videoLink'];
        $type = $_POST['type'];
        
        if ($id !== null) {
          $stmt = $pdo->prepare('UPDATE HomePage SET name = ?, category = ?, images = ?, description = ?, videoLink = ?, type = ? WHERE id = ?');
          $result = $stmt->execute([$name, $category, $images, $description, $videoLink, $type, $id]);
          
          $stmtN = $pdo->query('SELECT * FROM HomePage');
          $dataN = $stmtN->fetchAll(PDO::FETCH_ASSOC);

          if ($result) {
            echo json_encode(['code' => 0, 'message' => '更改成功','data' => $dataN]);
          } else {
            echo json_encode(['code' => 1, 'message' => '更改失敗','data' => $dataN]);
          }
        } else {
          echo json_encode(['code' => 1, 'message' => '更改失敗，請提供要更改的ID']);
        }
        break;
      case '3':
        // 刪除 HomePage 資料
        $id = $_POST['id'] ?? null;
        if ($id !== null) {
          $stmt = $pdo->prepare('DELETE FROM HomePage WHERE id = ?');
          $result = $stmt->execute([$id]);
          
          $stmtN = $pdo->query('SELECT * FROM HomePage');
          $dataN = $stmtN->fetchAll(PDO::FETCH_ASSOC);
          if ($result) {
            echo json_encode(['code' => 0, 'message' => '刪除成功','data' => $dataN]);
          } else {
            echo json_encode(['code' => 1, 'message' => '刪除失敗','data' => $dataN]);
          }
        } else {
          echo json_encode(['code' => 1, 'message' => '刪除失敗，請提供要刪除的ID']);
        }
        break;
      case '4':
        // 交換兩個ID的內容
        $id1 = $_POST['id1'];
        $id2 = $_POST['id2'];

        // Retrieve the data of the two rows with the given IDs
        $stmt1 = $pdo->prepare('SELECT * FROM HomePage WHERE id = ?');
        $stmt1->execute([$id1]);
        $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);

        $stmt2 = $pdo->prepare('SELECT * FROM HomePage WHERE id = ?');
        $stmt2->execute([$id2]);
        $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);

        // Swap the contents of the two rows
        if ($row1 && $row2) {
          $stmtUpdate1 = $pdo->prepare('UPDATE HomePage SET name = ?, category = ?, images = ?, description = ?, videoLink = ?, type = ? WHERE id = ?');
          $stmtUpdate2 = $pdo->prepare('UPDATE HomePage SET name = ?, category = ?, images = ?, description = ?, videoLink = ?, type = ? WHERE id = ?');

          $result1 = $stmtUpdate1->execute([$row2['name'], $row2['category'], $row2['images'], $row2['description'], $row2['videoLink'], $row2['type'], $id1]);
          $result2 = $stmtUpdate2->execute([$row1['name'], $row1['category'], $row1['images'], $row1['description'], $row1['videoLink'], $row1['type'], $id2]);

          // Fetch the updated data after swapping
          $stmtN = $pdo->query('SELECT * FROM HomePage');
          $dataN = $stmtN->fetchAll(PDO::FETCH_ASSOC);

          if ($result1 && $result2) {
            echo json_encode(['code' => 0, 'message' => '交換成功', 'data' => $dataN]);
          } else {
            echo json_encode(['code' => 1, 'message' => '交換失敗', 'data' => $dataN]);
          }
        } else {
          echo json_encode(['code' => 1, 'message' => '找不到指定的ID']);
        }
        break;
      default:
        echo json_encode(['code' => 1, 'message' => '不支援的操作']);
        break;
    }
  } catch (PDOException $e) {
    // 資料庫錯誤處理
    echo json_encode(['code' => 1, 'message' =>  $e->getMessage()]);
  } catch (Exception $e) {
    // 其他錯誤處理
    echo json_encode(['code' => 1, 'message' => '發生其他錯誤']);
  }
} else {
  echo json_encode(['code' => 1, 'message' => '不支援的請求方法']);
}
?>