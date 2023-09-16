




INSERT INTO `accounts` (`id`, `code`, `name_en`, `name_ar`, `type`, `budget`, `parent`, `top`, `hasChild`, `refId`, `def`) VALUES
(1, 'acc001', 'Customers', 'العملاء', 1, 3, 0, 0, '1', NULL, '1'),
(5, 'acc003', 'suppliers', 'الموردون', 3, 1, 0, 0, '1', NULL, '1'),
(28, 'acc004', 'Employees', 'الموظفين ', 3, 1, 0, 0, '1', NULL, '1'),
(32, 'acc006', 'Cash', 'الصندوق', 1, 3, 0, 0, '1', NULL, '1'),
(33, 'acc007', 'Main Cash', 'الصندوق العام', 1, 3, 32, 0, '0', NULL, '1'),
(34, 'acc008', 'Discount allowed', 'خصم مسموح به', 4, 9, 0, 0, '1', NULL, '1'),
(35, 'acc009', 'Stock adjustment ', 'تسويات المخزون', 4, 9, 0, 0, '1', NULL, '1'),
(37, 'acc011', 'Cost', 'تكلفة البضاعة المباعة', 4, 7, 0, 0, '1', NULL, '1'),
(40, 'acc013', 'inventory', 'المخزون', 6, 19, 0, 0, '1', NULL, '1'),
(42, 'acc015', 'banks', 'البنوك', 1, 3, 0, 0, '1', NULL, '1'),
(44, 'acc017', 'Fixed Assets', 'الأصول الثابتة', 1, 4, 0, 0, '1', NULL, '1'),
(47, 'acc018', 'Revenue from sales', 'إيرادات المبيعات', 2, 13, 0, 0, '1', NULL, '1'),
(49, 'acc020', 'Damaged goods', 'مخزون تالف', 4, 9, 0, 0, '1', NULL, '1'),
(51, 'acc022', 'Salary paid account', 'الرواتب والأجور الدمفوعة', 4, 9, 0, 0, '1', NULL, '1'),
(52, 'acc023', 'Depreciation account', 'مصروف الاهلاك', 4, 9, 0, 0, '1', NULL, '1'),
(53, 'acc024', 'Capital account', 'رأس المال', 5, 17, 0, 0, '1', NULL, '1'),
(54, 'acc025', 'Miscellaneous expenses', 'المصروفات النثرية', 4, 9, 0, 0, '1', NULL, '1'),
(55, 'acc026', 'Miscellaneous incomes', 'إيرادات أخرى', 2, 12, 0, 0, '1', NULL, '1'),
(56, 'acc027', 'Allowances paid account', 'البدلات والحوافز', 4, 9, 0, 0, '1', NULL, '1'),
(57, 'acc028', 'Tax', 'الضريبة ', 3, 1, 0, 0, '1', NULL, '1'),
(58, 'acc029', 'vat', 'ضريبة القيمة المضافة', 3, 1, 57, 0, '0', NULL, '1'),
(59, 'acc030', 'Outstanding revenues', 'إيرادات معلقة', 4, 9, 0, 0, '1', NULL, '1'),
(60, 'acc031', 'Provision against salary', 'مخصصات الرواتب', 3, 1, 0, 0, '1', NULL, '1'),
(61, 'acc032', 'Accumulated Profit & Loss', 'الأرباح والخسائر المبقاة', 5, 17, 0, 0, '1', NULL, '1'),
(64, 'acc034', 'Acquired discount', 'خصم مكتسب', 2, 12, 0, 0, '1', NULL, '1'),
(116, 'acc073', 'delivery', 'التوصيل', 2, 12, 55, 0, '0', 0, '0'),
(117, 'acc074', 'Viisa', 'فيزا', 1, 3, 42, 0, '0', 0, '0'),
(118, 'acc075', 'Mada', 'مدى', 1, 3, 42, 0, '0', 0, '0');


INSERT INTO `account_type` (`id`, `name_en`, `name_ar`) VALUES
(1, 'Assets', 'الأصول'),
(2, 'Income', 'الدخل'),
(3, 'Obligations', 'الإلتزامات'),
(4, 'Expenses', 'المصروفات'),
(5, 'Capital', 'راس المال'),
(6, 'Inventory', 'المخزون');


INSERT INTO `budget_set` (`id`, `code`, `name_en`, `name_ar`) VALUES
(1, 'bud001', 'CURRENT LIABILITIES	', 'الخصوم المتداولة	'),
(2, 'bud002', 'LONG TERM LIABILITIES	', 'الخصوم غير المتداولة'),
(3, 'bud003', 'CURRENT ASSETS	', 'الأصول المتداولة'),
(4, 'bud004', 'FIXED ASSETS	', 'الأصول الثابتة'),
(5, 'bud005', 'INVESTMENTS', 'الإستثمارات'),
(6, 'bud006', 'INTANGIBLE ASSETS	', 'الأصول غير الملموسة'),
(7, 'bud007', 'COST OF GOODS SOLD', 'تكلفة البضاعة المباعة'),
(8, 'bud008', 'COST OF GOODS PURCHASED', 'تكلفة البضاعة المشتراة'),
(9, 'bud009', 'OPERATING EXPENSES', 'المصروفات التشغيلية'),
(10, 'bud010', 'OTHER EXPENSES', 'المصروفات الأخرى'),
(11, 'bud011', 'EXTRAORDINARY ITEM', 'فئات اخرى'),
(12, 'bud012', 'OTHER REVENUES', 'الإيرادات الأخرى'),
(13, 'bud013', 'REVENUE FROM SALES', 'إيرادات المبيعات'),
(14, 'bud014', 'COST OF MERCHANDISE SOLD', 'تكلفة البضاعة الجاهزة للبيع'),
(15, 'bud015', 'TAX ON REVENUES', 'الضريبة على الإيرادات'),
(16, 'bud016', 'TAX ON PURCHASES', 'الضريبة على المشتريات'),
(17, 'bud017', 'OWNER''S EQUITY', 'حقوق المالك\n'),
(18, 'bud018', 'STOCKHOLDER''S EQUITY', 'حقوق المساهمين\n'),
(19, 'bud019', 'INVENTORY GOODS', 'مخزون البضاعة');
