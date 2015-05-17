              /*
              everything that's toggleable hide/show is hidden by the default css.  
              Then the 1st display js (if...checked) exposes what it finds.  
              Thereafter the click functions take over.
              */
              
              // from http://api.jquery.com/slideToggle/

                        jQuery.noConflict();

                        jQuery(document).ready(function($) {                                                        
                            //Candidate Population
                            $("#groupChoice").click(function(){
                                $("#vtmin-pop-in-cntl").show("slow");
                                $("#groupChoice-chosen").show("slow");
                                $("#cartChoice-chosen").hide("slow");
                                $("#inpop-varProdID-cntl").hide("slow");
                                $("#varChoice-chosen").hide("slow");
                                $("#singleChoice-chosen").hide("slow");
                                $("#singleChoice-span").hide("slow");                                
                               // return false;    this is commented, as it prevents the radio buttons from registering
                            });  
                            //**************************************************************************************************
                            //   'if...checked' only active on 1st iteration of screen, thereafter the click functions take over.   
                            //**************************************************************************************************
                           if($('#groupChoice').prop('checked')) { 
                                $("#vtmin-pop-in-cntl").show("slow");
                                $("#groupChoice-chosen").show("slow");
                                $("#cartChoice-chosen").hide();
                                $("#inpop-varProdID-cntl").hide();
                                $("#varChoice-chosen").hide();
                                $("#singleChoice-chosen").hide();
                                $("#singleChoice-span").hide();                                
                                }
                                
                            $("#cartChoice").click(function(){
                                $("#vtmin-pop-in-cntl").hide("slow");
                                $("#groupChoice-chosen").hide("slow");
                                $("#inpop-varProdID-cntl").hide("slow");
                                $("#varChoice-chosen").hide("slow");
                                $("#cartChoice-chosen").show("slow"); 
                                $("#singleChoice-chosen").hide("slow");
                                $("#singleChoice-span").hide("slow");                              
                            });
                             if($('#cartChoice').prop('checked')) {
                                $("#vtmin-pop-in-cntl").hide();
                                $("#groupChoice-chosen").hide();
                                $("#cartChoice-chosen").show("slow");
                                $("#inpop-varProdID-cntl").hide();
                                $("#varChoice-chosen").hide();
                                $("#singleChoice-chosen").hide();
                                $("#singleChoice-span").hide();                                
                                } 
                                 
                            $("#varChoice").click(function(){
                                $("#vtmin-pop-in-cntl").hide("slow");
                                $("#groupChoice-chosen").hide("slow");
                                $("#inpop-varProdID-cntl").show("slow");
                                $("#varChoice-chosen").show("slow");
                                $("#cartChoice-chosen").hide("slow"); 
                                $("#singleChoice-chosen").hide("slow");
                                $("#singleChoice-span").hide("slow");                              
                            });
                             if($('#varChoice').prop('checked')) {
                                $("#vtmin-pop-in-cntl").hide();
                                $("#groupChoice-chosen").hide();
                                $("#cartChoice-chosen").hide();
                                $("#inpop-varProdID-cntl").show("slow");
                                $("#varChoice-chosen").show("slow");
                                $("#singleChoice-chosen").hide();
                                $("#singleChoice-span").hide(); 
                                }
                                    
                            $("#singleChoice").click(function(){
                                $("#vtmin-pop-in-cntl").hide("slow");
                                $("#groupChoice-chosen").hide("slow");
                                $("#cartChoice-chosen").hide("slow"); 
                                $("#inpop-varProdID-cntl").hide("slow");
                                $("#varChoice-chosen").hide("slow");
                                $("#singleChoice-chosen").show("slow");
                                $("#singleChoice-span").show("slow");                             
                            });
                             if($('#singleChoice').prop('checked')) {
                                $("#vtmin-pop-in-cntl").hide();
                                $("#groupChoice-chosen").hide();
                                $("#cartChoice-chosen").hide();
                                $("#inpop-varProdID-cntl").hide();
                                $("#varChoice-chosen").hide();
                                $("#singleChoice-chosen").show("slow");
                                $("#singleChoice-span").show("slow");                                 
                                }  
                                
                            //Population Handling Specifics
                            $("#allChoice").click(function(){
                                $("#allChoice-chosen").show("slow");
                                $("#anyChoice-chosen").hide("slow");
                                $("#anyChoice-span").hide("slow"); 
                                $("#eachChoice-chosen").hide("slow");                                         
                            });
                            if($('#allChoice').prop('checked')) {
                                $("#allChoice-chosen").show("slow");
                                $("#anyChoice-chosen").hide();
                                $("#anyChoice-span").hide(); 
                                $("#eachChoice-chosen").hide();                              
                                }
                                
                            $("#anyChoice").click(function(){
                                $("#allChoice-chosen").hide("slow");
                                $("#anyChoice-chosen").show("slow");
                                $("#anyChoice-span").show("slow"); 
                                $("#eachChoice-chosen").hide("slow");
                            });
                            if($('#anyChoice').prop('checked')) {
                                $("#allChoice-chosen").hide();
                                $("#anyChoice-chosen").show("slow");
                                $("#anyChoice-span").show("slow"); 
                                $("#eachChoice-chosen").hide();                                
                                }
                                 
                            $("#eachChoice").click(function(){
                                $("#allChoice-chosen").hide("slow");
                                $("#anyChoice-chosen").hide("slow");
                                $("#anyChoice-span").hide("slow"); 
                                $("#eachChoice-chosen").show("slow");
                            });
                            if($('#eachChoice').prop('checked')) {
                                $("#allChoice-chosen").hide();
                                $("#anyChoice-chosen").hide();
                                $("#anyChoice-span").hide(); 
                                $("#eachChoice-chosen").show("slow");                                
                                }
                                
                            $("#qtySelected").click(function(){
                                $("#qtyChoice-chosen").show("slow");
                                $("#amtChoice-chosen").hide("slow");                              
                            });
                            if($('#qtySelected').prop('checked')) {
                                $("#qtyChoice-chosen").show("slow");
                                $("#amtChoice-chosen").hide();                             
                                }
                               
                            $("#amtSelected").click(function(){
                                $("#amtChoice-chosen").show("slow");
                                $("#qtyChoice-chosen").hide("slow");                           
                            });
                            if($('#amtSelected').prop('checked')) {
                                $("#amtChoice-chosen").show("slow");
                                $("#qtyChoice-chosen").hide();
                                }


                             //**************************************************************************************************
                            //   Ajax variations on Button click
                            //**************************************************************************************************                            
                            $("#ajaxVariationIn").click(function(){
                                //turn on loader animation
                                jQuery('div.inpopVar-loading-animation').css('visibility', 'visible');
                                
                                //hide slowly, then clean out existing variations/messages
                                //don't need the inVariationsArea statement, following statement is sufficient.
                                //$('div#inVariationsArea').fadeOut(300, function(){ $(this).remove();});
                                $('div#variations-in').hide("slow");
                                
                                var VarProdIDin = $('#inVarProdID').val();  //parent product ID from screen
                                                                                         
                                jQuery.ajax({
                                   type : "post",
                                   dataType : "html",
                                   url : variationsInAjax.ajaxurl,  
                                   data :  {action: "vtmin_ajax_load_variations", inVarProdID: VarProdIDin } ,
                                   //                                             inVarProdID = name referenced in PHP => refers to this variable declaration, not the original html element.
                                   success: function(response) {                                        
                                        //load the html output into #variations and show slowly
                                        $('div#variations-in').html(response).show("slow");
                                        //turn off loader animation
                                        jQuery('div.inpopVar-loading-animation').css('visibility', 'hidden');
                                    }
                                }) ;  
        
                             });   
                            //**************     
                            //  end Ajax
                            //**************
                                                        
                            //toggle "more info" areas
                            $("#pop-in-more").click(function(){
                                $("#pop-in-descrip").toggle("slow");                           
                            });
                            $("#inpopDescrip-more").click(function(){
                                $("#inpopDescrip-descrip").toggle("slow");                           
                            });
                            $("#inpop-varProdID-more").click(function(){
                                $("#inpop-varProdID-descrip").toggle("slow");                           
                            });

                           
                           //v1.08 begin 
                                 
                                 //If the saved msg = the default, user hasn't entered a msg, make it italic!
                                 if ($("#cust-msg-text").val() == $("#fullMsg").val() ) {
                                   jQuery('#cust-msg-text').css('color', '#666 !important').css("font-style","italic");                                                     
                                 };
                                 if ($("#cust-msg-text").val() <= ' ' ) {
                                      var elem = document.getElementById("cust-msg-text");
                                      elem.value = $("#fullMsg").val();//hidden field with lit
                                      jQuery('#cust-msg-text').css('color', '#666 !important').css("font-style","italic");                                                   
                                 };
    
               
                              	// input on focus  FUNCTION - REMOVE msg so they can type
                            		jQuery("#cust-msg-text[type=text]").focus(function() {
        
                                  var id = jQuery(this).attr('id'); 
                                  if (id == 'cust-msg-text') {
                            				if (this.value === $("#fullMsg").val()) {
                                      this.value = '';
                                    }
                            			}                         
                            			//jQuery(this).removeClass('blur');
                            			//return css to normal!!
                                  jQuery(this).css("color","#000").css("font-style","normal");
                            		});
                               
                                
                                //FUNCTION - put msg back if nothing is there!!!
                            		jQuery("#cust-msg-text[type=text]").blur(function() {                    				
                                  var id = jQuery(this).attr('id');
                            			if (id == 'cust-msg-text') {
                            				var default_value = $("#fullMsg").val();
                            			} 
                            			if(this.value === '') {
                            				this.value = default_value;
                            			//return css to normal!!
                                  jQuery(this).css("color","#666666").css("font-style","italic");
                            			}                    			
                            		});          
                            
                           //v1.08 end


                        }); //end ready function 
