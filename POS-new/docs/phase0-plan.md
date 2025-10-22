## خطة المرحلة 0 (مرجع التنفيذ)

### اللغة

- العربية فقط.

### مخطط جداول مبدئي (أسماء عربية مبسطة)

- users(id, name, username, password_hash, role, active, created_at)
- branches(id, name, phone)
- stores(id, branch_id, name, is_default)
- categories(id, name)
- units(id, name)
- items(id, category_id, unit_id, name, barcode, price, cost, min_qty, active)
- customers(id, name, phone, tax_no, address)
- suppliers(id, name, phone, tax_no, address)
- purchases(id, supplier_id, branch_id, store_id, date, total, tax, discount)
- purchase_items(id, purchase_id, item_id, qty, price, cost)
- sales(id, customer_id, branch_id, store_id, user_id, date, total, discount, tax, cash, card, status)
- sale_items(id, sale_id, item_id, qty, price, discount)
- returns(id, type, ref_id, date, total)
- return_items(id, return_id, item_id, qty, price)
- stock_moves(id, item_id, from_store_id, to_store_id, qty, type, ref_table, ref_id, date)
- discounts(id, name, type, value, active)
- settings(id, k, v)
- audit_logs(id, user_id, action, entity, entity_id, created_at)

ملاحظات:

- stock_moves لتوحيد (إدخال/إخراج/تحويل/تسوية) عبر type.
- returns: type = 'sale' | 'purchase'.

### واجهات API (مختصر)

- Auth: POST /api/login, POST /api/logout
- ماستر: CRUD categories, units, items, customers, suppliers
- مشتريات: POST/GET /api/purchases, /api/purchases/{id}
- مبيعات: POST/GET /api/sales, /api/sales/{id}
- مرتجعات: POST /api/returns
- مخزون: POST /api/stock-moves, GET /api/stock/summary
- تقارير: GET /api/reports/sales, /api/reports/items-movement
- إعدادات: GET/PUT /api/settings

### تدفق شاشة POS

1. تحديد العميل (افتراضي عميل نقدي)
2. بحث/باركود -> إضافة للسلة
3. تعديل الكميات/الخصم
4. اختيار الدفع (نقد/بطاقة)
5. حفظ الفاتورة -> تحديث المخزون -> طباعة
6. إغلاق وردية (ملخص المقبوضات)

### متطلبات أمان

- password_hash/verify، CSRF، استعلامات محضّرة، أدوار (كاشير/مدير)

### تعريف الجاهزية لكل وحدة

- CRUD كامل + صلاحيات + فحص إدخال + سجل تدقيق + تقرير أساسي
