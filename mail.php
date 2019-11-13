<?php
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

$graphql = '{
    inbox(namespace: "aaaa" tag: "bbbb" limit:1) {
        result message count emails {
            id
            tag
            timestamp
            subject
            text
            from from_parsed {
                address name
            }
            to to_parsed {
                address name
            }
        }
    }
}';

/* 方法1,使用远端API调用,本地无法直接访问邮局时推荐,需要远端部署程序. */
function sc_send($body)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://service.ap-hongkong.apigateway.myqcloud.com/xxx');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array('p' => 'xxxxx','c' => $body)));
    var_dump(curl_exec($curl));
    var_dump(curl_errno($curl));
    curl_close($curl);
}

/* 方法2,如果你的本地支持SMTP,就使用这个方法. */
function sc_smtp($body){
    $mail = new PHPMailer(true);
                
    $mail->SMTPDebug = SMTP::DEBUG_OFF;
    $mail->isSMTP();
    $mail->CharSet = 'UTF-8';
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = '10000@qq.com';
    $mail->Password = 'bfismyeebjqmcjib'; /* QQ邮箱授权码,发短信获取. */
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('10000@qq.com');
    $mail->addAddress('10000@qq.com');
    $mail->AddReplyTo('aaaa.bbbb@inbox.testmail.app');

    $mail->isHTML(false);
    $mail->Subject = "运行结果";
    $mail->Body = $body;
 
    $mail->send();
}

$prev_id = '';

while(true){
    $postfile = json_encode(array('query' => $graphql));

    $curl = curl_init();
    
    curl_setopt($curl, CURLOPT_URL, 'https://api.testmail.app/api/graphql');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postfile);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Bearer xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx", "Content-Type: application/json"));
    
    $response = json_decode(curl_exec($curl),true);
    $err = curl_error($curl);
    if(isset($response['data']['inbox']['emails'][0]['text'])){
        if(strcmp($prev_id,$response['data']['inbox']['emails'][0]['id']) != 0){
            $prev_id = $response['data']['inbox']['emails'][0]['id'];
            $cmd = explode(PHP_EOL,$response['data']['inbox']['emails'][0]['text'])[0];
            unset($output);
            unset($status);
            echo '['.$prev_id.'] => ['.$cmd.']'.PHP_EOL;
            exec($cmd,$output,$status);
            $cmd_result = '';
            foreach($output as $key => $result){
                $cmd_result .= $result.PHP_EOL;
            }
            sc_smtp($cmd_result);
        }
    }
    
    curl_close($curl);    
    sleep(1);
}

