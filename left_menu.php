
<div id="sidebar-left" class="collapse navbar-collapse nav-md span2">                    
<!--    <div class = 'pos'>     
        <a  href="pos.php" title="POS">         
            <img src="img/pos.jpg"> <span> Cashier</span>
        </a>   
    </div>-->
    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
        <div class="menu_section">
            <ul class="nav side-menu">
                <li><a><i class="fa fa-edit"></i> <?=FILES?><span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li><a href="categories.php"> <?=CATEGORIES?></a></li>
                        <li><a href="units.php"> <?=UNITS?></a></li>
                        <li><a href="items.php"> <?=ITEMS?></a></li>
                        <li><a href="customers.php"> <?=CUSTOMERS?></a></li>
                        <li><a href="suppliers.php"> <?=SUPPLIERS?></a></li>
                        <li><a href="employees.php"> <?=EMPLOYEES?></a></li>
                        <li><a href="branches.php"> <?=BRANCHES?></a></li>
                        <li><a href="stores.php"> <?=STORES?></a></li>
                        <li><a href="jobTitle.php"> <?=JOPTITLE?></a></li>
                        <li><a href="delivery_cost.php"> <?=DELIVERY_COST?></a></li>
                        <li><a href="shifts.php"> <?=SHIFTS?></a></li>
                    </ul>
                </li>
                <li><a><i class="fa fa-edit"></i> <?=SALES?><span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li><a href="quotations.php"> <?=QUOTATIONS?></a></li>
                        <li><a href="saleInvoice.php"> <?=SALES?></a></li>
                        <li><a href="salesRet.php"> <?=RETURNED_SALES?></a></li>
                        <li><a href="saleInvoice-report.php"> <?=INVOICE_REPORT?></a></li>
                        <li><a href="saleItem-report.php"> <?=ITEMS_REPORT?></a></li>                       
                    </ul>
                </li>
                <li><a><i class="fa fa-edit"></i> <?=PURCHASES?><span class="fa fa-chevron-down"></span></a>
                  <ul class="nav child_menu">
                        <li><a href="purchase.php"><?=PURCHASES?></a></li>
                        <li><a href="purchaseRet.php"><?=RETURNED_PURCHASES?></a></li>
                        <li><a href="purchInvoice-report.php"><?=INVOICE_REPORT?></a></li>
                        <li><a href="purchItem-erport.php"> <?=ITEMS_REPORT?></a></li>                       
                    </ul>
                </li>    
                <li><a><i class="fa fa-edit"></i> <?=STORED?><span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li><a href="item-movement.php"> <?=ITEM_MOVEMENT?></a></li>
                        <li><a href="store-inventory.php"> <?=STORES_INVENTORY?></a></li>
                        <li><a href="open-palance.php"> <?=OPENING_PALANCE?></a></li>
                        <li><a href="incoming-entry.php"> <?=INCOMING_ENTRY?></a></li>
                        <li><a href="outgoing-entry.php"> <?=OUTGOING_ENTRY?></a></li>
                        <li><a href="stores-transfer.php"> <?=STORE_TRANSFAIR?></a></li>
                    </ul>
                </li>                  
                <li><a><i class="fa fa-edit"></i> <?=F_ACCOUNTS?><span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                         <li><a href="accounts.php"> <?=ACCOUNTS?></a></li>                           
                         <li><a href="budget.php"> <?=PLBS?></a></li>                       
    <!--                        <li><a href="tills.php"> <?=TILL?></a></li>-->
                        <li><a href="journal.php"> <?=JOURNAL?></a></li>
                        <li><a href="account-statement.php"> <?=ACCOUNT_STATEMENT?></a></li>
                    </ul>
                </li>  
                <li><a><i class="fa fa-edit"></i><?=SETTINGS?><span class="fa fa-chevron-down"></span></a>    
                    <ul class="nav child_menu">
                         <li><a href="settings.php"><?=GENERALSET?></a></li>                           
                         <li><a href="permissions.php"> <?=PERMISSIONS?></a></li>                       
                        <li><a href="updates.php"> <?=UPDATES?></a></li>
                    </ul>                      
                </li>
            </ul>
        </div>
    </div>
</div>    