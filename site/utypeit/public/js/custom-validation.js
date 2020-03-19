var requiredError = 'This field is required!';
var emailError = "Email is not Valid!";
var TERMS_ERROR = "Please select the checkbox to agree our Terms & Conditions!";
var PercentError = "% should be less than OR equal to 100."
var decimalError = "Value should not be more than 2 decimal places."

function customValidate(formName)
	{
		var i = 0;	
		$(".custom-error").remove(); //remove all errors when submit the button
		
		$("form[name="+formName+"] :input[data-valid]").each(function(){
			var dataValidation = $(this).attr('data-valid');

			var splitDataValidation = dataValidation.split(' ');
			
			var j = 0; //for serail wise errors shown	
			if($.inArray("required", splitDataValidation) !== -1) //for required
				{
					if( !$(this).val() ) 
						{
							if(formName == "order")
								{
									$(".tab_1").trigger('click');
								}
							
							i++;
							j++;
							$(this).after(errorDisplay(requiredError));  
						}
				}
			if(j <= 0)
				{		
					if($.inArray("email", splitDataValidation) !== -1) //for email
						{
							if(!validateEmail($(this).val())) 
								{
									i++;
									$(this).after(errorDisplay(emailError));  
								}
						}
				}			
		});
		
		if(i > 0)
			{
				return false;
			}	
		else
			{
				$("form[name="+formName+"]").submit();
				return true;	
			}	
		
	}
	
function errorDisplay(error) {
	return "<span class='custom-error'>"+error+"</span>";
}