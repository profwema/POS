<div class="input-content">
    <table class="d-rate">            
        <tr >
            <td class="span6 tot-td">تحديد نسبة خصم</td>
            <td class="span6">
                <input type="text" value="" id="d-rate"> 
                <a href="javascript:void(0)"  id="dChange">
                    <img src="img/dis.png" width="20px">
                </a>
            </td>
        </tr>
    </table>
    <table class="totals"> 
        <tr>
            <td class="span6 tot-td"> <?=DISCOUNT?></td>
            <td class="span6">
                <input type="text" class="" value="0" id="d-total"readonly>
            </td>
        </tr>            
        <tr >
            <td class="span6 tot-td"><?=GROSS_TOTAL?></td>
            <td class="span6">
                <input type="text" name="gross_total" class="" value="0" id="gr-total" readonly>
            </td>
        </tr>                
        <tr >
            <td class="span6 tot-td"><?=VAT?></td>
            <td class="span6">
                <input type="text" name="vat_all" class="" value="0" id="vat-total"  readonly>
            </td>
        </tr>
        <tr >
            <td class="span6 tot-td"><?=DELIVERY_COST?></td>
            <td class="span6">
               <input type="text" name='deliver' class="" value="0" id="deliver"  readonly>
               <input type="hidden" name='final_discount' value='0'>

            </td>
        </tr>
        <tr >
            <td class="span6 tot-td"style="width:40%"><?=GRAND_TOTAL?></td>
            <td class="span6"style="width:60%">
                <input type="text" name="totalAll" class="" value="0" id="all-total"  readonly>
            </td>
         </tr>
    </table>
</div>

<table class="keyboard" id="keyboard">

	<TR>
		<TD class="bg-color-1" id="button-1" onclick="pressKey('1')">1</TD>
		<TD class="bg-color-2" id="button-2" onclick="pressKey('2')">2</TD>
		<TD class="bg-color-3" id="button-3" onclick="pressKey('3')">3</TD>
	</TR>
	<TR>
		<TD class="bg-color-3" id="button-4"  onclick="pressKey('4')">4</TD>
		<TD class="bg-color-4" id="button-5"  onclick="pressKey('5')">5</TD>
		<TD class="bg-color-2" id="button-6" onclick="pressKey('6')">6</TD>
	</TR>
	<TR>
		<TD class="bg-color-4"  id="button-7" onclick="pressKey('7')">7</TD>
		<TD class="bg-color-1"  id="button-8" onclick="pressKey('8')">8</TD>
		<TD class="bg-color-3"  id="button-9" onclick="pressKey('9')">9</TD>
	</TR>
	<TR>
		<TD class="bg-color-1"  id="button-10" onclick="pressKey('c')">C</TD>
		<TD class="bg-color-2"  id="button-11" onclick="pressKey('0')">0</TD>
		<TD class="bg-color-4"  id="button-12" onclick="pressKey('.')">.</TD>
	</TR>
</table>
