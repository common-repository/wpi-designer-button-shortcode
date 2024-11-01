;(function($){
	$(document).ready(function(){
							   
		$.fn.smartButtons=function(args){
			function SmartButtons(args){
				this.$el=args.el;
				this.init();
			}
			SmartButtons.prototype.init=function(){
				var self=this;
				this.MenuLinks=this.$el.find(".wpi_menu_links");	
				this.Button=this.$el.find(".wpi_designer_button");
				this.align();
				$(window).resize(function(){
					self.align();			  
				});
				this.Button.click(function(event){
					event.preventDefault();
					if(self.MenuLinks.hasClass("wpi_open")){
						self.MenuLinks.removeClass("wpi_open").addClass("wpi_close");	
					}else{
						self.MenuLinks.removeClass("wpi_close").addClass("wpi_open");	
					}
				});
			}
			SmartButtons.prototype.align=function(){
				var smb_w=this.$el.width();
				var ml_w=this.MenuLinks.width();
				var left=0;
				var right="auto";
				var align=this.$el.css("text-align");
				if(align=="right"){
					left="auto";
					right=0;
				}else if(align=="center"){
					left=(smb_w/2)-(ml_w/2);					
				}							
				this.MenuLinks.css({"left":left, "right":right});
			}
			return this.each(function(){
				var defaults={el:$(this)};
				var args=$.extend(defaults,args);     
				new SmartButtons(args);
			});
		}
		$.fn.wpiPopup=function(args){
			function WPiPopup(options){      
			  this.init(options);
			};
			WPiPopup.prototype.init=function(options){
			  var self=this;      
			  this.el=options['element'];
			  this.popup_ID=this.el.attr("href");
			  this.popup_base=$(this.popup_ID);
			  this.popup_type="";
			  this.backup=$(".wpi_popup_backup");  
			  this.wrapper=$(".wpi_popup_wrapper");  
			  this.overlay=$(".wpi_popup_overlay");
			  this.content=$(".wpi_popup_content");
			  this.close=$(".wpi_popup_close"); 
			  this.backup.append(this.popup_base);
			  
			  this.CF7data={};
			  this.processCF7data(); //with if condition for (popup with cf7 data exists)
			  if(this.popup_base.data("popup_button_type")){
				this.popup_type="popup_button_type_"+this.popup_base.data("popup_button_type");							
			  }
			  this.el.on("click",function(event){
				self.scrollTop=$("body").scrollTop();			  	
				event.preventDefault();
				self.content.append(self.popup_base);
				self.wrapper.addClass("active"); 
				self.wrapper.addClass(self.popup_type);	
				self.overlay.addClass("active"); 
				$('html').addClass("wpi_popup_active"); 
				self.setPopup();				
			  }); 
			  this.close.on("click",function(){        
				self.closePopup();
			  }); 
			  $(document).keyup(function(e) {
					if (e.keyCode == 27) { // escape key
						self.closePopup();
					}
				});
			  this.overlay.on("mousedown",function(e){        
				$(this).addClass("click");         
			  });
			  this.overlay.on("mouseup",function(e){ 
				$(this).removeClass("click");
			  }); 
			  this.overlay.on("click",function(e){         
				self.closePopup();
			  }); 
			  this.content.on("mouseup",function(e){
				e.stopPropagation();       
			  }); 
			  $(window).resize(function(){        
				self.setPopup();
			  }).trigger("resize");
			};
			WPiPopup.prototype.closePopup=function(){             
			   this.wrapper.removeClass("active"); 
			   this.wrapper.removeClass(this.popup_type);	
			   this.overlay.removeClass("active");
			   $('html').removeClass("wpi_popup_active"); 			  		   
			   this.backup.append(this.popup_base);
			    $("body").scrollTop(this.scrollTop); 
			};
			WPiPopup.prototype.setPopup=function(){			 
			  this.w_width=$(window).width(); 
			  this.w_height=$(window).height(); 
			  this.c_width=this.wrapper.innerWidth();
			  this.c_height=this.wrapper.innerHeight();       
			  if(this.w_width< 600+60){
				this.wrapper.css({"width":this.w_width-60} );
				this.wrapper.css({"margin-left":"30px"} );
				this.wrapper.css({"left":"0px"} );
			  }else{
				this.wrapper.css({"width":"600px"} );
				this.wrapper.css({"margin-left":"50%"} );
				this.wrapper.css({"left":-(this.c_width/2)} );
			  }   
			  if(this.w_height< 500 || this.c_height+100>this.w_height){        
				this.wrapper.css({"margin-top":"20px", "margin-bottom":"20px"} );
			  }else{
				this.wrapper.css({"margin-top":"50px", "margin-bottom":"50px"} );
			  }      
			}; 
			WPiPopup.prototype.processCF7data=function(){
				var data={};
				if(!$(this.popup_ID).find(".wpi_popup_base_cf7_data").length) return;
				
				this.CF7data.holder=$(this.popup_ID).find(".wpi_popup_base_cf7_data");				
				this.CF7data.custom_fields=[]; 
				this.CF7data.custom_fields_data=[];
				
				if(this.CF7data.holder.find(".wpi_popup_custom_field_1").length){
					this.CF7data.custom_fields.push(this.CF7data.holder.find(".wpi_popup_custom_field_1").text());
					this.CF7data.custom_fields_data.push(this.CF7data.holder.find(".wpi_popup_custom_field_1_data").text());
				}
				if(this.CF7data.holder.find(".wpi_popup_custom_field_2").length){
					this.CF7data.custom_fields.push(this.CF7data.holder.find(".wpi_popup_custom_field_2").text());
					this.CF7data.custom_fields_data.push(this.CF7data.holder.find(".wpi_popup_custom_field_2_data").text());
				}
				if(this.CF7data.holder.find(".wpi_popup_custom_field_3").length){
					this.CF7data.custom_fields.push(this.CF7data.holder.find(".wpi_popup_custom_field_3").text());
					this.CF7data.custom_fields_data.push(this.CF7data.holder.find(".wpi_popup_custom_field_3_data").text());
				}
				if($(this.popup_ID).find(".wpcf7").length){
					var self=this;
					this.CF7data.form=$(this.popup_ID).find(".wpcf7");						
					$.each( this.CF7data.custom_fields, function( index, value ){
						if(self.CF7data.form.find('input[name="'+value+'"]').length){
							var field=self.CF7data.form.find('input[name="'+value+'"]');
							var val=self.CF7data.custom_fields_data[index];
							field.val(val);							
						}						
					});
				}
						
				
			};
			 if($(".wpi_popup_button").length){         
				var popup_wrapper=$('<div class="wpi_popup_wrapper"></div>');
				var popup_close=$('<div class="wpi_popup_close"><i class="fa fa-times"></i></div>');
				var popup_content=$('<div class="wpi_popup_content"></div>');
				var popup_overlay=$('<div class="wpi_popup_overlay"></div>');
				var popup_backup=$('<div class="wpi_popup_backup"></div>');
				popup_wrapper.append(popup_close);
				popup_wrapper.append(popup_content);        
				$("body").append(popup_overlay).append(popup_wrapper).append(popup_backup);            
			 }
			 return this.each(function(){      
			   var defaults={element:$(this), wrapper:"wpi_popup_wrapper"};
			   var options=$.extend(defaults, args);
			   new WPiPopup(options);
			 });
		};
		  
		
		if ($(".wpi_popup_button").length) {
			$(".wpi_popup_button").wpiPopup();		
		}
		if ($(".wpi_slide_heading").length) {
			var headingTicker=$(".wpi_slide_heading").wpiTicker({animate:"play"});			
		}
		if ($(".wpi_share_buttons").length) {
			$(".wpi_share_buttons").each(function(){
				$(this).find("a").each(function(){
					$(this).click(function(){
						var href=$(this).attr("href");	
						window.open(href, "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=200, left=200, width=400, height=400");
					});
				});				
			});		 
		}
		if ($(".wpi_smart_buttons").length) {
			$(".wpi_smart_buttons").smartButtons({});
		}
		if($(".wpi_button_menu_container").length){
			$(".wpi_button_menu_container").each(function(){
				var el=$(this);
				var a={
					init:function(){	
						var self=this;
						if(el.find(".menu").length){	
							this.menu=el.find(".menu");												
						}						
						this.button=el.find(".wpi_button_menu");											
						this.button.on("click",function(e){							
							e.preventDefault();
							if(el.hasClass("wpi_button_menu_container_open")){
								el.removeClass("wpi_button_menu_container_open");
								self.setMenu(0);							
							}else{
								el.addClass("wpi_button_menu_container_open");	
								self.setMenu(1);						
							}							
												
						});	
										
					},
					setMenu:function(a){
						if(this.menu){
							if(a==1){
								this.menu.css("width", this.menu.outerWidth()+5);	
							}else{
								this.menu.css("width", "auto");	
							}														
							this.menu.css("margin-bottom",this.button.outerHeight()+10);
						}
					},
				};
				a.init();				
			});			
		}	
		
	});
})(jQuery);