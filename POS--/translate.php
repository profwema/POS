<?php
define("DEFAULT_LANGUAGE","ar");

if($_SESSION['lang'] == "")
	$_SESSION['lang'] = DEFAULT_LANGUAGE;




if($_SESSION['lang']=="en")
{
    define("DASHBOARD","Home");
    define("WELCOME","Welcome");
    define("FILES","Files");
    define("SMOKING","Tobacco Tax");
    define("PLBS","PLBS Group Names");    
    define("COUNTS","Totals");    
    define("DELIVER","delivery");
    define("CUSTOMER_NAME","Customer");   
    define("SALES_MAN","sales man");       
    define("VAT","Value Added Tax");
    define("VAT_VALUE","Vat Value");   

    define("SELT","Selective Tax");
    define("PRICETAX","Price includes tax");
    define("SITE_TITLE","POS :: Login");
    define("BARCODE","Barcode");
    define("LOGIN_TO_ACCOUNT","Login to account");
    define("EMAIL","Email");
    define("PASSWORD","Password");
    define("REMEMBER_ME","Remember me");
    define("LOGIN","Login");
    define("USERNAME","User Name");
    define("PASSWORD","Password");    
    define("FORGOT_PASSWORD","Forgot Password ?");
    define("CREATE_ACCOUNT","Create Account");
    define("ACCOUNT_STATEMENT","Account Statement");    

    define("REGISTRATION","Registration");
    define("NAME","Shop Name");
    define("LOGO","Logo");
    define("ALREADY_HAVE_ACCOUNT","Already have an account ?");

    define("REGISTER","Register");

    define("ERROR_NAME","Name cannot be blank");
    define("ERROR_EMAIL","Email cannot be blank");
    define("ERROR_EMAIL_INVALID","Email is not valid");
    define("ERROR_PASSWORD","Password cannot be blank");
    define("ERROR_PASSWORD_INVALID","Password must be minimum 6 characters");
    define("ERROR_NAME_INVALID","Name is not valid");
    define("ERROR_EMAIL_ALREADY_TAKEN","Email already taken");

    define("SUCCESS_EMAIL_AVAILABLE","Email available");
    define("ACCOUNT_CREATED_SUCCESSFULLY","Account created successfully");
    define("ACCOUNT_CREATED_WITHOUTIMAGE","Account created but was unable to upload the logo");

    define("IMG_PLACEHOLDER","placeholder.png");

    define("EMAIL_AVAILABLE","Email available");
    define("EMAIL_UNAVAILABLE","Email unavailable");

    define("FORGOT_PASSWORD_TITLE","Forgot password");
    define("PASSWORD_RESET","Reset password");

    define("ERROR_IN_RESETING_PWD","Error in resetting password");

    define("USER_DOES_NOT_EXIST","خطأ فى كلمة السر");

    define("FORGOT","Reset");

    define("TITLE_WELCOME_ADMIN","Welcome :: Admin");
    define("DASHBOARD","Dashboard");
    define("BRANCHES","Branches");
    define("CATEGORIES","Category");
    define("ITEMS","Items");
    define("EMPLOYEES","Employees");
    define("EXPENDITURES","Expenditures");
    define("SALES","Sales");
    define("QUOTATIONS","Quotations"); 
    define("QUOTE_NO","Quote No"); 
    define("RETURNED_SALES","Returned Sales"); 
    define("SALE_INVOICE","Sale Invoice No");    
    define("PURCHASE_INVOICE","Purchase Invoice");      
    define("ADD_RETURNED_SALES","Add Returned Sales");     
    define("ITEMS_REPORT","Items Report");    
    define("INVOICE_REPORT","Invoice Report"); 


    define("INVOICE_NUMBER","Invoice Number");
    define("INVOICE_DATE","Invoice Date");
     define("INVOICE_DETAILS","Invoice Details");   
     define("TOTAL_A_DIS","Total After discount");      

    define("NO_ITEM_ENTERED","No item entered");
    define("SUPPLIER_NAME_COMPULSORY","Supplier name is compulsory");
    define("INVOICE_NUMBER_COMPULSORY","Invoice number is compulsory");
    define("SUCCESS","Sucessfully completed...");

    define("BRANCH_NAME","Branch Name");
    define("STREET","Street");
    define("CITY","City");
    define("COUNTRY","Country");
    define("STATE","State");
    define("PHONE","Phone");
    define("FAX","Fax");
    define("ACTION","Actions");
    define("SELECT_COUNTRY","Select Country");
    define("SELECT_STATE","Select State");
    define("SELECT_CITY","Select City");

    define("BRANCH_ADDED_SUCCESSFULLY","Branch added successfully");

    define("COMMERCIAL_LICENCE_NUM","Cr No.");
    define("COMMERCIAL_LICENCE_APP","Cr issue");
    define("COMMERCIAL_LICENCE_EXP","Cr expiry");
    define("MUNICIPAL_LICENCE_NUM","Municipal");
    define("MUNICIPAL_LICENCE_APP","Municipal issue");
    define("MUNICIPAL_LICENCE_EXP","Municipal expiry");

    define("NAME_REQUIRED","Name cannot be empty");
    define("NAME_INVALID","Name is invalid");
    define("STREET_REQUIRED","Street is required");
    define("COUNTRY_REQUIRED","Country is required");
    define("COUNTRY_IS_INVALID","Invalid country selected");
    define("STREET_INVALID","Street is invalid");
    define("STATE_REQUIRED","State is required");
    define("STATE_IS_INVALID","Invalid state selected");
    define("CITY_REQUIRED","City required");
    define("CITY_IS_INVALID","Invalid city selected");
    define("PHONE_REQUIRED","Phone number is required");
    define("PHONE_IS_INVALID","Phone number is invalid");
    define("FAX_IS_INVALID","Fax is invalid");
    define("COMMERCIAL_REG_IS_INVALID","Commerical registration number is invalid.Must contain only alphanumeric characters");
    define("MUNICIPAL_REG_IS_INVALID","Municipal registration number is invalid.Must contain only alphanumeric characters");
    define("ARABIC_NAME_NOT_IN_ARABIC","Arabic name is not in arabic !!");
    define("ARABIC_STREET_NOT_IN_ARABIC","Arabic street is not in arabic !!");

    define("ERROR_IN_CREATING_NEW_BRANCH","Error creating new branch.Retry now or later");

    define("SAVE","Save");
    define("CANCEL","Cancel");

    define("BRANCH_DELETED_SUCCESSFULLY","Branch deleted successfully");
    define("BRANCH_UPDATED_SUCCESSFULLY","Branch updated successfully");
    define("CATEGORY_DELETED_SUCCESSFULLY","Category deleted successfully");
    define("CATEGORY_UPDATED_SUCCESSFULLY","Category updated successfully");

    define("LEAVE_BLANK_FOR_ALL","Leave it blank for all branches");

    define("ERROR_IN_CREATING_NEW_CATEGORY","Error creating new category.Retry again or later");

    define("ARE_YOU_SURE","Are you sure?");

    define("NEW_BRANCH","New Branch");

    define("ADD_NEW","Add");
    define("DELETE","Delete");
    define("EDIT","Edit");

    define("YES","Yes");
    define("NO","No");
    define("CATEGORY","Category");


    define("IN_BRANCH","الفرع");
    define("CAT_NAME","Cat Name");
    define("IMAGE","Image");
    define("TOTAL_ITEMS","Total Items");
    define("SHOW_TO_CASHIER","تفعيل");
    define("ENABLED","Enabled");
    define("EDITING","Editing");
    define("NEW_CAT","New Category");
    define("BRANCH","Branch");
    define("PRESENT_IN_ALL","Present in All");
    define("CATEGORY_ADDED_SUCCESSFULLY","Category added successfully");
    define("ERROR_ADDING_CATEGORY","Error adding the category");
    define("ERROR_IN_UPDATING_BRANCH","Error in updating the branch");
    define("ERROR_IN_UPDATING_CATEGORY","Error in updating the category");

    define("INTIMATE_STOCK","Stock threshold");
    define("IS_SERVICE","Service");
    define("ITEM_NAME","Item Name");
    define("PRICE","Price");
    define("PRICE_WITH_DISCOUNT","Price after discount");	
    define("RACK_LOCATION","Rack Location");
    define("COMMISSION","Commission");
    define("UNIT_PRESENT","Unit Present");
    define("EXPIRY_DATE","Expiry Date");
    define("EXPIRY_NOTIFICATION","Does it expires ?");

    define("DATA_DELETED_SUCCESSFULLY","Data deleted successfully");
    define("CATEGORY_NAME_CANNOT_BE_BLANK","Category name cannot be blank");
    define("PRICE_CANNOT_BE_BLANK","Please enter the price");
    define("ARABIC_LOCATION_NOT_IN_ARABIC","Arabic location name not in arabic");
    define("PROVIDED_THRESHOLD_BUT_UNIT_EMPTY","Provided threshold but unit is empty!!");
    define("UNIT_LESS_THAN_THRES","Unit less than intimation value");
    define("PRICE_LESS_THAN_COMM","Price and commission seems incorrect");
    define("PRICE_LESS_THAN_DISCOUNT","Price and discount seems incorrect");

    define("NEW_ITEM","New Item");
    define("ITEM_ADDED_SUCCESSFULLY","Item added successfully");
    define("ITEM_UPDATED_SUCCESSFULLY","Item updated successfully");

    define("ERROR_IN_ADDING_ITEM","Error in adding item");
    define("ERROR_IN_UPDATING_ITEM","Error in updating item");

    define("AVAILABLE_QTY","Available Qty");
    define("UPDATE_ITEM","Update Item");

    define("ADD_EMPLOYEE","Add Employee");
    define("EMPLOYEE_NAME","Employee Name");
    define("WILL_BE_USED_AS_LOGIN","Will be used as login id");
    define("MOBILE","Mobile");
    define("DESIGNATION","Job Title");
    define("PASSPORT_NUMBER","Passport Number");
    define("PASSPORT_EXPIRY","Passport Expiry");
    define("IQAMA_NUMBER","ID Number");
    define("IQAMA_EXPIRY","ID Expiry");
    define("INSURANCE_NUMBER","Insurance Number");
    define("INSURANCE_EXPIRY","Insurance Expiry");
    define("MEDICAL_NUMBER","Medical Number");
    define("MEDICAL_EXPIRY","Medical Expiry");
    define("LICENCE_NUMBER","License Number");
    define("LICENCE_EXPIRY","License Expiry");

    define("EMPLOYEE_NAME_BLANK","Employee name cannot be blank");
    define("EMAIL_IS_INVALID","Email is invalid");

    define("EMPLOYEE_ADDED_SUCCESSFULLY","Employee added successfully");
    define("EMPLOYEE_UPDATED_SUCCESSFULLY","Employee updated successfully");

    define("ERROR_IN_ADDING_EMPLOYEE","Error in adding Employee");
    define("ERROR_IN_UPDATING_EMPLOYEE","Error in updating Employee");
    define("EMAIL_UNAVAILABLE","Email is unavailable");

    define("UPDATE_EMPLOYEE","Update Employee");
    define("ADD_EXPENDITURE","Add Expenditure");

    define("COST","Cost");
    define("DESCRIPTION","Description");
    define("GENERAL","General");
    define("DESCRIPTION_CANNOT_BE_BLANK","Description cannot be blank");
    define("COST_CANNOT_BE_BLANK","Cost cannot be blank");

    define("ARABIC_DESC_NOT_IN_ARABIC","Arabic description not in arabic");
    define("COST_ADDED_SUCCESSFULLY","Cost added successfully");
    define("COST_UPDATED_SUCCESSFULLY","Cost updated successfully");
    define("EXPENDITURE_DATE","Expenditure Date");
    define("DATE_CANNOT_BE_BLANK","Date cannot be blank");
    define("ERROR_ADDING_COST","Error in adding cost");
    define("ERROR_UPDATING_COST","Error in updating cost");
    define("UPDATE_EXPENDITURE","Update Expenditure");
    define("EARNING","Earning");

    define("TABLES","Tables");
    define("TOTAL_TABLE_COUNT","Total table count");
    define("TABLE_COUNT_CANNOT_BE_BLANK","Table count cannot be blank");
    define("TABLE_UPDATED_SUCCESSFULLY","Table updated successfully");
    define("ERROR_UPDATING_TABLE","Error updating table");

    define("WELCOME_USER","Welcome :: ".ucwords(strtolower($_SESSION['name_en'])));

    define("TABLE","Table");
    define("TABLE_AVAILABLE","Available");
    define("ME","Me");
    define("ALL_TABLES","All Tables");
    define("MY_TABLES","My Tables");
    define("THIS_TABLE_IS_PROCESSED_BY_OTHER","This table is processed by another Person.");

    define("CLEAR_TABLE","Clear Table");
    define("PLACE_ORDER","Order");
    define("ORDER_PLACED_SUCCESSFULLY","Order placed successfully");
    define("ORDER_CANNOT_BE_PLACED"," Item(s) order cannot be placed");
    define("CURRENT_ORDER","Current Order");
    define("ITEM_REMOVED_FROM_ORDER","Item removed from order successfully");
    define("ERROR_IN_REMOVING_ORDERED_ITEM","Error in removing ordered item");
    define("ITEM_ALREADY_PROCESSED","Item already processed cannot remove :(");
    define("THIS_TABLE_WILL_BECOME_BUSY","If you click then the [TABLE_NUMBER] will be marked busy.\\n\\nAre you sure?");
    define("TABLE_CUSTOMER_LEFT","Customer on this table left ?");
    define("TABLE_FREED","The table is available now");
    define("ERROR_IN_FREEING_TABLE","Error in making the table available");
    define("UNAUTHORIZED","Unauthorized Access");
    define("ITEMS_OF"," Items of ");
    define("ADDED_ITEMS","Added Items");
    define("SERIAL","Sl."); // show in tables left column counter heading
    define("SEL_NAME","Name");
    define("QUANTITY","Quantity");
    define("UNIT_PRICE","Price");
    define("TOTAL_PRICE","Total");
    define("TOTAlS","Totals");  

    define("NO_ITEM_SELECTED","No Item is selected");
    define("DISCOUNT","Discount");
    define("DISCOUNT_VALUE","Discount Value");
    define("DISCOUNT_RASHIO","Discount Rashio");	
    define("DISCOUNT_ADDED","Discount Added");	    
    
    define("GROSS_TOTAL","Gross Total");
    define("GRAND_TOTAL","Grand Total");
    define("DISCOUNT_GR8R_THAN_TOTAL","Discount greater than total !!!");
    define("MIN_DISCOUNT_IS_ZERO","Discount could be minimum 0");

    define("MAHALI_ORDERS","Mahli Orders");
    define("MAHALI_ORDER","Mahli Order");
    define("SAFRI_ORDERS","Takeaway Orders");
    define("SAFRI_ORDER","Takeaway Order");
    define("TAKE_AWAY_NOW_CASH","Takeaway Cash");
    define("TAKE_AWAY_ENTER_PAYMENT","Takeaway Payment");
    define("DELIVERY_ORDERS","Delivery Orders");
    define("DELIVERY_ORDER","Delivery Order");
    define("OFF","Off");
    define("SELECT_INPUT_BOX","Please select a textbox");
    define("PAID_CUSTOMER","Paid Customer");
    define("CUSTOMER_PAID","Paid");
    define("RETURN_AMOUNT","Return");

    define("ORDER_ID","Order ID");
    define("TABLE_IS_BUSY","Table is occupied");

    define("UNAME","Name");
    define("PHONE","Phone");
    define("ADDRESS","Address");
    define("ONLY_NUMBERS","Enter only numbers in phone number");
    define("ADDRESS_CANNOT_BE_BLANK","Address cannot be blank");
    define("SELECT_A_DRIVER","Select a driver");
    define("TABLE_NUMBER","Table No.");
    define("TOTAL","Total");
    define("GRAND_TOTAL_TABLE","Grand<br>Total");
    define("DRIVER_NOT_YET_ALLOCATED","Pending");

    define("DRIVER","Driver");

    define("TRANSACTION_CLOSED","Transaction closed successfully");
    define("ERROR_IN_CLOSING_TRANSACTION","Error in closing the transaction");
    define("CLOSE_THIS_TRANSACTION","Close this transaction?");

    define("TRANSACTION_DELETED","Transaction deleted successfully");
    define("ERROR_IN_DELETING_TRANSACTION","Error in deleting the transaction");
    define("DELETE_THIS_TRANSACTION","Delete this transaction?");
    define("CONSUMER_SCREEN_IP","Consumer Screen IP");
    define("PRINTER_IP","Printer IP");
    define("SETTINGS","Settings");
    define("GENERALSET","General Settings");
    define("PERMISSIONS","User Permissions");
    define("UPDATES","Version Update");    

    define("TABLE_COUNT_CANNOT_BE_BLANK","Table count cannot be blank");

    define("PLEASE_FILL_SETTINGS","Please fill the values in <a href='settings.php' style='color:#0a0'> Settings </a>");

    define("SUMMARY","Summary");
    define("KITCHEN_PRINTER_IP","Kitchen Printer IP");
    define("BILL_PRINTER_NAME","Bill Printing Name");
    define("SAVED","Saved successfully");
    define("FAIL_INTIMATE_KITCHEN","Failed to inform the kitchen.Please kindly inform them.Seems like the App is not running in the kitchen.");
    define("FAIL_TO_PRINT","Failed to Print");
    define("PAYMENT","Payment");
    define("PAYMENT_TYPE","Payment Type");

    define("CASH","Cash");
    define("CARD_PAYMENT","Visa");
    define("SABAKAH","mada");
    define("PAID","Paid");    
    define("LEFT","آجل");

    define("ORDER_TYPE","Order Type");
    define("CARD_NUMBER","Card Number");
    define("CARD_TYPE","Card Type");
    define("EDIT_ORDER","Edit Order");
    define("PAY","Payment Received");
    define("ERROR_IN_TRANSACTION","Error in Transaction");
    define("ALL_USER_FIELDS_ARE_COMPULSORY","All user fields are compulsory");
    define("INVALID_CARD_NO","Invalid Card number");
    define("AMOUNT_CANNOT_BE_BLANK","Amount cannot be blank");
    define("DISCOUNT_UPDATED_SUCCESSFULLY","Discount updated successfully");
    define("ERROR_IN_UPDATING_DISCOUNT","Error in updating discount");
    define("MIN_AMOUNT_CANNOT_BE_BLANK","Minimum amount cannot be blank");
    define("DISCOUNT_GR8R_THN_MIN","Discount must be less than Minimum Amount");
    define("MIN_AMOUNT","Minimum amount for discount eligibility");
    define("QTY_VAL","Fills");//(cartoon,single etc..)
    define("NAME_AND_PRICE_WILL_BE_ENTERED","Quantity with name and price  NOT blank and 0 will be added");
    define("ITEM_PROP","Item Propereties");   
    define("TAX","Tax");   
    define("ITEM_TYPE","Item Nature");
    define("COMMODITY","Commodity");
    define("SERVICE","service");
    define("QTY_TYPE","Quantity Measure");
    define("COUNT","Count");
    define("WEIGHT","Weight");   
    define("ITEMS_ADDED"," Items added");
    define("ITEMS_UPDATED"," Items updated");


    //////////////////////////////////////////////////////////////////////////////////

    define("TOTAL_SALES","Total Sales");
    define("FROM_DATE","From Date");
    define("TO_DATE","To Date");
    define("CASHIER","Cashier");
    define("DATA_ENTRY","Data Entry");    
    define("TIME_RANGE","Time Range");
    define("TO_DATE_LESS_THAN_FROM","To date cannot be less than for date");
    define("FROM_DATE_GREATER_THAN_TO","From date cannot be more than to date");

    define("KITCHEN_PRINTER_NAME","Kitchen Printer name");
    define("RESET_INVOICE_NUMER","Reset invoice number");
    define("TEST_PRINTERS","Test Printer");
    define("RESET_INVOICE_COUNTER","Reset Invoice No.");
    define("RESET","Reset");
    define("SECTIONS","Sections");
    define("SAVE_SECTIONS","Save Sections");
    define("MAHALI_ORDER_PRE_PAY","Mahali Cash");
    define("MAHALI_ORDER_POST_PAY","Mahali TABLES");
    define("DELIVERY_ORDER_PRE_PAY","Delivery");
    define("DOOR_NUMBER","Door No.");



    define("DELIVERY_COST","Delivery Cost");

    define("PROCEED","Save");
    define("MAHLI_SECTIONS","Mahaly Section");

    define("ADD_ITEM_PRINTER","Select Items");
    define("PRINTER_NAME","Main Printer");

    define("CLEAR_POSITIONS","Clear Positions");

    define("LANGUAGE_1","Main Language");
    define("LANGUAGE_2","Other Language");
    define("ITEM_OPTIONS","Item Options");

    define("CUSTOMER_SCREEN_IP","Customer Screen IP");

    define("LOAD_FORM","Load");

    define("DELETE","حذف");
    define("ADMIN_PASSWORD","Password");
    define("PASSWORD_CANNOT_B_BLANK","Error In Passowrd");
    define("CANNOT_CONNECT_TO_SERVER","Error In Passowrd");
    define("KITCHEN_SCREEN_IP","Kitchen IP");

    define("PAYMENT_RECEIVED","Save &amp; Print");
    define("WORK_IN_PROGRESSS","Work in Progress");
    define("SAVE_DETAILS","Customer Details");


    define("WHO","Who");


    define("SERVICES","Services");
    define("ADD_SERVICE","Add Service");
    define("SERVICE_EN","Service");
    define("SERVICE_AR","Service (lang2)");
    define("SERVICE_NAME_EN","Service");
    define("SERVICE_NAME_AR","Service");



    define("EDIT_SERVICE","Edit Service");
    define("SERVICE_BLANK","Service name cannot be blank");
    define("SERVICE_UPDATED_SUCCESSFULLY","Service updated successfully");
    define("SERVICE_ADDED_SUCCESSFULLY","Service added successfully");
    define("ERROR_ADDING_SERVICES","Error adding service");
    define("ERROR_IN_UPDATING_SERVICE","Error updating service");
    define("ALL_SERVICES","All Services");
    define("SERVICE_IS","Is Service");

    define("IT","It.");
    define("QTY","Qty");
    define("PR","Price<br>after<br>discount");
    define("DIS","Dis");
    define("TOT","Tot");
    define("GET_DEVICES","Get Devices");
    define("PLEASE_PROVIDE_ID","Please provide device id");

    define("LOGIN_ID","Login Id");

    define("SHIFTS","Shifts");
    define("SHIFT","Shift");
    define("PURCHASES","Purchases");
    

    
    define("RETURNED_PURCHASES","Returned Purchases"); 
    define("SUPPLIER","Supplier");
    define("SUPPLIERS","Suppliers");
    define("SUPPLIER_INVOICE","Supplier Invoice");
    define("ADD_EDIT_SUPPLIER","Add - Edit Supplier");
    define("SUPPLIER_NAME","Supplier Name");
    define("ADD_SUPPLIER","Add Supplier");
    define("SUPPLIER_ALREADY_EXISTS","Supplier already exists");
    define("SHOW_REPORT","Show Report");
    define("TILL_CONTROL","Till Control");
    define("ACCOUNTS","Acounts");
    
    define("F_ACCOUNTS","Financial Accounting");    
    define("JOURNAL","Journal Voucher");        
    define("TILL","Till");
    define("ALLOW_PRICE_CHANGE","Allow Price Change");
    define("COST_PRICE","Cost Price");
    define("ELIGIBLE_FOR_GIFT","Gift Eligibilty");
    define("GIFT_QTY","Gift Qty");
    define("CREATED_ON","Entry date");
    define("PURCHASED_ITEMS","Purchased Items");
    define("DEVICE_ID","Device ID");
    define("ADD_INVOICE","New");
    define("BACK","Back");

    define("HOLD","Holding");	
    define("SITTING","Sitting");	
    define("REPORT","Report");	
    define("LANGUAGE","عربي");	
    define("LOGOUT","Logout");     
    define("SHIFT_UPDATED_SUCCESSFULLY","Shifts updated successfully");
    define("UNIT_UPDATED_SUCCESSFULLY","Units updated successfully");
    define("STORE_UPDATED_SUCCESSFULLY","Stores updated successfully");
    define("JOBTITLE_UPDATED_SUCCESSFULLY","Job Titles updated successfully");
   
    define("UNITS","Units");
    define("JOPTITLE","Jop Title");    
    define("STORES","Stores");
    define("STORE","Store");
    define("STORED","Stored");
define("INCOMING_ENTRY","Incoming Entry");
define("OUTGOING_ENTRY","Outgoing Entry");

    define("STORES_INVENTORY","Stores inventory");
    define("ITEM_MOVEMENT","Item Movement");

    define("STORE_TRANSFAIR","Store Transfair");
    define("STORE_CANNOT_BE_BLANK","Store cannot be blank");
    define("TOTAL_COST","Total Cost");
    define("TOTAL_TAX","Total Tax");
    define("TOTAL_COM","Total Com");      

    define("ENTRY_NUMBER","Entry Number");
    define("CREDIT","Credit");
    define("DEBIT","Debit");
    define("NET_BALANCE","Net balance");
    define("BALANCE","Balance");    
    define("DATE","Date");
    define("DOCUMENT","Document");       
    define("DISCRIPTION","Discription");
    define("BALANCE_BEFORE","Balance before transaction");
    define("BALANCE_AFTER","Balance after transaction");
    define("CREDIT_SIDE","Credit side");
    define("DEBIT_SIDE","Debit side");
    define("TRANSACTION_AMOUNT","Transaction amount");
    define("BALANCE_STATE","*if the balance by minus it is debit ");
    define("ENTRY_ADDED","Entry Added Successfully");
    define("ENTRY_UPDATED","Entry Updated Successfully");

    define("COM_REG","Commercial Registeration");
    define("TAX_NO","Tax Number");
define("OPENING_PALANCE","Opening balance");
define("CUSTOMERS","Customers");
define("CUSTOMER_NAME","Customer name");
define("CUSTOMER_PHONE","Customer Phone");

define("FILES","Files");    
define("COST_CENTER","Cost Center");
define("REFERENE","Reference");
define("ACCOUNT_NAME","Account Name");
define("JOURNAL_NUMBER","Juornal No");
define("COPIES_NO","Number of Copies");

}
else
{
        define("DASHBOARD","الرئيسية");
    define("WELCOME","مرحبا");
        
    define("FILES","ملفات");
        define("SMOKING","ضريبة التبغ");

    define("PLBS","مجموعة الميزانية العمومية");    
    define("COUNTS","إجماليات");
    define("DELIVER","التوصيل");
    define("CUSTOMER_NAME","العميل");   
    define("SALES_MAN","رجل المبيعات");       
    define("VAT","قيمة مضافة");
    define("VAT_VALUE","قيمة الضريبة");   
    
    define("SELT","انتقائية");
    define("PRICETAX","السعر شامل الضريبة");
    define("SITE_TITLE","POS :: Login");        
    define("BARCODE","باركود");


    define("LOGIN_ID","الاسم");
    define("SERVICES","خدمات");
    define("KITCHEN_SCREEN_IP","عنوان المطبخ");
    define("ITEM_OPTIONS","نوع العنصر");
    define("LANGUAGE_1","اللغة الانجليزية");
    define("LANGUAGE_2","اللغة العربية");

    define("MAHLI_SECTIONS","أقسام محلي");
    define("PROCEED","حفظ");

    define('SITE_TITLE','POS :: تسجيل الدخول');
    define('LOGIN_TO_ACCOUNT',' تسجيل الدخول للحساب');
    define('EMAIL','البريد الالكتروني');
    define('PASSWORD','كلمة المرور');
    define('REMEMBER_ME','تذكرنى');
    define('LOGIN','تسجيل الدخول');
    define("USERNAME","اسم الدخول");
    define("PASSWORD","كلمة السر");    
    define('FORGOT_PASSWORD','هل نسيت كلمة المرور ؟');
    
    define('CREATE_ACCOUNT','إنشاء حساب');
    define("ACCOUNT_STATEMENT","كشف حساب");    

    define('REGISTRATION','التسجيل');
    define('NAME','اسم المنشأة');
    define('LOGO','الشعار');
    define('ALREADY_HAVE_ACCOUNT','يوجد لديك حساب');
    define('REGISTER','التسجيل');
    define('ERROR_NAME','يجب كتابة الاسم');
    define('ERROR_EMAIL','يجب كتابة البريد الالكتروني');
    define('ERROR_EMAIL_INVALID','البريد الالكتروني غير صحيح');
    define('ERROR_PASSWORD','من فضلك اكتب كلمة المرور');
    define('ERROR_PASSWORD_INVALID',' يجب أن تكون كلمة المرور 6 حروف على الأقل');
    define('ERROR_NAME_INVALID','الاسم غير صحيح');
    define('ERROR_EMAIL_ALREADY_TAKEN','البريد الالكتروني مستخدم ');
    define('SUCCESS_EMAIL_AVAILABLE','البريد الإلكتروني متاح');
    define('ACCOUNT_CREATED_SUCCESSFULLY','تم إنشاء الحساب بنجاح');
    define('ACCOUNT_CREATED_WITHOUTIMAGE','تم إنشاء الحساب ولم نتمكن من تحميل الشعار');
    define('IMG_PLACEHOLDER','placeholder.png');
    define('EMAIL_AVAILABLE','البريد الإلكتروني متاح');
    define('EMAIL_UNAVAILABLE','البريد الإلكتروني غير متوفر');
    define('FORGOT_PASSWORD_TITLE','نسيت كلمة المرور');
    define('PASSWORD_RESET','إعادة تعيين كلمة المرور');
    define('ERROR_IN_RESETING_PWD','خطأ في إعادة تعيين كلمة المرور');
    define('USER_DOES_NOT_EXIST','اسم المستخدم غير موجود');
    define('FORGOT','إعادة تعيين');
    define('TITLE_WELCOME_ADMIN','نرحب :: المشرف');
    define('DASHBOARD','لوحة التحكم');
    define('BRANCHES','الفروع');
    define('CATEGORIES','الفئات');
    define('ITEMS','الاصناف');
    define('EMPLOYEES','الموظفين');
    define('EXPENDITURES','المصروفات');
    define('SALES','المبيعات');
    define("QUOTATIONS","عروض أسعار"); 
    define("ADD_QUOTATION","إضافة عرض سعر"); 
    define("EDIT_QUOTATION","تعديل عرض السعر"); 
    define("QUOTE_NO","عرض سعر رقم"); 
    define("RETURNED_SALES","مرتجع مبيعات"); 
     define("ADD_RETURNED_SALES","إضافة مرتجع مبيعات"); 
   
    define("SALE_INVOICE"," فاتورة المبيعات");    
    define("PURCHASE_INVOICE"," فاتورة المشتريات"); 

    define("ITEMS_REPORT","تقرير الأصناف");    
    define("INVOICE_REPORT","تقرير الفواتير"); 
    define('BRANCH_NAME','اسم الفرع');
    define('STREET','اسم الشارع');
    define('CITY','المدينة');
    define('COUNTRY','الدولة');
    define('STATE','المنطقة');
    define('PHONE','هاتف');
    define('FAX','فاكس');
    define('ACTION','تطبيقات');
    define('SELECT_COUNTRY','اختر الدولة');
    define('SELECT_STATE','اختر المنطقة');
    define('SELECT_CITY','احتر المدينة');
    define('BRANCH_ADDED_SUCCESSFULLY','تم إضافة الفرع بنجاح');
    define('COMMERCIAL_LICENCE_NUM','رقم السجل التجاري');
    define('COMMERCIAL_LICENCE_APP','تاريخ السجل التجاري');
    define('COMMERCIAL_LICENCE_EXP','تاريخ انتهاء السجل التجاري');
    define('MUNICIPAL_LICENCE_NUM','رقم رخصة البلدية');
    define('MUNICIPAL_LICENCE_APP','تاريخ رخصة البلدية');
    define('MUNICIPAL_LICENCE_EXP','تاريخ انتهاء رخصة البلدية');

    define("NAME_REQUIRED","يجب كتابة الاسم");
    define("NAME_INVALID","الاسم غير صحيح");
    define("STREET_REQUIRED","مطلوب كتابة اسم الشارع");
    define("COUNTRY_REQUIRED","مطلوب كتابة اسم الدولة");
    define("COUNTRY_IS_INVALID","اسم الدولة غير صحيح");
    define("STREET_INVALID","اسم الشارع غير صحيح");
    define("STATE_REQUIRED","مطلوب كنابة اسم المنطقة");
    define("STATE_IS_INVALID","اسم المنطقة غير صحيح");
    define("CITY_REQUIRED","مطلوب كتابة اسم المدينة");
    define("CITY_IS_INVALID","اسم المدينة غير صحيح");
    define("PHONE_REQUIRED","مطلوب كتابة رقم الهاتف");
    define("PHONE_IS_INVALID","رقم الهاتف غير صحيح");
    define("FAX_IS_INVALID","Fax is invalid");
    define("COMMERCIAL_REG_IS_INVALID"," رقم السجل التجاري غير صحيح يجب أن يحتوي فقط على ارقام");
    define("MUNICIPAL_REG_IS_INVALID","رقم رخصة البلدية غير صحيح يجب ان يحتوي فقط على ارقام");
    define("ARABIC_NAME_NOT_IN_ARABIC"," يجب كتابة الاسم باللغة العربية");
    define("ARABIC_STREET_NOT_IN_ARABIC","يجب كتابة اسم الشارع باللغة العربية");

    define("ERROR_IN_CREATING_NEW_BRANCH","حدث خطأ في إنشاء فرع جديد حاول مرة اخرى");

    define("SAVE","حفظ");
    define("CANCEL","الغاء");

    define("BRANCH_DELETED_SUCCESSFULLY","تم مسح الفرع بنجاح");
    define("BRANCH_UPDATED_SUCCESSFULLY","تم تحديث الفرع بنجاح");
    define("CATEGORY_DELETED_SUCCESSFULLY","تم مسح الفئات بنجاح");
    define("CATEGORY_UPDATED_SUCCESSFULLY","تم تحديث الفئات بنجاح");

    define("LEAVE_BLANK_FOR_ALL","اتركها خالية لجميع الفروع");


    define("ARE_YOU_SURE","هل انت متأكد?");

    define("NEW_BRANCH","فرع جديد");

    define("ADD_NEW","إضافة");
    define("DELETE","حذف");
    define("EDIT","تعديل");

    define("YES","نعم");
    define("NO","لا");
    define("CATEGORY","فئة");



    define("IN_BRANCH","في الفرع");
    define("CAT_NAME","Cat Name");
    define("IMAGE","صورة");
    define("TOTAL_ITEMS","مجموع الاصناف");
    define("SHOW_TO_CASHIER","إظهار للكاشير");
    define("ENABLED","تفعيل");
    define("EDITING","تعديل");
    define("NEW_CAT","فئة جديدة");
    define("BRANCH","فرع");
    define("PRESENT_IN_ALL","موجود في الكل");
    define("CATEGORY_ADDED_SUCCESSFULLY","تم إضافة الفئة بنجاح");
    define("ERROR_ADDING_CATEGORY","خطأ في إضافة الفئة");
    define("ERROR_IN_UPDATING_BRANCH","حطأ في تحديث الفرع");
    define("ERROR_IN_UPDATING_CATEGORY","خطأ في تحديث الفئة");

    define("INTIMATE_STOCK","المخزون");
    define("IS_SERVICE","الخدمة");
    define("ITEM_NAME","اسم الصنف");
    define("PRICE","السعر");
    define("PRICE_WITH_DISCOUNT","السعر بعد الخصم");		
    define("RACK_LOCATION","موقع الرف");
    define("COMMISSION","العمولة");
    define("UNIT_PRESENT","سعر الوحدة");
    define("EXPIRY_DATE","تاريخ الانتهاء");
    define("EXPIRY_NOTIFICATION","هل تنتهي؟");

    define("DATA_DELETED_SUCCESSFULLY","تم مسح البيانات بنجاح");
    define("CATEGORY_NAME_CANNOT_BE_BLANK","يجب ادخال اسم الفئة");
    define("PRICE_CANNOT_BE_BLANK","يجب ادخال السعر");
    define("ARABIC_LOCATION_NOT_IN_ARABIC","يجب كتابة الاسم باللغة العربية");
    define("PROVIDED_THRESHOLD_BUT_UNIT_EMPTY","Provided threshold but unit is empty!!");
    define("UNIT_LESS_THAN_THRES","Unit less than intimation value");
    define("PRICE_LESS_THAN_COMM","Price and commission seems incorrect");
    define("PRICE_LESS_THAN_DISCOUNT","السعر والحسم غير صحيح");

    define("NEW_ITEM","صنف جديد");
    define("ITEM_ADDED_SUCCESSFULLY","تم إضافة الصنف بنجاح");
    define("ITEM_UPDATED_SUCCESSFULLY","تم تحديث الصنف بنجاح");

    define("ERROR_IN_ADDING_ITEM","خطأ في إضافة الصنف");
    define("ERROR_IN_UPDATING_ITEM","خطأ في تحديث الصنف");

    define("AVAILABLE_QTY","الكمية المتوفرة");
    define("UPDATE_ITEM","تحديث الصنف");

    define("ADD_EMPLOYEE","إضافة موظف");
    define("EMPLOYEE_NAME","اسم الموظف");
    define("WILL_BE_USED_AS_LOGIN","سيتم استخدامه للدخول للحساب");
    define("MOBILE","رقم الجوال");
    define("DESIGNATION","المسمى الوظيفى");
    define("PASSPORT_NUMBER","رقم الجواز");
    define("PASSPORT_EXPIRY","تاريح انتهاء الجواز");
    define("IQAMA_NUMBER","رقم الهوية");
    define("IQAMA_EXPIRY","تاريخ انتهاء الهوية");
    define("INSURANCE_NUMBER"," رقم التأمين");
    define("INSURANCE_EXPIRY","تاريخ انتهاء التأمين");
    define("MEDICAL_NUMBER","رقم الملف الطبي");
    define("MEDICAL_EXPIRY","تاريخ انتهاء الملف الطبي");
    define("LICENCE_NUMBER","رقم رخصة القيادة");
    define("LICENCE_EXPIRY","تاريخ انتهاء رخصة القيادة");

    define("EMPLOYEE_NAME_BLANK","يجب ادخال اسم الموظف");
    define("EMAIL_IS_INVALID","البريد الالكتروني غير صحيح");

    define("EMPLOYEE_ADDED_SUCCESSFULLY","تم إضافة الموظف بنجاح");
    define("EMPLOYEE_UPDATED_SUCCESSFULLY","تم تحديث بيانات الموظف بنجاح");

    define("ERROR_IN_ADDING_EMPLOYEE","خطأ في إضافة موظف");
    define("ERROR_IN_UPDATING_EMPLOYEE","خطأ في تحديث بيانات موظف");
    define("EMAIL_UNAVAILABLE","البريد الالكتروني غير متوفر");

    define("UPDATE_EMPLOYEE","تحديث موظف");
    define("ADD_EXPENDITURE","إضافة مصروفات");

    define("COST","التكلفة");
    define("DESCRIPTION","الوصف");
    define("GENERAL","عام");
    define("DESCRIPTION_CANNOT_BE_BLANK","يجب ادخال الوصف");
    define("COST_CANNOT_BE_BLANK","يجب ادخال التكلفة");

    define("ARABIC_DESC_NOT_IN_ARABIC","يجب ادخال الوصف باللغة العربية");
    define("COST_ADDED_SUCCESSFULLY","تم اضافة التكلفة بنجاح");
    define("COST_UPDATED_SUCCESSFULLY","تم تحديث التكلفة بنجاح");
    define("EXPENDITURE_DATE","تاريخ المصروفات");
    define("DATE_CANNOT_BE_BLANK","يجب ادخال التاريخ");
    define("ERROR_ADDING_COST","خطأ في إضافة التكلفة");
    define("ERROR_UPDATING_COST","خطأ في تحديث التكلفة");
    define("UPDATE_EXPENDITURE","تحديث المصروفات");
    define("EARNING","الربح");

    define("TABLES","الطاولات");
    define("TOTAL_TABLE_COUNT","اجمالي عدد الطاولات");
    define("TABLE_COUNT_CANNOT_BE_BLANK","يجب ادخال العدد الاجمالي للطاولات");
    define("TABLE_UPDATED_SUCCESSFULLY","تم تحديث الطاولات بنجاح");
    define("ERROR_UPDATING_TABLE","خطأ في تحديث الطاولات");

    define("WELCOME_USER","Welcome :: ".ucwords(strtolower($_SESSION['name_en'])));

    define("TABLE","طاوله");
    define("TABLE_AVAILABLE","متاح");
    define("ME","أنا");
    define("ALL_TABLES","جميع الطاولات");
    define("MY_TABLES","طاولاتي");
    define("THIS_TABLE_IS_PROCESSED_BY_OTHER","هذه الطاولة تمت خدمتها عن طريق شخص آخر");

    define("CLEAR_TABLE","تنظيف الطاولة");
    define("PLACE_ORDER","الطلب");
    define("ORDER_PLACED_SUCCESSFULLY","تم إضافة الطلب بنجاح");
    define("ORDER_CANNOT_BE_PLACED"," لايمكن إضافة الصنف للطلب");
    define("CURRENT_ORDER","الطلب الحالي");
    define("LOAD_FORM","عرض");
    define("ITEM_REMOVED_FROM_ORDER","تم إزالة الصنف من الطلب بنجاح");
    define("ERROR_IN_REMOVING_ORDERED_ITEM","خطأ في إزالة الصنف من الطلب");
    define("ITEM_ALREADY_PROCESSED","تم عمل الطلب لا يمكن ازالة صنف");
    define("THIS_TABLE_WILL_BECOME_BUSY"," ستظهر مشغولة هل أنت متأكد؟ [TABLE_NUMBER] إذا قمت بالضغط فإن ");
    define("TABLE_CUSTOMER_LEFT","الزبون غادر هذه الطاولة؟");
    define("TABLE_FREED","الطاولة متوفرة الآن");
    define("ERROR_IN_FREEING_TABLE","خطأ في تسجيل الطاولة متوفرة");
    define("UNAUTHORIZED","غير مصرح له");
    define("ITEMS_OF"," اصناف من ");
    define("ADDED_ITEMS","الاصناف المضافة");
    define("SERIAL","م"); // show in tables left column counter heading
    define("SEL_NAME","الاسم");
    define("QUANTITY","الكميه");
    define("UNIT_PRICE","السعر");
    define("TOTAL_PRICE","الاجمالى");
    define("TOTAlS","اجماليات");  

    define("NO_ITEM_SELECTED","لم يتم اختيار صنف");
    define("DISCOUNT","الخصم");
    define("DISCOUNT_VALUE","مقدار الخصم");
    define("DISCOUNT_RASHIO","نسبة الخصم");	
    define("DISCOUNT_ADDED","خصم إضافي");	    
    define("GROSS_TOTAL","الاجمالى");
    define("GRAND_TOTAL","الصافي");
    define("DISCOUNT_GR8R_THAN_TOTAL","التخفيض أكثر من المجموع");
    define("MIN_DISCOUNT_IS_ZERO","أقل نسبة تخفيض هي صفر");

    define("MAHALI_ORDERS","محلى");
    define("MAHALI_ORDER","محلى");
    define("SAFRI_ORDERS","سفرى");
    define("SAFRI_ORDER","سفرى");
    define("TAKE_AWAY_NOW_CASH","تيك اواى");
    define("TAKE_AWAY_ENTER_PAYMENT","دفع طلب السفري");
    define("OFF","إيقاف");
    define("SELECT_INPUT_BOX","اختر مربع النص من فضلك");
    define("PAID_CUSTOMER","دفع نقدى");
    define("CUSTOMER_PAID","المدفوع");
    define("RETURN_AMOUNT","الباقي");

    define("ORDER_ID","رقم الطلب");
    define("TABLE_IS_BUSY","الطاولة مشغولة");

    define("UNAME","الاسم");
    define("PHONE","الهاتف");
    define("ADDRESS","العنوان");
    define("ONLY_NUMBERS","يجب ادخال ارقام فقط");
    define("ADDRESS_CANNOT_BE_BLANK","يجب ادخال العنوان");
    define("SELECT_A_DRIVER","اختيار السائق");
    define("TABLE_NUMBER","رقم الطاولة");
    define("TOTAL","إجمالي");
    define("GRAND_TOTAL_TABLE","إجمالي الطاولات");
    define("DRIVER_NOT_YET_ALLOCATED","معلق");

    define("DRIVER","السائق");

    define("TRANSACTION_CLOSED","تم الانتهاء من اقفال العملية");
    define("ERROR_IN_CLOSING_TRANSACTION","خطأ في اقفال العملية");
    define("CLOSE_THIS_TRANSACTION","أقفل هذه العملية؟");

    define("TRANSACTION_DELETED","تم إزالة العملية بنجاح");
    define("ERROR_IN_DELETING_TRANSACTION","خطأ في إزالة العملية");
    define("DELETE_THIS_TRANSACTION","إزالة هذه العملية؟");
    define("CONSUMER_SCREEN_IP","شاشة العميل");
    define("PRINTER_IP","Printer IP");
    define("SETTINGS","اعدادات");
    define("GENERALSET","اعدادات عامة");
    define("PERMISSIONS","صلاحيات المستخدم");
    define("UPDATES","تحديث الإصدار");    

    define("TABLE_COUNT_CANNOT_BE_BLANK","يجب ادخال رقم الطاولة");

    define("PLEASE_FILL_SETTINGS","من فضلك أدخل البيانات  <a href='settings.php' style='color:#0a0'> Settings </a>");

    define("SUMMARY","ملخص");
    define("KITCHEN_PRINTER_IP","رقم معرف طابعة المطبخ");
    define("BILL_PRINTER_NAME","اسم طابعة الفواتير");
    define("SAVED","تم الحقظ بنجاح");
    define("FAIL_INTIMATE_KITCHEN","فشل في إبلاغ المطبخ من فضلك قم بإبلاغ المطبخ من الممكن ان التطبيق لايعمل بالمطبخ");
    define("FAIL_TO_PRINT","فشل في الطباعة");
    define("PAYMENT","الدفع");
    define("PAYMENT_TYPE","نوع عملية الدفع");



    define("CASH","كاش");
    define("CARD_PAYMENT","فيزا");
    define("Master Card","ماستر كارد");
    define("SABAKAH","مدى");
    define("PAID","مدفوع");    
    define("LEFT","آجل");

    define("ORDER_TYPE","Order Type");
    define("CARD_NUMBER","رقم الفيزا");
    define("CARD_TYPE","نوع البطاقة");
    define("EDIT_ORDER","تحرير الطلب");
    define("PAY","تم الدفع");
    define("ERROR_IN_TRANSACTION","خطأ في عملية الدفع");
    define("ALL_USER_FIELDS_ARE_COMPULSORY","جميع حقول المستخدم الزامية");
    define("INVALID_CARD_NO","رقم البطاقة غير صحيح");
    define("AMOUNT_CANNOT_BE_BLANK","يجب كتابة المبلغ");
    define("DISCOUNT_UPDATED_SUCCESSFULLY","تم تحديث التخفيض بنجاح");
    define("ERROR_IN_UPDATING_DISCOUNT","خطأ في تحديث التخفيض");
    define("MIN_AMOUNT_CANNOT_BE_BLANK","يجب ادخال اقل قيمة");
    define("DISCOUNT_GR8R_THN_MIN","التخفيض يجب أن يكون أقل من أقل قيمة");
    define("MIN_AMOUNT","اقل قيمة يسمح التخفيض فيها");
    define("QTY_VAL","التعبئة");//(cartoon,single etc..)
    define("NAME_AND_PRICE_WILL_BE_ENTERED","يجب ادخال الكمية والاسم والسعر  ");
    define("ITEM_PROP","خصاىص الصنف");   
    define("TAX","الضرائب");   
    define("ITEM_TYPE","طبيعة الصنف");
    define("COMMODITY","سلعي");
    define("SERVICE","خدمي");
    define("QTY_TYPE","قياس الكمية");
    define("COUNT","عدد");
    define("WEIGHT","وزن");    
    define("ITEMS_ADDED"," تم الاضافة");
    define("ITEMS_UPDATED"," تم التعديل");


    define('SAVE','حفظ');
    define('CANCEL','إلغاء');
    define('NEW_BRANCH','فرع جديد');
    define('ADD_NEW','+ إضافة جديد');
    define('DELETE','حذف');
    define('EDIT','تحرير');

    define("WELCOME_USER","مرحبا :: ".$_SESSION['name_ar']);




    ////////////////////
    define("TOTAL_SALES","الاجمالى");
    define("FROM_DATE","من تاريخ");
    define("TO_DATE","الى تاريخ");
    define("CASHIER","كاشير");
    define("DATA_ENTRY","مدخل البيانات");    
    define("TIME_RANGE","Time Range");
    define("TO_DATE_LESS_THAN_FROM","To date cannot be less than for date");
    define("FROM_DATE_GREATER_THAN_TO","From date cannot be more than to date");

    define("KITCHEN_PRINTER_NAME","النسخه الثانية");
    define("RESET_INVOICE_NUMER","رقم الفاتورة");
    define("TEST_PRINTERS","اختبار الطابعة");
    define("RESET_INVOICE_COUNTER","رقم الفاتورة.");
    define("RESET","الباقى");
    define("SECTIONS","قسم");

    define("DELIVERY_ORDERS","توصيل");
    define("DELIVERY_ORDER","توصيل");
    define("KITCHEN_PRINTER_NAME","النسخه الثانية");
    define("RESET_INVOICE_NUMER","تصفير رقم الفاتورة");
    define("TEST_PRINTERS","اختبار الطابعة");
    define("RESET_INVOICE_COUNTER","Reset Invoice No.");
    define("RESET","استعادة");
    define("SECTIONS","قسم");
    define("SAVE_SECTIONS","حفظ القسم");
    define("MAHALI_ORDER_PRE_PAY","محلى كاش");
    define("MAHALI_ORDER_POST_PAY","محلى طاولات");
    define("DELIVERY_ORDER_PRE_PAY","توصيل");
    define("DOOR_NUMBER","رقم المبنى");

    define("DELIVERY_COST","خدمة توصيل");

    define("PROCEED","طباعة");
    define("MAHLI_SECTIONS","محلى");

    define("ADD_ITEM_PRINTER","تحديد أصناف");
    define("PRINTER_NAME","اسم الطابعة");

    define("CLEAR_POSITIONS","الافتراضى");

    define("LANGUAGE_1","اللغةالاولى");
    define("LANGUAGE_2","اللغةالاختيارية");
    define("ITEM_OPTIONS","اختيار");

    define("CUSTOMER_SCREEN_IP","شاشة العميل");

    define("SHIFTS","أوقات الدوام");
    define("PURCHASES","المشتريات");
    define("RETURNED_PURCHASES","مرتجع مشتريات"); 
    define("PURCHASES_REPORT","تقارير المشتريات"); 

    
    define("INVOICE_NUMBER","رقم الفاتورة");
        define("INVOICE_DATE","تاريخ الفاتورة");
     define("INVOICE_DETAILS","تفاصيل الفاتورة");   
     define("TOTAL_A_DIS","الإجمالى بعد الخصم");      
     
        
    define("SUPPLIER","المورد");
    define("SUPPLIERS","الموردون");        
    define("SUPPLIER_INVOICE","رقم فاتورة المورد");
    define("ADD_EDIT_SUPPLIER","اضافة وتعديل المورد");
    define("SUPPLIER_NAME","اسم المورد");
    define("ADD_SUPPLIER","اضافة مورد");
    define("SUPPLIER_ALREADY_EXISTS","تم اضافة المورد");

    define("SHOW_REPORT","الاطلاع على التقرير");
    define("TILL_CONTROL","التحكم فى الصندوق");
        define("F_ACCOUNTS","المحاسبه الماليه ");
    define("JOURNAL","قيود اليومية");               
    define("TILL","الصندوق");
    define("ALLOW_PRICE_CHANGE","السماح بتعديل السعر");
    define("COST_PRICE","سعر التكلفة");
    define("ADD_INVOICE","جديد");
    define("BACK","العودة");	
    define("HOLD","فواتير معلقة");	
    define("SITTING","إعدادات");	
    define("REPORT","طباعة");	
    define("LANGUAGE","Language");	
    define("LOGOUT","خروج");
    define("SHIFT_UPDATED_SUCCESSFULLY","تم تعديل أوقات الدوام بنجاح");
    define("UNIT_UPDATED_SUCCESSFULLY","تم تعديل الوحدات  بنجاح");
    define("STORE_UPDATED_SUCCESSFULLY","تم تعديل المخازن  بنجاح");
    define("JOBTITLE_UPDATED_SUCCESSFULLY","تم تعديل المسميات الوظيفية  بنجاح");
    
    define("UNITS","الوحدات");
    define("STORES","المخازن");
    define("JOPTITLE","المسمى الوظيفي");    
    
    define("STORE","المخزن");
    define("STORED","المخزون");
    define("STORES_INVENTORY","جرد المخازن");
    define("INCOMING_ENTRY","إذن إضافة");
define("OUTGOING_ENTRY","إذن صرف");
    define("ITEM_MOVEMENT","كارت الصنف");
    define("STORE_TRANSFAIR","تحويل مخزن");
    
    define("STORE_CANNOT_BE_BLANK","يجب اختيار المخزن");
    define("TOTAL_COST","اجمالي التكلفة");
    define("TOTAL_TAX","اجمالى الضريبة");   
    define("TOTAL_COM","اجمالى المكسب");      
    define("ENTRY_NUMBER","رقم القيد");
    define("CREDIT","دائن");
    define("DEBIT","مدين");
    define("NET_BALANCE","صافي الرصيد");
    define("BALANCE","الرصيد");    
    define("DATE","تاريخ");
    define("DOCUMENT","مستند");       
    define("DISCRIPTION","بيان");       
    define("BALANCE_BEFORE","الرصيد قبل التحويل");
    define("BALANCE_AFTER","الرصيد بعد التحويل");
    define("CREDIT_SIDE","الجانب الدائن");
    define("DEBIT_SIDE","الجانب المدين");
    define("TRANSACTION_AMOUNT","مبلغ التحويل");
    define("BALANCE_STATE","*اذا كان الرصيد بالسالب فالرصيد مدين");
    define("ENTRY_ADDED","تم اضافة القيد بنجاح");
    define("ENTRY_UPDATED","تم تعديل القيد بنجاح");
    define("COM_REG","السجل التجارى");
    define("TAX_NO","الرقم الضريبي");
    define("OPENING_PALANCE","رصيد افتتاحي");
define("CUSTOMERS","العملاء");
define("CUSTOMER_NAME","اسم العميل");
define("CUSTOMER_PHONE","تليفون العميل");

define("FILES","ملفات");
define("COST_CENTER","مركز التكلفة");
define("REFERENE","مرجع");
define("ACCOUNT_NAME","اسم الحساب");
define("JOURNAL_NUMBER","رقم القيد");
define("COPIES_NO","عدد النسخ");
}


define("HOSTED_URL","http://smartfgpos.com/");
define("SERVER_NAME","www.smartfgpos.com");
define("SERIAL_AR","saf");
define("SEL_NAME_AR","ismo");
define("QUANTITY_AR","kum");
define("UNIT_PRICE_AR","kum");
define("OFF_AR","disc");
define("TOTAL_PRICE_AR","kul");

define("SERIAL_EN","Sl.");
define("SEL_NAME_EN","Name");
define("QUANTITY_EN","Qty");
define("UNIT_PRICE_EN","Price");
define("OFF_EN","Off");
define("TOTAL_PRICE_EN","Total");
////////////////////////////////////////////////////////////////////////////////////////


define("IMAGE_DIRECTORY",$_SERVER['CONTEXT_DOCUMENT_ROOT']."/images/");
define("CONSTANT_BRANCH_ADDED","1");
define("CONSTANT_BRANCH_UPDATED","2");
define("ENC_KEY","tU2s!f6w0~!@$%@@$$@!~@@!");
define("ENC_BS",256);
define("DEFAULT_CURRENCY","SAR");

define("DIR_CAT",1);
define("DIR_CAT_NAME","categories");
define("DIR_ITEM",2);
define("DIR_ITEM_NAME","items");
define("DIR_EMP",3);
define("DIR_EMP_NAME","employees");

define("ALL_BRANCHES",-2);
define("NO_BRANCHES",-1);

define("NO_IMAGE","../images/ic_noavatar.png");
define("TABLE_IMAGE","../images/table.jpg");
define("IMG_DIR","img/");

define("GENERAL_ORDER","cook");

$arrayColor[0]	= array("purple","green","red","greenDark");
$arrayColor[1]	= array("green","red","greenDark","purple");
?>