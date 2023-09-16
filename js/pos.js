     
setTimeout(function(){$("#search-items").focus();},1400);    
function addCustomerDialog()
{
    $("#customer_inf").show();
}

function addDeliverInf()
{    
    $("#deliver").val(delcost);
    totalAll();
    $("#deliver_inf").show();
}

function closeTables()
{
     $(".delivery-table-content").hide();
}

function getCustomers()
{
    var u="controller.php?f=getCustomers";
    $.ajax({
        url:u,
        success:function(result)
        {
                $("#customer").html(result);
                $("#customer").chosen("destroy").chosen();                         
        }		
    });
}


function getThisItem(id,qty)
{    
        
var audio = new Audio('sounds/coin.mp3');
audio.play()
                    

    var chick = 0 ;                
    var i =  $("#item-list-container tbody tr").length;
    if (i != 0)
    {
        var inputs = $(".item");
        for(var j = 0; j < inputs.length; j++)
        {
            if($(inputs[j]).val() == id)
            {
                var num = $(inputs[j]).attr('id').replace('barcode-','');
                var oldQty = parseFloat( $("#item-qty-"+num).val());
                $("#item-qty-"+num).val(oldQty + qty);
                chick = 1;
                i = j;
                break;
            }
        }
    }
    if(chick == 0)
    {
        showThisitem(id,i,qty)
    }

      
    setTimeout(function(){
             totalParshase(i);    
     getBalance(id,i);       
 
    }, 1000);

}                

function showThisitem(id,i,qty)
{
            $.ajax({
            url: "controller.php", 
            type : "POST",
            data : {"f":"getItemdata","id":id},
            success: function(data)
            {    
                var json = $.parseJSON(data);  
                var name_en 	= json[0].name_en;
                var name_ar 	= json[0].name_ar;
                var unit_en 	= json[0].unit_en;
                var unit_ar 	= json[0].unit_ar;
                var price       = json[0].price;
                var discount 	= json[0].discount;
                var vat         = json[0].vat;
                var taxInPrice  = json[0].taxInPrice;
                var type        = json[0].item_type;                
                var d = discount / 100;
                var v = vat / 100;
                
                if (taxInPrice==1)
                    price = (price/(1+v)).toFixed(2);

                var name = (currentLanguage == "en")? name_en +' '+ unit_en : name_ar +' '+ unit_ar ;
                var preTotal = price * qty;
                var disVal = preTotal*d;
                var afterDisTotal = preTotal - disVal;


                var temp ='';
                    temp += "<tr id='row-v-"+i+"' class='elements-row'>";

                    temp += "<td id = 's-"+i+"'>"+ (1+i);
                    temp += "<input type='hidden' id='store-"+i+"'        name='store[]'      value='"+store+"'>";
                    temp += "<input type='hidden' id='barcode-"+i+"'      name='barcode[]'    value='"+id+"' class='item' ";
                    temp += "enableNegative = '"+$enableNegative+"' ";
                    temp +=  "type  = '"+type+"'> ";
                    temp += "</td>";

                    temp += "<td>"+name+"</td>";     

                    temp += "<td>";
                    temp += "<input type='text'   id='balance-"+i+"'       name='balance[]' readonly>";
                    temp += "</td>";

                    temp += "<td><span class='number-wrapper'>";
                    temp += "<input type='number' class='item-qty' id='item-qty-"+i+"'      name='item_qty[]'   value='"+qty+"'  step='any' >";
                    temp += "</span></td>";

                    temp += "<td>";
                    temp += "<input type='text'  class='item-price'  id='item-price-"+i+"'    name='item_price[]' value='"+price+"'>";
                    temp += "</td>";

                    temp += "<td>";
                    temp += "<input type='text' id='item-discount-"+i+"' name='item_disc[]' value='"+discount+"' class='ratio' readonly> ";
                    temp += "</td>";
                    
                    temp += "<td>";
                    temp += "<input type='text'  id='item-disc-value-"+i+"' readonly>";
                    temp += "</td>";                    
                    
                    temp += "<td>";
                    temp += "<input type='text'  id='item-total-after-dis-"+i+"'  name='item_total_after_dis[]' readonly>";
                    temp += "</td>";

                    temp += "<td>";
                    temp += "<input type='text'  id='item-vat-"+i+"'      name='item_vat[]' value='"+vat+"' class='ratio' readonly> ";                    
                    temp += "</td>";
                    
                    temp += "<td>";
                    temp += "<input type='text'   id='item-vat-value-"+i+"'  readonly>";
                    temp += "</td>";                    

                    temp += "<td>";
                    temp +="<input type='text'    id='item-total-"+i+"'    name='item-total[]' readonly>";
                    temp += "</td>";

                    temp += "<td> <div class='del-item' id='"+i+"'>&#10006;</div>";
                        
                    temp += "</tr>";
                    $("#item-list-container tbody").append(temp);    
                     // $('#' + rowId).remove();  
            }  
        });
}
function delItem(id)
{
    $("#row-v-"+id).remove();
    k =  $("#item-list-container tbody tr").length;
    for ( var i = id; i <= k; i++ )
    {
       $("#row-v-"+i).attr('id', 'row-v-'+(i-1));
       $("#s-"+i).html(i)           
       $("#s-"+i).attr('id', 's-'+(i-1));
       $("#store-"+i).attr('id', 'store-'+(i-1));
       $("#barcode-"+i).attr('id', 'barcode-'+(i-1));
       $("#balance-"+i).attr('id', 'balance-'+(i-1));
       $("#item-qty-"+i).attr('id', 'item-qty-'+(i-1));
       $("#item-price-"+i).attr('id', 'item-price-'+(i-1));
       $("#item-discount-"+i).attr('id', 'item-discount-'+(i-1));
       $("#item-disc-value-"+i).attr('id', 'item-disc-value-'+(i-1));
       $("#item-total-after-dis-"+i).attr('id', 'item-total-after-dis-'+(i-1));
       $("#item-vat-"+i).attr('id', 'item-vat-'+(i-1));
       $("#item-vat-value-"+i).attr('id', 'item-vat-value-'+(i-1));
       $("#item-total-"+i).attr('id', 'item-total-'+(i-1));
       $("#"+i).attr('id', (i-1));
    }
    totalAll();
} 
 
 
function totalParshase(id)
{  
   
    // بيجسب اجمالى الكمية فى السعر
   var preTotal =  $("#item-qty-"+id).val() * $("#item-price-"+id).val();

   // بيحسب مقدار الخصم
   var discount = preTotal * $("#item-discount-"+id).val() / 100;
   $("#item-disc-value-"+id).val( discount.toFixed(2) );   
   // بيحسب الاجمالى بعد الخصم ويعرضه
   var disTotal = preTotal - discount;
   $("#item-total-after-dis-"+id).val( disTotal.toFixed(2) );
   //-----------------------
   // بيحسب قيمة الضريبة ويعرضها 
   var vaTax    = disTotal * $("#item-vat-"+id).val() / 100;
   $("#item-vat-value-"+id).val( vaTax.toFixed(2) );
   //-----------------------
   // بيحصب الاجمالى الاخير للصنف 
   var total    = disTotal + vaTax ;
   $("#item-total-"+id).val( total.toFixed(2) );   
   // يحسب الاجمالى فى الاسفل
    totalAll();
}


    
function totalAll()
{
    var total_gross = 0;
    var total_dis = 0;
    var total_after_dis = 0;
    var total_vat = 0;
    var total_after_vat = 0;
    var delver = 0;
    var total_all = 0;
    
    var k =  $("#item-list-container tbody tr").length;
    for ( var i = 0 ; i<k; i++)
    {
        if ( $("#item-qty-"+i).val() != '')
        {
            total_dis += parseFloat( $("#item-disc-value-"+i).val()); 
            total_after_dis += parseFloat( $("#item-total-after-dis-"+i).val()); 
            total_vat += parseFloat( $("#item-vat-value-"+i).val()); 
            total_after_vat += parseFloat( $("#item-total-"+i).val()); 
        }
    }   
    
     $("#d-total").val(total_dis.toFixed(2) ) ;
     $("#gr-total").val(total_after_dis.toFixed(2) ) ;
     $("#vat-total").val(total_vat.toFixed(2) ) ;
     var deliver = parseFloat($("#deliver").val());
     total_all = deliver + total_after_vat ; 
     $("#all-total").val(total_all.toFixed(2) ) ;    
//    $("#total-before-vat").val( total_before_vat.toFixed(2) ) ;
//    $("#total-vat-value").val( total_vat_value.toFixed(2) ) ;
//    $("#total-after-vat").val( total_after_vat.toFixed(2) ) ;
//    // بيحسب الاجمالى النهائى للكل فى الاسفل باضافه الاجمالى شامل الضريبه على الخصم الاضافي
//    var totalAll = total_after_vat + parseFloat($("#deliver").val()) - parseFloat($("#final_discount").val());
//    $("#totalAll").val ( totalAll.toFixed(2) ) ;
    var cash = total_all.toFixed(2) - $("#visa").val () - $("#mada").val ();
    $("#cash").val ( cash.toFixed(2) ) ;
    var left = total_all - $("#cash").val () - $("#visa").val () - $("#mada").val ();
    $("#left").val ( left.toFixed(2) ) ;
    
    $("#ret-amt").val ( total_all.toFixed(2)- $("#cust-paid").val()  )   ;
    
}
    var lastElementFocused;
    var WAIT_DUR = 1200;
    var append = Array("0","1","2","3","4","5","6","7","8","9","0",".");    
    var isItNew ;
    var lastUpdatedTime = $.now();

function pressKey(v)
    {
        if(lastElementFocused != "")
        {       

         
            var prevVal = $(lastElementFocused).val();         

            if(append.indexOf(v) >= 0)
            {    
                var diff        = $.now() - lastUpdatedTime;
                if( diff > WAIT_DUR)
                    prevVal	    = v;
                else
                    prevVal	    = prevVal +""+v;
                lastUpdatedTime	    = $.now();
                $(lastElementFocused).val(prevVal);                                			
            }
            else if(v == "c")
            {
                var removed = prevVal.substring(0,prevVal.length-1);
                $(lastElementFocused).val(removed);	                				
            }
            $(lastElementFocused).change();
            $(lastElementFocused).focus();

        }
        else
            alert('no_element_focused');	
    }                        
   


		
	