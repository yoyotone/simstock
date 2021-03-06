var vue = new Vue({
    el: "#login",
    data: {
        username: '',
        password: '',
        remember: 0, 
        url: api_host + "/index/index/autoLogin" 
    },
    methods: {
        doLogin: function() {
            if(this.check()){
               $.post(
                    this.url, {
                        login_email:this.username,
                        login_password:this.password,
                        login_remember:this.remember
                    },
                    function(msg) {
                        if(msg.status == "success"){   
                            $.getJSON(api_host + 'index/base/syncLogin',{
                                token:msg.data.token
                            },function(reg){
                                if(reg.status == 'success'){
                                    $.cookie('login_eamil',reg.data.login_email,{
                                        path:'/',
                                        expiress:7,
                                        domin:".local.com"
                                    });
                                    $.cookie('login_password',reg.data.password,{
                                        path:'/',
                                        expiress:7,
                                        domin:".local.com"
                                    });
                                    setTimeout(function(){
                                        location.reload();
                                    },1000);
                                }
                            });
                            
                        }else{
                            modal.imitateAlert(msg.data);
                        }
                    },
                    'json'
                ); 
            }
            
        },
        check:function(){
            if(this.username == ''){
                modal.imitateAlert("用户名不能为空");
                return false;
            }else if(this.password == ''){
                modal.imitateAlert("密码不能为空");
                return false;
            }else{
                return true;
            }
        }
    }
});
