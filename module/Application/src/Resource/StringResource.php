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
    const BASKET_SAYCHAS_do = "в течение часа";
    
    /*
     * авторизация
     */
    const ERROR_MESSAGE = "Ошибка. ";
    const ERROR_INPUT_PHONE_MESSAGE = "Укажите корректный номер телефона. ";
    const ERROR_INPUT_PASSWORD_MESSAGE = "Введите пароль для&nbsp;входа. ";
    const ERROR_INPUT_NAME_SMS_MESSAGE = "Введите ваше имя и&nbsp;код&nbsp;из&nbsp;СМС. ";
    const ERROR_SEND_SMS_MESSAGE = "Ошибка отправки СМС";
    const ERROR_SEND_SMS_CODE_MESSAGE = "Некорректно указан код";
    const ERROR_SEND_USERNAME_MESSAGE = "Некорректно представились ";
    const ERROR_SEND_EMAIL_MESSAGE = "Некорректнный адрес email  ";
    const MESSAGE_ENTER_OR_REGISTER_TITLE = "Войти или зарегистрироваться";
    const MESSAGE_REGISTER_TITLE = "Регистрация";
    
    const MESSAGE_ENTER_OR_REGISTER_TEXT = "Для продолжения необходимо зарегистрироваться";
    const BUTTON_LABLE_CONTINUE = "Продолжить";
    const BUTTON_LABLE_ENTER = "Войти";
    const BUTTON_LABLE_PASS_CHANGE = "Изменить и войти";
    const BUTTON_LABLE_RETURN = "&larr;Вернуться";            
    const BUTTON_LABLE_REGISTER = "Зарегистрироваться";            
    const MESSAGE_PASSFORGOT_TITLE = "Изменение пароля";            
    const USER_LABLE_HELLO = "Привет, ";

    
}