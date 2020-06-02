<?php

include 'Telegram.php';

$telegram = new Telegram('1135520500:AAEZBgpl_4OBzC-UfhH4quhblq9jza1RIrM');
$filePath = "users/step.txt";
$data = $telegram ->getData();
$message = $data['message'];
//$telegram->sendMessage([
    //'chat_id' => $telegram->ChatID(),
   // 'text' => json_encode($data, JSON_PRETTY_PRINT)
//]);
$text = $message ['text'];
$chat_id = $message ['chat'] ['id'];
$name = $message ['chat'] ['first_name'];
$surname = $message ['chat'] ['last_name'];

$orderTypes = ["1kg - 50 000so'm","1,5kg(1L)-75 000so'm","4.5kg(3L)-220 000so'm","7.5kg(5L)-370 000so'm" ] ;

switch ($text){
    case "/start":
        showStart();
        break;
    case "🍯Biz haqimizda":
        showAbout();
        break;
    case"🍯Buyurtma berish":
        showOrder();
        break;
    case "orqaga":
        switch (file_get_contents($filePath)){
            case 'order':
                showStart();
                break;
            case 'about':
                showStart();
                break;
            case "phone":
                showOrder();
                break;
            case "deliver":
                askContact();
                break;
            case "location":
                showDeliveryType();
                break;
        }
        break;
    case "Asosiy menu":
        newOffer();
        showStart();
        break;
    case"🚚Yetkazib berish🚚":
        showGetOrder ();
        break;
    case "🚶Borib olish🚶":
        showLastWord ();
        break;
    default:
        if (in_array($text, $orderTypes)){
            file_put_contents('users/massa.txt',$text);
            askContact();
        }else {
            switch (file_get_contents($filePath)){
                case 'phone':
                    if ($message['contact']['phone_number'] !=''){
                        file_put_contents('users/phone.txt',$message['contact'] ['phone_number']);
                    }
                    else {
                        file_put_contents('users/phone.txt',$text);
                    }
                    showDeliveryType ();
                    break;
                case 'location':
                    if ($message['location'] ['latitude']!=''){
                        file_put_contents('users/location.txt', $message['location']['latitude']);
                        showLastWord();
                    }
                    else {
                    file_put_contents('users/location.txt',$text);
                    showLastWord();
                }
            }
        }
        break;

}

function showStart (){
    global $telegram,$chat_id,$name, $surname;
    $option = array(
        //First row
        array($telegram->buildKeyboardButton("🍯Biz haqimizda"),),
        //Second row
        array($telegram->buildKeyboardButton("🍯Buyurtma berish"),),
        //Third row
    );
    $keyb = $telegram->buildKeyBoard($option, $onetime = true, $resize = true);
    $content = array('chat_id' => $chat_id, 'text' => "Assalom alaykum $name $surname !
Ushbu bot orqali siz BeeO asal - arichilik firmasidan tabiiy asal va  asal mahsulotlarini sotib olishingiz mumkin!");
    $telegram->sendMessage($content);
    $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "Mening ismim Jamshid, ko`p yillardan beri oilaviy arichilik bilan shug`illanib kelamiz!
BeeO - asalchilik firmamiz mana 3 yildirki, Toshkent shahri aholisiga toza, tabiiy asal yetkizib bermoqda va ko`plab xaridorlarga ega bo`ldik, shukurki, shu yil ham arichiligimizni biroz kengaytirib siz azizlarning ham dasturxoningizga tabiiy - toza asal yetkazib berishni niyat qildik!");
    $telegram->sendMessage($content);

}
function showAbout (){
    global $telegram, $chat_id, $filePath;
    file_put_contents($filePath,'about');
    $option = array(
        //First row
        array($telegram->buildKeyboardButton("orqaga"),),
    );
    $keyb = $telegram->buildKeyBoard($option, $onetime = false, $resize = true);
    $content = array('chat_id' => $chat_id,'reply_markup' => $keyb,  'text' => "Biz haqimizda! <a href = 'https://telegra.ph/Biz-haqimizda-05-14' > Havola</a > ", 'parse_mode' => 'html');
    $telegram->sendMessage($content);
}
function showOrder (){
    global $telegram, $chat_id, $filePath;
    file_put_contents($filePath,'order');
    $option = array(
        //First row
        array($telegram->buildKeyboardButton("1kg - 50 000so'm"),),
        array($telegram->buildKeyboardButton("1,5kg(1L)-75 000so'm"),),
        array($telegram->buildKeyboardButton("4.5kg(3L)-220 000so'm"),),
        array($telegram->buildKeyboardButton("7.5kg(5L)-370 000so'm"),),
        array($telegram->buildKeyboardButton("orqaga"),),
    );
    $keyb = $telegram->buildKeyBoard($option, $onetime = false, $resize = true);
    $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "Buyurtma berish uchun hajmlardan birini tanlang yoki o'zingiz hohlagan hajmni kiriting.");
    $telegram->sendMessage($content);
}
function showDeliveryType (){
    global $telegram, $chat_id, $filePath;
    file_put_contents($filePath, "deliver");
    $option = array(
        //First row
        array($telegram->buildKeyboardButton("🚚Yetkazib berish🚚")),
        array($telegram->buildKeyboardButton("🚶Borib olish🚶")),
        array($telegram->buildKeyboardButton("orqaga"),),
    );
    $keyb = $telegram->buildKeyBoard($option, $onetime = false, $resize = true);
    $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "Bizda Toshkent shahri bo'ylab yetkazib berish xizmati mavjud. Yoki, o'zingiz tashrif buyurib olib ketishingiz mumkin!
Manzil: Toshkent sh, Olmazor tum. Talabalar shaharchasi.");
    $telegram->sendMessage($content);
    $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "Iltimos, quyidagi tugmalardan birini tanlang");
    $telegram->sendMessage($content);
}
function askContact (){
    global $telegram, $chat_id;

    file_put_contents('users/step.txt','phone');
    $option = array(
        //First row
        array($telegram->buildKeyboardButton("Raqamni jo'natish", $request_contact = true,$request_location = false)),
        array($telegram->buildKeyboardButton("orqaga"),),
    );
    $keyb = $telegram->buildKeyBoard($option, $onetime = false, $resize = true);
    $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "Hajm tanlandi, endi telefon raqamingnizni kiritsangiz ..");
    $telegram->sendMessage($content);

}
function showGetOrder (){
    global $telegram, $chat_id;
    file_put_contents('users/step.txt','location');
    $option = array(
        //First row
        array($telegram->buildKeyboardButton("Lokatsiya jo'natish",  $request_contact = false,$request_location = true)),
        array($telegram->buildKeyboardButton("Lokatsiya jo'nata olmayman")),
        array($telegram->buildKeyboardButton("orqaga"),),
    );
    $keyb = $telegram->buildKeyBoard($option, $onetime = false, $resize = true);
    $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "Iltimos quyidagi tugmalardan birini bo'sing");
    $telegram->sendMessage($content);
}
function showLastWord (){
    global $telegram, $chat_id;
    $option = array(
        array($telegram->buildKeyboardButton("Asosiy menu"),),
    );
    $keyb = $telegram->buildKeyBoard($option, $onetime = false, $resize = true);
    $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "Sizning buyurtmangiz qabul qilindi. Tez orada siz bilan bog'lanamiz. Murojaatingiz uchun rahmat! 😊");
    $telegram->sendMessage($content);
}
function newOffer () {
    global $telegram,$chat_id,$name, $surname;
    $content = array('chat_id' => $chat_id, 'text' => "$name $surname! Bizning xizmatimizdan foydalanganingiz uchun kattakon rahmat! Xoxlagan paytingiz sifatli asalga buyurtma berishingiz mumkin! Sizni yana kutib qolamiz!
");
    $telegram->sendMessage($content);
}
