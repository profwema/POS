$(document).ready(function(){
	$("#state").chosen();
	$("#city").chosen();
	$("#create-account").click(function(){
		var nameval 	 	= $("#name").val();
		var email	 	= $("#emailid").val();
		var password 		= $("#password").val();
	
		if(nameval=="")
			alert(nameError);
		
		else if(email=="")
			alert(emailError);
		else if(!validateEmail(email))
			alert(emailInvalid);
		else if(password == "")
			alert(passwordError);
		else if(password.length < 6)
			alert(passwordInvalid);
		else
			$("#form")[0].submit();
	});

	$("#login").click(function(){
		
		var email	 	= $("#email").val();
		var password 		= $("#password").val();

		if(email=="")
			alert(emailError);
		else if(!validateEmail(email))
			alert(emailInvalid);
		else if(password == "")
			alert(passwordError);
		else if(password.length < 6)
			alert(passwordInvalid);
		else
		{
			$("#overlay").show();
			var val = $("#form").serialize();	
			console.log(val);
			$.ajax({
					url: "controller.php?f=login", 
					type : "POST",
					data : val,
					success: function(data)
					{
						$("#overlay").hide();
						if($.trim(data)=="1")
							location.replace('.');
						else
							alert(data);
					}
				});
		}
	});

	$("#forgot").click(function(){
		var email	 	= $("#email").val();
		if(email=="")
			alert(emailError);
		else if(!validateEmail(email))
			alert(emailInvalid);
		else
		{
			$("#overlay").show();
			var val = $("#form").serialize();	
			console.log(val);
			$.ajax({
					url: "controller.php?f=forgot", 
					type : "POST",
					data : val,
					success: function(data)
					{
						$("#overlay").hide();
						if($.trim(data)=="1")
							location.replace('login.php');
						else
							alert(data);
					}
				});
		}
	});


	$('body').on('keyup', '#emailid', function(ev){
		var email = $("#emailid").val();
		if(validateEmail(email))
		{
			$.ajax({
				url: "controller.php?f=ajaxEmailPresent", 
				type : "POST",
				data : {'email':email},
				success: function(data)
				{
					console.log(data+" -- "+($.trim(data)=="1"));
					if($.trim(data)=="1")
					{
						$("#email-avl").html(emailUnavailable);
						$("#email-avl").removeClass("available");
						$("#email-avl").addClass("unavailable");
					}
					else if(data=="0")
					{
						$("#email-avl").html(emailAvailable);
						$("#email-avl").removeClass("unavailable");
						$("#email-avl").addClass("available");
					}
				}
			});

		}
		else
			$("#email-avl").html('');
	});


	$("#submit-branch").click(function(){
		var formValues = $("#add-branch").serialize();
		
		var nameVal	= $("#name").val();
		var street	= $("#street").val();
		var country	= $("#country").val();
		var state	= $("#state").val();
		var city	= $("#city").val();
		var phone	= $("#phone").val();

		if($.trim(nameVal) == "")
		{
			alert(nameBlank);
			$("#name").focus();
			return;
		}
		else if($.trim(street) == "")
		{
			alert(streetBlank);
			$("#street").focus();
			return;
		}
		else if($.trim(country) == "")
		{
			alert(countryBlank);
			$("#country").focus();
			return;
		}
		else if($.trim(state) == "")
		{
			alert(stateBlank);
			$("#state").focus();
			return;
		}
		else if($.trim(city) == "")
		{
			alert(cityBlank);
			$("#city").focus();
			return;
		}
		else if($.trim(phone) == "")
		{
			alert(phoneBlank);
			$("#phone").focus();
			return;
		}
		else
		{
			if(1)//confirm(are_you_sure))
			{
				$("#overlay").show(300);
				$.ajax({
						url:"controller.php",
						type : "POST",
						data : formValues,	
						success:function(result){
							$("#overlay").hide(300);
							if($.trim(result) == branchAddedSucces)
							{
								alert(branchAddedSucces);
								location.replace("branches.php");
							}
							else if($.trim(result) == branchUpSuccess)
							{
								alert(branchUpSuccess);
								location.replace("branches.php");
							}
							else
								$(".error-red").html(result);
						}		
				
				});
			}
		}
	});


        $("#submit-service").click(function(){
		var formValues = $("#add-service").serialize();
		var nameVal	= $("#name").val();
	
		if($.trim(nameVal) == "")
		{
			alert(service_name_cannot);
			$("#name").focus();
			return;
		}
		else
		{
			$("#overlay").show(300);
                        $.ajax({
                                        url:"controller.php",
                                        type : "POST",
                                        data : formValues,	
                                        success:function(result){
                                                $("#overlay").hide(300);
                                                if($.trim(result) == s_added_success)
                                                {
                                                        alert(s_added_success);
                                                        location.replace("services.php");
                                                }
                                                else if($.trim(result) == s_updateded_success)
                                                {
                                                        alert(s_updateded_success);
                                                        location.replace("services.php");
                                                }
                                                else
                                                        $(".error-red").html(result);
                                        }		

                        });
		}
	});

	$("#country").on('change', function (e) {
		var cid = $(this).val();
		if(cid != "")
		{
			var u="controller.php?f=getStates&cid="+cid;
			$("#overlay").show(300);
			$.ajax({
					url:u,
					success:function(result){
						$("#overlay").hide(300);
						$("#state").html(result);

						$("#state").chosen("destroy").chosen();
					}		
			
			});
		}
	});
        
        

	$("#state").on('change', function (e) {
		var sid = $(this).val();
		if(sid != "")
		{
			var u="controller.php?f=getCity&sid="+sid;
			console.log(u);
			$("#overlay").show(300);
			$.ajax({
					url:u,
					success:function(result){
						$("#overlay").hide(300);
						$("#city").html(result);

						$("#city").chosen("destroy").chosen();
					}		
			
			});
		}
	});



	$("#submit-cat").click(function(){
		
		var formValues = $("#add-cat").serialize();
		var nameVal	= $("#name").val();
		
		if($.trim(nameVal) == "")
		{
			alert(nameBlank);
			$("#name").focus();
			return;
		}
		else
		{
			if(1)//confirm(are_you_sure))
			{
				$("#overlay").show(300);
				var fd = new FormData();
				var file_data = $('input[type="file"]')[0].files; // for multiple files
				for(var i = 0;i<file_data.length;i++){
					fd.append("file_"+i, file_data[i]);
				}
				fd.append("file_len",file_data.length);
				var other_data = $("#add-cat").serializeArray();
				$.each(other_data,function(key,input){
					fd.append(input.name,input.value);
				});

				$.ajax({
						url:"controller.php",
						type : "POST",
						data : fd,
						contentType: false,       // The content type used when sending data to the server.
						cache: false,             // To unable request pages to be cached
						processData:false,        // To send DOMDocument or non processed data file it is set to false	
						success:function(result){
							$("#overlay").hide(300);
							if($.trim(result) == categoryAddSucces)
							{
								alert(categoryAddSucces);
								location.replace("categories.php");
							}
							else if($.trim(result) == categoryUpSuccess)
							{
								alert(categoryUpSuccess);
								location.replace("categories.php");
							}
							else
								$(".error-red").html(result);
						}		
				
				});
			}
		}
	});


	$("#submit-item").click(function(){
		
		var formValues = $("#add-item").serialize();
		var nameVal	= $("#name").val();
		var name_arVal	= $("#name_ar").val();
		var catName	= $("#cat").val();
		//var priceVal	= $("#price").val();
		//var unitVal	= $("#unit").val();
		//var thresHold	= $("#intimate").val();
		//var commission	= $("#commission").val();
		//var discount	= $("#discount").val();
		//var isChecked 	= $("#type_of_item").is(':checked');
               // var isChecked2 	= $("#item_options").is(':checked');
                  //alert(isChecked);
                  
//		if(isChecked)
//		{
//			if($.trim(nameVal) == "" && $.trim(name_arVal) == "")
//			{
//				alert(nameBlank);
//				$("#name").focus();
//				return;
//			}
//			if($.trim(catName) == "")
//			{
//				alert(catBlank);
//				$("#cat").focus();
//				return;
//			}
//			else if(parseInt(thresHold) > 0 && unitVal=="")
//			{
//				alert(unitCannotBeBlank);
//				$("#unit").focus();
//				return;
//			}
//			else if(parseInt(unitVal) < parseInt(thresHold))
//			{
//				alert(unitIsLessThanThres);
//				$("#unit").focus();
//				return;
//			}
//			else if($.trim(priceVal) == "")
//			{
//				alert(priceBlank);
//				$("#price").focus();
//				return;
//			}
//			else if(parseFloat(priceVal) <= parseInt(commission))
//			{
//				alert(priceLessThanComm);
//				$("#price").focus();
//				return;
//			}
//			else if(parseFloat(priceVal) <= parseInt(discount))
//			{
//				alert(priceLessThanDis);
//				$("#price").focus();
//				return;
//			}
//			if(1)//confirm(are_you_sure))
//			{
//				$("#overlay").show(300);
//				var fd = new FormData();
//				var file_data = $('input[type="file"]')[0].files; // for multiple files
//				for(var i = 0;i<file_data.length;i++){
//					fd.append("file_"+i, file_data[i]);
//				}
//				fd.append("file_len",file_data.length);
//				var other_data = $("#add-item").serializeArray();
//				$.each(other_data,function(key,input){
//					fd.append(input.name,input.value);
//				});
//	
//				$.ajax({
//						url:"controller.php",
//						type : "POST",
//						data : fd,
//						contentType: false,       // The content type used when sending data to the server.
//						cache: false,             // To unable request pages to be cached
//						processData:false,        // To send DOMDocument or non processed data file it is set to false	
//						success:function(result){
//							$("#overlay").hide(300);
//							if($.trim(result) == itemAddedSuccess)
//							{
//								alert(itemAddedSuccess);
//								location.replace("items.php");
//							}
//							else if($.trim(result) == itemUpdatedSuccess)
//							{
//								alert(itemUpdatedSuccess);
//								location.replace("items.php");
//							}
//							else
//								$(".error-red").html(result);
//						}		
//				
//				});
//			}
//		}

                        if($.trim(catName) == "")
			{
				alert(catBlank);
				$("#cat").focus();
				return;
			}
			else if($.trim(nameVal) == "" && $.trim(name_arVal) == "")
			{
				alert(nameBlank);
				$("#name").focus();
				return;
			}

			if(1)//confirm(are_you_sure))
			{
				$("#overlay").show(300);
				$.ajax({
						url:"controller.php",
						type : "POST",
						data : $("#add-item").serializeArray(),
						success:function(result){
							$("#overlay").hide(300);
							if(parseInt($.trim(result)) > 0)
							{
								if($("#submit-item").attr('prop') == "e")
									alert(result+" "+updated_successfully);
								else
									alert(result+" "+added_successfully);
								location.replace("items.php");
							}
							else
								$(".error-red").html(result);
						}		
				
				});
			}
		


		
	});



	
	$("#branch-items").on('change', function (e) {
		var bid = $(this).val();
		if(bid != "")
		{
			var u="controller.php?f=getBranchCategories&bid="+bid;
			console.log(u);
			$("#overlay").show(300);
			$.ajax({
					url:u,
					success:function(result){
						$("#overlay").hide(300);
						$("#cat").html(result);
						$("#cat").chosen("destroy").chosen();
					}		
			
			});
		}
	});

	$("#expiry").on('change',function(){

		if($("#expiry").prop('checked'))
			$("#expiry-date").show(300);
		else
			$("#expiry-date").hide(300);
		
	});

	$("#inlineCheckbox1").on('change',function(){

		if($("#inlineCheckbox1").prop('checked'))
		{
			$(".anti-service input[type='text']").val('');
			$(".anti-service").hide(200);
			
		}
		else
		{	
			$(".anti-service").show(200);
		}
		
	});


	$("#from_date").change(function(){
		 reloadPage();

	});

	$("#to_date").change(function(){
		 reloadPage();

	});

	$("#submit-vat").click(function(){
			var amount	 	= $("#percentage").val();
			if(amount=="")
				amount = "0";
				$("#overlay").show();
				$.ajax({
						url: "controller.php?f=vatAdd&percentage="+amount, 
						success: function(data)
						{
							$("#overlay").hide();
							if($.trim(data)=="1")
													{
															alert("VAT updated...");
								location.reload();

													}
													else
								alert(data);
						}
					});
		});

        $("#submit-delivery").click(function(){
		var amount	 	= $("#amount").val();
		if(amount=="")
			alert("Amount is blank");
		else
		{
			$("#overlay").show();
			$.ajax({
					url: "controller.php?f=updateDelivery&amount="+amount, 
					success: function(data)
					{
						$("#overlay").hide();
						if($.trim(data)=="1")
                                                {
                                                        alert("Delivery updated...");
							location.reload();

                                                }
                                                else
							alert(data);
					}
				});
		}
	});
        
        $("#submit-supplier").click(function(){
		var name	 	= $("#name").val();
		if(name=="")
                {
			alert(nameBlank);
                        $("#name").focus();
                }
		else
		{
                    $("#overlay").show();
                    var fd = new FormData();
                    var other_data = $("#add-sup").serializeArray();
                    $.each(other_data,function(key,input){
                            fd.append(input.name,input.value);
                    });

                    $.ajax({
                                    url:"controller.php",
                                    type : "POST",
                                    data : fd,
                                    contentType: false,       // The content type used when sending data to the server.
                                    cache: false,             // To unable request pages to be cached
                                    processData:false,
                                    success:function(result){
                                            $("#overlay").hide(300);
                                            if($.trim(result) == "1")
                                            {
                                                    location.reload();
                                            }
                                            else
                                                    $(".error-red").html(result);
                                    }		

                    });
		}
	});
});


function reloadPage()
{
	//$("#form")[0].submit();
}

function loadReport()
{
    
	$("#form")[0].submit();
}
function changeLang(val)
{
	if(val != currentLanguage)
	{
		$.ajax({
				url:"controller.php?f=setLang&l="+val,
				success:function(result){
					location.reload();
				}		
		
			});
	}
}

function validateEmail(sEmail) {var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;if (filter.test(sEmail)) {return true;}else {return false;}}

function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}


function deleteData(v , l , d)
{
	if(1)//confirm(are_you_sure))
	{
		var u = "controller.php";
		$("#overlay").show(300);
		$.ajax({
				url:u,
				data : {'f':'delete_data','a':v,'b':l,'d':d},
				type : "POST",
				success:function(result){
					$("#overlay").hide(300);
					if(parseInt($.trim(result)) > 0)
					{
						alert(data_deleted);
						location.reload();
					}
					else
						alert(result);
				}		
		
		});
	}
}


function showAlert()
{
	alert('h');
}



// JQUERY ".Class" SELECTOR.
$(document).ready(function() {

$('.float-val').keypress(function (event) {
	
	return isNumber(event, this)

});

$('.int-val').keypress(function (event) {
	
	return /\d/.test(String.fromCharCode(event.keyCode));

});

$("#email").change(function(){
	var email=$("#email").val();
	if(validateEmail(email))
	{
		$(".email-un").html('');
		var u = "controller.php?f=checkEmailPresentEmp&email="+email+"&u=1&nd="+$("#nd").val();	
		$.ajax({
			url:u,
			success:function(result){
			if(parseInt($.trim(result)) > 0 )
			{
				
				$(".email-un").html(emailUnAvailable);
			}
			}
		});
	}
});




	$("#submit-emp").click(function(){
		
		var formValues = $("#add-emp").serialize();
		var nameVal	= $("#name").val();
		var emailVal	= $("#email").val();

		if($.trim(nameVal) == "")
		{
			alert(empNameBlank);
			$("#name").focus();
			return;
		}
		else if(emailVal == "")
		{
			alert(emailInvalid);
			$("#email").focus();
			return;
		}
		
		else
		{
			if(1)//confirm(are_you_sure))
			{
				$("#overlay").show(300);
				var fd = new FormData();
				var file_data = $('input[type="file"]')[0].files; // for multiple files
				for(var i = 0;i<file_data.length;i++){
					fd.append("file_"+i, file_data[i]);
				}
				fd.append("file_len",file_data.length);
				var other_data = $("#add-emp").serializeArray();
				$.each(other_data,function(key,input){
					fd.append(input.name,input.value);
				});

				$.ajax({
						url:"controller.php",
						type : "POST",
						data : fd,
						contentType: false,       // The content type used when sending data to the server.
						cache: false,             // To unable request pages to be cached
						processData:false,        // To send DOMDocument or non processed data file it is set to false	
						success:function(result){
							$("#overlay").hide(300);
							if($.trim(result) == empAddedSuccess)
							{
								alert(empAddedSuccess);
								location.replace("employees.php");
							}
							else if($.trim(result) == empUpdatedSuccess)
							{
								alert(empUpdatedSuccess);
								location.replace("employees.php");
							}
							else
								$(".error-red").html(result);
						}		
				
				});
			}
		}
	});



	$("#submit-expenditure").click(function(){
		
		var formValues 	= $("#add-expenditure").serialize();
		var desc	= $("#desc_en").val();
		var cost	= $("#cost").val();
		var dated	= $("#dated").val();

		if($.trim(desc) == "")
		{
			alert(descBlank);
			$("#desc_en").focus();
			return;
		}
		else if($.trim(cost) == "")
		{
			alert(costBlank);
			$("#cost").focus();
			return;
		}
		else if($.trim(dated) == "")
		{
			alert(dateBlank);
			$("#dated").focus();
			return;
		}
		else
		{
			if(1)//confirm(are_you_sure))
			{

				$.ajax({
						url:"controller.php",
						type : "POST",
						data : formValues,
						success:function(result){
							$("#overlay").hide(300);
							if($.trim(result) == costAddSuccessfully)
							{
								alert(costAddSuccessfully);
								location.replace("expenditure.php");
							}
							else if($.trim(result) == costUpdatedSuccess)
							{
								alert(costUpdatedSuccess);
								location.replace("expenditure.php");
							}
							else
								$(".error-red").html(result);
						}		
				
				});
			}
		}
	});


	$("#submit-table").click(function(){
		
		var formValues 	= $("#update-table").serialize();
		var table	= $("#table").val();

		if($.trim(table) == "")
		{
			alert(tableBlank);
			$("#table").focus();
			return;
		}
		else
		{
			if(1)//confirm(are_you_sure))
			{

				$.ajax({
						url:"controller.php",
						type : "POST",
						data : formValues,
						success:function(result){
							$("#overlay").hide(300);
							if($.trim(result) == tableUpdatedSuccess)
							{
								alert(tableUpdatedSuccess);
								location.replace("table.php");
							}
							else
								$(".error-red").html(result);
						}		
				
				});
			}
		}
	});

        $("#submit-shifts").click(function(){
		
		var formValues 	= $("#update-shifts").serialize();
		$.ajax({
                                url:"controller.php",
                                type : "POST",
                                data : formValues,
                                success:function(result){
                                        $("#overlay").hide(300);
                                        if($.trim(result) == "1")
                                        {
                                                alert(shiftUpdatedSuccess);
                                                location.replace("shifts.php");
                                        }
                                        else
                                                $(".error-red").html(result);
                                }		

                });
	});


	$("#submit-discount").click(function(){
		
		var formValues 	= $("#update-discount").serialize();
		var amount	= $("#amount").val();

		if($.trim(amount) == "")
		{
			alert(amountBlank);
			$("#amount").focus();
			return;
		}
		else
		{
			if(1)//confirm(are_you_sure))
			{

				$.ajax({
						url:"controller.php",
						type : "POST",
						data : formValues,
						success:function(result){
							$("#overlay").hide(300);
							if($.trim(result) == discountUpdated)
							{
								alert(discountUpdated);
								location.replace("current_discounts.php");
							}
							else
								$(".error-red").html(result);
						}		
				
				});
			}
		}
	});
        
        
        $("#submit-purchase").click(function(){
		
		var formValues 	= $("#purchase-form").serialize();
		$.ajax({
                                url:"controller.php",
                                type : "POST",
                                data : formValues,
                                success:function(result){
                                        $("#overlay").hide(300);
                                        if($.trim(result) == "1")
                                        {
                                                alert(success);
                                                location.replace("purchase.php");
                                        }
                                        else
                                                $(".error-red").html(result);
                                }		

                });
	});

	$("#logout").click(function(){
		$.ajax({
				url:"controller.php?l=1&f=logout",
				success:function(result){
					$("#overlay").hide(300);
					location.replace("index.php");
				}		
				
			});
	});


	$(".del-qty").click(function(){
		var prop	= parseInt($(this).attr("prop"));
		var nxt		= prop + 1;
		
		$('#qty-nm-'+prop).val('');
		$('#qty-prc-'+prop).val('');
		$('#qty-nm-ar-'+prop).val('');
		$('#discount-'+prop).val('');
		$('#unit-present-'+prop).val('');
		$('#intimate-qty-'+prop).val('');
		if(prop > 0)
			$('#row-val-'+prop).hide(100);
		
	});

	$(".more-qty").click(function(){
		var prop	= parseInt($(this).attr("prop"));
		var nxt		= prop + 1;
		
		$('#row-val-'+nxt).show(100);
		
	});
        
        
        $(".del-qty-item").click(function(){
		var prop	= parseInt($(this).attr("prop"));
		var nxt		= prop + 1;
		
                $('#qty-nm-item-'+prop).val('');
		$('#item-price-'+prop).val('');
		$('#qty-nm-ar-item-'+prop).val('');
		if(prop > 0)
			$('#row-v-'+prop).hide(100);
		
	});

	$(".more-qty-item").click(function(){
		var prop	= parseInt($(this).attr("prop"));
		var nxt		= prop + 1;
		
		$('#row-v-'+nxt).show(100);
		
	});
});
    // THE SCRIPT THAT CHECKS IF THE KEY PRESSED IS A NUMERIC OR DECIMAL VALUE.
function isNumber(evt, element) {

var charCode = (evt.which) ? evt.which : event.keyCode

if (
	(charCode != 45 || $(element).val().indexOf('-') != -1) &&      // “-” CHECK MINUS, AND ONLY ONE.
	(charCode != 46 || $(element).val().indexOf('.') != -1) &&      // “.” CHECK DOT, AND ONLY ONE.
	(charCode < 48 || charCode > 57))
	return false;

return true;
}    


function validateEmail(sEmail) {
    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    if (filter.test(sEmail)) {
        return true;
    }
    else {
        return false;
    }
}

function selectType(ele)
{
        var isChecks    = $("#item_options").is(':checked');
        var chck        = $(ele).is(':checked');
        if(!chck)
        {
            if(isChecks)
            {
                $("#type_of_item").prop("checked",true);
                $("#type_of_item").parent().removeClass("unchecked");
                $("#type_of_item").parent().addClass("checked");
                return;
            }
           
        }
        
        
	var isChecked = $(ele).is(':checked');
        
	if(isChecked)
	{
		if(!$("#single-type").is(':visible') || $("#multi-type").is(':visible'))
		{
			$("#single-type").show(200);
			$("#multi-type").hide(200);
		}
	}
	else
	{
		if($("#single-type").is(':visible') || !$("#multi-type").is(':visible'))
		{
			$("#single-type").hide(200);
			$("#multi-type").show(200);
		}
	
	}
	
}


function selectTypeItem(ele)
{
	var isChecked = $(ele).is(':checked');
         
	if(isChecked)
	{
            if(!$("#type_of_item").is(':checked'))
            {
                $("#type_of_item").prop("checked",isChecked);
                $("#type_of_item").parent().removeClass("unchecked");
                $("#type_of_item").parent().addClass("checked");
                selectType("#type_of_item");
            }
            if(!$("#item-type-opt").is(':visible'))
		$("#item-type-opt").show(200);
	}
	else
	{
            if($("#item-type-opt").is(':visible'))
                $("#item-type-opt").hide(200);
	
	}
}

function selectServ(ele)
{
	var isChecked = $(ele).is(':checked');
         
	if(isChecked)
	{
            $("#serv-col").show(100);
            $("#item_type_radio").hide();
            $("#multi-type").hide();
            $("#item-opt").hide();
            $("#single-type").show(100);
	}
	else
	{
            $("#serv-col").hide();
            $("#item_type_radio").show(100);
            $("#item-opt").show(100);
	
	}
        
        
      
}


function shiftUpdate(ele)
{
    
    var el = $(ele).val();
    if(el == "")
    {
        $("#from_div").show(50);
        $("#to_div").show(50);
    }
    else
    {
         $("#from_div").hide(50);
        $("#to_div").hide(50);
    }
}




$(function(){
    
    $(".chzn-select").chosen().change(function() {
            var idv     = $(this).val();
            var splitted= idv.split("@@");
            var idvalue = splitted[1];
            var indexv  = splitted[0];
            
            var ab      = "";
            try
            {
                ab      = itemsAlready[idvalue];
                if(typeof ab !== "undefined" && ab != "")
                {
                    $("#item_qty_list_"+indexv).html(ab);
                    $("#item_qty_list_"+indexv).chosen("destroy").chosen();
                    return;
                }
            }catch(err){}
            
            $(".overlay").show();
            $.ajax({
                            url:"controller.php?f=getQtyOfItems&it="+idvalue,
                            type : "POST",
                            success:function(result){
                                    $("#overlay").hide();
                                    $("#item_qty_list_"+indexv).html(result);
                                    $("#item_qty_list_"+indexv).chosen("destroy").chosen();
                            }		

            });
       
    });
    
    for(var i=1;i<100;i++)
    {
        if(i > numAlready)
            $("#row-v-"+i).hide();
    }
    
    
    
})