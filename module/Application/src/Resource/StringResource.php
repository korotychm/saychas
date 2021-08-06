<?php
// src/Resource/StringResource.php

namespace Application\Resource;

class StringResource
{
    const YES   = 'да';
    const NO    = 'нет';
    const USER_ADDREES_ERROR_MESSAGE = "Необходимо указать адрес до номера дома!";
    const PRODUCT_FAILURE_MESSAGE = "Объект product не&nbsp;получен";
    const PRODUCT_SUCCESS_MESSAGE = "Объект product &nbsp;получен";
    const SESSION_NAMESPACE = "session_namespace";
    const CODE_CONFIRMATION_SESSION_NAMESPACE = "code_confirmation_session_namespace";
    const BASKET_SAYCHAS_title = "сейчас за час";
    const BASKET_SAYCHAS3_title = "сейчас за три часа";
    const BASKET_SAYCHAS_do = "в течение часа";
    const BASKET_SAYCHAS3_do = "в течение трех часов";
    const ERROR_MESSAGE = "Ошибка! ";
    
    /*
     * авторизация
     */
    
    const ERROR_INPUT_PHONE_MESSAGE = "Укажите корректный номер телефона. ";
    const ERROR_INPUT_PASSWORD_MESSAGE = "Введите пароль для&nbsp;входа. ";
    const ERROR_INPUT_NAME_SMS_MESSAGE = "Введите ваше имя и&nbsp;код&nbsp;из&nbsp;СМС. ";
    const ERROR_SEND_SMS_MESSAGE = "Ошибка отправки СМС";
    const ERROR_SEND_SMS_CODE_MESSAGE = "Некорректно указан код";
    const ERROR_SEND_USERNAME_MESSAGE = "Некорректно представились ";
    const ERROR_SEND_EMAIL_MESSAGE = "Некорректнный адрес email  ";
    const MESSAGE_ENTER_OR_REGISTER_TITLE = "Войти или зарегистрироваться";
    const MESSAGE_REGISTER_TITLE = "Регистрация";
    const ERROR_PASS_VALIDATION_MESSAGE ="Некорректно указан пароль";
    const ERROR_PASS_SECOND_MESSAGE ="Пароли не совпадают";
    
    const MESSAGE_ENTER_OR_REGISTER_TEXT = "Для продолжения необходимо зарегистрироваться";
    const BUTTON_LABLE_CONTINUE = "Продолжить";
    const BUTTON_LABLE_ENTER = "Войти";
    const BUTTON_LABLE_PASS_CHANGE = "Изменить и войти";
    const BUTTON_LABLE_RETURN = "&larr;Вернуться";            
    const BUTTON_LABLE_REGISTER = "Зарегистрироваться";            
    const MESSAGE_PASSFORGOT_TITLE = "Изменение пароля";            
    const USER_LABLE_HELLO = "Привет, ";

    /*
     * статусы магазинов
     */
    const STORE_CLOSE_FOR_NIGHT = "Магазин закрыт на ночь";
    const STORE_CLOSE_FOR_NIGHT_ALT = "К сожалению, ближайший к вам магазин закрыт на ночь. Откроется ";
    
    const STORE_UNAVALBLE = "Магазин временно недоступен";
    const STORE_UNAVALBLE_ALT = "К сожалению, ближайший к вам магазин закрыт. Возможно, у него выходные, праздники, или ремонт";
    
    const STORE_OUT_OF_RANGE = "Магазин вне зоны доставки";
    const STORE_OUT_OF_RANGE_ALT = "К сожалению, ближайший к вам магазин находится слишком далеко - мы не сможем привезти товары вовремя";
} 