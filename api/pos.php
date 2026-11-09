<?php
/**
 * POS Fast JSON Endpoints (Sub-millisecond latency)
 * WAM Tech POS & ERP
 */
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json; charset=utf-8');

if (!Auth::check()) {
    Helpers::jsonResponse(['success' => false, 'message' => 'غير مصرح لك. يرجى تسجيل الدخول.'], 401);
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$userId = (int)Auth::id();

try {
    switch ($action) {
        // 1. بحث فائق السرعة عن الأصناف (بالباركود، باركود الميزان، أو الاسم)
        case 'search_item':
            $q = trim($_GET['q'] ?? '');
            if (empty($q)) {
                Helpers::jsonResponse(['success' => true, 'items' => []]);
            }

            // فحص باركود الميزان أو الباركود المباشر أولاً
            $match = Item::findByBarcodeOrScale($q);
            if ($match) {
                // Attach current stock
                $activeShift = Shift::getActiveShift($userId);
                $storeId = $activeShift ? (int)$activeShift['store_id'] : 2;
                $avail = (float)Database::getInstance()->fetchValue("
                    SELECT balance_after FROM stock_moves 
                    WHERE item_id = ? AND store_id = ? 
                    ORDER BY id DESC LIMIT 1
                ", [$match['id'], $storeId]);
                $match['available_stock'] = $avail;

                Helpers::jsonResponse(['success' => true, 'items' => [$match], 'exact_match' => true]);
            }

            // بحث بالاسم أو الكود
            $items = Item::getAll(['search' => $q, 'is_active' => 1], 15);
            Helpers::jsonResponse(['success' => true, 'items' => $items, 'exact_match' => false]);
            break;

        // 2. إتمام عملية البيع والدفع (Checkout)
        case 'checkout':
            Security::verifyCsrf();
            $payload = json_decode(file_get_contents('php://input'), true);
            if (!$payload) {
                $payload = $_POST;
            }

            $result = PosService::checkout($payload, $userId);
            Helpers::jsonResponse($result);
            break;

        // 3. حالة الوردية الحالية
        case 'get_shift_status':
            $shift = Shift::getActiveShift($userId);
            if ($shift) {
                $summary = Shift::getShiftSummary($shift['id']);
                Helpers::jsonResponse(['success' => true, 'has_shift' => true, 'shift' => $summary['shift'], 'expected_cash' => $summary['expected_cash'], 'total_sales' => $summary['total_sales']]);
            } else {
                Helpers::jsonResponse(['success' => true, 'has_shift' => false]);
            }
            break;

        // 4. فتح وردية جديدة
        case 'open_shift':
            Security::verifyCsrf();
            $openingBalance = (float)($_POST['opening_balance'] ?? 0.0);
            $storeId = (int)($_POST['store_id'] ?? 2);
            $notes = trim($_POST['notes'] ?? '');

            $shift = Shift::openShift($userId, $storeId, $openingBalance, $notes);
            Logger::audit('shift_opened', 'shifts', (int)$shift['id'], null, [
                'shift_number'    => $shift['shift_number'],
                'opening_balance' => $openingBalance
            ]);
            Helpers::jsonResponse(['success' => true, 'shift' => $shift]);
            break;

        // 5. إغلاق الوردية
        case 'close_shift':
            Security::verifyCsrf();
            $shiftId = (int)($_POST['shift_id'] ?? 0);
            $actualCash = (float)($_POST['actual_cash'] ?? 0.0);
            $notes = trim($_POST['notes'] ?? '');

            $res = Shift::closeShift($shiftId, $actualCash, $notes);
            Logger::audit('shift_closed', 'shifts', (int)$shiftId, null, [
                'shift_number' => $res['shift_number'],
                'actual_cash'  => $actualCash,
                'difference'   => $res['difference']
            ]);
            Helpers::jsonResponse(['success' => true, 'summary' => $res]);
            break;

        // 6. تعليق سلة (Hold Cart)
        case 'hold_cart':
            Security::verifyCsrf();
            $payload = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $shift = Shift::getActiveShift($userId);
            $shiftId = $shift ? (int)$shift['id'] : null;

            $ref = trim($payload['reference'] ?? '');
            $customerId = (int)($payload['customer_id'] ?? 1);
            $cartItems = $payload['items'] ?? [];
            $total = (float)($payload['total'] ?? 0.0);

            $holdId = PosService::holdCart($userId, $shiftId, $ref, $customerId, $cartItems, $total);
            Helpers::jsonResponse(['success' => true, 'hold_id' => $holdId, 'message' => 'تم تعليق الفاتورة بنجاح.']);
            break;

        // 7. جلب السلات المعلقة
        case 'get_held_carts':
            $held = PosService::getHeldCarts($userId);
            Helpers::jsonResponse(['success' => true, 'held_carts' => $held]);
            break;

        // 8. استرجاع سلة معلقة
        case 'resume_held_cart':
            Security::verifyCsrf();
            $holdId = (int)($_POST['hold_id'] ?? 0);
            $cart = PosService::resumeHeldCart($holdId);
            Helpers::jsonResponse(['success' => true, 'cart' => $cart]);
            break;

        // 9. بحث عن عميل
        case 'search_customer':
            $q = trim($_GET['q'] ?? '');
            $customers = Customer::getAll($q, 15);
            Helpers::jsonResponse(['success' => true, 'customers' => $customers]);
            break;

        // 10. إنشاء عميل سريع من شاشة الكاشير
        case 'quick_customer':
            Security::verifyCsrf();
            $name = trim($_POST['name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');

            if (empty($name)) {
                throw new Exception("اسم العميل مطلوب.");
            }

            if (!empty($phone)) {
                $exist = Customer::findByPhone($phone);
                if ($exist) {
                    Helpers::jsonResponse(['success' => true, 'customer' => $exist, 'message' => 'العميل مسجل مسبقاً بهذا الهاتف.']);
                    exit();
                }
            }

            $cid = Customer::create([
                'name'         => $name,
                'phone'        => $phone,
                'credit_limit' => (float)($_POST['credit_limit'] ?? 0.0),
                'notes'        => 'عميل مضاف سريعاً من شاشة الكاشير'
            ]);

            $newCust = Customer::findById($cid);
            Helpers::jsonResponse(['success' => true, 'customer' => $newCust, 'message' => 'تمت إضافة العميل بنجاح.']);
            break;

        default:
            Helpers::jsonResponse(['success' => false, 'message' => 'إجراء غير معروف.'], 400);
    }
} catch (Exception $e) {
    Helpers::jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
}
