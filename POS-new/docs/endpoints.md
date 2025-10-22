## REST API (مختصر)

الاستجابة القياسية:

- النجاح: { ok: true, data }
- الخطأ: { ok: false, error: { code, message } }

المصادقة:

- POST /api/login
  - in: { username, password }
  - out: { ok, data: { user, token } }
- POST /api/logout

التصنيفات/الوحدات/الأصناف:

- GET /api/categories
- POST /api/categories
- PUT /api/categories/{id}
- DELETE /api/categories/{id}
- GET /api/units
- POST /api/units
- GET /api/items?search=باركود|اسم
- POST /api/items
- PUT /api/items/{id}
- DELETE /api/items/{id}

العملاء/الموردون:

- GET /api/customers
- POST /api/customers
- PUT /api/customers/{id}
- GET /api/suppliers
- POST /api/suppliers
- PUT /api/suppliers/{id}

المشتريات:

- GET /api/purchases?from&to
- POST /api/purchases { supplier_id, store_id, items:[{item_id, qty, price, cost}] }
- GET /api/purchases/{id}

المبيعات (POS):

- POST /api/sales { customer_id?, store_id, items:[{item_id, qty, price, discount?}], payments:{cash, card} }
- GET /api/sales?from&to
- GET /api/sales/{id}

المرتجعات:

- POST /api/returns { type:'sale'|'purchase', ref_id, items:[{item_id, qty, price}] }

المخزون:

- POST /api/stock-moves { type:'transfer'|'adjust', item_id, from_store_id?, to_store_id?, qty }
- GET /api/stock/summary?store_id

التقارير:

- GET /api/reports/sales?from&to&store_id?
- GET /api/reports/items-movement?item_id&from&to

الإعدادات:

- GET /api/settings
- PUT /api/settings { key: value }
