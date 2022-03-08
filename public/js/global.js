$(document).ready(function() {
	//Validators
	$("#registerForm").submit(function(e){
		if(validateSubmit()) {
			$("registerForm").submit();
		} else {
			return false;
		}
	});

	$("#terms_placeholder").click(function(){
		if($("#terms").val() == "off") {
			$("#terms").val("on");
		} else {
			$("#terms").val("off");
		}
	});


	function validateSubmit() {
		if(validateUsername() && validatePassword() && validatePasswordCheck() && validateEmail() && validateGender() && validateTerms()) {
			return true;
		}

		return false;
	}


	//Functions
	function validateUsername() 
	{
		var result = validate.single(
			document.getElementById("login").value,
			{
				presence: true,
				format: {
					pattern: "^[0-9a-zA-Z]{5,24}$",
					message: "Username must have only letters and numbers between 5 and 24 characters"
				}
			}
		)
		
		if(result != null) {
			$("#login").css("border", "1px solid red");
			$.toast({
				heading: "Check field",
				text: result[0],
				showHideTransition: 'slide',
				icon: "warning"
			});
			return false;
		} else {
			$("#login").css("border", "1px solid #6526d3");
			return true;
		}
	}

	function validatePassword() 
	{
		var result = validate.single(
			document.getElementById("password").value,
			{
				presence: true,
				format: {
					pattern: /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\W]).{8,24}$/,
					message: "The password must be at least 8 characters, case-sensitive, containing at least one number and one special character."
				}
			}
		)
		
		if(result != null) {
			$("#password").css("border", "1px solid red");
			$.toast({
				heading: "Check field",
				text: result[0],
				showHideTransition: 'slide',
				icon: "warning"
			});
			return false;
		} else {
			$("#password").css("border", "1px solid #6526d3");
			return true;
		}
	}

	function validatePasswordCheck() 
	{
		password = document.getElementById("password").value;
		checkPassword = document.getElementById("re-password").value;
		
		if(password != checkPassword) {
			$("#re-password").css("border", "1px solid red");
			$.toast({
				heading: "Check field",
				text: "The passwords doesn't match",
				showHideTransition: 'slide',
				icon: "warning"
			});
			return false;
		} else {
			$("#re-password").css("border", "1px solid #6526d3");
			return true;
		}
	}

	function validateEmail()
	{
		var result = validate.single(
			document.getElementById("email").value,
			{
				presence: true,
				email: {
					message: "Enter a valid email address"
				}
			}
		)

		if(result != null) {
			$("#email").css("border", "1px solid red");
			$.toast({
				heading: "Check field",
				text: result[0],
				showHideTransition: 'slide',
				icon: "warning"
			});
			return false;
		} else {
			$("#email").css("border", "1px solid #6526d3");
			return true;
		}
	}

	function validateGender() 
	{
		gender = document.getElementById("gender").value;
		
		if(gender != 0 && gender != 1) {
			$("#gender").css("border", "1px solid red");
			$.toast({
				heading: "Check field",
				text: "Please select a valid gender",
				showHideTransition: 'slide',
				icon: "warning"
			});
			return false;
		} else {
			$("#gender").css("border", "1px solid #6526d3");
			return true;
		}
	}

	function validateTerms() 
	{
		if($("#terms").val() != "on") {
			$.toast({
				heading: "Check field",
				text: "You must agree to the terms of use to proceed",
				showHideTransition: 'slide',
				icon: "warning"
			});
			return false;
		} else {
			return true;
		}
	}

	//Navbar
	$(document).mouseup(function(e) 
	{
		var container = $(".navbar ul li ul");

		if (!container.is(e.target) && container.has(e.target).length === 0) 
		{
			container.slideUp(1000);
			$("#drop").removeClass("dp");;
		}
	});

	$('#drop').on("click", function() {
         if(!$('.navbar ul li ul').is(":visible"))
		 {
			$('.navbar ul li ul').slideDown(1000);
			$("#drop").addClass("dp");
		 }else{
			 $('.navbar ul li ul').slideUp(1000);
			 $("#drop").removeClass("dp");
		 }
    });
	
	//Login
	$("#login").submit(function(event) {
		
		event.preventDefault();
		var data = $(this).serialize();
	});
	
	//Donate
	$('.page-container .btn-donate li').click(function () {
		var tab_id = $(this).attr('payment');

		$('.page-container .btn-donate li').removeClass('active');
		$('.page-container .donate').css("display", "none");

		$(this).addClass('active');
		$('#' + tab_id).css("display", "block");
	});

	
	//Form requisition
	$(".ajaxform").submit(function(event)
	{
		event.preventDefault();
		var url = $(this).attr("url");
		var data = $(this).serialize();
		
		$.ajax({
			type : 'POST',
			url  : url,						
			crossDomain: true,
			data : data,
			dataType: 'json',
			beforeSend: function() {
				$("#btn_enter").addClass("disabled");
				$(".btn-confirm").addClass("disabled");
			},
			success :  function(response){
				if($("div.g-recaptcha").length > 0) {
					grecaptcha.reset();
				}
				
				if(response.code){
					if(response.code == 0) {
						$.toast({
							heading: response.title,
							text: response.message,
							showHideTransition: 'slide',
							icon: response.type
						})
						if(response.url != null)
						{
							setTimeout(function() {
								window.location.href = response.url;
							}, 3000);
						}
					} else {
						$("#btn_enter").removeClass("disabled");
						$(".btn-confirm").removeClass("disabled");
						$.toast({
							heading: response.title,
							text: response.message,
							showHideTransition: 'slide',
							icon: response.type
						})
					}
				}
				else {
					$.toast({
						heading: "Fatal error",
						text: "We are unavailable now, try again later.",
						showHideTransition: 'slide',
						icon: 'error'
					})
				}
			},
			error: function(){
				$.toast({
					heading: "Fatal error",
					text: "Could not connect to the server.",
					showHideTransition: 'slide',
					icon: 'error'
				})
			}
			
		});
	});
	
	$(".payments").submit(function(event) {
		
		event.preventDefault();
		var url = $(this).attr("url");
		var data = $(this).serialize();
		
		$.ajax({
			
			type : 'POST',
			url  : url,						
			crossDomain: true,
			data : data,
			dataType: 'json',
			success :  function(response){
				
				if(response.codigo == "1"){	
					$.toast({
						heading: response.title,
						text: response.mensagem,
						showHideTransition: 'slide',
						icon: 'warning'
					})
				}
				else if(response.codigo == "2"){	
					$.toast({
						heading: response.title,
						text: response.mensagem,
						showHideTransition: 'slide',
						icon: 'error'
					})
				}
				else {
					$.toast({
						heading: response.title,
						text: response.mensagem,
						showHideTransition: 'slide',
						icon: 'success'
					})
					if(response.url != null)
					{
						setTimeout(function() {
							window.location.href = response.url;
						}, 3000);
					}
				}
				
			}
			
		});
	});
});