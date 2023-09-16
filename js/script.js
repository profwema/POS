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
		
		var user	 	= $("#user").val();
		var password 		= $("#password").val();

		if(user=="")
			alert(emailError);

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

	$("#branch").on('change', function (e) {
		var cid = $(this).val();             
		if(cid != "")
		{
                    getStore(cid);
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
		var image	= $("#photo").val();
		var catName	= $("#cat").val();
		var priceVal	= $("#price").val();
		var unitVal	= $("#unit").val();
		var thresHold	= $("#intimate").val();
		var commission	= $("#commission").val();
		var discount	= $("#discount").val();
                
		var isChecked 	= $("#type_of_item").is(':checked');
                var isChecked2 	= $("#item_options").is(':checked');

		if(isChecked)
		{
			if($.trim(nameVal) == "" && $.trim(name_arVal) == "")
			{
				alert(nameBlank);
				$("#name").focus();
				return;
			}
			if($.trim(catName) == "")
			{
				alert(catBlank);
				$("#cat").focus();
				return;
			}
			if($.trim(image) == "")
			{
				alert(catBlank);
				$("#photo").focus();
				return;
			}
			if(1)//confirm(are_you_sure))
			{
				$("#overlay").show(300);
				var fd = new FormData();
				var file_data = $('input[type="file"]')[0].files; // for multiple files
				for(var i = 0;i<file_data.length;i++){
					fd.append("file_"+i, file_data[i]);
				}
				fd.append("file_len",file_data.length);
				var other_data = $("#add-item").serializeArray();
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
							if($.trim(result) == itemAddedSuccess)
							{
								alert(itemAddedSuccess);
								location.replace("items.php");
							}
							else if($.trim(result) == itemUpdatedSuccess)
							{
								alert(itemUpdatedSuccess);
								location.replace("items.php");
							}
							else
								$(".error-red").html(result);
						}		
				
				});
			}
		}
		else
		{
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
                            var form = $('#add-item')[0];
                           var fd = new FormData(form);
                                $("#overlay").show(300);
				$.ajax({
						url:"controller.php",
						type : "POST",
						data : fd,
                                                enctype: 'multipart/form-data',
                                                contentType: false,       // The content type used when sending data to the server.
						cache: false,             // To unable request pages to be cached
						processData:false,     
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
        $("#open-palance-date").click(function()
        {
		var amount	 	= $("#palance-date").val();
		if(amount=="")
			alert("Date is blank");
		else
		{
			$("#overlay").show();
			$.ajax({
					url: "controller.php?f=updatepalDate&date="+amount, 
					success: function(data)
					{
						$("#overlay").hide();
						if($.trim(data)=="1")
                                                {
                                                        alert("Date updated...");
							location.reload();

                                                }
                                                else
							alert(data);
					}
				});
		}
	});
        
        
       $("#submit-supplier").click(function()
       {

            var formValues 	= $("#add-sup").serialize();
            
            var name	 	= $("#name").val();
            //alert(name);
            if($.trim(name) == "")
                {
			alert(nameBlank);
                        $("#name").focus();
                }
            else
            {
                if(1)//confirm(are_you_sure))
                {
                    $.ajax({
                        url:"controller.php",
                        type : "POST",
                        data : formValues,
                        success:function(result)
                        {
                                $("#overlay").hide(300);
                                if($.trim(result) == "1")
                                {
                                        alert(success);
                                        location.replace("suppliers.php");
                                }
                                else
                                        $(".error-red").html(result);
                        }		

                    });
                }
            }
	});
        
       $("#submit-customer").click(function()
       {
           var source          = ($(this).attr('src'))? true: false;
            var formValues 	= $("#add-customer").serialize();
            var name	 	= $("#name").val();
            var phone	 	= $("#phone").val();
            //alert(phone);
            if($.trim(name) == "" )
                {
			alert(nameBlank);
                        $("#name").focus();
                        return;
                }
            if($.trim(phone) == "")
                {
			alert(phoneBlank);
                        $("#phone").focus();
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
                        success:function(result)
                        {
                               
                                if($.trim(result) == "1")
                                {
                                    if (source)
                                    {
                                        alert(result);
                                        getCustomers();
                                        closeTables();
                                    }
                                    else
                                    {
                                        alert(success);
                                        location.replace("customers.php");
                                    }
                                }
                                else
                                        $(".error-red").html(result);
                        }		

                    });
                }
            }
	});  
        
       $("#submit-settings").click(function()
       {
          // alert('phone');

            var formValues 	= $("#settings").serialize();
                if(1)//confirm(are_you_sure))
                {
                    $.ajax({
                        url:"controller.php",
                        type : "POST",
                        data : formValues,
                        success:function(result)
                        {
                                $("#overlay").hide(300);
                                if($.trim(result) == "1")
                                {
                                        alert(success);
                                        location.reload();
                                }
                                else
                                        $(".error-red").html(result);
                        }		
                    });
                }
	});    
       $("#submit-Qsettings").click(function()
       {
           //alert('phone');

            var formValues 	= $("#q-settings").serialize();
                if(1)//confirm(are_you_sure))
                {
                    $.ajax({
                        url:"controller.php",
                        type : "POST",
                        data : formValues,
                        success:function(result)
                        {
                                $("#overlay").hide(300);
                                if($.trim(result) == "1")
                                {
                                        alert(success);
                                        //location.reload();
                                        location.replace("quotations.php");
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
function deleteInvoice(vt , id ,ot)
{
	if(1)//confirm(are_you_sure))
	{
		var u = "controller.php";
		$("#overlay").show(300);
		$.ajax({
				url:u,
				data : {'f':'delete_invoice','vt':vt,'id':id,'ot':ot },
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

function deleteTrans( id )
{
	if(1)//confirm(are_you_sure))
	{
		var u = "controller.php";
		$("#overlay").show(300);
		$.ajax({
				url:u,
				data : {'f':'delete_trans','id':id },
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
function deleteJournal( no )
{
	if(1)//confirm(are_you_sure))
	{
		var u = "controller.php";
		$("#overlay").show(300);
		$.ajax({
				url:u,
				data : {'f':'delete_journal','no':no },
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
	
//	return /\d/.test(String.fromCharCode(event.keyCode));
        
        
    var char = String.fromCharCode(event.which)
    var txt = $(this).val();
    var arabicCharUnicodeRange = /[\u0600-\u06FF]/;
    if ( arabicCharUnicodeRange.test(char) )
    {
        $(this).val(txt.replace(char, ''));
       
    } alert($(this).val());
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




	$("#submit-emp").click(function()
            {

                        if (v1 && v2)
                        {
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







                $("#overlay").show(300);
                var fd = new FormData();
                var files = $('#fileInput')[0].files;        
                fd.append('file',files[0]);
                var other_data = $("#add-emp").serializeArray();
                $.each(other_data,function(key,input){
                        fd.append(input.name,input.value);
                });

                $.ajax({
                                url:"controller.php",
                                type : "POST",
                                data : fd,
                              contentType: false,
                                     cache: false,
                               processData:false,       // To send DOMDocument or non processed data file it is set to false	
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

      $("#submit-units").click(function(){
		
		var formValues 	= $("#update-units").serialize();
		$.ajax({
                                url:"controller.php",
                                type : "POST",
                                data : formValues,
                                success:function(result){
                                        $("#overlay").hide(300);
                                        if($.trim(result) == "1")
                                        {
                                                alert(unitUpdatedSuccess);
                                                location.replace("units.php");
                                        }
                                        else
                                                $(".error-red").html(result);
                                }		

                });
	});
      $("#submit-jobTitle").click(function(){
		var formValues 	= $("#update-jobTitle").serialize();
		$.ajax({
                                url:"controller.php",
                                type : "POST",
                                data : formValues,
                                success:function(result){
                                        $("#overlay").hide(300);
                                        if($.trim(result) == "1")
                                        {
                                                alert(jobTitleUpdatedSuccess);
                                                location.replace("jobTitle.php");
                                        }
                                        else
                                                $(".error-red").html(result);
                                }		

                });
	});
      $("#submit-stores").click(function(){
		
		var formValues 	= $("#update-stores").serialize();
               
		$.ajax({
                                url:"controller.php",
                                type : "POST",
                                data : formValues,
                                success:function(result){
                                        $("#overlay").hide(300);
                                        if($.trim(result) == "1")
                                        {
                                                alert(storeUpdatedSuccess);
                                                location.replace("stores.php");
                                        }
                                        else
                                                $(".error-red").html(result);
                                }		

                });
	});    
      $("#submit-flbs").click(function(){
		
		var formValues 	= $("#update-flbs").serialize();
		$.ajax({
                                url:"controller.php",
                                type : "POST",
                                data : formValues,
                                success:function(result){
                                        $("#overlay").hide(300);
                                        if($.trim(result) == "1")
                                        {
                                                alert(success);
                                                location.replace("budget.php");
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
//                                success:function(result){
//                                        $("#overlay").hide(300);
//                                        if($.trim(result) == "1")
//                                        {
//                                                alert(success);
//                                                location.replace("purchase.php");
//                                        }
//                                        else
//                                                $(".error-red").html(result);
//                                }
                                success:function(result){
                                        $("#overlay").hide(300);
                                        var jsonParse = $.parseJSON(result);
                                        if($.trim(jsonParse['success']) == "1")
                                        {
                                                alert(success);
                                                var invoice =jsonParse['invoice'];
                                                window.open('reports/purchase.php?sd='+invoice, '_blank')  
                                                location.replace("purchase.php");
                                        }
                                        else
                                        {
                                            var error =jsonParse['error'];
                                            $(".error-red").html(error);
                                        }
                                }
                               

                });
	});
        $("#submit-purchaseRet").click(function(){
		
		var formValues 	= $("#purchaseRet-form").serialize();
		$.ajax({
                                url:"controller.php",
                                type : "POST",
                                data : formValues,
//                                success:function(result){
//                                        $("#overlay").hide(300);
//                                        if($.trim(result) == "1")
//                                        {
//                                                alert(success);
//                                                location.replace("purchaseRet.php");
//                                        }
//                                        else
//                                                $(".error-red").html(result);
//                                }	
                                success:function(result){
                                        $("#overlay").hide(300);
                                        var jsonParse = $.parseJSON(result);
                                        if($.trim(jsonParse['success']) == "1")
                                        {
                                                alert(success);
                                                var invoice =jsonParse['invoice'];
                                                window.open('reports/purchaseRet.php?sd='+invoice, '_blank')  
                                                location.replace("purchaseRet.php");
                                        }
                                        else
                                        {
                                            var error =jsonParse['error'];
                                            $(".error-red").html(error);
                                        }
                                }

                });
	});
        $("#submit-income").click(function()
        {
		var formValues 	= $("#incoming-form").serialize();
		$.ajax({
                                url:"controller.php",
                                type : "POST",
                                data : formValues,
//                                success:function(result){
//                                        $("#overlay").hide(300);
//                                        if($.trim(result) == "1")
//                                        {
//                                                alert(success);
//                                                location.replace("incoming-entry.php");
//                                        }
//                                        else
//                                                $(".error-red").html(result);
//                                }
                                success:function(result){
                                        $("#overlay").hide(300);
                                        var jsonParse = $.parseJSON(result);
                                        if($.trim(jsonParse['success']) == "1")
                                        {
                                                alert(success);
                                                var invoice =jsonParse['invoice'];
                                                window.open('reports/incoming.php?sd='+invoice, '_blank')  
                                                location.replace("incoming-entry.php");
                                        }
                                        else
                                        {
                                            var error =jsonParse['error'];
                                            $(".error-red").html(error);
                                        }
                                }                                

                });
	});  
        $(".submit-sale").click(function()
        {
            var source = 0;
             source      = ($(this).attr('src'))? true: false;
		var formValues 	= $("#sales-form").serialize();
                $("#overlay").show(300);
		$.ajax({
                                url:"controller.php",
                                type : "POST",
                                data : formValues,
                                success:function(result)
                                {
                                        $("#overlay").hide(300);
                                        var jsonParse = $.parseJSON(result);
                                        if (source)
                                        {
                                            if($.trim(jsonParse['success']) == "1")
                                            {
                                                    alert(success);
                                                    var invoice = jsonParse['invoice'];
                                                    alert(invoice);
//                                                    window.open('reports/sales.php?sd='+invoice, '_blank')  
                                                    location.reload();
                                            }
                                            else
                                            {
                                                var error =jsonParse['error'];
                                                alert(error);
                                            }
                                        }
                                        else
                                        {
                                            if($.trim(jsonParse['success']) == "1")
                                            {
                                                    alert(success);
                                                    //var invoice =jsonParse['invoice'];
                                                    //window.open('reports/sales.php?sd='+invoice, '_blank')  
                                                    location.replace("saleInvoice.php");
                                            }
                                            else
                                            {
                                                var error =jsonParse['error'];
                                                $(".error-red").html(error);
                                            }                                                                                        
                                        }
                                }
                });
	});  
        $("#submit-saleRet").click(function()
        {
		var formValues 	= $("#saleRet-form").serialize();
		$.ajax({
                                url:"controller.php",
                                type : "POST",
                                data : formValues,                                
                                success:function(result){
                                        $("#overlay").hide(300);
                                        var jsonParse = $.parseJSON(result);
                                        if($.trim(jsonParse['success']) == "1")
                                        {
                                                alert(success);
                                                var invoice =jsonParse['invoice'];
                                                window.open('reports/reSales.php?sd='+invoice, '_blank')  
                                                location.replace("salesRet.php");
                                        }
                                        else
                                        {
                                            var error =jsonParse['error'];
                                            $(".error-red").html(error);
                                        }
                                }                                

                });
	});           

        $("#submit-outgo").click(function()
        {
		var formValues 	= $("#outgoing-form").serialize();
		$.ajax({
                                url:"controller.php",
                                type : "POST",
                                data : formValues,
//                                success:function(result){
//                                        $("#overlay").hide(300);
//                                        if($.trim(result) == "1")
//                                        {
//                                                alert(success);
//                                                location.replace("outgoing-entry.php");
//                                        }
//                                        else
//                                                $(".error-red").html(result);
//                                }	
                                success:function(result){
                                        $("#overlay").hide(300);
                                        var jsonParse = $.parseJSON(result);
                                        if($.trim(jsonParse['success']) == "1")
                                        {
                                                alert(success);
                                                var invoice =jsonParse['invoice'];
                                                window.open('reports/outgoing.php?sd='+invoice, '_blank')  
                                                location.replace("outgoing-entry.php");
                                        }
                                        else
                                        {
                                            var error =jsonParse['error'];
                                            $(".error-red").html(error);
                                        }
                                }

                });
	});      
        $("#submit-quote").click(function()
        {
		var formValues 	= $("#quote-form").serialize();
                
		$.ajax({
                                url:"controller.php",
                                type : "POST",
                                data : formValues,
                                success:function(result){
                                        $("#overlay").hide(300);
                                        if($.trim(result) == "1")
                                        {
                                                alert(success);
                                                location.reload();
                                        }
                                        else
                                                $(".error-red").html(result);
                                }		

                });
	});                      

        $("#submit-storeTrans").click(function()
        {
		var formValues 	= $("#storeTrans-form").serialize();
                
		$.ajax({
                                url:"controller.php",
                                type : "POST",
                                data : formValues,
                                success:function(result){
                                        $("#overlay").hide(300);
                                        if($.trim(result) == "1")
                                        {
                                                alert(success);
                                                location.replace("stores-transfer.php");
                                        }
                                        else
                                                $(".error-red").html(result);
                                }		

                });
	});      
        


       $("#submit-openPalance").click(function()
       {
		
            var formValues 	= $("#openPalance-form").serialize();
            var store	= $("#store").val();
            //alert(store);
            if($.trim(store) == "")
            {
                    alert(storeBlank);
                    $("#store").focus();
                    return;
            }

            if(1)//confirm(are_you_sure))
            {
                $.ajax({
                    url:"controller.php",
                    type : "POST",
                    data : formValues,
                    success:function(result)
                    {
                            $("#overlay").hide(300);
                            if($.trim(result) == "1")
                            {
                                    alert(success);
                                    location.replace("open-palance.php");
                            }
                            else
                                    $(".error-red").html(result);
                    }		

                });
            }
	}); 
        
                
    $("#submit-journal").click(function()
    {
        alert($("#difference").val ());
       if( $("#difference").val ()==='0.00')
       {
        var formValues 	= $("#journal-form").serialize();
        $.ajax({
            url:"controller.php",
            type : "POST",
            data : formValues,
            success:function(result){
                $("#overlay").hide(300);
                if($.trim(result) === "1")
                {
                    alert(success);
                    location.replace("journal.php");
                }
                else
                    $(".error-red").html(result);
            }		
            
        });
    }
    else
    {
        alert("Total Debit must be equal Total Credit");
    }
    });  
        

	$("#logout").click(function()
        {
            logout();
	});


	$(".del-qty").click(function(){
		var prop	= parseInt($(this).attr("prop"));

		
		$('#qtyunit-'+prop).val('');
		$('#unit-fill-'+prop).val('');
		$('#qty-barcode-'+prop).val('');
                $('#qty-prc-'+prop).val('');
		$('#discount-'+prop).val('');
		$('#discountPer-'+prop).val('');
		$('#qty-priceDis-'+prop).val('');
		if(prop > 0)
			$('#row-v-'+prop).hide(100);
		
	});

	$(".more-qty").click(function(){
		var prop	= parseInt($(this).attr("prop"));
		var nxt		= prop + 1;
		
		$('#row-v-'+nxt).show(100);
		
	});
        
        //------------------------------------
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
        //---------------------------------------
   
       $(".del-palance-item").click(function(){
		var prop	= parseInt($(this).attr("prop"));
		var nxt		= prop + 1;
		//alert(prop);
                $('#item-qty-'+prop).val('');
		$('#item-price-'+prop).val('');
		$('#item-discount-'+prop).val('');
                $('#item-vat-'+prop).val('');
                $('#item-total-'+prop).val('');
                
		if(prop > 0)
                    $('#row-v-'+prop).hide(100);
                
		
	});

	$(".more-palance-item").click(function(){
		var prop	= parseInt($(this).attr("prop"));
		var nxt		= prop + 1;
		//alert(nxt);
		$('#row-v-'+nxt).show(100);

		
	});
        //---------------------------------------
       $(".del-parchase-item").click(function(){
		var prop	= parseInt($(this).attr("prop"));
		var nxt		= prop + 1;
                 $('#store-'+prop).prop('selectedIndex',0);
                $('#barcode-'+prop).prop('selectedIndex',0);
                $('#item-qty-'+prop).val('');
		$('#item-price-'+prop).val('');
		$('#item-discount-'+prop).val('');
                $('#item-vat-'+prop).val('');
                $('#item-total-'+prop).val('');
                
		if(prop > 0)
                {
                    $('#row-v-'+prop).hide(100);
                }
                totalParshase(prop);
	});
       //--------------------------------------- 

	$(".more-parchase-item").click(function(){
		var prop	= parseInt($(this).attr("prop"));
		var nxt		= prop + 1;
		//alert(nxt);
		$('#row-v-'+nxt).show(100);
                $("#store-"+nxt).html($("#store-"+prop).html());
                $("#store-"+nxt).chosen("destroy").chosen();     
		
	});       
        //---------------------------------------
       $(".del-parchaseRet-item").click(function(){
		var prop	= parseInt($(this).attr("prop"));
		var nxt		= prop + 1;

                $('#barcode-'+prop).prop('selectedIndex',0);
                $('#store-'+prop).val('');                
                $('#item-qty-'+prop).val('');
		$('#item-price-'+prop).val('');
		$('#item-discount-'+prop).val('');
                $('#item-vat-'+prop).val('');
                $('#item-total-'+prop).val('');
                
		if(prop > 0)
                {
                    $('#row-v-'+prop).hide(100);
                }
		 totalParshase(prop);
	});
       //--------------------------------------- 

	$(".more-parchaseRet-item").click(function(){
		var prop	= parseInt($(this).attr("prop"));
		var nxt		= prop + 1;
		//alert(nxt);
		$('#row-v-'+nxt).show(100);
	});             
       //--------------------------------------- 
       $(".del-jurnal-item").click(function(){
		var prop	= parseInt($(this).attr("prop"));

                $('#account-'+prop).val('').trigger("chosen:updated"); 
                $('#debit-'+prop).val('');
		$('#credit-'+prop).val('');
		$('#disc-'+prop).val('');
                $('#ref-'+prop).val('');
		if(prop > 0)
                {
                    $('#row-v-'+prop).hide(100);
                }
	});

	$(".more-jurnal-item").click(function(){
		var prop	= parseInt($(this).attr("prop"));
		var nxt		= prop + 1;
		//alert(nxt);
		$('#row-v-'+nxt).show(100);
	});    
	
	$("#updareCheck").click(function()
	{

	    var currentVer = $("#ver").val();

	    
	    
	    $.ajax(
		     {  
		    type: "POST",  
		    url:"controller.php?l=1&f=updateCheck",
		    data : {'current':currentVer},
		    success: function(msg)
		    {  
			if(msg == true)
			{ 

			    $("#checkResult").html('<span style = "color:green">Up To Date </span>');						
			} 
			else  
			{  
			    $("#checkResult").html('<span style = "color:blue">There is new version available now ');
                             $("#updare").show();
			}
		    }
		});  	    
	});
	
	
	$("#updare").click(function()
	{

	    var currentVer = $("#ver").val();
            $("#updare").hide();    
            $("#checkResult").html('Updating DB .............<br>');
	    $.ajax(
		     {  
		    type: "POST",  
		    url:"controller.php?l=1&f=dbUpdate",
		    data : {'current':currentVer},
		    success:function(result)
		    {           
                        if($.trim(result) === "1")
                        {
                            $("#checkResult").html( $("#checkResult").html()+'OK <br>');
                            $("#checkResult").html( $("#checkResult").html()+'Updating System Files.........');
                            $.ajax(
                                    {  
                                        type: "POST",  
                                        url:"controller.php?l=1&f=filesUpdate",
                                        success: function(result)
                                        {  
                                            if($.trim(result) === "1")
                                            {
                                                $("#checkResult").html( $("#checkResult").html()+'OK <br>');
                                                $("#checkResult").html( $("#checkResult").html()+'Loging out Within 5 minutes');            
                                                window.setTimeout(function()
                                                {      
                                                    logout();                    
                                                },2000 );
                                            }
                                            else
                                            {                                                                          
                                                $("#checkResult").html( $("#checkResult").html()+result);
                                            }
                                                
                                        }
                                    });            
                        }
                        else
                        {
                            $("#checkResult").html( $("#checkResult").html()+result);                            
                        }
		    }
		});  			
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

        function totalItemOpenPalance(id)
        {
               var preTotal =  $("#item-qty-"+id).val() * $("#item-price-"+id).val();
               //alert($("#item-qty-"+id).val());
               var discount = preTotal * $("#item-discount-"+id).val() / 100;
               var disTotal = preTotal - discount;
               var vaTax    = disTotal * $("#item-vat-"+id).val() / 100;
               var total    = disTotal + vaTax ;
               $("#item-total-"+id).val( total.toFixed(2) );
        }

        function getStore(cid)
        {
            var u="controller.php?f=getStores&cid="+cid;
            $.ajax({
                url:u,
                success:function(result)
                {
                      for(var i =0;i<10;i++)
                      {
                        $("#store-"+i).html(result);
                        $("#store-"+i).chosen("destroy").chosen();                         
                    }
                }		
            });
        }

    function getBalance(itemId,indexv)
    {
        //alert(itemId);
        var store          =  $("#store-"+indexv).val(); 
        var itemType        = $("#barcode-"+indexv).attr('type');       
        var enableNegative  = $("#barcode-"+indexv).attr('enableNegative');     
        var old             =($('#item-old-'+indexv).val()) ? $('#item-old-'+indexv).val() : 0 ;
//alert(store);

        if (itemType==1 || enableNegative==1) $("#balance-"+indexv).val(-1);
        else
        {

            var u="controller.php?f=getItemBalance&item="+itemId+"&store="+store+"&old="+old;    
            $.ajax
            ({
                url:u,
                success:function(result)
                {
                    var d = $.trim(result)
                    $("#balance-"+indexv).val(d);
                }		
            });
        }
    }
    function logout()
{
		$.ajax({
				url:"controller.php?l=1&f=logout",
				success:function(result){
					$("#overlay").hide(300);
					location.replace("index.php");
				}		
				
			});
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
//                alert(ab);
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
    $(".barcode-select").chosen().change(function() {
            var idv     = $(this).val();
            var splitted= idv.split("@@");
            var idvalue   = splitted[1];
            var indexv    = splitted[0];
            var vatValue  = splitted[2];
           
            $("#item-vat-"+indexv).val(vatValue);
            totalItemOpenPalance(indexv);       
    });

    $(".barcode-select-sale").chosen().change(function() 
    {
            var indexv          = $('option:selected', this).attr('index');
            var price           = $('option:selected', this).attr('price');
            var disc            = $('option:selected', this).attr('disc');
            var vat             = $('option:selected', this).attr('vat');

            var itemId           = $(this).val();
            var indexv          = $(this).attr('id').replace('barcode-','');    
            getBalance(itemId,indexv);
            $("#item-price-"+indexv).val(price);
            $("#item-discount-"+indexv).val(disc);
            $("#item-vat-"+indexv).val(vat);
//            $("#store-"+indexv).removeAttr("disabled");  
//            $("#store-"+indexv).val('').trigger("chosen:updated");
            totalParshase(indexv);

    });    
    
    
        $(".barcode-select-Ret").chosen().change(function() 
        {
            var indexv          = $('option:selected', this).attr('index');
            var store           = $('option:selected', this).attr('store');    
            var storeName       = $('option:selected', this).attr('storeName');             
            var qty             = $('option:selected', this).attr('qty');
            var cost            = $('option:selected', this).attr('cost');
            var dis             = $('option:selected', this).attr('dis');
            var vat             = $('option:selected', this).attr('vat');

            $("#store-"+indexv).val(store);
            $("#storeName-"+indexv).val(storeName);
            $("#item-qty-"+indexv).val(qty);
            $("#item-price-"+indexv).val(cost);
            $("#item-discount-"+indexv).val(dis);
            $("#item-vat-"+indexv).val(vat);
            totalParshase(indexv);
    });    
    
//    $(".store").chosen().change(function() 
//    {
//        var store           = $(this).val();
//        var indexv          = $(this).attr('id').replace('store-','');    
//        getBalance(store,indexv);
//    }); 
    
    for(var i=1;i<100;i++)
    {
        if(i > numAlready)
        $("#row-v-"+i).hide();
    }
    
    
    
})