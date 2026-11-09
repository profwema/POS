<?php
/**
 * Inventory AJAX & API Endpoints
 * WAM Tech POS & ERP
 */
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json; charset=utf-8');

// Ensure user is authenticated
if (!Auth::check()) {
    Helpers::jsonResponse(['success' => false, 'message' => 'غير مصرح لك بالوصول. يرجى تسجيل الدخول.'], 401);
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'search':
            $q = trim($_GET['q'] ?? '');
            if (empty($q)) {
                Helpers::jsonResponse(['success' => true, 'data' => []]);
            }

            // Check if it's a barcode or scale barcode first
            $scaleMatch = Item::findByBarcodeOrScale($q);
            if ($scaleMatch) {
                Helpers::jsonResponse(['success' => true, 'data' => [$scaleMatch]]);
            }

            // Otherwise, search by name, barcode, or sku
            $items = Item::getAll(['search' => $q, 'is_active' => 1], 20);
            Helpers::jsonResponse(['success' => true, 'data' => $items]);
            break;

        case 'get_item':
            $id = (int)($_GET['id'] ?? 0);
            $item = Item::findById($id);
            if (!$item) {
                Helpers::jsonResponse(['success' => false, 'message' => 'الصنف غير موجود'], 404);
            }
            $item['store_stocks'] = Item::getStoreStocks($id);
            $item['total_stock'] = Item::getTotalStock($id);
            $item['batches'] = Item::getBatches($id);
            Helpers::jsonResponse(['success' => true, 'data' => $item]);
            break;

        case 'get_history':
            $id = (int)($_GET['id'] ?? 0);
            $storeId = !empty($_GET['store_id']) ? (int)$_GET['store_id'] : null;
            $moves = Item::getMoveHistory($id, $storeId, 50);
            Helpers::jsonResponse(['success' => true, 'data' => $moves]);
            break;

        case 'generate_barcode':
            // Generate standard EAN-13 compatible code starting with 622 (Egypt Country Code)
            $random = '622' . str_pad((string)mt_rand(100000000, 999999999), 9, '0', STR_PAD_LEFT);
            // Calculate EAN-13 checksum digit
            $sum = 0;
            for ($i = 0; $i < 12; $i++) {
                $sum += (int)$random[$i] * (($i % 2 === 0) ? 1 : 3);
            }
            $checksum = (10 - ($sum % 10)) % 10;
            $barcode = $random . $checksum;

            Helpers::jsonResponse(['success' => true, 'barcode' => $barcode]);
            break;

        case 'quick_adjust':
            Security::verifyCsrf();
            Auth::requirePermission('inventory.edit');

            $itemId = (int)($_POST['item_id'] ?? 0);
            $storeId = (int)($_POST['store_id'] ?? 0);
            $actualQty = (float)($_POST['actual_qty'] ?? 0);
            $reason = trim($_POST['reason'] ?? 'تسوية جردية يدوية من شاشة الأصناف');

            if (!$itemId || !$storeId) {
                Helpers::jsonResponse(['success' => false, 'message' => 'بيانات الصنف أو المخزن غير مكتملة.'], 400);
            }

            $res = InventoryService::adjustStock($itemId, $storeId, $actualQty, $reason, Auth::id());
            Logger::audit('stock_adjusted', "تسوية جردية للصنف ID: {$itemId} في المخزن {$storeId} إلى {$actualQty}", 'stock_moves', $res['move_id'] ?? null);

            Helpers::jsonResponse(['success' => true, 'message' => 'تمت التسوية بنجاح.', 'data' => $res]);
            break;

        default:
            Helpers::jsonResponse(['success' => false, 'message' => 'إجراء غير معروف.'], 400);
    }
} catch (Exception $e) {
    Helpers::jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
}

