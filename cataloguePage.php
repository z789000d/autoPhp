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
$action = $_POST['action'] ?? '';
if ($method === 'POST') {
    try {
        switch ($action) {
            case 0:
                                        // 查詢操作
            $stmt = $pdo->prepare("SELECT CataloguePage.id, CataloguePage.name, CataloguePage.imageId, CataloguePageImage.id AS imageId, CataloguePageImage.imageUrl FROM CataloguePage LEFT JOIN CataloguePageImage ON CataloguePage.id = CataloguePageImage.imageId ORDER BY CataloguePage.id");
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
                                                                    'name' => $row['name'],
                                                                    'image' => []
                                                                ];
                }
                $data[$row['id']]['image'][] = $imageData;
            }
            // 將關聯陣列轉換為索引陣列
            $data = array_values($data);
            echo json_encode(['code' => 0, 'data' => $data]);
            break;
            case '1':
                                        // 新增操作
            $name = $_POST['name'];
            $imageUrl = $_POST['imageUrl'];
            $stmtInsert = $pdo->prepare("INSERT INTO CataloguePage (name, imageId) VALUES (:name, :imageId)");
            $stmtInsert->bindParam(':name', $name);
            $stmtInsert->bindParam(':imageId', $imageId);

            // 執行插入操作
            $stmtInsert->execute();

            // 獲取新插入的資料的 id
            $imageId = $pdo->lastInsertId();
            // Get the auto-incremented id
            $stmtInsertImage = $pdo->prepare("INSERT INTO CataloguePageImage (imageId, imageUrl) VALUES (:imageId, :imageUrl)");
            $stmtInsertImage->bindParam(':imageId', $imageId);
            $stmtInsertImage->bindParam(':imageUrl', $imageUrl);
            $stmtInsertImage->execute();
            // 重新查詢資料
            $stmt = $pdo->prepare("SELECT CataloguePage.id, CataloguePage.name, CataloguePage.imageId, CataloguePageImage.id AS imageId, CataloguePageImage.imageUrl FROM CataloguePage LEFT JOIN CataloguePageImage ON CataloguePage.id = CataloguePageImage.imageId ORDER BY CataloguePage.id");
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
                                                                    'name' => $row['name'],
                                                                    'image' => []
                                                                ];
                }
                $data[$row['id']]['image'][] = $imageData;
            }
            // 將關聯陣列轉換為索引陣列
            $data = array_values($data);
            echo json_encode(['code' => 0, 'data' => $data]);
            break;
            case '2':
                                        // 新增 CataloguePageImage
            $imageId = $_POST['imageId'];
            $imageUrl = $_POST['imageUrl'];
            $stmtInsertImage = $pdo->prepare("INSERT INTO CataloguePageImage (imageId, imageUrl) VALUES (:imageId, :imageUrl)");
            $stmtInsertImage->bindParam(':imageId', $imageId);
            $stmtInsertImage->bindParam(':imageUrl', $imageUrl);
            $stmtInsertImage->execute();
             // 重新查詢資料
            $stmt = $pdo->prepare("SELECT CataloguePage.id, CataloguePage.name, CataloguePage.imageId, CataloguePageImage.id AS imageId, CataloguePageImage.imageUrl FROM CataloguePage LEFT JOIN CataloguePageImage ON CataloguePage.id = CataloguePageImage.imageId ORDER BY CataloguePage.id");
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
                                                                    'name' => $row['name'],
                                                                    'image' => []
                                                                ];
                }
                $data[$row['id']]['image'][] = $imageData;
            }
            // 將關聯陣列轉換為索引陣列
            $data = array_values($data);
            echo json_encode(['code' => 0, 'data' => $data]);
            break;
            case '3':
                                        // 刪除操作
            $id = $_POST['id'];
            // 刪除 CataloguePageImage 中 imageId = $id 的資料
            $stmtDeleteCatalogueImage = $pdo->prepare("DELETE FROM CataloguePageImage WHERE imageId = :imageId");
            $stmtDeleteCatalogueImage->bindParam(':imageId', $id);
            $stmtDeleteCatalogueImage->execute();
            // 刪除 CataloguePage 中 id = $id 的資料
            $stmtDeleteCataloguePage = $pdo->prepare("DELETE FROM CataloguePage WHERE id = :id");
            $stmtDeleteCataloguePage->bindParam(':id', $id);
            $stmtDeleteCataloguePage->execute();
              // 重新查詢資料
            $stmt = $pdo->prepare("SELECT CataloguePage.id, CataloguePage.name, CataloguePage.imageId, CataloguePageImage.id AS imageId, CataloguePageImage.imageUrl FROM CataloguePage LEFT JOIN CataloguePageImage ON CataloguePage.id = CataloguePageImage.imageId ORDER BY CataloguePage.id");
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
                                                                    'name' => $row['name'],
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
            // 刪除 CataloguePageImage 中 id = $imageId 的資料
            $stmtDeleteImage = $pdo->prepare("DELETE FROM CataloguePageImage WHERE id = :id");
            $stmtDeleteImage->bindParam(':id', $imageId);
            $stmtDeleteImage->execute();
            // 重新查詢資料並回傳
              // 重新查詢資料
            $stmt = $pdo->prepare("SELECT CataloguePage.id, CataloguePage.name, CataloguePage.imageId, CataloguePageImage.id AS imageId, CataloguePageImage.imageUrl FROM CataloguePage LEFT JOIN CataloguePageImage ON CataloguePage.id = CataloguePageImage.imageId ORDER BY CataloguePage.id");
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
                                                                    'name' => $row['name'],
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
            $name = $_POST['name'];
            // 更新 CataloguePage 表中的資料
            $stmtUpdate = $pdo->prepare("UPDATE CataloguePage SET name = :name WHERE id = :id");
            $stmtUpdate->bindParam(':id', $id);
            $stmtUpdate->bindParam(':category', $category);
            $stmtUpdate->bindParam(':name', $name);
            $stmtUpdate->bindParam(':description', $description);
            $stmtUpdate->bindParam(':videoLink', $videoLink);
            $stmtUpdate->execute();
              // 重新查詢資料
            $stmt = $pdo->prepare("SELECT CataloguePage.id, CataloguePage.name, CataloguePage.imageId, CataloguePageImage.id AS imageId, CataloguePageImage.imageUrl FROM CataloguePage LEFT JOIN CataloguePageImage ON CataloguePage.id = CataloguePageImage.imageId ORDER BY CataloguePage.id");
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
                                                                    'name' => $row['name'],
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
                    $stmt = $pdo->prepare('UPDATE CataloguePageImage SET imageUrl = ? WHERE id = ?');
                    $dbPath = 'http://' . $_SERVER['HTTP_HOST']. '/img/' . $filename;
                    $result = $stmt->execute([$dbPath, $id]);
                    // Assuming you have the ID of the row to update in $id variable
                    if ($result) {
                        // Fetch the updated data from the database
                           // 重新查詢資料
            $stmt = $pdo->prepare("SELECT CataloguePage.id, CataloguePage.name, CataloguePage.imageId, CataloguePageImage.id AS imageId, CataloguePageImage.imageUrl FROM CataloguePage LEFT JOIN CataloguePageImage ON CataloguePage.id = CataloguePageImage.imageId ORDER BY CataloguePage.id");
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
                                                                    'name' => $row['name'],
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
             case '7':
    // 交換操作
    $id1 = $_POST['id1'];
    $id2 = $_POST['id2'];

    // 取得原始資料
    $stmtSelect1 = $pdo->prepare("SELECT * FROM CataloguePage WHERE id = :id1");
    $stmtSelect1->bindParam(':id1', $id1);
    $stmtSelect1->execute();
    $row1 = $stmtSelect1->fetch(PDO::FETCH_ASSOC);

    $stmtSelect2 = $pdo->prepare("SELECT * FROM CataloguePage WHERE id = :id2");
    $stmtSelect2->bindParam(':id2', $id2);
    $stmtSelect2->execute();
    $row2 = $stmtSelect2->fetch(PDO::FETCH_ASSOC);

    if (!$row1 || !$row2) {
        echo json_encode(['code' => 1, 'message' => '指定的 ID 不存在']);
        break;
    }

    try {
        $pdo->beginTransaction();

        // 更新第一筆資料
        $stmtUpdate1 = $pdo->prepare("UPDATE CataloguePage SET name = :name1 WHERE id = :id1");
        $stmtUpdate1->bindParam(':id1', $id1);
        $stmtUpdate1->bindParam(':name1', $row2['name']);
        $stmtUpdate1->execute();

        // 更新第二筆資料
        $stmtUpdate2 = $pdo->prepare("UPDATE CataloguePage SET name = :name2 WHERE id = :id2");
        $stmtUpdate2->bindParam(':id2', $id2);
        $stmtUpdate2->bindParam(':name2', $row1['name']);
        $stmtUpdate2->execute();

        $pdo->commit();


        $pdo->beginTransaction();

        // 查詢 imageId = id1 的資料
        $stmt1 = $pdo->prepare("SELECT * FROM CataloguePageImage WHERE imageId = :id1");
        $stmt1->bindParam(':id1', $id1);
        $stmt1->execute();
        $result1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);

        // 查詢 imageId = id1 的資料
        $stmt2 = $pdo->prepare("SELECT * FROM CataloguePageImage WHERE imageId = :id2");
        $stmt2->bindParam(':id2', $id2);
        $stmt2->execute();
        $result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        if (count($result1) > 0) {
            foreach ($result1 as $row) {
                $stmtUpdate3 = $pdo->prepare("UPDATE CataloguePageImage SET imageId = $id2 WHERE id = :id");
                $stmtUpdate3->bindParam(':id', $row['id']);
                $stmtUpdate3->execute();
         }
        } 

        if (count($result2) > 0) {
            foreach ($result2 as $row) {
             $stmtUpdate4 = $pdo->prepare("UPDATE CataloguePageImage SET imageId = $id1 WHERE id = :id");
             $stmtUpdate4->bindParam(':id', $row['id']);
             $stmtUpdate4->execute();
         }
        } 
        $pdo->commit();

        // 重新查詢資料
            $stmt = $pdo->prepare("SELECT CataloguePage.id, CataloguePage.name, CataloguePage.imageId, CataloguePageImage.id AS imageId, CataloguePageImage.imageUrl FROM CataloguePage LEFT JOIN CataloguePageImage ON CataloguePage.id = CataloguePageImage.imageId ORDER BY CataloguePage.id");
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
                                                                    'name' => $row['name'],
                                                                    'image' => []
                                                                ];
                }
                $data[$row['id']]['image'][] = $imageData;
            }
            // 將關聯陣列轉換為索引陣列
            $data = array_values($data);
            echo json_encode(['code' => 0, 'data' => $data]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['code' => 1, 'message' => $e->getMessage()]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['code' => 1, 'message' => '發生其他錯誤']);
    }
    break;

    case '8':
    // 交換操作
    $id1 = $_POST['id1'];
    $id2 = $_POST['id2'];

    // 取得原始資料
    $stmtSelect1 = $pdo->prepare("SELECT * FROM CataloguePageImage WHERE id = :id1");
    $stmtSelect1->bindParam(':id1', $id1);
    $stmtSelect1->execute();
    $row1 = $stmtSelect1->fetch(PDO::FETCH_ASSOC);

    $stmtSelect2 = $pdo->prepare("SELECT * FROM CataloguePageImage WHERE id = :id2");
    $stmtSelect2->bindParam(':id2', $id2);
    $stmtSelect2->execute();
    $row2 = $stmtSelect2->fetch(PDO::FETCH_ASSOC);

    if (!$row1 || !$row2) {
        echo json_encode(['code' => 1, 'message' => '指定的 ID 不存在']);
        break;
    }

    try {
        $pdo->beginTransaction();

        // 更新第一筆資料
        $stmtUpdate1 = $pdo->prepare("UPDATE CataloguePageImage SET imageUrl = :imageUrl1 WHERE id = :id1");
        $stmtUpdate1->bindParam(':id1', $id1);
        $stmtUpdate1->bindParam(':imageUrl1', $row2['imageUrl']);
        $stmtUpdate1->execute();

        // 更新第二筆資料
        $stmtUpdate2 = $pdo->prepare("UPDATE CataloguePageImage SET imageUrl = :imageUrl2 WHERE id = :id2");
        $stmtUpdate2->bindParam(':id2', $id2);
        $stmtUpdate2->bindParam(':imageUrl2', $row1['imageUrl']);
        $stmtUpdate2->execute();

        $pdo->commit();

        // 重新查詢資料
            $stmt = $pdo->prepare("SELECT CataloguePage.id, CataloguePage.name, CataloguePage.imageId, CataloguePageImage.id AS imageId, CataloguePageImage.imageUrl FROM CataloguePage LEFT JOIN CataloguePageImage ON CataloguePage.id = CataloguePageImage.imageId ORDER BY CataloguePage.id");
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
                                                                    'name' => $row['name'],
                                                                    'image' => []
                                                                ];
                }
                $data[$row['id']]['image'][] = $imageData;
            }
            // 將關聯陣列轉換為索引陣列
            $data = array_values($data);
            echo json_encode(['code' => 0, 'data' => $data]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['code' => 1, 'message' => $e->getMessage()]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['code' => 1, 'message' => '發生其他錯誤']);
    }
    break;

     case '9':
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
                    $stmt = $pdo->prepare('UPDATE CataloguePageImage SET imageUrl = ? WHERE id = ?');
                    $dbPath = 'http://' . $_SERVER['HTTP_HOST']. '/img/' . $filename;
                    $result = $stmt->execute([$dbPath, $id]);
                    // Assuming you have the ID of the row to update in $id variable
                    if ($result) {
                        // 重新查詢資料
            $stmt = $pdo->prepare("SELECT CataloguePage.id, CataloguePage.name, CataloguePage.imageId, CataloguePageImage.id AS imageId, CataloguePageImage.imageUrl FROM CataloguePage LEFT JOIN CataloguePageImage ON CataloguePage.id = CataloguePageImage.imageId ORDER BY CataloguePage.id");
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
                                                                    'name' => $row['name'],
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