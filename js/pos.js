// WAM Tech POS - Optimized pos.js (High-Speed Scanning & POS Keyboard Shortcuts)

$(document).ready(function() {
    $("#search-items").focus();
});

function addCustomerDialog() {
    $("#customer_inf").show();
}

function addDeliverInf() {    
    $("#deliver").val(typeof delcost !== 'undefined' ? delcost : 0);
    totalAll();
    $("#deliver_inf").show();
}

function closeTables() {
    $(".delivery-table-content").hide();
    if ($("#pos-shortcuts-modal").length) {
        $("#pos-shortcuts-modal").fadeOut(150);
    }
}

function getCustomers() {
    var u = "controller.php?f=getCustomers";
    $.ajax({
        url: u,
        success: function(result) {
            $("#customer").html(result);
            $("#customer").chosen("destroy").chosen();                         
        }		
    });
}

function getThisItem(id, qty) {    
    try {
        var audio = new Audio('sounds/coin.mp3');
        audio.play();
    } catch(e) {}

    var chick = 0;                
    var i = $("#item-list-container tbody tr").length;
    var inputs = $(".item");
    if (inputs.length > 0) {
        for (var j = 0; j < inputs.length; j++) {
            if ($(inputs[j]).val() == id) {
                var num = $(inputs[j]).attr('id').replace('barcode-', '');
                var oldQty = parseFloat($("#item-qty-" + num).val()) || 0;
                var newQty = oldQty + qty;
                $("#item-qty-" + num).val(newQty);
                chick = 1;
                // Immediate synchronous recalculation without arbitrary timeout
                totalParshase(num);    
                getBalance(id, num);
                break;
            }
        }
    }
    if (chick === 0) {
        showThisitem(id, i, qty, function(newIndex) {
            totalParshase(newIndex);
            getBalance(id, newIndex);
        });
    }
}                

function showThisitem(id, i, qty, callback) {
    $.ajax({
        url: "controller.php", 
        type: "POST",
        data: {"f": "getItemdata", "id": id},
        success: function(data) {    
            var json = $.parseJSON(data);  
            var name_en = json[0].name_en || '';
            var name_ar = json[0].name_ar || '';
            var unit_en = json[0].unit_en || '';
            var unit_ar = json[0].unit_ar || '';
            var price = parseFloat(json[0].price) || 0;
            var discount = parseFloat(json[0].discount) || 0;
            var vat = parseFloat(json[0].vat) || 0;
            var taxInPrice = json[0].taxInPrice;
            var type = json[0].item_type;                
            var d = discount / 100;
            var v = vat / 100;
            
            if (taxInPrice == 1) {
                price = (price / (1 + v)).toFixed(2);
            }

            var name = (typeof currentLanguage !== 'undefined' && currentLanguage === "en") ? (name_en + ' ' + unit_en) : (name_ar + ' ' + unit_ar);
            var preTotal = price * qty;
            var disVal = preTotal * d;
            var afterDisTotal = preTotal - disVal;

            var curStore = typeof store !== 'undefined' ? store : '';
            var curNegative = typeof $enableNegative !== 'undefined' ? $enableNegative : 0;

            var temp = '';
            temp += "<tr id='row-v-" + i + "' class='elements-row'>";
            temp += "<td id='s-" + i + "'>" + (1 + i);
            temp += "<input type='hidden' id='store-" + i + "' name='store[]' value='" + curStore + "'>";
            temp += "<input type='hidden' id='barcode-" + i + "' name='barcode[]' value='" + id + "' class='item' ";
            temp += "enableNegative='" + curNegative + "' ";
            temp += "type='" + type + "'> ";
            temp += "</td>";

            temp += "<td>" + name + "</td>";     

            temp += "<td>";
            temp += "<input type='text' id='balance-" + i + "' name='balance[]' readonly>";
            temp += "</td>";

            temp += "<td><span class='number-wrapper'>";
            temp += "<input type='number' class='item-qty' id='item-qty-" + i + "' name='item_qty[]' value='" + qty + "' step='any'>";
            temp += "</span></td>";

            temp += "<td>";
            temp += "<input type='text' class='item-price' id='item-price-" + i + "' name='item_price[]' value='" + price + "'>";
            temp += "</td>";

            temp += "<td>";
            temp += "<input type='text' id='item-discount-" + i + "' name='item_disc[]' value='" + discount + "' class='ratio' readonly>";
            temp += "</td>";
            
            temp += "<td>";
            temp += "<input type='text' id='item-disc-value-" + i + "' readonly>";
            temp += "</td>";                    
            
            temp += "<td>";
            temp += "<input type='text' id='item-total-after-dis-" + i + "' name='item_total_after_dis[]' readonly>";
            temp += "</td>";

            temp += "<td>";
            temp += "<input type='text' id='item-vat-" + i + "' name='item_vat[]' value='" + vat + "' class='ratio' readonly>";                    
            temp += "</td>";
            
            temp += "<td>";
            temp += "<input type='text' id='item-vat-value-" + i + "' readonly>";
            temp += "</td>";                    

            temp += "<td>";
            temp += "<input type='text' id='item-total-" + i + "' name='item-total[]' readonly>";
            temp += "</td>";

            temp += "<td> <div class='del-item' id='" + i + "'>&#10006;</div></td>";
            temp += "</tr>";

            $("#item-list-container tbody").append(temp);

            // Instant calculation trigger inside AJAX callback
            if (typeof callback === 'function') {
                callback(i);
            } else {
                totalParshase(i);
                getBalance(id, i);
            }
        }  
    });
}

function delItem(id) {
    $("#row-v-" + id).remove();
    var k = $("#item-list-container tbody tr").length;
    for (var i = parseInt(id); i <= k; i++) {
       $("#row-v-" + i).attr('id', 'row-v-' + (i - 1));
       $("#s-" + i).html(i);           
       $("#s-" + i).attr('id', 's-' + (i - 1));
       $("#store-" + i).attr('id', 'store-' + (i - 1));
       $("#barcode-" + i).attr('id', 'barcode-' + (i - 1));
       $("#balance-" + i).attr('id', 'balance-' + (i - 1));
       $("#item-qty-" + i).attr('id', 'item-qty-' + (i - 1));
       $("#item-price-" + i).attr('id', 'item-price-' + (i - 1));
       $("#item-discount-" + i).attr('id', 'item-discount-' + (i - 1));
       $("#item-disc-value-" + i).attr('id', 'item-disc-value-' + (i - 1));
       $("#item-total-after-dis-" + i).attr('id', 'item-total-after-dis-' + (i - 1));
       $("#item-vat-" + i).attr('id', 'item-vat-' + (i - 1));
       $("#item-vat-value-" + i).attr('id', 'item-vat-value-' + (i - 1));
       $("#item-total-" + i).attr('id', 'item-total-' + (i - 1));
       $("#" + i).attr('id', (i - 1));
    }
    totalAll();
} 
 
function totalParshase(id) {  
    var qty = parseFloat($("#item-qty-" + id).val()) || 0;
    var price = parseFloat($("#item-price-" + id).val()) || 0;
    var preTotal = qty * price;

    var discRate = parseFloat($("#item-discount-" + id).val()) || 0;
    var discount = (preTotal * discRate) / 100;
    $("#item-disc-value-" + id).val(discount.toFixed(2));   
    
    var disTotal = preTotal - discount;
    $("#item-total-after-dis-" + id).val(disTotal.toFixed(2));

    var vatRate = parseFloat($("#item-vat-" + id).val()) || 0;
    var vaTax = (disTotal * vatRate) / 100;
    $("#item-vat-value-" + id).val(vaTax.toFixed(2));

    var total = disTotal + vaTax;
    $("#item-total-" + id).val(total.toFixed(2));   
    
    totalAll();
}

function totalAll() {
    var total_dis = 0;
    var total_after_dis = 0;
    var total_vat = 0;
    var total_after_vat = 0;
    
    var k = $("#item-list-container tbody tr").length;
    for (var i = 0; i < k; i++) {
        if ($("#item-qty-" + i).val() != '') {
            total_dis += parseFloat($("#item-disc-value-" + i).val()) || 0; 
            total_after_dis += parseFloat($("#item-total-after-dis-" + i).val()) || 0; 
            total_vat += parseFloat($("#item-vat-value-" + i).val()) || 0; 
            total_after_vat += parseFloat($("#item-total-" + i).val()) || 0; 
        }
    }   
    
    $("#d-total").val(total_dis.toFixed(2));
    $("#gr-total").val(total_after_dis.toFixed(2));
    $("#vat-total").val(total_vat.toFixed(2));
    var deliver = parseFloat($("#deliver").val()) || 0;
    var total_all = deliver + total_after_vat; 
    $("#all-total").val(total_all.toFixed(2));    

    var visaVal = parseFloat($("#visa").val()) || 0;
    var madaVal = parseFloat($("#mada").val()) || 0;
    var cashVal = total_all - visaVal - madaVal;
    if (cashVal < 0) cashVal = 0;
    $("#cash").val(cashVal.toFixed(2));
    
    var left = total_all - cashVal - visaVal - madaVal;
    $("#left").val(left.toFixed(2));
    
    var paid = parseFloat($("#cust-paid").val()) || 0;
    var retAmt = paid > 0 ? (paid - total_all) : 0;
    $("#ret-amt").val(retAmt.toFixed(2));
}

var lastElementFocused;
var WAIT_DUR = 1200;
var append = Array("0","1","2","3","4","5","6","7","8","9","0",".");    
var isItNew;
var lastUpdatedTime = $.now();

function pressKey(v) {
    if (lastElementFocused != "") {       
        var prevVal = $(lastElementFocused).val();         

        if (append.indexOf(v) >= 0) {    
            var diff = $.now() - lastUpdatedTime;
            if (diff > WAIT_DUR)
                prevVal = v;
            else
                prevVal = prevVal + "" + v;
            lastUpdatedTime = $.now();
            $(lastElementFocused).val(prevVal);                                			
        } else if (v == "c") {
            var removed = prevVal.substring(0, prevVal.length - 1);
            $(lastElementFocused).val(removed);	                				
        }
        $(lastElementFocused).change();
        $(lastElementFocused).focus();
    } else {
        alert('no_element_focused');	
    }
}                        

// ═══════════════════════════════════════════════════════════════
// Keyboard Shortcuts & Fast POS Helpers
// ═══════════════════════════════════════════════════════════════

function posFastCashSubmit() {
    var rowCount = $("#item-list-container tbody tr").length;
    if (rowCount === 0) {
        alert("يرجى إضافة صنف واحد على الأقل للفاتورة أولاً.");
        $("#search-items").focus();
        return;
    }
    var total = parseFloat($("#all-total").val()) || 0;
    $("#cash").val(total.toFixed(2));
    $("#visa").val("0");
    $("#mada").val("0");
    $("#left").val("0.00");
    $("#sales-form").submit();
}

function posNewSale() {
    var rowCount = $("#item-list-container tbody tr").length;
    if (rowCount === 0 || confirm("هل ترغب في بدء فاتورة جديدة ومسح محتويات الشاشة؟")) {
        location.href = "pos.php";
    }
}

function togglePosShortcutsModal() {
    if ($("#pos-shortcuts-modal").length === 0) {
        var modalHtml = `
        <div id="pos-shortcuts-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:99999; justify-content:center; align-items:center;">
            <div style="background:#fff; border-radius:12px; width:450px; max-width:90%; padding:20px; box-shadow:0 15px 35px rgba(0,0,0,0.3); font-family:inherit; direction:rtl;">
                <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:2px solid #eee; padding-bottom:10px; margin-bottom:15px;">
                    <h3 style="margin:0; color:#1e293b; font-size:17px;"><i class="fa fa-keyboard-o"></i> اختصارات لوحة المفاتيح لشاشة البيع</h3>
                    <button type="button" onclick="$('#pos-shortcuts-modal').fadeOut(150);" style="background:none; border:none; font-size:20px; cursor:pointer; color:#94a3b8;">&times;</button>
                </div>
                <table style="width:100%; font-size:13px; border-collapse:collapse;">
                    <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px;"><kbd style="background:#f1f5f9; padding:3px 8px; border-radius:4px; font-weight:bold; border:1px solid #cbd5e1;">F1</kbd></td><td>عرض دليل الاختصارات</td></tr>
                    <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px;"><kbd style="background:#f1f5f9; padding:3px 8px; border-radius:4px; font-weight:bold; border:1px solid #cbd5e1;">F2</kbd></td><td>التركيز المباشر على مسح الباركود / البحث</td></tr>
                    <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px;"><kbd style="background:#f1f5f9; padding:3px 8px; border-radius:4px; font-weight:bold; border:1px solid #cbd5e1;">F4</kbd></td><td>دفع كاش سريع وحفظ الفاتورة مباشرة</td></tr>
                    <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px;"><kbd style="background:#f1f5f9; padding:3px 8px; border-radius:4px; font-weight:bold; border:1px solid #cbd5e1;">F6</kbd></td><td>التركيز على حقل نسبة الخصم</td></tr>
                    <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px;"><kbd style="background:#f1f5f9; padding:3px 8px; border-radius:4px; font-weight:bold; border:1px solid #cbd5e1;">F7</kbd></td><td>دفع شبكة / مدى</td></tr>
                    <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px;"><kbd style="background:#f1f5f9; padding:3px 8px; border-radius:4px; font-weight:bold; border:1px solid #cbd5e1;">F8</kbd></td><td>فاتورة بيع جديدة وتصفير الشاشة</td></tr>
                    <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px;"><kbd style="background:#f1f5f9; padding:3px 8px; border-radius:4px; font-weight:bold; border:1px solid #cbd5e1;">F9</kbd></td><td>اختيار العميل</td></tr>
                    <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px;"><kbd style="background:#f1f5f9; padding:3px 8px; border-radius:4px; font-weight:bold; border:1px solid #cbd5e1;">F10</kbd></td><td>بيانات طلب التوصيل (دليفري)</td></tr>
                    <tr><td style="padding:8px;"><kbd style="background:#f1f5f9; padding:3px 8px; border-radius:4px; font-weight:bold; border:1px solid #cbd5e1;">Esc</kbd></td><td>إلغاء وإغلاق النوافذ والرجوع لمسح الباركود</td></tr>
                </table>
                <div style="text-align:center; margin-top:15px;">
                    <button type="button" onclick="$('#pos-shortcuts-modal').fadeOut(150);" style="background:#2563eb; color:#fff; border:none; padding:8px 24px; border-radius:6px; cursor:pointer; font-size:13px;">حسناً</button>
                </div>
            </div>
        </div>
        `;
        $("body").append(modalHtml);
    }
    $("#pos-shortcuts-modal").css("display", "flex").hide().fadeIn(150);
}

$(document).keydown(function(e) {
    // ESC: close modals, clear suggestions, and refocus barcode scanner
    if (e.key === "Escape" || e.keyCode === 27) {
        closeTables();
        $("#search-items22").autocomplete("close");
        $("#search-items").focus();
        return;
    }

    // F1: Help modal
    if (e.key === "F1" || e.keyCode === 112) {
        e.preventDefault();
        togglePosShortcutsModal();
        return;
    }

    // F2: Focus barcode
    if (e.key === "F2" || e.keyCode === 113) {
        e.preventDefault();
        $("#search-items").focus().select();
        return;
    }

    // F4: Fast Cash & Submit
    if (e.key === "F4" || e.keyCode === 115) {
        e.preventDefault();
        posFastCashSubmit();
        return;
    }

    // F6: Discount field
    if (e.key === "F6" || e.keyCode === 117) {
        e.preventDefault();
        $("#d-rate").focus().select();
        return;
    }

    // F7: Network / Mada payment
    if (e.key === "F7" || e.keyCode === 118) {
        e.preventDefault();
        var total = parseFloat($("#all-total").val()) || 0;
        $("#mada").val(total.toFixed(2));
        $("#cash").val("0");
        $("#visa").val("0");
        $("#left").val("0.00");
        $("#mada").focus().select();
        return;
    }

    // F8: New Sale / Reset POS
    if (e.key === "F8" || e.keyCode === 119) {
        e.preventDefault();
        posNewSale();
        return;
    }

    // F9: Focus customer chosen
    if (e.key === "F9" || e.keyCode === 120) {
        e.preventDefault();
        $("#customer").trigger('chosen:open');
        return;
    }

    // F10: Delivery Info
    if (e.key === "F10" || e.keyCode === 121) {
        e.preventDefault();
        addDeliverInf();
        return;
    }
});