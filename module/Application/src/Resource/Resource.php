<?php

// src/Resource/Resource.php

namespace Application\Resource;

class Resource
{
    const YES = 'да';
    const NO = 'нет';
    const FOR_EXAMPLE_TEXT = "Например: ";
    const THE_BASKET = "Корзина ";
    const THE_CATALOG = "Каталог ";
    const THE_CHARACTERISTICS = "Характеристики";
    const THE_DESCRIPTION = "Описание";
    const THE_CATALOG_OF_PRODUCTS = "Каталог товаров ";
    const THE_ALL_PRODUCTS = "Все товары ";
    const THE_CATEGORY = "Категории ";
    const THE_BRANDS = "Торговые марки ";
    const THE_CATEGORY_OF_PRODUCTS = "Категории товаров ";
    const THE_ORDER_NUM = "Заказ № ";
    const THE_DELIVERY = "Доставка";
    const THE_PICKUP = "Самовывоз";
    const DEFAULT_IMAGE = "nophoto.jpeg";
    const ONE_HOUR_SLOGAN = "Доставим за 1 час!";
    const PICKUP_FROM_STORE = "Самовывоз из магазина";
    const PICKUP_FROM_STORE_NOTICE = "Можете забрать самостоятельно из ближайшего к вам магазина";
    //const THE_ALL_PRODUCTS = "Все товары";
    
    /*
     * Cripting options
     */
    const CRYPT_TOKEN = "!@Banzaii!~!&!";
    const CRYPT_TYPE = "AES-128-CTR";
    
    /*
     * Session & Cookie
     */
    const SESSION_NAMESPACE = "session_namespace";
    const CODE_CONFIRMATION_SESSION_NAMESPACE = "code_confirmation_session_namespace";
    const CATEGORY_TREE_CACHE_NAMESPACE = "category_tree_array";
    const USER_COOKIE_NAME = "_schuid";
    const USER_COOKIE_TIME_LIVE = 2592000; //60*60*24*30

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
    const PRODUCT_CODE = "Код товара";
    const REMOVE_FROM_FAVORITES = "Убрать из избранного";
    const FAVORITES_TITLE = "Избранные товары";
    const HISTORY_TITLE = "Просмотренные товары";
    const BUTTON_LABLE_ADD_TO_BASKET = "В корзину";
    const BUTTON_LABLE_PAY_NOW = "Купить сейчас";
    const ADD_TO_FAVORITES = "Добавить в избранное";
    const CURRENCY_RUBL = "&nbsp;&#8381; ";
    const USER_ADDREES_PLASEHOLDER = "Укажи адрес доставки";
    const USER_ADDREES_ERROR_MESSAGE = "Необходимо указать адрес до номера дома!";
    const PRODUCT_FAILURE_MESSAGE = "Объект product не&nbsp;получен";
    const PRODUCT_SUCCESS_MESSAGE = "Объект product &nbsp;получен";
    const BASKET_SAYCHAS_title = "сейчас за час";
    const BASKET_SAYCHAS3_title = "сейчас за три часа";
    const BASKET_SAYCHAS_do = "в течение часа";
    const BASKET_SAYCHAS3_do = "в течение трех часов";
    const BASKET_SAYCHAS_short = "за час";
    const BASKET_SAYCHAS3_short = "за три часа";
    const ERROR_MESSAGE = "Ошибка! ";
    const PRODUCTCARD_SORT_ORDER = [ 0 => "id desc", 1 => "rating desc", 2 => "price asc", 3 => "price desc" ] ;
    const ONE_HOUR_SLOGAN_NOTES = "При заказе товаров из одного магазина";

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
    const ORDER_PAYMENT_DELIVERY = "Стоимость доставки "; // описание оплаты доставки для платежной системы
    const ORDER_NEW_PAYCARD_TEXT = "Расплачивайтесь — мы запомним данные вашей карты, что бы не вводить её в следущий раз";
    const ORDER_NEW_PAYCARD_TITLE = "Оплата новой картой";
    const ORDER_MEMBER_PAYCARD_TITLE = "Оплата с привязанной карты";
    const ORDER_DELIVERY_TITLE = "Доставка";
    const ORDER_PICKUP_TITLE = "Самовывоз";
    const ORDER_PAYMENT_TITLE = "Оплата";
    const ORDER_MORE_INFO = "Подробности заказа";
    const ORDER_PAYMENT_BUTTON = "Перейти к оплате";
    const ORDER_AGGREMENT_NOTES = "Нажимая на кнопку, вы соглашаетесь с <a href=\"#\">Условиями обработки перс. данных</a>, а также с <a href=\"#\">Условиями продажи</a>";
    
    /*
     * Числовые данные
     */
    const LIMIT_USER_ADDRESS_LIST = 5;  // лимит отображение введенных адресов в layout
    const SQL_LIMIT_PRODUCTCARD_TOP = 24; //максимально количество отображаемых ТОП товаров на глвной
    const SQL_LIMIT_PRODUCTCARD_IN_SLIDER = 12; //максимальное количество карточек товара в сладере
    const SQL_LIMIT_BRAND_SLIDER = 36; //максимальное количество брендов в сладере
    const SQL_LIMIT_PRODUCTCARD_IN_PAGE = 24; //максимальное карточек товаров на странице
    const SQL_LIMIT_PRODUCTCARD_HISTORY = 12; //максимальное карточек товаров в истории просмотров
    const SQL_LIMIT_PRODUCTCARD_FAVORITES = 24; //максимальное карточек товаров в избранном

    /*
     * Статус заказа
     */
    const ORDER_STATUS_CODE_NEW         = ["id" => 0, "description" => "Новый" ];
    const ORDER_STATUS_CODE_REGISTRED   = ["id" => 1, "description" => "Ожидаеет оплаты" ];
    const ORDER_STATUS_CODE_PACK        = ["id" => 2, "description" => "Собирается" ];
    const ORDER_STATUS_CODE_DELIVERY    = ["id" => 3, "description" => "Доставляется" ];
    const ORDER_STATUS_CODE_CONFIRMED   = ["id" => 4, "description" => "Доставлен" ];
    const ORDER_STATUS_CODE_CANCELED    = ["id" => 5, "description" => "Отменен" ];

    /*
     * Статус доставки
     */
    const THE_DELIVERY_PROGRESS = "Сборка";
    const THE_DELIVERY_READY = "Доставляется";
    const THE_DELIVERY_DELIVERED = "Доставлен";
    const THE_DELIVERY_CANCELED  = "Отменен";

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

    /**
     *  Rating & review
     */
    const REVIEWS_TITLE = "Отзывы о товаре";
    const PRODUCT_RATING_VALUES = [0 => 10, 1 => 20, 2 => 30, 3 => 40, 4 => 50, ];
    const REVIEW_MESSAGE_VALID_MIN_LENGHT = 4;
    const REVIEW_EMPTY_MEASSAGES_LIST = "У этого товара еще нет ни одного отзыва. Будьте первым!";
    const REVIEW_ADD_MEASSAGE_BUTTON =  "Оставить отзыв";
    const REVIEW_THANKS =  "Спасибо за отзыв!";
    const REVIEW_MANUAL_LINK_TEXT = "Правила оформления отзывов →";
    const REVIEW_SORT_OPTIONS = [ 0=> "Сначала свежие", 1 => "Сначала хорошие", 2 => "Сначала отрицательные" ];
    const REVIEWER_VALID_ERROR_404 = "Войдите или зарегистрируйтесь, чтобы оставить отзыв";
    const REVIEWER_VALID_ERROR_400 = "Вы должны купить товар, чтобы оставить отзыв к нему";
    const REVIEW_PAGING_BUTTON = "Показать еще отзывы";
    const REVIEW_MESSAGE_VALID_ERROR = "Отзыв должен быть более подробным ";
    const LEGAL_IMAGE_TYPES = ["image/jpeg", "image/png"];
    const LEGAL_IMAGE_NOTICE = "Допустимые форматы загружаемых файлов: ";
    const REVIEW_IMAGE_RESIZE = [ "width" => 1500,  "height" => 1500, "crop" => false, "type" => "jpeg" ];
    const REVIEW_IMAGE_THUMBNAILS = [ "width" => 100,  "height" => 100, "crop" => true, "type" => "jpeg" ];
    const REVIEWS_PAGING_LIMIT = 10;
    const REVIEWS_SORT_ORDER_RATING = [ 0 => "time_created desc", 1 => "rating desc", 2 => "rating asc" ] ;
    const REVIEWS_IMAGE_GALLARY_LIMIT = 24;
    
}
