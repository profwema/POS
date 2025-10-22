
    <div class="count-cont">	
        <table border="1" class="item-sel-table" id="item-list-container">	
            <thead>            
                <tr class='heading-tr'>
                    <TD  style='width:4%'><?=SERIAL?></TD>
                    <TD  style='width:20%'><?=SEL_NAME?></TD>
                    <TD  style='width:8%'><?=BALANCE?></TD>
                    <TD  style='width:8%'><?=QUANTITY?></TD>
                    <TD  style='width:8%'><?=UNIT_PRICE?></TD>
                    <TD  style='width:8%'><?=DISCOUNT?> %</TD>
                    <TD  style='width:8%'><?=DISCOUNT?></TD>
                    <TD  style='width:8%'><?=TOTAL?></TD>
                    <TD  style='width:8%'><?=VAT?> %</TD>                    
                    <TD  style='width:8%'><?=VAT?></TD>
                    <TD  style='width:8%'><?=TOTAL_PRICE?></TD>
                    <TD  style='width:4%'></TD>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>


<div class="bottom-side">
        <div class="butom">
            <table class="payType">
                <tr>
                    <TH class="tot-td"><?=CUSTOMER_PAID?></TH>
                    <TH class="tot-td"><?=RETURN_AMOUNT?></TH>
                </tr>
                <tr>
                    <td >
                        <input type="text" class="float-val" value="0" id="cust-paid">
                    </td>
                    <td>
                        <input type="text" class="float-val" value="0" id="ret-amt"  disabled="true">
                    </td>
                </tr>
            </table>
            <?php
            if($language == "ar")
                $dir = "direction:rtl;";                                
            ?>
            <table  style="<?=$dir?>" class="payType">                                                
                <tr>
                    <TH><?=CASH?> </th>
                    <TH><?=CARD_PAYMENT?></TH>    
                    <TH><?=SABAKAH?></TH>                     
                    <TH><?=LEFT?></TH>                                                                                                                                        
                </tr>
                <tr>
                    <td >
                        <input type="text" id='cash' class='old' maxlength="100" name='cash' value="0">
                    </td>
                    <td >
                        <input type="text" id='visa' class='old' maxlength="100" name='visa' value="0">
                    </td> 
                    <td >
                        <input type="text" id='mada' class='old' maxlength="100" name='mada' value="0"> 
                    </td> 
                    <td >
                        <input type="text" id='left'  maxlength="100" name='left' readonly> 
                    </td>  
                </tr>
            </table>                            
        </div>
        <div class="butom">
            <table class="direct">
                <tr>
                    <td colspan="2"><button type="submit"><?=SAVE?></button></td>
                </tr>
                <tr>
                    <td colspan="2"><button type="button" onclick="addDeliverInf()"><?=DELIVERY_ORDER?></button> </td>
                </tr>
            </table>
        </div>      
    </div>
