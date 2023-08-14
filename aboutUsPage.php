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
                $stmt = $pdo->prepare("SELECT AboutusPage.id AS aboutusId, AboutusPage.text, AboutUsPageImage.id AS imageId, AboutUsPageImage.imageId AS imageDataId, AboutUsPageImage.imageUrl FROM AboutusPage LEFT JOIN AboutUsPageImage ON AboutusPage.imageId = AboutUsPageImage.imageId WHERE AboutUsPageImage.imageId = 0");
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $data = [
                    'id' => $result[0]['aboutusId'], // Assuming the id is same for all rows
                    'text' => $result[0]['text'], // Assuming the text is same for all rows
                    'imageData' => []
                ];

                foreach ($result as $row) {
                    $data['imageData'][] = [
                        'id' => $row['imageId'], // Using AboutUsPageImage id here
                        'imageUrl' => $row['imageUrl'],
                        'imageId' => $row['imageDataId'] // Using AboutUsPageImage imageId here
                    ];
                }

                echo json_encode(['code' => 0, 'data' => $data]);
                break;
            case 1:
                // 新增操作
                $imageUrl = $_POST['imageUrl'];

                // 插入新的 AboutUsPageImage 資料
                $stmt = $pdo->prepare("INSERT INTO AboutUsPageImage (imageId, imageUrl) VALUES (0, :imageUrl)");
                $stmt->bindParam(':imageUrl', $imageUrl);
                $stmt->execute();

                // 重新查詢並回傳
                $stmt = $pdo->prepare("SELECT AboutusPage.id AS aboutusId, AboutusPage.text, AboutUsPageImage.id AS imageId, AboutUsPageImage.imageId AS imageDataId, AboutUsPageImage.imageUrl FROM AboutusPage LEFT JOIN AboutUsPageImage ON AboutusPage.imageId = AboutUsPageImage.imageId WHERE AboutUsPageImage.imageId = 0");
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $data = [
                    'id' => $result[0]['aboutusId'],
                    'text' => $result[0]['text'],
                    'imageData' => []
                ];

                foreach ($result as $row) {
                    $data['imageData'][] = [
                        'id' => $row['imageId'],
                        'imageUrl' => $row['imageUrl'],
                        'imageId' => $row['imageDataId']
                    ];
                }

                echo json_encode(['code' => 0, 'message' => 'Insert successful', 'data' => $data]);
                break;
            case 2:
                // 修改 text 操作
                $id = $_POST['id'];
                $text = $_POST['text'];

                $stmt = $pdo->prepare("UPDATE AboutusPage SET text = :text WHERE id = :id");
                $stmt->bindParam(':text', $text);
                $stmt->bindParam(':id', $id);
                $stmt->execute();

                // 重新查詢並回傳
                $stmt = $pdo->prepare("SELECT AboutusPage.id AS aboutusId, AboutusPage.text, AboutUsPageImage.id AS imageId, AboutUsPageImage.imageId AS imageDataId, AboutUsPageImage.imageUrl FROM AboutusPage LEFT JOIN AboutUsPageImage ON AboutusPage.imageId = AboutUsPageImage.imageId WHERE AboutUsPageImage.imageId = 0");
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $data = [
                    'id' => $result[0]['aboutusId'],
                    'text' => $result[0]['text'],
                    'imageData' => []
                ];

                foreach ($result as $row) {
                    $data['imageData'][] = [
                        'id' => $row['imageId'],
                        'imageUrl' => $row['imageUrl'],
                        'imageId' => $row['imageDataId']
                    ];
                }

                echo json_encode(['code' => 0, 'message' => 'Update text successful', 'data' => $data]);
                break;
            case 3:
                // 修改圖片操作
                $imageId = $_POST['imageId'];
                $newImageUrl = $_POST['newImageUrl'];

                // 更新 AboutUsPageImage 資料
                $stmt = $pdo->prepare("UPDATE AboutUsPageImage SET imageUrl = :newImageUrl WHERE id = :imageId");
                $stmt->bindParam(':newImageUrl', $newImageUrl);
                $stmt->bindParam(':imageId', $imageId);
                $stmt->execute();

                // 重新查詢並回傳
                $stmt = $pdo->prepare("SELECT AboutusPage.id AS aboutusId, AboutusPage.text, AboutUsPageImage.id AS imageId, AboutUsPageImage.imageId AS imageDataId, AboutUsPageImage.imageUrl FROM AboutusPage LEFT JOIN AboutUsPageImage ON AboutusPage.imageId = AboutUsPageImage.imageId WHERE AboutUsPageImage.imageId = 0");
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $data = [
                    'id' => $result[0]['aboutusId'],
                    'text' => $result[0]['text'],
                    'imageData' => []
                ];

                foreach ($result as $row) {
                    $data['imageData'][] = [
                        'id' => $row['imageId'],
                        'imageUrl' => $row['imageUrl'],
                        'imageId' => $row['imageDataId']
                    ];
                }

                echo json_encode(['code' => 0, 'message' => 'Update image successful', 'data' => $data]);
                break;
            case 4:
                // 交換操作
                $id1 = $_POST['id1'];
                $id2 = $_POST['id2'];

                // 獲取兩個 id 對應的資料
                $stmt1 = $pdo->prepare("SELECT * FROM AboutUsPageImage WHERE id = :id");
                $stmt1->bindParam(':id', $id1);
                $stmt1->execute();
                $data1 = $stmt1->fetch(PDO::FETCH_ASSOC);

                $stmt2 = $pdo->prepare("SELECT * FROM AboutUsPageImage WHERE id = :id");
                $stmt2->bindParam(':id', $id2);
                $stmt2->execute();
                $data2 = $stmt2->fetch(PDO::FETCH_ASSOC);

                // 交換資料
                if ($data1 && $data2) {
                    $stmtUpdate1 = $pdo->prepare("UPDATE AboutUsPageImage SET imageUrl = :imageUrl WHERE id = :id");
                    $stmtUpdate1->bindParam(':imageUrl', $data2['imageUrl']);
                    $stmtUpdate1->bindParam(':id', $id1);
                    $stmtUpdate1->execute();

                    $stmtUpdate2 = $pdo->prepare("UPDATE AboutUsPageImage SET imageUrl = :imageUrl WHERE id = :id");
                    $stmtUpdate2->bindParam(':imageUrl', $data1['imageUrl']);
                    $stmtUpdate2->bindParam(':id', $id2);
                    $stmtUpdate2->execute();

                    // 重新查詢並回傳
                    $stmt = $pdo->prepare("SELECT AboutusPage.id AS aboutusId, AboutusPage.text, AboutUsPageImage.id AS imageId, AboutUsPageImage.imageId AS imageDataId, AboutUsPageImage.imageUrl FROM AboutusPage LEFT JOIN AboutUsPageImage ON AboutusPage.imageId = AboutUsPageImage.imageId WHERE AboutUsPageImage.imageId = 0");
                    $stmt->execute();
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    $data = [
                        'id' => $result[0]['aboutusId'],
                        'text' => $result[0]['text'],
                        'imageData' => []
                    ];

                    foreach ($result as $row) {
                        $data['imageData'][] = [
                            'id' => $row['imageId'],
                            'imageUrl' => $row['imageUrl'],
                            'imageId' => $row['imageDataId']
                        ];
                    }

                    echo json_encode(['code' => 0, 'message' => 'Swap successful', 'data' => $data]);
                } else {
                    echo json_encode(['code' => 1, 'message' => 'One or both of the IDs do not exist']);
                }
                break;
            case 5:
                // 刪除圖片操作
                $imageId = $_POST['imageId'];

                // 刪除指定的 AboutUsPageImage 資料
                $stmt = $pdo->prepare("DELETE FROM AboutUsPageImage WHERE id = :imageId");
                $stmt->bindParam(':imageId', $imageId);
                $stmt->execute();

                // 重新查詢並回傳
                $stmt = $pdo->prepare("SELECT AboutusPage.id AS aboutusId, AboutusPage.text, AboutUsPageImage.id AS imageId, AboutUsPageImage.imageId AS imageDataId, AboutUsPageImage.imageUrl FROM AboutusPage LEFT JOIN AboutUsPageImage ON AboutusPage.imageId = AboutUsPageImage.imageId WHERE AboutUsPageImage.imageId = 0");
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $data = [
                    'id' => $result[0]['aboutusId'],
                    'text' => $result[0]['text'],
                    'imageData' => []
                ];

                foreach ($result as $row) {
                    $data['imageData'][] = [
                        'id' => $row['imageId'],
                        'imageUrl' => $row['imageUrl'],
                        'imageId' => $row['imageDataId']
                    ];
                }

                echo json_encode(['code' => 0, 'message' => 'Delete image successful', 'data' => $data]);
                break;

                 case '6':
    // Check if the file is uploaded successfully
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $id = $_POST['id']; // 假設從 POST 請求中獲取了要更新的資料庫記錄的 ID
        $currentDirectory = getcwd(); // 當前工作目錄的絕對路徑
        $parentDirectory = dirname($currentDirectory); // 上一頁的目錄路徑
        $imgDirectory = $parentDirectory . '/img/'; // 上一頁的 img 資料夾路徑
        $filename = uniqid() . '.jpg'; // 生成唯一的檔名
        $filePath = $imgDirectory . $filename;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
            // File upload successful
            // Now update the database with the new file path
            $stmt = $pdo->prepare('UPDATE AboutUsPageImage SET imageUrl = ? WHERE id = ?');

            $dbPath = 'http://' . $_SERVER['HTTP_HOST']. '/img/' . $filename;
            $result = $stmt->execute([$dbPath, $id]); // Assuming you have the ID of the row to update in $id variable

            if ($result) {
                // Fetch the updated data from the database
                // 重新查詢並回傳
                $stmt = $pdo->prepare("SELECT AboutusPage.id AS aboutusId, AboutusPage.text, AboutUsPageImage.id AS imageId, AboutUsPageImage.imageId AS imageDataId, AboutUsPageImage.imageUrl FROM AboutusPage LEFT JOIN AboutUsPageImage ON AboutusPage.imageId = AboutUsPageImage.imageId WHERE AboutUsPageImage.imageId = 0");
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $data = [
                    'id' => $result[0]['aboutusId'],
                    'text' => $result[0]['text'],
                    'imageData' => []
                ];

                foreach ($result as $row) {
                    $data['imageData'][] = [
                        'id' => $row['imageId'],
                        'imageUrl' => $row['imageUrl'],
                        'imageId' => $row['imageDataId']
                    ];
                }

                echo json_encode(['code' => 0, 'message' => '檔案上傳成功', 'data' => $data]);
            } else {
                echo json_encode(['code' => 1, 'message' => '更新資料庫失敗']);
            }
        } else {
            echo json_encode(['code' => 1, 'message' => '檔案移動失敗']);
        }
    } else {
        echo json_encode(['code' => 1, 'message' => '檔案上傳失敗']);
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
}
?>