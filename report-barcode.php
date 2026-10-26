<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$storeid = intval($_SESSION['storeid'] ?? 1);
$language = $_SESSION['lang'] ?? 'ar';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qty-id']) && is_array($_POST['qty-id'])) {
    // Empty existing report table
    mysqli_query($adController->MySQL, "DELETE FROM report_barcode");

    $ids      = $_POST['qty-id'];
    $prices   = $_POST['qty-price'] ?? [];
    $barcodes = $_POST['qty-barcode'] ?? [];
    $fills    = $_POST['qty-fill'] ?? [];

    // Pre-cache item details in a single query
    $cleanIds = array_map('intval', $ids);
    $cleanIds = array_filter($cleanIds, fn($id) => $id > 0);

    $itemsMap = [];
    if (!empty($cleanIds)) {
        $idStr = implode(',', $cleanIds);
        $itemQ = "SELECT i.id, i.name_ar, i.name_en, u.name_ar AS unit_ar, u.name_en AS unit_en 
                  FROM items i 
                  LEFT JOIN units u ON u.id = i.unit AND u.storeid = '$storeid'
                  WHERE i.id IN ($idStr) AND i.storeid = '$storeid'";
        $itemRes = mysqli_query($adController->MySQL, $itemQ);
        if ($itemRes) {
            while ($row = mysqli_fetch_assoc($itemRes)) {
                $itemsMap[$row['id']] = $row;
            }
        }
    }

    $insertValues = [];
    $totalCount = count($ids);

    for ($i = 0; $i < $totalCount; $i++) {
        $itemId    = intval($ids[$i] ?? 0);
        $priceVal  = floatval($prices[$i] ?? 0);
        $barVal    = trim($barcodes[$i] ?? '');
        $fillCount = max(1, min(1000, intval($fills[$i] ?? 1))); // Clamp 1..1000 to prevent DOS

        $itemData = $itemsMap[$itemId] ?? null;
        $name_ar  = $itemData ? $itemData['name_ar'] : '';
        $name_en  = $itemData ? $itemData['name_en'] : '';
        $unit_ar  = $itemData ? ($itemData['unit_ar'] ?? '') : '';
        $unit_en  = $itemData ? ($itemData['unit_en'] ?? '') : '';

        $escNameAr = mysqli_real_escape_string($adController->MySQL, $name_ar);
        $escNameEn = mysqli_real_escape_string($adController->MySQL, $name_en);
        $escBar    = mysqli_real_escape_string($adController->MySQL, $barVal);
        $escUnitAr = mysqli_real_escape_string($adController->MySQL, $unit_ar);
        $escUnitEn = mysqli_real_escape_string($adController->MySQL, $unit_en);

        for ($j = 0; $j < $fillCount; $j++) {
            $insertValues[] = "('$escNameEn', '$escNameAr', '$escBar', '$priceVal', '$escUnitEn', '$escUnitAr')";

            // Batch insert every 500 rows to keep memory & query length optimal
            if (count($insertValues) >= 500) {
                $batchQ = "INSERT INTO `report_barcode` (`name_en`, `name_ar`, `barcode`, `price`, `unit_en`, `unit_ar`) VALUES " . implode(',', $insertValues);
                mysqli_query($adController->MySQL, $batchQ);
                $insertValues = [];
            }
        }
    }

    if (!empty($insertValues)) {
        $batchQ = "INSERT INTO `report_barcode` (`name_en`, `name_ar`, `barcode`, `price`, `unit_en`, `unit_ar`) VALUES " . implode(',', $insertValues);
        mysqli_query($adController->MySQL, $batchQ);
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>طباعة الباركود</title>
</head>
<body>
    <script type="text/javascript">
        location.replace('reports/barcode.php');
    </script>
</body>
</html>
