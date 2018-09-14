(function(){
	new Vue({
		el:"#frmRegister",

		data:{
			submitted:false,

			model:{
				user:"",
				pass:"",
				passConfirm:""
			}
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

			handler_pass_input:function(evt){
				if (!this.model.passConfirm.length){
					return;
				}
				if (this.model.pass !== this.model.passConfirm){
					txtPassConfirm.setCustomValidity("Passwords do not match");
				} else {
					txtPassConfirm.setCustomValidity("");
				}
			},
			
			handler_frmRegister_submit:function(evt){
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