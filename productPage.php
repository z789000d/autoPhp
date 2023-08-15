<?php
// 資料庫連線設定
$host = '127.0.0.1';
// 資料庫主機
$dbname = 'test1';
// 資料庫名稱
$username = 'root';
// 資料庫使用者名稱
$password = '';
// 資料庫密碼
// 建立資料庫連線
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    die("資料庫連線失敗: " . $e->getMessage());
}
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
// 可以添加其他支援的請求方法，例如 PUT、DELETE 等
header('Access-Control-Allow-Headers: Content-Type');
// 如果您使用了自定義的 Header，請將它們添加到這裡
/// 處理 HTTP 請求
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST') {
    try {
        switch ($action) {
            case 0:
                                        // 查詢操作
            $stmt = $pdo->prepare("SELECT ProductPage.id, ProductPage.category, ProductPage.name, ProductPage.description, ProductPage.videoLink, ProductPage.imageId, ProductPageImage.id AS imageId, ProductPageImage.imageUrl FROM ProductPage LEFT JOIN ProductPageImage ON ProductPage.id = ProductPageImage.imageId");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $data = [];
            foreach ($result as $row) {
                $imageData = [
                                                        'id' => $row['imageId'],
                                                        'imageUrl' => $row['imageUrl'],
                                                        'imageId' => $row['id']
                                                    ];
                if (!isset($data[$row['id']])) {
                    $data[$row['id']] = [
                                                                    'id' => $row['id'],
                                                                    'category' => $row['category'],
                                                                    'name' => $row['name'],
                                                                    'description' => $row['description'],
                                                                    'videoLink' => $row['videoLink'],
                                                                    'image' => []
                                                                ];
                }
                $data[$row['id']]['image'][] = $imageData;
            }
            // 將關聯陣列轉換為索引陣列
            $data = array_values($data);
            echo json_encode(['code' => 0, 'data' => $data]);
            break;
            case 1:
                                        // 新增操作
            $category = $_POST['category'];
            $name = $_POST['name'];
            $description = $_POST['description'];
            $videoLink = $_POST['videoLink'];
            $imageUrl = $_POST['imageUrl'];
            $stmtInsert = $pdo->prepare("INSERT INTO ProductPage (category, name, description, videoLink, imageId) VALUES (:category, :name, :description, :videoLink, :imageId)");
            $stmtInsert->bindParam(':category', $category);
            $stmtInsert->bindParam(':name', $name);
            $stmtInsert->bindParam(':description', $description);
            $stmtInsert->bindParam(':videoLink', $videoLink);
            $stmtInsert->bindParam(':imageId', $imageId);
            $stmtInsert->execute();
            $imageId = $pdo->lastInsertId();
            // Get the auto-incremented id
            $stmtInsertImage = $pdo->prepare("INSERT INTO ProductPageImage (imageId, imageUrl) VALUES (:imageId, :imageUrl)");
            $stmtInsertImage->bindParam(':imageId', $imageId);
            $stmtInsertImage->bindParam(':imageUrl', $imageUrl);
            $stmtInsertImage->execute();
            // 重新查詢資料
            $stmt = $pdo->prepare("SELECT ProductPage.id, ProductPage.category, ProductPage.name, ProductPage.description, ProductPage.videoLink, ProductPage.imageId, ProductPageImage.id AS imageId, ProductPageImage.imageUrl FROM ProductPage LEFT JOIN ProductPageImage ON ProductPage.id = ProductPageImage.imageId");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $data = [];
            foreach ($result as $row) {
                $imageData = [
                                                        'id' => $row['imageId'],
                                                        'imageUrl' => $row['imageUrl'],
                                                        'imageId' => $row['id']
                                                    ];
                if (!isset($data[$row['id']])) {
                    $data[$row['id']] = [
                                                                    'id' => $row['id'],
                                                                    'category' => $row['category'],
                                                                    'name' => $row['name'],
                                                                    'description' => $row['description'],
                                                                    'videoLink' => $row['videoLink'],
                                                                    'image' => []
                                                                ];
                }
                $data[$row['id']]['image'][] = $imageData;
            }
            // 將關聯陣列轉換為索引陣列
            $data = array_values($data);
            echo json_encode(['code' => 0, 'data' => $data]);
            break;
            case 2:
                                        // 新增 ProductPageImage
            $imageId = $_POST['imageId'];
            $imageUrl = $_POST['imageUrl'];
            $stmtInsertImage = $pdo->prepare("INSERT INTO ProductPageImage (imageId, imageUrl) VALUES (:imageId, :imageUrl)");
            $stmtInsertImage->bindParam(':imageId', $imageId);
            $stmtInsertImage->bindParam(':imageUrl', $imageUrl);
            $stmtInsertImage->execute();
            // 重新查詢資料
            $stmt = $pdo->prepare("SELECT ProductPage.id, ProductPage.category, ProductPage.name, ProductPage.description, ProductPage.videoLink, ProductPage.imageId, ProductPageImage.id AS imageId, ProductPageImage.imageUrl FROM ProductPage LEFT JOIN ProductPageImage ON ProductPage.id = ProductPageImage.imageId");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $data = [];
            foreach ($result as $row) {
                $imageData = [
                                                        'id' => $row['imageId'],
                                                        'imageUrl' => $row['imageUrl'],
                                                        'imageId' => $row['id']
                                                    ];
                if (!isset($data[$row['id']])) {
                    $data[$row['id']] = [
                                                                    'id' => $row['id'],
                                                                    'category' => $row['category'],
                                                                    'name' => $row['name'],
                                                                    'description' => $row['description'],
                                                                    'videoLink' => $row['videoLink'],
                                                                    'image' => []
                                                                ];
                }
                $data[$row['id']]['image'][] = $imageData;
            }
            // 將關聯陣列轉換為索引陣列
            $data = array_values($data);
            echo json_encode(['code' => 0, 'data' => $data]);
            break;
            case 3:
                                        // 刪除操作
            $id = $_POST['id'];
            // 刪除 ProductPageImage 中 imageId = $id 的資料
            $stmtDeleteProductImage = $pdo->prepare("DELETE FROM ProductPageImage WHERE imageId = :imageId");
            $stmtDeleteProductImage->bindParam(':imageId', $id);
            $stmtDeleteProductImage->execute();
            // 刪除 ProductPage 中 id = $id 的資料
            $stmtDeleteProductPage = $pdo->prepare("DELETE FROM ProductPage WHERE id = :id");
            $stmtDeleteProductPage->bindParam(':id', $id);
            $stmtDeleteProductPage->execute();
            // 重新查詢資料
            $stmt = $pdo->prepare("SELECT ProductPage.id, ProductPage.category, ProductPage.name, ProductPage.description, ProductPage.videoLink, ProductPage.imageId, ProductPageImage.id AS imageId, ProductPageImage.imageUrl FROM ProductPage LEFT JOIN ProductPageImage ON ProductPage.id = ProductPageImage.imageId");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $data = [];
            foreach ($result as $row) {
                $imageData = [
                                                        'id' => $row['imageId'],
                                                        'imageUrl' => $row['imageUrl'],
                                                        'imageId' => $row['id']
                                                    ];
                if (!isset($data[$row['id']])) {
                    $data[$row['id']] = [
                                                                    'id' => $row['id'],
                                                                    'category' => $row['category'],
                                                                    'name' => $row['name'],
                                                                    'description' => $row['description'],
                                                                    'videoLink' => $row['videoLink'],
                                                                    'image' => []
                                                                ];
                }
                $data[$row['id']]['image'][] = $imageData;
            }
            // 將關聯陣列轉換為索引陣列
            $data = array_values($data);
            echo json_encode(['code' => 0, 'data' => $data]);
            break;
            case '4':
                            // 刪除操作
            $imageId = $_POST['id'];
            // 獲取帶入的 id
            // 刪除 ProductPageImage 中 id = $imageId 的資料
            $stmtDeleteImage = $pdo->prepare("DELETE FROM ProductPageImage WHERE id = :id");
            $stmtDeleteImage->bindParam(':id', $imageId);
            $stmtDeleteImage->execute();
            // 重新查詢資料並回傳
            $stmt = $pdo->prepare("SELECT ProductPage.id, ProductPage.category, ProductPage.name, ProductPage.description, ProductPage.videoLink, ProductPage.imageId, ProductPageImage.id AS imageId, ProductPageImage.imageUrl FROM ProductPage LEFT JOIN ProductPageImage ON ProductPage.id = ProductPageImage.imageId");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $data = [];
            foreach ($result as $row) {
                $imageData = [
                                            'id' => $row['imageId'],
                                            'imageUrl' => $row['imageUrl'],
                                            'imageId' => $row['id']
                                        ];
                if (!isset($data[$row['id']])) {
                    $data[$row['id']] = [
                                                        'id' => $row['id'],
                                                        'category' => $row['category'],
                                                        'name' => $row['name'],
                                                        'description' => $row['description'],
                                                        'videoLink' => $row['videoLink'],
                                                        'image' => []
                                                    ];
                }
                $data[$row['id']]['image'][] = $imageData;
            }
            // 將關聯陣列轉換為索引陣列
            $data = array_values($data);
            echo json_encode(['code' => 0, 'data' => $data]);
            break;
            case '5':
                            // 修改操作
            $id = $_POST['id'];
            $category = $_POST['category'];
            $name = $_POST['name'];
            $description = $_POST['description'];
            $videoLink = $_POST['videoLink'];
            // 更新 ProductPage 表中的資料
            $stmtUpdate = $pdo->prepare("UPDATE ProductPage SET category = :category, name = :name, description = :description, videoLink = :videoLink WHERE id = :id");
            $stmtUpdate->bindParam(':id', $id);
            $stmtUpdate->bindParam(':category', $category);
            $stmtUpdate->bindParam(':name', $name);
            $stmtUpdate->bindParam(':description', $description);
            $stmtUpdate->bindParam(':videoLink', $videoLink);
            $stmtUpdate->execute();
            // 重新查詢資料並回傳
            $stmt = $pdo->prepare("SELECT ProductPage.id, ProductPage.category, ProductPage.name, ProductPage.description, ProductPage.videoLink, ProductPage.imageId, ProductPageImage.id AS imageId, ProductPageImage.imageUrl FROM ProductPage LEFT JOIN ProductPageImage ON ProductPage.id = ProductPageImage.imageId");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $data = [];
            foreach ($result as $row) {
                $imageData = [
                                            'id' => $row['imageId'],
                                            'imageUrl' => $row['imageUrl'],
                                            'imageId' => $row['id']
                                        ];
                if (!isset($data[$row['id']])) {
                    $data[$row['id']] = [
                                                        'id' => $row['id'],
                                                        'category' => $row['category'],
                                                        'name' => $row['name'],
                                                        'description' => $row['description'],
                                                        'videoLink' => $row['videoLink'],
                                                        'image' => []
                                                    ];
                }
                $data[$row['id']]['image'][] = $imageData;
            }
            // 將關聯陣列轉換為索引陣列
            $data = array_values($data);
            echo json_encode(['code' => 0, 'data' => $data]);
            break;
            case '6':
                            // Check if the file is uploaded successfully
            if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                $id = $_POST['id'];
                // 假設從 POST 請求中獲取了要更新的資料庫記錄的 ID
                $currentDirectory = getcwd();
                // 當前工作目錄的絕對路徑
                $parentDirectory = dirname($currentDirectory);
                // 上一頁的目錄路徑
                $imgDirectory = $parentDirectory . '/img/';
                // 上一頁的 img 資料夾路徑
                $filename = uniqid() . '.jpg';
                // 生成唯一的檔名
                $filePath = $imgDirectory . $filename;
                // Move the uploaded file to the target directory
                if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
                    // File upload successful
                    // Now update the database with the new file path
                    $stmt = $pdo->prepare('UPDATE ProductPageImage SET imageUrl = ? WHERE id = ?');
                    $dbPath = 'http://' . $_SERVER['HTTP_HOST']. '/img/' . $filename;
                    $result = $stmt->execute([$dbPath, $id]);
                    // Assuming you have the ID of the row to update in $id variable
                    if ($result) {
                        // Fetch the updated data from the database
                        // 重新查詢並回傳
                        $stmt = $pdo->prepare("SELECT ProductPage.id, ProductPage.category, ProductPage.name, ProductPage.description, ProductPage.videoLink, ProductPage.imageId, ProductPageImage.id AS imageId, ProductPageImage.imageUrl FROM ProductPage LEFT JOIN ProductPageImage ON ProductPage.id = ProductPageImage.imageId");
                        $stmt->execute();
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $data = [];
                        foreach ($result as $row) {
                            $imageData = [
                                                                    'id' => $row['imageId'],
                                                                    'imageUrl' => $row['imageUrl'],
                                                                    'imageId' => $row['id']
                                                                ];
                            if (!isset($data[$row['id']])) {
                                $data[$row['id']] = [
                                                                                'id' => $row['id'],
                                                                                'category' => $row['category'],
                                                                                'name' => $row['name'],
                                                                                'description' => $row['description'],
                                                                                'videoLink' => $row['videoLink'],
                                                                                'image' => []
                                                                            ];
                            }
                            $data[$row['id']]['image'][] = $imageData;
                        }
                        // 將關聯陣列轉換為索引陣列
                        $data = array_values($data);
                        echo json_encode(['code' => 0, 'data' => $data]);
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
    }
    catch (PDOException $e) {
        echo json_encode(['code' => 1, 'message' => $e->getMessage()]);
    }
    catch (Exception $e) {
        echo json_encode(['code' => 1, 'message' => '發生其他錯誤']);
    }
} else {
    echo json_encode(['code' => 1, 'message' => '不支援的請求方法']);
}
?>