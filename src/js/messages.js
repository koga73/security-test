(function(){
	new Vue({
		el:"#frmMessages",

		data:{
			submitted:false,

			model:{
				message:""
			}
		},

		computed:{
			incomplete:function(){
				return !(this.model.message.length);
			}
		},

		mounted:function(){
			this.$refs.txtMessage.focus();
		},

		methods:{
			//Add error class if input is invalid
            handler_input_invalid:function(evt){
                evt.target.classList.add("error");
            },

            //Run HTML5 input validation on focus out
            handler_input_blur:function(evt){
                var value = evt.target.value || "";
                if (value.length){
                    if (evt.target.checkValidity()){
                        evt.target.classList.remove("error");
                    }
                }
			},
			
			handler_form_submit:function(evt){
				if (this.submitted){
					evt.preventDefault();
                    return false;
				}
				//Validate form
                var form = this.$refs.form;
                if (!form.checkValidity()){
					form.reportValidity();
					evt.preventDefault();
                    return false;
				}
				
				console.log("SUBMIT!");
				this.submitted = true;
			}
		}
	});
})();