const ProductEdit = {
  template: `<div class="cp-container product">
                <div v-if="editable">
                  <div class="product__info product__info--top">
                    <div class="product__attribute">
                        <h2>Код товара</h2>
                        <p>{{ product.id }}</p>
                    </div>
                    <div class="product__attribute">
                        <h2>Статус</h2>
                        <p class="product__status" v-if="product.moderated"><span class="product__status-circle product__status-circle--0"></span>Товар прошел модерацию и опубликован на сайте.</p>
                        <p class="product__status" v-if="!product.moderated && product.processed"><span class="product__status-circle product__status-circle--2"></span>Товар не прошел модерацию. Измените товар и сохраните снова.</p>
                        <p class="product__status" v-if="!product.moderated && !product.processed"><span class="product__status-circle product__status-circle--1"></span>Товар проходит модерацию, вы пока не можете изменять этот товар.</p>
                    </div>
                  </div>
                  <div class="product__category">
                      <h2 :class="{'input-error' : (!selectedCategoryName && errors)}">Категория <span class="required">*</span></h2>
                      <div class="search-select">
                          <input class="input search-select__input" type="text" value="selectedCategoryName" v-model="categorySearch" @focusout="checkCategory()" />
                          <div class="search-select__suggestions">
                              <div v-if="!categorySearch" class="search-select__empty">Начните вводить название категории для поиска</div>
                              <div v-if="(categorySearch && !filteredCategories.length)" class="search-select__empty">Ничего не найдено</div>
                              <div v-if="(categorySearch && filteredCategories.length)">
                                <label v-for="category in filteredCategories">
                                  <input type="radio" name="suggest" :checked="(category.id == selectedCategoryId)" />
                                  <span class="search-select__suggestion" @mousedown="selectCategory(category.id, category.name)">
                                    <span class="search-select__suggestion-category--parent">{{category.parent}}</span>
                                    <span class="search-select__suggestion-category">{{category.name}}</span>
                                  </span>
                                </label>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div v-if="product.category_id" class="product__info">
                    <div class="product__main-attributes">
                      <div class="product__attribute  product__attribute--short">
                          <h2 :class="{'input-error' : (!product.vendor_code && errors)}">Артикул <span class="required">*</span></h2>
                          <input class="input checktext" type="text" v-model="product.vendor_code" @input="checkText('vendor_code')" />
                      </div>
                      <div v-if="(product.country_id !== undefined)" class="product__attribute  product__attribute--short">
                          <h2 :class="{'input-error' : (!selectedCountryName && errors)}">Страна производства <span class="required">*</span></h2>
                            <div class="search-select">
                                <input class="input search-select__input" type="text" value="product.country_name" v-model="countrySearch" @focusout="checkCountry()" />
                                <div class="search-select__suggestions">
                                    <div v-if="!countrySearch" class="search-select__empty">Начните вводить название страны для поиска</div>
                                    <div v-if="(countrySearch && !filteredCountries.length)" class="search-select__empty">Ничего не найдено</div>
                                    <div v-if="(countrySearch && filteredCountries.length)">
                                      <label v-for="country in filteredCountries">
                                        <input type="radio" name="suggest" :checked="(country.id == selectedCountryId)" />
                                        <span class="search-select__suggestion" @mousedown="selectCountry(country.id, country.title)">
                                          <span>{{ country.title }}</span>
                                        </span>
                                      </label>
                                    </div>
                                </div>
                            </div>
                      </div>
                      <div v-if="(product.brand_id !== undefined && showBrand != -1)" class="product__attribute product__attribute--short">
                          <h2>Бренд</h2>
                            <div class="search-select">
                                <input class="input search-select__input" type="text" value="product.brand_name" v-model="brandSearch" @focusout="checkBrand()" />
                                <div class="search-select__suggestions">
                                    <div v-if="!brandSearch" class="search-select__empty">Начните вводить название бренда для поиска</div>
                                    <div v-if="(brandSearch && !filteredBrands.length)" class="search-select__empty">Ничего не найдено</div>
                                    <div v-if="(brandSearch && filteredBrands.length)">
                                      <label v-for="brand in filteredBrands">
                                        <input type="radio" name="suggest" :checked="(brand.id == selectedBrandId)" />
                                        <span class="search-select__suggestion" @mousedown="selectBrand(brand.id, brand.title)">
                                          <span>{{ brand.title }}</span>
                                        </span>
                                      </label>
                                    </div>
                                </div>
                            </div>
                      </div>
                      <div class="product__attribute">
                          <h2 :class="{'input-error' : (!product.title && errors)}">Название товара <span class="required">*</span></h2>
                          <input class="input" type="text" v-model="product.title" @input="checkText('title')" />
                      </div>
                      <div v-if="(product.color_id !== undefined && showColor != -1)" class="product__attribute">
                          <h2>Цвет</h2>
                            <div class="product__colors">
                                <label v-for="color in product.colors" class="color-checkbox">
                                  <input type="radio" :value="color.id" name="color" :checked="product.color_id == color.id" v-model="product.color_id">
                                  <span class="color-checkbox__check">
                                    <span class="color-checkbox__check-color" :style="{'backgroundColor' : color.value}"></span>
                                  </span>
                                </label>
                            </div>
                      </div>
                      <div class="product__attribute">
                          <h2 :class="{'input-error' : (!product.description && errors)}">Описание товара <span class="required">*</span></h2>
                          <textarea class="textarea" v-model="product.description" @input="checkText('description')"></textarea>
                      </div>
                      <div class="product__attribute">
                          <h2>Вес с упаковкой</h2>
                          <input class="input input--number" type="number" v-model="product.weight" />
                          <span class="unit">(г)</span>
                      </div>
                      <div class="product__attribute">
                          <h2>Размер упаковки</h2>
                          <div class="size-input-group">
                            <div class="size-input-group__item">
                              <input class="input input--number" type="number" v-model="product.length" />
                              <span>Д</span>
                            </div>
                            <div class="size-input-group__item">
                              <input class="input input--number" type="number" v-model="product.width" />
                              <span>Ш</span>
                            </div>
                            <div class="size-input-group__item">
                              <input class="input input--number" type="number" v-model="product.height" />
                              <span>В</span>
                            </div>
                          </div>
                          <span class="unit">(см)</span>
                      </div>
                      <div class="product__attribute product__attribute--short">
                          <h2>Ставка НДС <span class="required">*</span></h2>
                          <div class="custom-select custom-select--radio" style="width: 253px;">
                            <div class="custom-select__label input" tabindex="0"></div>
                            <div class="custom-select__dropdown">
                              <div class="custom-select__dropdown-inner">
                                <label class="custom-select__option">
                                  <input type="radio" :checked="(product.vat === 'Без НДС')" value="Без НДС" name="vat_select" v-model="product.vat" />
                                  <span>Без НДС</span>
                                </label>
                                <label class="custom-select__option">
                                  <input type="radio" :checked="(product.vat === '0')" value="0" name="vat_select" v-model="product.vat" />
                                  <span>0%</span>
                                </label>
                                <label class="custom-select__option">
                                  <input type="radio" :checked="(product.vat === '10')" value="10" name="vat_select" v-model="product.vat" />
                                  <span>10%</span>
                                </label>
                                <label class="custom-select__option">
                                  <input type="radio" :checked="(product.vat === '18')" value="18" name="vat_select" v-model="product.vat" />
                                  <span>18%</span>
                                </label>
                                <label class="custom-select__option">
                                  <input type="radio" :checked="(product.vat === '20')" value="20" name="vat_select" v-model="product.vat" />
                                  <span>20%</span>
                                </label>
                              </div>
                            </div>
                          </div>
                      </div>
                    </div>
                    <input class="product__additional-attributes-trigger" type="checkbox" id="additional-attributes" /><label for="additional-attributes"><span>Раскрыть дополнительные поля</span></label>
                    <div class="product__additional-attributes">
                      <div v-for="(characteristic,index) in product.characteristics">
                        <div v-if="characteristic.type != 0 && characteristic.id != '000000001' && characteristic.id != '000000002' && characteristic.id != '000000003' && characteristic.id != '000000004'" class="product__attribute product__attribute--short">
                            <h2>{{ characteristic.characteristic_name }} <span v-if="characteristic.mandatory" class="required">*</span></h2>
                            <!-- Тип 4 - справочник (обычный ) -->
                            <div class="custom-select custom-select--radio" v-if="(characteristic.type == 4 && !Array.isArray(characteristic.value))">
                              <div class="custom-select__label input"></div>
                              <!-- выпадающий список -->
                              <div class="custom-select__dropdown">
                                <div class="custom-select__dropdown-inner">
                                  <label class="custom-select__option">
                                    <input type="radio" checked value="" :name="'option' + characteristic.id" v-model="characteristic.value" />
                                    <span>Не выбрано</span>
                                  </label>
                                  <label v-for="(val,idx) in characteristic.available_values" class="custom-select__option">
                                    <input type="radio" :checked="(val.id === characteristic.value)" :value="val.id" :name="'option' + characteristic.id" v-model="characteristic.value" />
                                    <span>{{val.title}}</span>
                                  </label>
                                </div>
                              </div>
                              <!-- /выпадающий список -->
                            </div>
                            <!-- Тип 4 - справочник (мульти) -->
                            <div class="custom-select custom-select--checkboxes" v-if="(characteristic.type == 4 && Array.isArray(characteristic.value))">
                              <div class="custom-select__label input" tabindex="0">Добавить</div>
                              <!-- выпадающий список -->
                              <div class="custom-select__dropdown">
                                <div class="custom-select__dropdown-inner">
                                  <label v-for="(val,idx) in characteristic.available_values" class="custom-select__option">
                                    <input :id="characteristic.id + '-' + val.id" type="checkbox" :checked="(characteristic.value.includes(val.id))" :value="val.id" :name="'option' + characteristic.id" v-model="characteristic.value" />
                                    <span>{{val.title}}</span>
                                  </label>
                                </div>
                              </div>
                              <!-- /выпадающий список -->
                              <!-- выбранные значения -->
                              <div v-if="characteristic.value.length" class="custom-select__selected">
                                <div v-for="val in characteristic.available_values">
                                  <div v-if="(characteristic.value.includes(val.id))" class="custom-select__selected-item">
                                    {{val.title}}
                                    <label :for="characteristic.id + '-' + val.id" class="custom-select__selected-del">
                                      <img src="/img/ui/plus.svg" />
                                    </label>
                                  </div>
                                </div>
                              </div>
                              <!-- /выбранные значения -->
                            </div>
                            <!-- Тип 1 - текст (обычный) -->
                            <div>
                              <input v-if="characteristic.type == 1 && !Array.isArray(characteristic.value)" type="text" class="input checktext" v-model="characteristic.value" @input="checkText(index,true)" :maxlength="characteristic.line_length ? characteristic.line_length : ''"/>
                            </div>
                            <!-- Тип 1 - текст (мульти)-->
                            <div v-if="characteristic.type == 1 && Array.isArray(characteristic.value)" class="multiple-input">
                              <div class="multiple-input">
                                <div v-if="!characteristic.value.length" class="multiple-input__item">
                                  <input type="text" class="input input--multiple" v-model="characteristic.value[0]"/>
                                </div>
                                <div v-for="(value, idx) in characteristic.value" class="multiple-input__item">
                                  <input type="text" class="input input--multiple" v-model="characteristic.value[idx]"/>
                                  <div v-if="!idx == 0" class="multiple-input__del" @click="deleteValue(index,idx)">
                                    <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="19px" height="17px">
                                    <path fill-rule="evenodd"  fill="currentColor"
                                    d="M18.230,4.736 L17.308,4.736 L15.96,16.370 C15.9,16.757 14.713,17.2 14.325,17.2 L4.674,17.2 C4.286,17.2 3.990,16.757 3.901,16.362 L1.691,4.736 L0.768,4.736 C0.338,4.736 0.1,4.406 0.1,3.983 C0.1,3.561 0.338,3.231 0.768,3.231 L6.361,3.231 L6.361,2.67 C6.361,0.927 7.306,0.0 8.467,0.0 L10.476,0.0 C11.637,0.0 12.582,0.927 12.582,2.67 L12.582,3.231 L18.175,3.231 C18.590,3.231 18.998,3.603 18.998,3.983 C18.998,4.406 18.661,4.736 18.230,4.736 ZM11.48,2.67 C11.48,1.787 10.791,1.560 10.476,1.560 L8.467,1.560 C8.152,1.560 7.895,1.787 7.895,2.67 L7.895,3.231 L11.48,3.231 L11.48,2.67 ZM3.272,4.736 L5.313,15.496 L13.629,15.496 L15.728,4.736 L3.272,4.736 ZM11.424,13.59 L11.368,13.59 C11.172,13.59 10.981,12.974 10.844,12.826 C10.704,12.674 10.636,12.473 10.656,12.275 L10.936,7.501 C10.977,7.97 11.291,6.785 11.665,6.785 C11.688,6.785 11.712,6.786 11.735,6.788 L11.747,6.790 L11.759,6.790 C11.955,6.790 12.146,6.875 12.283,7.23 C12.423,7.175 12.491,7.376 12.471,7.574 L12.192,12.348 C12.191,12.766 11.869,13.59 11.424,13.59 ZM7.562,13.59 L7.519,13.59 C7.123,13.59 6.793,12.749 6.751,12.338 L6.473,7.584 C6.427,7.132 6.811,6.828 7.208,6.788 C7.232,6.786 7.255,6.785 7.278,6.785 C7.652,6.785 7.966,7.97 8.8,7.511 L8.286,12.265 C8.312,12.521 8.202,12.696 8.105,12.798 C7.970,12.941 7.773,13.36 7.562,13.59 Z"/>
                                    </svg>
                                  </div>
                                </div>
                              </div>
                              <button class="btn btn--secondary multiple-input__add" @click="addValue(index)">Добавить значение</button>
                            </div>
                            <!-- Тип 2 - число (обычный)-->
                            <input v-if="characteristic.type == 2" type="number" min="0" class="input input--number" v-model="characteristic.value" :class="{'integer':!characteristic.fractional_part}"/>
                            <!-- Тип 3 - булево -->
                            <label v-if="characteristic.type == 3" class="boolean">
                              <input type="checkbox" v-model="characteristic.value" :checked="characteristic.value">
                              <span class="boolean__check"></span>
                            </label>
                            <!-- Тип 7 - цвет -->
                            <div class="product__colors" v-if="characteristic.type == 7">
                                <label v-for="color in product.colors" class="color-checkbox">
                                  <input type="radio" :value="color.id" name="color" :checked="characteristic.value == color.id" v-model="characteristic.value">
                                  <span class="color-checkbox__check">
                                    <span class="color-checkbox__check-color" :style="{'backgroundColor' : color.value}"></span>
                                  </span>
                                </label>
                            </div>
                            <span v-if="characteristic.unit" class="unit"> ({{ characteristic.unit }})</span>
                        </div>
                      </div>
                    </div>
                    <div v-if="product.images" class="product__images">
                        <div class="product__attribute">
                            <h2><span :class="{'input-error' : (!product.images.length && errors)}">Фото товара <span class="required">*</span></span> <p>Рекомендуемый размер <br>фото — 1000х1000 px. </p><p>Вы можете загрузить до 8 фотографий.</p><p>Формат фото - JPG, JPEG, PNG.</p><p>Максимальный размер файла - 5 Мб.</p></h2>
                            <div class="product__images-wrap">
                                <div class="product__images-nav"><button class="product__images-arrow product__images-arrow--up disabled" data-shift="-1"></button>
                                    <div class="product__images-list product__images-list--slider">
                                        <div class="product__images-track" data-shift="0" data-viewed="5">
                                            <div v-if="product.images">
                                              <div class="product-small-img" v-for="(image, index) in product.images" :class="{ 'active' : (image == currentImg) }" @click="currentImg = image">
                                                <img :src="image" />
                                              </div>
                                            </div>
                                        </div>
                                    </div><button class="product__images-arrow product__images-arrow--down" data-shift="1"></button>
                                </div>
                                <div class="product__images-selected">
                                    <label for="photo-upload" class="product__images-empty">Не загружено ни одной фотографии.<br>Загрузите хотя бы одну.</label><img :src="currentImg ? currentImg : ''" />
                                </div>
                                <div class="product__images-controls">
                                    <input type="file" id="photo-upload" accept=".jpg, .jpeg, .png" style="display:none;" @change="uploadFile"/>
                                    <label for="photo-upload" class="product__images-control product__images-control--add"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="15px" height="15px">
                                            <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M1.499,5.999 L13.499,5.999 C14.328,5.999 14.999,6.671 14.999,7.499 C14.999,8.328 14.328,8.999 13.499,8.999 L1.499,8.999 C0.671,8.999 0.0,8.328 0.0,7.499 C0.0,6.671 0.671,5.999 1.499,5.999 Z" />
                                            <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M7.499,0.0 C8.328,0.0 8.999,0.671 8.999,1.499 L8.999,13.499 C8.999,14.328 8.328,14.999 7.499,14.999 C6.671,14.999 5.999,14.328 5.999,13.499 L5.999,1.499 C5.999,0.671 6.671,0.0 7.499,0.0 Z" />
                                        </svg>
                                        <span>добавить фото</span>
                                    </label>
                                    <div class="product__images-control product__images-control--up" @click="moveImg(-1)"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="19px" height="10px">
                                            <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M10.532,0.431 L17.567,7.436 C18.149,8.16 18.149,8.957 17.567,9.537 C16.984,10.118 16.39,10.118 15.456,9.537 L8.422,2.532 C7.839,1.952 7.839,1.11 8.422,0.431 C9.5,0.149 9.949,0.149 10.532,0.431 Z" />
                                            <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M2.574,9.537 L9.608,2.532 C10.191,1.952 10.191,1.11 9.608,0.431 C9.25,0.149 8.80,0.149 7.498,0.431 L0.463,7.436 C0.118,8.16 0.118,8.957 0.463,9.537 C1.46,10.118 1.991,10.118 2.574,9.537 Z" />
                                        </svg>
                                        <span>поднять вверх</span>
                                    </div>
                                    <div class="product__images-control product__images-control--down" @click="moveImg(1)"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="19px" height="10px">
                                            <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M10.532,0.431 L17.567,7.436 C18.149,8.16 18.149,8.957 17.567,9.537 C16.984,10.118 16.39,10.118 15.456,9.537 L8.422,2.532 C7.839,1.952 7.839,1.11 8.422,0.431 C9.5,0.149 9.949,0.149 10.532,0.431 Z" />
                                            <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M2.574,9.537 L9.608,2.532 C10.191,1.952 10.191,1.11 9.608,0.431 C9.25,0.149 8.80,0.149 7.498,0.431 L0.463,7.436 C0.118,8.16 0.118,8.957 0.463,9.537 C1.46,10.118 1.991,10.118 2.574,9.537 Z" />
                                        </svg>
                                        <span>опустить вниз</span>
                                    </div>
                                    <div @click="delImg" class="product__images-control product__images-control--del"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="14px" height="14px">
                                            <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M3.317,1.196 L12.803,10.681 C13.388,11.267 13.388,12.217 12.803,12.803 C12.217,13.389 11.267,13.389 10.681,12.803 L1.196,3.317 C0.610,2.732 0.610,1.782 1.196,1.196 C1.782,0.610 2.732,0.610 3.317,1.196 Z" />
                                            <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M12.803,1.196 C13.389,1.782 13.389,2.732 12.803,3.317 L3.317,12.803 C2.732,13.388 1.782,13.388 1.196,12.803 C0.610,12.217 0.610,11.267 1.196,10.681 L10.681,1.196 C11.267,0.610 12.217,0.610 12.803,1.196 Z" />
                                        </svg>
                                        <span>удалить фото</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="product__save-back">
                      <router-link :to="$route.meta.back_route" class="btn btn--secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="8px" height="13px">
                            <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M0.903,4.974 L4.974,0.903 C5.560,0.317 6.510,0.317 7.96,0.903 C7.681,1.489 7.681,2.439 7.96,3.25 L3.25,7.96 C2.439,7.681 1.489,7.681 0.903,7.96 C0.318,6.510 0.318,5.560 0.903,4.974 Z" />
                            <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M7.96,9.974 L3.25,5.903 C2.439,5.317 1.489,5.317 0.903,5.903 C0.318,6.489 0.318,7.439 0.903,8.25 L4.974,12.96 C5.560,12.681 6.510,12.681 7.96,12.96 C7.681,11.510 7.681,10.560 7.96,9.974 Z" />
                        </svg>
                        <span>Вернуться</span>
                      </router-link>
                      <button class="btn btn--primary" :class="{disabled: (!product.moderated && !product.processed) || !isChanged}" :disabled="!isChanged" @click="saveProduct(false)">Сохранить и отправить на модерацию</button>
                    </div>
                  </div>
                </div>
                <div v-else class="product__error">Вы не можете редактировать этот товар</div>
            </div>`,
  data: function () {
    return {
      imgPath: productImgPath,
      imgPathModerated: productImgPathModerated,
      editable: true,
      categories: [],
      categoriesFlat: [],
      categorySearch: '',
      selectedCategoryId: '',
      selectedCategoryName: '',
      brands: [],
      brandSearch: '',
      selectedBrandId: '',
      selectedBrandName: '',
      countries: [],
      countrySearch: '',
      selectedCountryId: '',
      selectedCountryName: '',
      product: {},
      currentImg : '',
      deleteImages: [],
      cloneImages: [],
      errors: false,
      showBrand: -1,
      showColor: -1,
      originalData: null,
      originalBrandSearch: null,
      originalCategorySearch: null,
      originalSelectedCategoryId: null,
      originalSelectedCategoryName: null,
      originalVendorCode: null,
      originalCountrySearch: null,
      originalSelectedCountryName: null,
      originalProductDescription: null,
      originalProductWeight: null,
      originalProductLength: null,
      originalProductWidth: null,
      originalProductHeigth: null,
      originalProductVat: null,
      originalProductCharactristic: null,
    }
  },
  computed: {
    isChanged () {
      if (JSON.stringify(this.product) !== this.originalData) {
        console.log('оригинал дата сработала')
        return true;
      }
      if (JSON.stringify(this.product.vendor_code) !== this.originalVendorCode) {
        console.log('оригинал вендор код')
        return true;
      }
      if (JSON.stringify(this.countrySearch) !== this.originalCountrySearch) {
        console.log('оригинал кантри серч')
        return true;
      }
      if (JSON.stringify(this.selectedCountryName) !== this.originalSelectedCountryName) {
        console.log('оригинал селектед кантри нейм ')
        return true;
      }
      if (JSON.stringify(this.brandSearch) !== this.originalBrandSearch) {
        console.log('ригинал бренд серч')
        return true;
      }
      if (JSON.stringify(this.product.description) !== this.originalProductDescription) {
        console.log('оригинал продукт дескрипшен')
        return true;
      }
      if (JSON.stringify(this.product.weight) !== this.originalProductWeight) {
        console.log('оригинал вей')
        return true;
      }
      if (JSON.stringify(this.product.length) !== this.originalProductLength) {
        console.log('Оригинал ленгхт')
        return true;
      }
      if (JSON.stringify(this.product.width) !== this.originalProductWidth) {
        console.log('ригинал видс')
        return true;
      }
      if (JSON.stringify(this.product.height) !== this.originalProductHeigth) {
        console.log('оригинал хейгхт')
        return true;
      }
      if (JSON.stringify(...this.product.characteristics) !== this.originalProductCharactristic) {
        console.log('оригинал Продукт Характеристик')
        return true;
      }
      if (JSON.stringify(this.categorySearch) !== this.originalCategorySearch) {
        console.log('оригинал Категори серч')
        return true;
      }
      return false;
    },
    filteredCategories(){
      if (this.categorySearch == '') return false;
      let categories = this.categoriesFlat;
      categories = categories.filter((category) => {
        return (category.name.toLowerCase().includes(this.categorySearch.toLowerCase()))
      })
      return categories;
    },
    filteredBrands(){
      if (this.brandSearch == '') return false;
      let brands = this.brands;
      brands = brands.filter((brand) => {
        return (brand.title.toLowerCase().includes(this.brandSearch.toLowerCase()))
      })
      return brands;
    },
    filteredCountries(){
      if (this.countrySearch == '') return false;
      let countries = this.countries;
      countries = countries.filter((country) => {
        return (country.title.toLowerCase().includes(this.countrySearch.toLowerCase()))
      })
      return countries;
    }
  },
  methods: {
    checkText(target, characteristics = false){
      let input = '';
      if (characteristics){
        input = this.product.characteristics[target].value;
      } else {
        input = this.product[target];
      }
      let output = input;
      for (var i = 0; i < input.length; i++) {
            let c = input.charCodeAt(i);
            if (c < 32 || c == 96 || (c > 255 && c < 1040) || (c > 1103 && c != 8470)) {
                output = output.replace(input[i],'');
            }
      }
      if (characteristics){
        this.product.characteristics[target].value = output;
      } else {
        this.product[target] = output;
      }
    },
    checkRequired(){
      if (!this.selectedCategoryName || !this.product.vendor_code || !this.selectedCountryName || !this.product.title  || !this.product.description || !this.product.images.length){
        return true;
      }
      return false;
    },
    delImg(){
      var currentIndex = this.product.images.indexOf(this.currentImg);
      var images = [...this.product.images];
      var shift = (!(currentIndex == 0));
      if ((currentIndex + 1) == images.length){
        next = currentIndex- 1;
      } else {
        next = currentIndex + 1
      }
      this.deleteImages.push(images[currentIndex]);
      this.currentImg = images[next];
      images.splice(currentIndex, 1);
      this.product.images = images;
      // удаляем из clone
      currentIndex = this.cloneImages.indexOf(this.currentImg);
      if (currentIndex != -1){
        this.cloneImages.splice(currentIndex, 1);
      }
    },
    moveImg(shift) {
      var currentIndex = this.product.images.indexOf(this.currentImg);
      var images = [...this.product.images];
      [images[currentIndex],images[currentIndex + shift]] = [images[currentIndex + shift], images[currentIndex]];
      this.product.images = images;
      checkProductImagesSlider();
    },
    uploadFile() {
      var data = new FormData();
      var imagefile = document.querySelector('#photo-upload');
      if (imagefile.files[0].size > 5242880){
        showServicePopupWindow('Невозможно загрузить фото', 'Размер фото превышает 5Мб.');
      } else {
        data.append('file', imagefile.files[0]);
        data.append('product_id', this.product.id);
        data.append('provider_id', this.product.provider_id);
        axios.post('/control-panel/upload-product-image', data, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
        })
        .then(response => {
          console.log(response);
          let newImgPath = this.imgPath + '/' + response.data.image_file_name;
          this.product.images.push(newImgPath);
          this.currentImg = newImgPath;
          checkProductImagesSlider();
        })
        .catch(error => {
          console.log(error.response)
        })
      }
    },
    flatCategories() {
      console.log('Категории',this.categories);
      let categoriesFlat = [];
      function iterateArray(array, parent) {
        for (category of array){
          if (parent){
            category.parent = parent;
          }
          if (category.children){
            let newParent = category.title;
            if (parent) {
              newParent = category.parent + ' > ' + category.title;
            }
            iterateArray(category.children, newParent);
          } else {
            categoriesFlat.push({
              id: category.id,
              name: category.title,
              parent: category.parent
            })
          }
        }
      }
      iterateArray(this.categories, false);
      this.categoriesFlat = categoriesFlat;
      this.categorySearch = this.categoriesFlat.find(x => x.id === this.product.category_id).name;
      this.selectedCategoryName = this.categorySearch;
      this.selectedCategoryId = this.product.category_id;
    },
    saveProduct(categoryChange = false, oldCategory = null) {
        let requestUrl = '/control-panel/update-product';
        if (categoryChange) {
          requestUrl = '/control-panel/request-category-characteristics';
        } else {
          this.errors = this.checkRequired();
        }
        if (!this.errors || categoryChange){
          const headers = { 'X-Requested-With': 'XMLHttpRequest' };
          let chars = JSON.parse(JSON.stringify(this.product.characteristics));
          for (characteristic of chars){
            delete characteristic.characteristic_name;
            delete characteristic.real_value;
            delete characteristic.title;
            delete characteristic.available_values;
            // Страна
            if (characteristic.id == '000000001'){
              characteristic.value = this.selectedCountryId;
            }
            // Бренд
            if (characteristic.id == '000000003' && this.showBrand != -1){
              characteristic.value = this.selectedBrandId;
            }
            // Цвет
            if (characteristic.id == '000000004' && this.showColor != -1){
              characteristic.value = this.product.color_id;
            }
          }
          let category_in_request = this.selectedCategoryId;
          if (oldCategory) {
            category_in_request = oldCategory;
          }
          let cloneImages = JSON.parse(JSON.stringify(this.product.images));
          for (image in cloneImages){
            cloneImages[image] = cloneImages[image].split('/').slice(-1).pop();
          }
          if (this.showBrand == -1){
            this.selectedBrandId = '';
            this.product.brand_id = '';
          }
          if (this.showColor == -1){
            this.product.color_id = '';
          }
          let request = {
            id : this.product.id,
            vat: this.product.vat,
            brand_id: this.selectedBrandId,
            category_id: category_in_request,
            color_id: this.product.color_id,
            provider_id: this.product.provider_id,
            country_id: this.selectedCountryId,
            description: this.product.description,
            title: this.product.title,
            characteristics: chars,
            images: cloneImages,
            vendor_code: this.product.vendor_code,
            del_images: this.deleteImages,
            clone_images: this.cloneImages,
            width: this.product.width,
            height: this.product.height,
            length: this.product.length,
            weight: this.product.weight
          }
          console.log(request);
          axios
            .post(requestUrl,
              Qs.stringify({
                data: {
                  new_category_id: this.selectedCategoryId,
                  product : JSON.stringify(request)
                }
              }),
              {
                headers
              })
              .then(response => {
                if (categoryChange) {
                  let product = response.data.answer.data.product;
                  console.log(product);
                  this.product.characteristics = product.characteristics;
                  if (product.brand_id !== undefined){
                    this.product.brand_id = product.brand_id;
                    this.product.brand_name = product.brand_name;
                  } else {
                    delete this.product.brand_id;
                    delete this.product.brand_name;
                  }
                  if (product.country_id !== undefined){
                    this.product.country_id = product.country_id;
                    this.product.country_name = product.country_name;
                  } else {
                    delete this.product.country_id;
                    delete this.product.country_name;
                  }
                  if (product.color_id !== undefined){
                    this.product.color_id = product.color_id;
                  } else {
                    delete this.product.color_id;
                  }
                  if (product.characteristics){
                    this.showBrand = product.characteristics.findIndex(x => x.id === '000000003');
                    this.showColor = product.characteristics.findIndex(x => x.id === '000000004');
                  } else {
                    this.showBrand = -1;
                    this.showColor = -1;
                  }

                } else {
                  if (response.data.result){
                    showMessage('Информация о товаре сохранена и отправлена на модерацию.');
                    router.replace('/products');
                  }
                }
              })
              .catch(error => {
                console.log(error);
                if (error.response.status == '403'){
                  this.editable = false;
                  $('.main__loader').hide();
                }
              });
      } else {
        showServicePopupWindow('Невозможно сохранить изменения', 'Пожалуйста, заполните все необходимые поля (отмечены <span class="required">*</span>)');
      }
    },
    addImagesPath() {

      if (this.product.moderated) {
        this.cloneImages = JSON.parse(JSON.stringify(this.product.images));
      }
      for (image in this.product.images) {
        let currentImgPath = this.imgPath;
        if (this.product.moderated) {
          currentImgPath = this.imgPathModerated;
        }
        console.log(this.product.images[image]);
        this.product.images[image] = currentImgPath + this.product.images[image];
      }
    },
    getProduct() {
      let requestUrl = '/control-panel/edit-product';
      const headers = { 'X-Requested-With': 'XMLHttpRequest' };
      axios
        .post(requestUrl,
          Qs.stringify({
            product_id : this.$route.params.id
          }),{headers})
          .then(response => {
            if (response.data.data) {
              location.reload();
            } else {
              this.originalData = JSON.stringify(response.data.product)
              this.originalVendorCode = JSON.stringify(response.data.product.vendor_code)
              this.originalCountrySearch = JSON.stringify(response.data.product.country_name)
              this.originalSelectedCountryName = JSON.stringify(response.data.product.country_name)
              this.originalBrandSearch = JSON.stringify(response.data.product.brand_name)
              this.originalProductDescription = JSON.stringify(response.data.product.description)
              this.originalProductWeight = JSON.stringify(response.data.product.weight)
              this.originalProductLength = JSON.stringify(response.data.product.length)
              this.originalProductWidth = JSON.stringify(response.data.product.width)
              this.originalProductHeigth = JSON.stringify(response.data.product.height)
              this.originalProductVat = JSON.stringify(response.data.product.vat)
              this.originalProductCharactristic = JSON.stringify(...response.data.product.characteristics)
              this.categories = response.data.category_tree;
              this.product = response.data.product;
              this.addImagesPath();
              this.brandSearch = this.product.brand_name;
              this.selectedBrandId = this.product.brand_id;
              this.selectedBrandName = this.product.brand_name;
              this.brands = this.product.brands;
              this.countrySearch = this.product.country_name;
              this.selectedCountryId = this.product.country_id;
              this.selectedCountryName = this.product.country_name;
              this.countries = this.product.countries;
              if (this.product.images.length){
                this.currentImg = this.product.images[0];
              }
              this.showBrand = this.product.characteristics.findIndex(x => x.id === '000000003');
              this.showColor = this.product.characteristics.findIndex(x => x.id === '000000004');
              this.flatCategories();
              console.log(this.product);
            }
          })
          .catch(error => {
            if (error.response.status == '403'){
              this.editable = false;
              $('.main__loader').hide();
            }
          });
    },
    checkCategory() {
      setTimeout(() => {
        if (this.categorySearch != this.selectedCategoryName){
          this.categorySearch = this.selectedCategoryName;
        }
      }, 100);
    },
    checkBrand() {
      setTimeout(() => {
        if (this.brandSearch != this.selectedBrandName){
          this.brandSearch = this.selectedBrandName;
        }
      }, 100);
    },
    checkCountry() {
      setTimeout(() => {
        if (this.countrySearch != this.selectedCountryName){
          this.countrySearch = this.selectedCountryName;
        }
      }, 100);
    },
    selectCategory(id,value) {
      if (id != this.selectedCategoryId){
        let oldCategory = this.selectedCategoryId;
        this.selectedCategoryId = id;
        this.categorySearch = value;
        this.selectedCategoryName = value;
        this.saveProduct(true, oldCategory);
      }
    },
    selectBrand(id,value) {
      this.selectedBrandId = id;
      this.brandSearch = value;
      this.selectedBrandName = value;
    },
    selectCountry(id,value) {
      this.selectedCountryId = id;
      this.countrySearch = value;
      this.selectedCountryName = value;
    },
    deleteValue(characteristicIndex,valueIndex){
      if (this.product.characteristics[characteristicIndex].value.length > 1){
        this.product.characteristics[characteristicIndex].value.splice(valueIndex, 1);
      } else {
        Vue.set(this.product.characteristics[characteristicIndex].value, 0, '')
      }
    },
    addValue(characteristicIndex){
      this.product.characteristics[characteristicIndex].value.push('');
    }
  },
  created () {
    $('.main__loader').show();
    this.getProduct();
  },
  updated: function(){
    checkProductImagesSlider();
    setAllCustomSelects();
    $('.main__loader').hide();
  }
}

$(document).on('focusin','.search-select__input',function(){
  $(this).parent().find('.search-select__suggestions').addClass('active').css('pointer-events','auto');
});

$(document).on('focusout','.search-select__input',function(){
  let el = $(this).parent().find('.search-select__suggestions');
  el.removeClass('active')
  setTimeout(function() {
    el.css('pointer-events','none');
  }, 500);
});


function checkProductImagesSlider(){
  if ($('.product__images-nav .product-small-img').length){
    $('.product__images-empty').hide();
    $('.product__images-selected img').show();
  } else {
    $('.product__images-empty').show();
    $('.product__images-selected img').hide();
  }
  if ($('.product__images-nav .product-small-img').length < 6){
    $('.product__images-arrow').hide();
    $('.product__images-list').removeClass('product__images-list--slider');
    $('.product__images-track').css('transform','none');
    $('.product__images-track').data('shift','0');
  } else {
    $('.product__images-arrow').show();
    $('.product__images-list').addClass('product__images-list--slider');
  }
  if ($('.product__images-nav .product-small-img').length == 8){
    $('.product__images-control--add').addClass('disabled');
  } else {
    $('.product__images-control--add').removeClass('disabled');
  }
  if ($('.product__images-nav .product-small-img').length < 2){
    $('.product__images-control--up, .product__images-control--down').addClass('disabled');
  } else {
    $('.product__images-control--up, .product__images-control--down').removeClass('disabled');
  }
  if ($('.product__images-nav .product-small-img').length < 1){
    $('.product__images-control--del').addClass('disabled');
  } else {
    $('.product__images-control--del').removeClass('disabled');
  }
  if ($('.product__images-nav .product-small-img.active').index() < 1){
    $('.product__images-control--up').addClass('disabled');
  } else {
    $('.product__images-control--up').removeClass('disabled');
  }
  if ($('.product__images-nav .product-small-img.active').index() == ($('.product__images-nav .product-small-img').length - 1)){
    $('.product__images-control--down').addClass('disabled');
  } else {
    $('.product__images-control--down').removeClass('disabled');
  }
}

$(document).on('click','.product__images-arrow',function(){
  let track = $(this).parent().find('.product__images-track'),
      shift = +track.data('shift') + +$(this).data('shift'),
      slidesCount = track.find('.product-small-img').length,
      maxShift = slidesCount - track.data('viewed');
      track.data('shift',shift);
      track.css('transform', 'translateY(' + (shift * -80) + 'px)');
      console.log(shift,slidesCount,maxShift);
      if (shift == 0){
        $('.product__images-arrow--up').addClass('disabled');
      } else {
        $('.product__images-arrow--up').removeClass('disabled');
      }
      if (shift == maxShift) {
        $('.product__images-arrow--down').addClass('disabled');
      } else {
        $('.product__images-arrow--down').removeClass('disabled');
      }
});


function setCustomSelectLabels(el) {
  let textValue = el.find('input:checked + span').html();
  el.find('.custom-select__label').html('<span class="select-selected-value">'+textValue+'</span>');
}

function setAllCustomSelects() {
  $('.select-selected-value').remove();
  $('.custom-select--radio').each(function(){
    setCustomSelectLabels($(this));
  });
}

$(document).on('change','.custom-select--radio input',function(){
  let el = $(this).parent().parent().parent().parent();
  el.removeClass('active');
  setCustomSelectLabels(el);
});

$(document).on('click','.custom-select--radio input:checked',function(){
  let el = $(this).parent().parent().parent().parent();
  el.removeClass('active');
});

$(document).on('click','.custom-select__label',function(){
  $('.custom-select__label').not(this).parent().removeClass('active');
  $(this).parent().toggleClass('active');
  $(this).focus();
});

$(document).click( function(e){
    if ( $(e.target).closest('.custom-select').length ) {
        return;
    }
    $('.custom-select').removeClass('active');
});

$('.custom-select__label').keydown(function(e){
  console.log('down');
  if (e.keyCode == 40){
    $(this).parent().find('input').eq(0).focus();
  }
});

$('.custom-select__option input').keydown(function(e){
  e.preventDefault();
  let activeEl = $(this).parent().find('input:focus'),
      index = activeEl.parent().index() - 1,
      length = $(this).parent().find('input').length,
      step = 0;
  if (e.keyCode == 13){
    $(this).parent().find('input:focus').prop('checked',true);
    return;
  }
  if (e.keyCode == 40){
    index++;
    if (index == length){
      index = 0;
    }
  }
  if (e.keyCode == 38){
    index--;
    if (index == -1){
      index = length - 1;
    }
  }
  console.log('pressed',index);
  $(this).parent().find('input').eq(index).focus();
});
