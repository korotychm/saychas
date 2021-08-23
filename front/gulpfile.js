// Выбираем препроцессор
let preprocessor = 'stylus';

// Определяем константы Gulp
const { src, dest, parallel, series, watch } = require('gulp');
// Подключаем Browsersync
const browserSync = require('browser-sync').create();
// Подключаем gulp-concat
const concat = require('gulp-concat');
// Подключаем gulp-uglify-es
const uglify = require('gulp-uglify-es').default;
// Подключаем модули препроцессоров
const stylus = require('gulp-stylus');
const less = require('gulp-less');
const sass = require('gulp-sass');
// Подключаем Autoprefixer
const autoprefixer = require('gulp-autoprefixer');
// Подключаем модуль gulp-clean-css
const cleancss = require('gulp-clean-css');
// Подключаем gulp-imagemin для работы с изображениями
const imagemin = require('gulp-imagemin');
// Подключаем модуль gulp-newer
const newer = require('gulp-newer');
// Подключаем модуль del
const del = require('del');
// Подключаем jade/pug
const pug = require('gulp-pug');
// Подключаем htmlbeautify
const htmlbeautify = require('gulp-html-beautify');


function browsersync() {
  browserSync.init({
    server: { baseDir: 'test/' }, // Указываем папку сервера
    notify: false, // Отключаем уведомления
    online: true // Режим работы
  })
}
function pugtohtml() {
  return src(['!assets/pug/mixins/*.pug','assets/pug/**/*.pug']) // Выбираем источник
  .pipe(pug()) // Преобразуем в html
  .pipe(htmlbeautify()) // Наводим красоту
  .pipe(dest('assets/html')) // Выгрузим результат
}

function html() {
  return src('assets/html/*.html') // Выбираем источник
  .pipe(dest('test/')) // Выгрузим на прод
  .pipe(browserSync.stream()) // Триггерим Browsersync
}

function prep() {
  return src('assets/' + preprocessor + '/**/*') // Выбираем источник
  .pipe(eval(preprocessor)()) // Преобразуем в css
  .pipe(dest('assets/css/')) // Выгрузим в папку css
}

function styles() {
  return src([ // Берём файлы из источников
    'assets/css/core/*', // Фреймворки
    'assets/css/plugins/**/*', // Плагины
    'assets/css/common/*', // Общие стили
    'assets/css/elements/**/*', // Стили элементов
    'assets/css/blocks/**/*', // Стили блоков
  ])
  .pipe(concat('style.min.css')) // Конкатенируем
  .pipe(autoprefixer({ overrideBrowserslist: ['last 10 versions'], grid: true })) // Создадим префиксы
  .pipe(cleancss( { level: { 1: { specialComments: 0 } }/* , format: 'beautify' */ } )) // Минифицируем
  .pipe(dest('test/css/')) // Выгрузим результат в прод
  .pipe(dest('../public/css/')) // Выгрузим результат в прод
  .pipe(browserSync.stream()) // Триггерим Browsersync
}

function scripts() {
  return src([ // Берём файлы из источников
    'assets/js/core/*', // Фреймворки
    'assets/js/plugins/**/*', // Плагины
    'assets/js/common/*', // Общие скрипты
    'assets/js/blocks/**/*', // Скрипты блоков
    'assets/js/elements/**/*', // Скрипты элементов
    ])
  .pipe(concat('scripts.min.js')) // Конкатенируем
  .pipe(uglify()) // Сжимаем
  .pipe(dest('test/js/')) // Выгружаем готовый файл
  .pipe(dest('../public/js/')) // Выгружаем готовый файл
  .pipe(browserSync.stream()) // Триггерим Browsersync
}

function img() {
	return src('assets/img/**/*') // Берём все изображения из папки источника
	.pipe(newer('test/img')) // Проверяем, было ли сжато изображение ранее
	.pipe(imagemin()) // Сжимаем и оптимизируем изображеня
	.pipe(dest('test/img/')) // Выгружаем оптимизированные изображения в папку назначения
  .pipe(dest('../public/img/')) // Выгружаем оптимизированные изображения в папку назначения
  .pipe(browserSync.stream()) // Триггерим Browsersync
}

function images() {
	return src('assets/images/**/*') // Берём все изображения из папки источника
	.pipe(newer('test/images')) // Проверяем, было ли сжато изображение ранее
	.pipe(imagemin()) // Сжимаем и оптимизируем изображеня
	.pipe(dest('test/images/')) // Выгружаем оптимизированные изображения в папку назначения
  .pipe(dest('../public/images/')) // Выгружаем оптимизированные изображения в папку назначения
  .pipe(browserSync.stream()) // Триггерим Browsersync
}

function cleanimg() {
	return del('test/img/**/*', { force: true }) // Удаляем все изображения прода
}
function cleanimages() {
	return del('test/images/**/*', { force: true }) // Удаляем все изображения прода
}
function cleanstyles() {
	return del('test/css/**/*', { force: true }) // Удаляем все стили прода
}
function cleanscripts() {
	return del('test/js/**/*', { force: true }) // Удаляем все скрипты прода
}
function cleanhtml() {
	return del('test/*.html', { force: true }) // Удаляем html прода
}
function cleanall() {
	cleanimg();
  cleanimages();
  cleanstyles();
  cleanscripts();
  cleanhtml();
  return
}

function startwatch() {
  watch(['assets/' + preprocessor + '/**/*'], prep);
  watch(['assets/css/**/*'], styles);
  watch(['assets/js/**/*'], scripts);
  watch(['assets/pug/**/*'], pugtohtml);
  watch(['assets/html/*.html'], html);
  watch(['assets/img/**/*'], img);
  watch(['assets/images/**/*'], images);
}

// Экспортируем функцию browsersync() как таск browsersync. Значение после знака = это имеющаяся функция.
exports.browsersync = browsersync;

// Экспортируем функцию html() в таск html
exports.html = html

// Экспортируем функцию prep() в таск prep
exports.prep = prep

// Экспортируем функцию styles() в таск styles
exports.styles = styles

// Экспортируем функцию scripts() в таск scripts
exports.scripts = scripts;

// Экспорт функции images() в таск images
exports.img = img;

// Экспорт функции images() в таск images
exports.images = images;

// Экспортируем функции очистки как таски
exports.cleanimg = cleanimg;
exports.cleanstyles = cleanstyles;
exports.cleanscripts = cleanscripts;
exports.cleanhtml = cleanhtml;
exports.cleanall = cleanall;

// Экспортируем функцию pugtohtml() как таск pug
exports.pugtohtml = pugtohtml;

// Экспортируем функцию startwatch() как таск startwatch
exports.startwatch = startwatch;

// Экспортируем дефолтный таск с нужным набором функций
exports.default = parallel(browsersync, startwatch);

// Build
exports.build = series(pugtohtml, html, prep, styles, scripts, images, img);
