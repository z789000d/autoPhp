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

                $imageId = $pdo->lastInsertId(); // Get the auto-incremented id

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

            default:
                echo json_encode(['code' => 1, 'message' => '不支援的操作']);
                break;
        }
    } catch (PDOException $e) {
        echo json_encode(['code' => 1, 'message' => $e->getMessage()]);
    } catch (Exception $e) {
        echo json_encode(['code' => 1, 'message' => '發生其他錯誤']);
    }
} else {
    echo json_encode(['code' => 1, 'message' => '不支援的請求方法']);
}
?>