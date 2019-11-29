<?php 

session_start();
// ログインした状態と同等にするためセッションを開始します

// 暗号学的的に安全なランダムなバイナリを生成し、それを16進数に変換することでASCII文字列に変換します
  $toke_byte = openssl_random_pseudo_bytes(16);
  $csrf_token = bin2hex($toke_byte);
  // 生成したトークンをセッションに保存します
  $_SESSION['csrf_token'] = $csrf_token;

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://unpkg.com/vue-cookies@1.5.13/vue-cookies.js"></script>
    <link rel="stylesheet" href="css/style.css">

    <!-- bootstrap css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css">

    <!-- vue js -->
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.auto.min.js"></script> 

    <!-- vue cookie -->
    <script src="https://unpkg.com/vue-cookies@1.5.12/vue-cookies.js"></script>

    <!-- bootstrap js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.15.0/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <title>vue-form</title>
</head>

<style type="text/css">
    /*初期表示時にテンプレートが一瞬表示されてしまうのを防ぐ*/
    [v-cloak] {
        display: none;
    }
</style>
<body>
    <section id="contact" class="content6">
    <div class="contact-head-content">
    </div>
        <div class="wrapper">
            <div class="back-to-home-btn-wrapper">
                <a href="../" class="back-to-home-btn">
                  <span class="text-muted">ホームに戻る</span>
                </a> 
            </div>
            <div class="sent-complate" v-if="contact_sent" v-cloak>
                <h1 class="text-center text-success">お問い合わせ、ありがとうございます。</h1>
                <p>{{ param.email }}に弊社からお問い合わせ完了のメールが届いています、メールが見つからない場合は迷惑メールをチェックするか、メールアドレスをご確認の上もう一度お試しください。</p>
                <div class="text-center">
                    <button class="btn entry-btn w-50 text-light re-entry-btn" @click="reset">
                       <span>もう一度お問い合わせする</span> 
                    </button>
                </div>
            </div>
            <div class="" v-if="!contact_sent">
                <div class="section-title">
                    <h1>お問い合わせ</h1>
                </div>

                <form  @submit = "checkValidate" method="post" class="" >
                <div class="form-group">
                    <label for="exampleInputEmail1"><i class="text-danger">&lowast;</i>お名前</label>
                    <input v-model="param.name" type="text" class="form-control" 
                    v-bind:class="{'is-invalid':params_err.name && has_submited, 'is-valid':!params_err.name && has_submited}" 
                    @keyup="inputChanged('name', param.name)"
                    v-bind:disabled="input_checker"
                    id="exampleInputEmail1"
                    aria-describedby="emailHelp"
                    placeholder="お名前を入力してください" >
                </div>
                <div class="form-group has-success has-feedback">
                    <label for="exampleInputPassword1"><i class="text-danger">&lowast;</i> お名前(よみがな)</label>
                    <input v-model="param.name2"
                    v-bind:class="{'is-invalid':params_err.name2 && has_submited, 'is-valid':!params_err.name2 && has_submited}" 
                    @keyup="inputChanged('name2', param.name2)"
                    v-bind:disabled="input_checker"
                    type="text" class="form-control" id="exampleInputPassword1" placeholder="よみがなを入力してください">
                </div>
                <label class=""> <i class="text-danger">&lowast;</i>メールアドレス</label>
                <div class="form-row form-group">
                    <div class="col-lg-7 col-12">
                        <input v-model="param.email" 
                        v-bind:class="{'is-invalid':params_err.email && has_submited, 'is-valid':!params_err.email && has_submited}" 
                        @keyup="inputChanged('email', param.email)"
                        v-bind:disabled="input_checker"
                        type="email" class="form-control" id="exampleInputPassword1" placeholder="メールアドレスを入力してください" required>
                    </div>
                    <div class="col-6 col-lg-2 col-xl-2 sm-my-2 sm-md-2">
                        <button v-on:click="sendPass"
                        v-bind:disabled="input_checker"
                        type="button"
                        class="btn btn-secondary form-cotrol w-100 btn-disabled">
                            <span>認証</span>  
                            <div v-show="spinner" class="spinner-border spinner-border-sm" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </button>
                    </div>
                    <div class="col-col-3 col-6 col-xl-3 sm-my-2 sm-md-2">
                        <input v-model="input_passcode"
                        v-bind:class="{'is-invalid':params_err.input_passcode && has_submited, 'is-valid':!params_err.input_passcode && has_submited}" 
                        @keyup="inputChanged('input_passcode', input_passcode)"
                        v-bind:disabled="input_checker"
                        type="text" class="form-control" id="exampleInputPassword1" placeholder="パスコードを入力してください">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="exampleFormControlTextarea1">
                        <i class="text-danger">&lowast;</i>
                        お問い合わせ内容
                    </label>
                    <textarea v-model="param.contact_info" 
                    v-bind:class="{'is-invalid':params_err.contact_info && has_submited, 'is-valid':!params_err.contact_info && has_submited}" 
                    @keyup="inputChanged('contact_info', param.contact_info)"
                    v-bind:disabled="input_checker"
                    class="form-control" id="exampleFormControlTextarea1" rows="5" placeholder="お問い合わせ内容を入力してくださ"></textarea>
                </div>
                <transition name="slide-fade">
                    <div :class="{'error-alert':alert_error}" class="alert" v-if="msg">
                        <p class="text-justify"　v-html="alertMsg">
                        </p>
                    </div>
                </transition>
                <button type="submit"  v-bind:disabled="btnDisabled" class="btn btn-primary w-100 entry-btn">
                    <span>{{ entry_btn_text }}</span>    
                    <div v-show="spinner" class="spinner-border text-light" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </button>
                <button type="button" @click="edit()" v-if="edit_btn" class="btn btn-light bg-lightGray w-100 text-center edit-btn my-3">
                    <i class="fas fa-arrow-circle-left text-dark fa-1x"></i>   
                    <span>編集に戻る</span> 
                </button>

                </form>
            </div>

        </div>
        
    </section>
    <script>

    </script>

        <script>
        
        
        const app = new Vue({
            el: '#contact',
            data: {
                current: 1,
                param:{
                    name:'',
                    name2:'',
                    email:'',
                    contact_info:'',
                    token:'<?=   $csrf_token  ?>',
                },
                params_err:{
                    name:true,
                    name2:true,
                    email:true,
                    event:true,
                    input_passcode:true,
                    contact_info:true,

                },
                errors:{
                    empty:null,
                },
                notValid:false,
                input_passcode : '',

                contact_passcode:null,
                contact_passcode__email:null,
                alertMsg:null,
                spinner:false,
                btnDisabled:false,
                has_submited: false,
                input_checker:false,
                entry_btn_text:"次へ",
                edit_btn: false,
                contact_sent:false,
                alert_error:false,
                msg:false,
            },
            mounted() {
                this.param.email =$cookies.get('sent_email');

                this.contact_sent =$cookies.get('contact_sent');

            },
            watch:{
                
            },
            methods: {

                reset_pass:function(){

                    $cookies.remove('contact_passcode');
                    $cookies.remove('contact_passcode_email_address');
                    $cookies.remove('contact_sent');

                    this.btnDisabled = false;
                    this.passcode = null;
                    this.alertMsg = null;

                },
                remove_error_msg:function(){

                    var errors = this.errors;

                    Object.keys(this.errors).forEach(function (key) {

                        errors[key] = null;

                    });
                    this.errors = errors;
                },

                //編集ボタン押した時の処理
                edit:function(){
                    this.input_checker = false;
                    this.edit_btn = false;
                    this.entry_btn_text = "次へ";
                    this.alert_error = false;
                    this.msg = false;

                },
                reset:function(){

                    $cookies.remove("contact_sent","/LandingPage/contact");
                    
                    this.contact_sent = false;
                    this.spinner = false;
                    this.input_checker = false;
                    this.btnDisabled = false;
                    this.entry_btn_text = "次へ";
                    this.msg = false;

                    // 現在表示されているページをリロードする
                    location.reload();

                },
                inputChanged:function(position, text) {
                    if (this.has_submited) {
                        switch(position) {
                            case 'name':
                                if (text == '') {
                                    this.params_err.name = true;
                                } else {
                                    this.params_err.name = false;

                                }
                            break;
                            case 'name2':
                                if (text == '') {
                                    this.params_err.name2 = true;
                                } else {
                                    this.params_err.name2 = false;

                                }
                            break;
                            case 'contact_info':
                                if (text == '') {
                                    this.params_err.contact_info = true;
                                } else {
                                    this.params_err.contact_info = false;

                                }
                            break;
                            case 'email':
                                if (text == '') {
                                    this.params_err.email = true;
                                } else {
                                    this.params_err.email = false;
                                }
                            break;
                            case 'event':
                                if (text == '') {
                                    this.params_err.event = true;
                                } else {
                                    this.params_err.event = false;
                                }
                            break;
                            case 'input_passcode':
                                if (text == '') {
                                    this.params_err.input_passcode = true;
                                } else {
                                    this.params_err.input_passcode = false;
                                }
                            break;

                        }
                    }
                },
                checkValidate: function (e){

                    // e.preventDefault();

                    this.has_submited = true;

                    e.preventDefault();
                    

                    if( this.param.name == '' || this.param.name2 == '' || this.param.email == '' || this.param.contact_info == '' || this.input_passcode == '' ){


                        if (this.param.name != '') {
                            this.params_err.name = false;
                        }
                        if (this.param.name2 != '') {
                            this.params_err.name2 = false;
                        }
                        if (this.param.contact_info != '') {
                            this.params_err.contact_info = false;
                        }
                        if (this.param.email != '') {
                            this.params_err.email = false;
                        }
                        if (this.param.event != '') {
                            this.params_err.event = false;
                        }
                        if (this.input_passcode != '') {
                            this.params_err.input_passcode = false;
                        }


                    }else{
                        //全部項目が入力された場合

                        this.params_err.name = false;
                        this.params_err.name2 = false;
                        this.params_err.contact_info = false;
                        this.params_err.email = false;
                        this.params_err.event = false;
                        this.params_err.input_passcode = false;

                        //inputをdisabledにして確認させ、編集に戻るボタンを表示させる
                        this.input_checker = true;
                        //もし編集に戻るボタンが既に表示されている場合
                        if(this.edit_btn){

                            //パスコードをチェックさせる
                            if(this.input_passcode !== this.contact_passcode || this.contact_passcode_email !== this.param.email){
                                this.alert_error = true;
                                this.alertMsg = `正しい認証コードを入力してください！<br>
                                    <small>*パスコードの期限が3分までです*</small><br>
                                    <small>*既に無効になっている場合もございますので「編集に戻る」ボタンを押してからもう一度認証コード発行してください*</small>`;
                            }else{
                                this.edit_btn = false;
                                this.postData();
                            }

                        }else{

                            this.edit_btn = true;
                            this.msg = true;
                            this.entry_btn_text = "確認して送信";
                            this.alertMsg = `申し込み内容を間違いないかチェックして、「確認して送信」ボタンを押してください。<br>
                            編集したい場合は「編集に戻る」ボタンを押してください。`;

                        }

                    }
                },

                // ---------------------------------------------------------------------------------//
                //メールアドレス認証用のpassを送る
                sendPass:function(){

                    this.btnDisabled = true;
                    this.spinner = true;
                    //表示されているpasscode errorを消す
                    // this.notValid = false;


                    const sendPassApi = axios.create({
                        headers:{
                            'Content-type':'aplication/json',
                        }
                    })
                    sendPassApi.post('mail.php',JSON.stringify(
                        {
                            email : this.param.email,
                            token : this.param.token,
                            code : true,
                        }
                    ))
                        .then((response)=>{

                            if( response.data == 'success'){

                                this.alertMsg = "パスコードを送信しました。<br><small> ※メールが見つからない場合迷惑メールをチェックして下さい。</small>"
                                this.msg = true;
                                this.spinner = false;
                                this.contact_passcode = $cookies.get('contact_passcode');
                                this.contact_passcode_email = $cookies.get('contact_passcode_email_address');
                                this.btnDisabled = false;
                                //時間指定でcookie削除
                                setTimeout(this.reset_pass,180000);

                            }else{
                                // this.notValid = true;
                                this.errors.empty = null;
                                this.errors.email_error = response.data;
                                this.btnDisabled = false;
                                this.spinner = false;
                            }
                        }).catch((error)=>{
                        console.log(error);
                    });
                },
                //全データをajaxでphpに送信
                postData:function(){

                    //前の段階で表示されてたエラー消す
                    this.remove_error_msg();

                    this.btnDisabled = true;
                    this.spinner = true;

                    const axiosApi = axios.create({
                        headers : {
                            'Content-type':'aplication/json',
                        }
                    })
                    axiosApi.post('mail.php',JSON.stringify(this.param))
                        .then((response) => {
                            if(response.data == 'success'){
                                //koocie をゲット
                                this.spinner = false;
                                this.contact_sent = true;
                                // $cookies.get("contact_sent");
                                // $cookies.get("contact_sent_email");
                                window.scrollTo(0,0);
                            }else{
                                this.msg = true;
                                this.alert_error = true;
                                this.alertMsg = response.data;
                            }

                        })
                        .catch((error) => {
                            console.log(error)
                        })
                },

            },
        })
        </script>

</body>
</html>
    