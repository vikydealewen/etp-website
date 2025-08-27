<?php

$secret = '6LdTErIrAAAAAJKUnBjneR7-7umr2CdWbHAgJDTT';
//$secret = '6Ld71EgpAAAAAPc3savQnWNcHJuNJ6SqSKDTm8X1'; основной сайт
$to = "zhukova.vika14@google.com";//Почтовый ящик на который будет отправлено сообщение
$subject = "Тема сообщения";//Тема сообщения
$message = "Message, сообщение!";//Сообщение, письмо
$error = true;
//Шапка сообщения, содержит определение типа письма, от кого, и кому отправить ответ на письмо

if (!empty($_POST['g-recaptcha-response'])) {
    $curl = curl_init('https://www.google.com/recaptcha/api/siteverify');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, 'secret=' . $secret . '&response=' . $_POST['g-recaptcha-response']);
    $out = curl_exec($curl);
    curl_close($curl);

    $out = json_decode($out);
    if ($out->success == true) {
      $error = false;
    }
}

if ($error) {
    echo "Ошибка заполнения капчи. Перейти на сайт <a href='https://etp-pro.ru/'>ЭкспертТрансПроект</a>, если вас не перенаправило вручную.";

}else{
    // Проверяем или метод запроса POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Поочередно проверяем или были переданные параметры формы, или они не пустые
        if (isset($_POST['name']) && $_POST['name'] != "") {
            //Если параметр есть, присваеваем ему переданое значение
            $name = trim(strip_tags($_POST['name']));
        }

        if (isset($_POST['tel']) && $_POST['tel'] != "") {
            $number = trim(strip_tags($_POST['tel']));
        }

        if (isset($_POST['mail']) && $_POST['mail'] != "") {
            $mail = trim(strip_tags($_POST['mail']));
        }

        if (isset($_POST['work'])) {
          $type = "Общий запрос";

          if (strpos($_SERVER['REQUEST_URI'], 'railway') !== false) {
            $type = "Путевая часть";
          } elseif (strpos($_SERVER['REQUEST_URI'], 'scb') !== false) {
            $type = "СЦБ";
          } elseif (strpos($_SERVER['REQUEST_URI'], 'communication') !== false) {
            $type = "Сети связи";
          } elseif (strpos($_SERVER['REQUEST_URI'], 'radio') !== false) {
            $type = "Вычисление расчетов радиосвязи";
          } elseif (strpos($_SERVER['REQUEST_URI'], 'highway') !== false) {
            $type = "Автомобильные дороги";
          } elseif (strpos($_SERVER['REQUEST_URI'], 'general-plan') !== false) {
            $type = "Генеральный план";
          } elseif (strpos($_SERVER['REQUEST_URI'], 'electricity') !== false) {
            $type = "Электроснабжение и освещение";
          } elseif (strpos($_SERVER['REQUEST_URI'], 'security') !== false) {
            $type = "Транспортная безопасность";
          } else {
            $type = trim(strip_tags($_POST['work']));
          }
        }

        if (isset($_POST['description'])) {
          $description = trim(strip_tags($_POST['description']));
        }

        // Формируем письмо
        $message = "Заявка на расчет: \n";
        $message .= "Имя: " . $name;
        $message .= "\n";
        $message .= "Телефон: " . $number;
        $message .= "\n";
        $message .= "Почта: " . $mail;
        $message .= "\n";
        $message .= "Тип работ: " . $type;
        $message .= "\n";
        $message .= "Подробнее: " . $description;
        // Окончание формирования тела письма

        // Заголовки письма
        $headers  = "Content-type: text/plain; charset=utf-8\r\n";
        $headers .= "From: Сайт ETP <no-reply@etp-pro.ru>\r\n";
        $headers .= "Reply-To: $mail\r\n";

        // Посылаем письмо
        $send = mail($to, $subject, $message, $headers);

        if ($send) { //проверяем, отправилось ли сообщение
            header('Location: https://etp-pro.ru');
            echo "Сообщение отправлено успешно!
            		Перейти на сайт <a href='https://etp-pro.ru/'>ЭкспертТрансПроект</a>, если вас не перенаправило вручную.";
        } else {
            echo "Ошибка, сообщение не отправлено! Возможно, проблемы на сервере";
        }

    } else {
        exit;
    }
}

?>