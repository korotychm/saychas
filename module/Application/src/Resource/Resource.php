<?php

// src/Resource/Resource.php

namespace Application\Resource;

class Resource
{
    /*
     * common
     */

    const FOR_EXAMPLE_TEXT = "Например: ";
    const THE_BASKET = "Корзина ";
    const THE_CATALOG = "Каталог ";
    const THE_CATALOG_OF_PRODUCTS = "Каталог товаров ";
    const THE_ALL_PRODUCTS = "Все товары ";
    const THE_CATEGORY = "Категории ";
    const THE_BRANDS = "Торговые марки ";
    const THE_CATEGORY_OF_PRODUCTS = "Категории товаров ";
    const THE_ORDER_NUM = "Заказ № ";
    const THE_PICKUP = "Самовывоз";

    /*
     *  user
     */
    const USER_MENU_PROFILE = "Профиль";
    const USER_MENU_PROFILE_PAGE = "Личный кабинет";
    const USER_MENU_ORDERS = "Мои заказы";
    const USER_MENU_POST = "Мои оповещения";
    const USER_MENU_EXIT = "Выход";
    

    /*
     * Поиск
     */
    const SEARCH_INPUT_PLACEHOLDER = "Быстрый поиск";
    const SEARCH_PANEL_HELPER = "Смартфон с диагональю до 5 дюймов";

    /*
     * Товары
     */
   
   
    const REMOVE_FROM_FAVORITES = "Убрать из избранного";
    const FAVORITES_TITLE = "Избранные товары";
    const HISTORY_TITLE = "Просмотренные товары";
    const BUTTON_LABLE_ADD_TO_BASKET = "В корзину";
    const BUTTON_LABLE_PAY_NOW = "Купить сейчас";
    const ADD_TO_FAVORITES = "Добавить в избранное";
    const CURRENCY_RUBL = "&nbsp;&#8381; ";
    const YES = 'да';
    const NO = 'нет';
    const USER_ADDREES_PLASEHOLDER = "Укажи адрес доставки";
    const USER_ADDREES_ERROR_MESSAGE = "Необходимо указать адрес до номера дома!";
    const PRODUCT_FAILURE_MESSAGE = "Объект product не&nbsp;получен";
    const PRODUCT_SUCCESS_MESSAGE = "Объект product &nbsp;получен";
    const SESSION_NAMESPACE = "session_namespace";
    const CODE_CONFIRMATION_SESSION_NAMESPACE = "code_confirmation_session_namespace";
    const CATEGORY_TREE_CACHE_NAMESPACE = "category_tree_array";
    const BASKET_SAYCHAS_title = "сейчас за час";
    const BASKET_SAYCHAS3_title = "сейчас за три часа";
    const BASKET_SAYCHAS_do = "в течение часа";
    const BASKET_SAYCHAS3_do = "в течение трех часов";
    const BASKET_SAYCHAS_short = "за час";
    const BASKET_SAYCHAS3_short = "за три часа";
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
    const ERROR_PASS_VALIDATION_MESSAGE = "Некорректно указан пароль";
    const ERROR_PASS_SECOND_MESSAGE = "Пароли не совпадают";
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

    /*
     * Ввод адреса доставки
     */
    const BRING_NOW = "Привезем сейчас";
    const BRING_NOW_ACTION = "Укажи адрес и получи заказ за час!";
    const BRING_NOW_ACTION_DO = "Ввести адрес";

    /*
     * Заказы
     */
    const ORDER_TITLE = "Мои заказы";
    const ORDER_EMPTY = "Заказов не найдено";
    const ORDER_PAYMENT_DESCRIPTION = "Оплата заказа <OrderId/>. Saychas.ru "; // описание оплаты для платежной системы
    const ORDER_PAYMENT_DELIVERY = "Стоимость доставки "; // описание оплаты для платежной системы
    const ORDER_NEW_PAYCARD_TEXT = "Расплачивайтесь — мы запомним данные вашей карты, что бы не вводить её в следущий раз";
    const ORDER_NEW_PAYCARD_TITLE = "Оплата новой картой";
    const ORDER_MEMBER_PAYCARD_TITLE = "Оплата с привязанной карты";
    const ORDER_DELIVERY_TITLE = "Доставка";
    const ORDER_PICKUP_TITLE = "Самовывоз";
    const ORDER_PAYMENT_TITLE = "Оплата";
    const ORDER_PAYMENT_BUTTON = "Перейти к оплате";
    const ORDER_AGGREMENT_NOTES = "Нажимая на кнопку, вы соглашаетесь с <a href=\"#\">Условиями обработки перс. данных</a>, а также с <a href=\"#\">Условиями продажи</a>";
    
    
    
    /*
     * Числовые данные
     */
    const LIMIT_USER_ADDRESS_LIST = 5;  // лимит отображение введенных адресов в layout
    const SQL_LIMIT_PRODUCTCARD_IN_SLIDER = 12; //максимальное количество карточек товара в сладере
    const SQL_LIMIT_BRAND_SLIDER = 40; //максимальное количество брендов в сладере
    

    /*
     * Статусы заказов
     */
    const ORDER_STATUS_CODE_NEW         = ["id" => 0, "description" => "новый" ];
    const ORDER_STATUS_CODE_REGISTRED   = ["id" => 1, "description" => "зарегистрирован" ];
    const ORDER_STATUS_CODE_DELIVERY    = ["id" => 2, "description" => "доставляется" ];
    const ORDER_STATUS_CODE_CONFIRMED   = ["id" => 3, "description" => "доставлен" ];
    const ORDER_STATUS_CODE_CANCELED    = ["id" => 4, "description" => "отменен" ];

    /**
     * Characteristic types
     */
    const HEADER = 0;
    const STRING = 1;
    const INTEGER = 2;
    const BOOLEAN = 3;
    const CHAR_VALUE_REF = 4;
    const PROVIDER_REF = 5;
    const BRAND_REF = 6;
    const COLOR_REF = 7;
    const COUNTRY_REF = 8;
    
    const PRODUCT_RATING_VALUES = [10,20,30,40,50];
    
    const DEFAULT_IMAGE = "nophoto.jpeg";


}
