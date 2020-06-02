<?php
require_once 'Telegram.php';
require_once 'user.php';

$telegram = new Telegram('1135520500:AAEZBgpl_4OBzC-UfhH4quhblq9jza1RIrM');
$ADMIN_CHAT_ID = 1161868712;

$data = $telegram ->getData();

$message = $data['message'];

$text = $message ['text'];
$chat_id = $message ['chat'] ['id'];
$name = $message ['chat'] ['first_name'];
$surname = $message ['chat'] ['last_name'];

$orderTypes = ["1kg - 50 000so'm","1,5kg(1L)-75 000so'm","4.5kg(3L)-220 000so'm","7.5kg(5L)-370 000so'm" ] ;

if ($text == '/start'){
    showStart();
} else {
    switch (getPage($chat_id)) {
        case "main":
            if ($text == "🍯Biz haqimizda") {
                showAbout();
            } elseif ($text == "🍯Buyurtma berish") {
                showMass();
            } else {
                chooseButtons();
            }
            break;
        case "back":
            if ($text == "🔙Orqaga"){
                showStart();
            }else {
                chooseButtons();
            }
            break;
        case "mass":
            if (in_array($text, $orderTypes)) {
                setMass($chat_id, $text);
                showPhone();
            } elseif ($text == "🔙Orqaga") {
                showStart();
            } else {
                chooseButtons();
            }
            break;
        case "phone":
            if ($message['contact']['phone_number'] != '') {
                setPhone($chat_id, $message['contact']['phone_number']);
                showDeliveryType();
            } elseif ($text == "🔙Orqaga") {
                showMass();
            } else {
                chooseButtons();
            }
            break;
        case "deliver":
            if ($text == '🚚Yetkazib berish🚚') {
                showLocation();
            } elseif ($text == '🚶Borib olish🚶') {
                showLastWord();
            } elseif ($text == "🔙Orqaga") {
                showPhone();
            } else {
                chooseButtons();
            }
            break;
        case "location":
            if ($message['location'] ['latitude']!=''){
                setLatitude ($chat_id, $message['location'] ['latitude']);
                setLongitude ($chat_id, $message['location'] ['longitude']);
                showLastWord();
            }elseif ($text == "Lokatsiya jo'nata olmayman") {
                showLastWord();
            }elseif ($text == "🔙Orqaga"){
                showDeliveryType();
            } else {
                chooseButtons();
            }
            break;
        case "ready":
            if ($text == 'Asosiy menu'){
                showStart();
            }else {
                chooseButtons();
            }
            break;
    }
}

function showStart (){
    global $telegram,$chat_id,$name, $surname;
    setPage($chat_id, 'main');
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
    global $telegram, $chat_id;
    setPage($chat_id, 'back');
    $option = array(
        //First row
        array($telegram->buildKeyboardButton("🔙Orqaga"),),
    );
    $keyb = $telegram->buildKeyBoard($option, $onetime = false, $resize = true);
    $content = array('chat_id' => $chat_id,'reply_markup' => $keyb,  'text' => "Biz haqimizda! <a href = 'https://telegra.ph/Biz-haqimizda-05-14' > Havola</a > ", 'parse_mode' => 'html');
    $telegram->sendMessage($content);
}
function showMass (){
    global $telegram, $chat_id;
    setPage($chat_id,'mass');
    $option = array(
        //First row
        array($telegram->buildKeyboardButton("1kg - 50 000so'm"),),
        array($telegram->buildKeyboardButton("1,5kg(1L)-75 000so'm"),),
        array($telegram->buildKeyboardButton("4.5kg(3L)-220 000so'm"),),
        array($telegram->buildKeyboardButton("7.5kg(5L)-370 000so'm"),),
        array($telegram->buildKeyboardButton("🔙Orqaga"),),
    );
    $keyb = $telegram->buildKeyBoard($option, $onetime = false, $resize = true);
    $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "Buyurtma berish uchun hajmlardan birini tanlang yoki o'zingiz hohlagan hajmni kiriting.");
    $telegram->sendMessage($content);
}
function showPhone (){
    global $telegram, $chat_id;

    setPage($chat_id, 'phone');
    $option = array(
        //First row
        array($telegram->buildKeyboardButton("Raqamni jo'natish", $request_contact = true,$request_location = false)),
        array($telegram->buildKeyboardButton("🔙Orqaga"),),
    );
    $keyb = $telegram->buildKeyBoard($option, $onetime = false, $resize = true);
    $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "Hajm tanlandi, endi telefon raqamingnizni kiritsangiz ..");
    $telegram->sendMessage($content);
}
function showDeliveryType (){
    global $telegram, $chat_id;
    setPage($chat_id, 'deliver');
    $option = array(
        //First row
        array($telegram->buildKeyboardButton("🚚Yetkazib berish🚚")),
        array($telegram->buildKeyboardButton("🚶Borib olish🚶")),
        array($telegram->buildKeyboardButton("🔙Orqaga"),),
    );
    $keyb = $telegram->buildKeyBoard($option, $onetime = false, $resize = true);
    $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "Bizda Toshkent shahri bo'ylab yetkazib berish xizmati mavjud. Yoki, o'zingiz tashrif buyurib olib ketishingiz mumkin!
Manzil: Toshkent sh, Olmazor tum. Talabalar shaharchasi.");
    $telegram->sendMessage($content);
    $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "Iltimos, quyidagi tugmalardan birini tanlang");
    $telegram->sendMessage($content);
    }
function showLocation () {
    global $telegram, $chat_id;
    setPage($chat_id,'location');
    $option = array(
        //First row
        array($telegram->buildKeyboardButton("Lokatsiya jo'natish",  $request_contact = false,$request_location = true)),
        array($telegram->buildKeyboardButton("Lokatsiya jo'nata olmayman")),
        array($telegram->buildKeyboardButton("🔙Orqaga"),),
    );
    $keyb = $telegram->buildKeyBoard($option, $onetime = false, $resize = true);
    $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "Iltimos quyidagi tugmalardan birini bo'sing");
    $telegram->sendMessage($content);
}
function chooseButtons (){
    global $telegram, $chat_id;
    $content = array('chat_id' => $chat_id, 'text' => "Iltimos quyidagi tugmalardan birini bosing!");
    $telegram->sendMessage($content);
}
function  showLastWord(){
    global $telegram, $chat_id, $text, $ADMIN_CHAT_ID;
    setPage($chat_id,'ready');
    $option = array(
        array($telegram->buildKeyboardButton("Asosiy menu"),),
    );
    $keyb = $telegram->buildKeyBoard($option, $onetime = false, $resize = true);
    $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "Sizning buyurtmangiz qabul qilindi. Tez orada siz bilan bog'lanamiz. Murojaatingiz uchun rahmat! 😊");
    $telegram->sendMessage($content);

    $text ="Yangi buyurtma keldi!";

    $text.= "\n";
    $text.="Hajm:" .getMass($chat_id);
    $text.= "\n";
    $text.="Telefon raqam:" .getPhone($chat_id);
    $text.= "\n";

    $content = array('chat_id' => $ADMIN_CHAT_ID, 'reply_markup' => $keyb, 'text' => $text);
    $telegram->sendMessage($content);

    if (getLongitude($chat_id) != ""){
        $content = array('chat_id' => $ADMIN_CHAT_ID, "latitude" =>getLatitude($chat_id), "longitude" => getLongitude($chat_id));
        $telegram->sendLocation($content);
    }

}