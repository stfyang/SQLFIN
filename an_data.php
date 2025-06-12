<?php
//營運分析後端
header('Content-Type: application/json; charset=utf-8');
include 'db.php';

try {
    // 本月每日銷售資料 (總銷售額)
    $stmt = $pdo->prepare("
        SELECT DATE(點餐日期與時間) AS 日期, SUM(小計) AS 總銷售 
        FROM 訂單 
        WHERE MONTH(點餐日期與時間) = MONTH(CURDATE())
          AND YEAR(點餐日期與時間) = YEAR(CURDATE())
        GROUP BY DATE(點餐日期與時間)
    ");
    $stmt->execute();
    $data = $stmt->fetchAll();

    // 本月總營收
    $stmt = $pdo->prepare("
        SELECT SUM(小計) AS total 
        FROM 訂單 
        WHERE MONTH(點餐日期與時間) = MONTH(CURDATE())
          AND YEAR(點餐日期與時間) = YEAR(CURDATE())
    ");
    $stmt->execute();
    $totalRevenue = $stmt->fetchColumn() ?: 0;

    // 本月銷售最佳品項（份數）
    $stmt = $pdo->prepare("
        SELECT b.菜品名, SUM(b.數量) AS 次數
        FROM 包含 b
        JOIN 訂單 o ON b.訂單編號 = o.訂單編號
        WHERE MONTH(o.點餐日期與時間) = MONTH(CURDATE())
          AND YEAR(o.點餐日期與時間) = YEAR(CURDATE())
        GROUP BY b.菜品名
        ORDER BY 次數 DESC
        LIMIT 1
    ");
    $stmt->execute();
    $bestDish = $stmt->fetch();

    // 本月所有餐點銷售數量
    $stmt = $pdo->prepare("
        SELECT b.菜品名, SUM(b.數量) AS 次數
        FROM 包含 b
        JOIN 訂單 o ON b.訂單編號 = o.訂單編號
        WHERE MONTH(o.點餐日期與時間) = MONTH(CURDATE())
          AND YEAR(o.點餐日期與時間) = YEAR(CURDATE())
        GROUP BY b.菜品名
        ORDER BY 次數 DESC
    ");
    $stmt->execute();
    $dishSales = $stmt->fetchAll();

    // 取得本月每個菜品銷售量
    $stmt = $pdo->prepare("
        SELECT b.菜品名, SUM(b.數量) AS 銷售數量
        FROM 包含 b
        JOIN 訂單 o ON b.訂單編號 = o.訂單編號
        WHERE MONTH(o.點餐日期與時間) = MONTH(CURDATE())
          AND YEAR(o.點餐日期與時間) = YEAR(CURDATE())
        GROUP BY b.菜品名
    ");
    $stmt->execute();
    $salesData = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // 取得每個菜品消耗的原物料量及最新價格
    $stmt = $pdo->prepare("
        SELECT x.菜品名, x.原物料名稱, x.消耗物料量, p.價格
        FROM 消耗 x
        JOIN (
            SELECT 原物料名稱, 價格
            FROM 擁有
            WHERE (原物料名稱, 期限) IN (
                SELECT 原物料名稱, MAX(期限)
                FROM 擁有
                GROUP BY 原物料名稱
            )
        ) p ON x.原物料名稱 = p.原物料名稱
    ");
    $stmt->execute();
    $consumptionData = $stmt->fetchAll();

    // 計算成本
    $totalCost = 0;
    foreach ($consumptionData as $row) {
        $dish = $row['菜品名'];
        $原料消耗 = $row['消耗物料量'];
        $price = $row['價格'];
        $salesQty = $salesData[$dish] ?? 0;

        $totalCost += $salesQty * $原料消耗 * $price;
    }

    // 利潤
    $profit = $totalRevenue - $totalCost;

    // 原物料使用分析
    $materialUsage = [];
    foreach ($consumptionData as $row) {
        $原料 = $row['原物料名稱'];
        $dish = $row['菜品名'];
        $消耗量 = $row['消耗物料量'];
        $銷售數 = $salesData[$dish] ?? 0;
        $materialUsage[$原料] = ($materialUsage[$原料] ?? 0) + $消耗量 * $銷售數;
    }

    
    $sql_material_cost = "
        SELECT 原物料名稱, 
               SUM(價格) / SUM(原料數量) AS 平均單價
        FROM 擁有
        GROUP BY 原物料名稱
    ";
    $stmt = $pdo->query($sql_material_cost);
    $materialCosts = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $materialCosts[$row['原物料名稱']] = floatval($row['平均單價']);
    }

    // 取得所有菜品名稱與單價
    $sql_products = "SELECT 菜品名, 單價 FROM 菜品";
    $stmt = $pdo->query($sql_products);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $avgCostProfit = [];
    foreach ($products as $product) {
        $productName = $product['菜品名'];
        $salePrice = floatval($product['單價']);

        // 查菜品所有消耗的原物料與消耗量
        $sql_consumption = "
            SELECT 原物料名稱, 消耗物料量
            FROM 消耗
            WHERE 菜品名 = ?
        ";
        $stmt = $pdo->prepare($sql_consumption);
        $stmt->execute([$productName]);
        $consumptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $cost = 0.0;
        foreach ($consumptions as $consumption) {
            $materialName = $consumption['原物料名稱'];
            $amountUsed = floatval($consumption['消耗物料量']);
            $unitCost = $materialCosts[$materialName] ?? 0;

            $cost += $unitCost * $amountUsed;
        }

        $profitSingle = $salePrice - $cost;

        $avgCostProfit[] = [
            '菜品名' => $productName,
            '單價' => $salePrice,
            '成本' => round($cost, 2),
            '利潤' => round($profitSingle, 2),
        ];
    }

    
    echo json_encode([
        "chartData" => $data,
        "totalRevenue" => $totalRevenue,
        "totalCost" => $totalCost,
        "profit" => $profit,
        "bestDish" => $bestDish,
        "dishSales" => $dishSales,
        "materialUsage" => $materialUsage,
        "avgCostProfit" => $avgCostProfit,  // 新增欄位
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
