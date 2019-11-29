<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';


//session check 
session_start();
//token sessionのチェック
if(!isset($_SESSION['csrf_token'])){

    die();

}else{

    $jsonString = file_get_contents('php://input');
    $data = json_decode($jsonString ,true) ;

    //session と post requestのtoken値が一致するかどうかのチェック
    if($data['token'] === $_SESSION['csrf_token'] ){

           
        //もしcode変数がある場合emailにpass code送信する
        if(isset($data['code'])){

            $email = $data['email'];

            //passcode をランダムで作成
            $toke_byte = openssl_random_pseudo_bytes(2);
            $pass_code = bin2hex($toke_byte);

            
            
           


            // Load Composer's autoloader
            require 'vendor/autoload.php';

            // Instantiation and passing `true` enables exceptions
            $mail = new PHPMailer(true);

            try {
                //Server settings
                mb_language("japanese");
                mb_internal_encoding("UTF-8");
                // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
                $mail->isSMTP();                                            // Send using SMTP
                $mail->Host       = 'smpt.doamin.com';                    // Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                $mail->Username   = 'xxx@domain.com';                     // SMTP username
                $mail->Password   = 'xxxx';                               // SMTP password
                // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;                                    // TCP port to connect to

                //Recipients
                $mail->setFrom('xxx@domain.com','your server name');
                $mail->addAddress($email,'pass code');     // Add a recipient
                $mail->addReplyTo('xxx@domain.com','your server name');
                // $mail->addCC('cc@example.com');
                // $mail->addBCC('bcc@example.com');

                // Attachments
                // $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
                // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

                // Content
                $mail->isHTML(true);
                $mail->CharSet = 'utf-8';                                  
                $sub = '認証コード';
                $mail->Subject = $sub;                           // Set email format to HTML
                $mail->Body    = '<b>お問い合わせありがとうございます！ <br></b>
                <br>
                <br>

                        以下の４桁のパスコードをフォームに入力してください。


                       <h1> '.$pass_code.'</h1>

                <p><span style="color:red">注意: </span>パスコードの有効期限は3分です</p>
                <br>
                        ※このメールはシステムからの自動返信です <br>

                        ※このメールには返信しないで下さい。 <br>
                <br>
                <br>
                <br>';

                    if($mail->send()){

                        //cookie にpasscodeを入れて時間制限を設定する
                        $expire = time() + 3 * 60;

                        //パスコードを送信完了したら、cookieに送信完了変数を入れる
                        setcookie("contact_passcode",$pass_code ,$expire);
                        setcookie("contact_passcode_email_address",$email ,$expire);
            

                        echo "success";
                        die();
                    }else{
                        echo "パスコードが送信できませんでした、暫く経ってからやり直してください！";
                        die();
                    }

            }catch(Exception $e){

                echo "無効なメールアドレスです";
                die();

            }

            die();


        }else{ 
            //もしcode変数がない場合普段のエントリーメールの送信を実行する



        //input  post date 
        $name = $data['name'];
        $name2 = $data['name2'];
        $email = $data['email'];
        $contact_info = $data['contact_info'];


        // Load Composer's autoloader
        require 'vendor/autoload.php';

        // Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            mb_language("japanese");
            mb_internal_encoding("UTF-8");
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smpt.doamin.com';                    // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = 'xxx@domain.com';                     // SMTP username
            $mail->Password   = 'xxxx';                               // SMTP password
            // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;                                    // TCP port to connect to

            

            //Recipients
            $mail->setFrom('xxx@domain.com','your server name');
            $mail->addAddress($email,'pass code');     // Add a recipient
            $mail->addReplyTo('xxx@domain.com','your server name');
            // $mail->addCC('cc@example.com');
            // $mail->addBCC('bcc@example.com');

            // Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

            // Content
            $mail->isHTML(true);
            $mail->CharSet = 'utf-8';                                  
            $sub = 'お問い合わせ、ありがとうございます。';
            $mail->Subject = $sub;                           // Set email format to HTML
            $mail->Body    = '<b>お問い合わせ、ありがとうございます！ <br></b>
            <br>
            <br>
                    改めて、担当よりご連絡をさせていただきますので、今しばらくお待ちください！<br>
                    <br>
                    <br>
                    ※このメールはシステムからの自動返信です <br>
                    <br>
                    なお、営業時間は平日10時〜18時となっております。<br>
                    時間外のお問い合わせは翌営業日にご連絡差し上げます。<br>
            <br>
                    ご理解・ご了承の程よろしくお願い致します。<br>
                    <br>
                    ';

            if($mail->send()){


                //送信完了したら、cookieに送信完了変数を入れる
                setcookie("contact_sent",true,time() + 140 * 60);

                echo 'success';

                //自分に俺通知のメールを送信する
                $mail->ClearAddresses();
                $mail->Subject = 'お問い合わせがあります';

                $mail->AddAddress('xx@domain.com');
                $mail->Body = ' 
                
                        お問い合わせがあります。<br>
                <br>


                     <p> [名前]:'.$name.' </p>
                     <p> [名前(読み仮名)]:'.$name2.' </p>
                     <p> [メールアドレス]:'.$email.'</p>
                     <p> [お問い合わせ内容]: '.$contact_info.'</p>
                        <br>
                        <br>
                        <br>
                        
                        以上。
                        
                        ';


                $mail->Send();

                exit();

            }else{
                echo "エラが発生しました、暫く経ってからやり直してください";
                exit();
            }

        } catch (Exception $e) {
            echo '無効なメールアドレスです、もう一度チェックしてください。';
            // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            die();
        }
    }//tokenがあり、データ送信完了したelse

    // tokenがない場合のelse
    }else{
        echo "attacked";
    }

}


?>
