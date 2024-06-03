//import './vendor/bootstrap/dist/css/bootstrap.min.css';
import '@fortawesome/fontawesome-free/css/all.css';
import './styles/app.css';

// start the Stimulus application
import './bootstrap.js';

import { Toast, Tooltip, Popover } from 'bootstrap';

const toasts = document.getElementById('liveToast');
if (toasts) {
    toasts.forEach((toast) => {
        (new Toast(toast, { delay: 5000 })).show();
    });
}

/*
import Tagify from '@yaireo/tagify';
var input = document.querySelector('input[name=tags]');
new Tagify(input,
    {
        originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(',')
    }
)
*/

//import 'bootstrap';
import 'htmx.org';

import Like from './like.js';

document.addEventListener('DOMContentLoaded', () => {
    console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
    // Like's system
    const likeElements = [].slice.call(document.querySelectorAll('a[data-action="like"]'));
    if (likeElements) {
        new Like(likeElements);
    }
});

"use strict";
!function () {
    window.Element.prototype.removeClass = function () {
        let className = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : "",
            selectors = this;
        if (!(selectors instanceof HTMLElement) && selectors !== null) {
            selectors = document.querySelector(selectors);
        }
        if (this.isVariableDefined(selectors) && className) {
            selectors.classList.remove(className);
        }
        return this;
    }, window.Element.prototype.addClass = function () {
        let className = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : "",
            selectors = this;
        if (!(selectors instanceof HTMLElement) && selectors !== null) {
            selectors = document.querySelector(selectors);
        }
        if (this.isVariableDefined(selectors) && className) {
            selectors.classList.add(className);
        }
        return this;
    }, window.Element.prototype.toggleClass = function () {
        let className = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : "",
            selectors = this;
        if (!(selectors instanceof HTMLElement) && selectors !== null) {
            selectors = document.querySelector(selectors);
        }
        if (this.isVariableDefined(selectors) && className) {
            selectors.classList.toggle(className);
        }
        return this;
    }, window.Element.prototype.isVariableDefined = function () {
        return !!this && typeof (this) != 'undefined' && this != null;
    }
}();

var e = {
    init: function () {
        e.preLoader(),
            e.stickyHeader(),
            e.toolTipFunc(),
            e.popOverFunc(),
            e.backTotop()
    },
    isVariableDefined: function (el) {
        return typeof !!el && (el) != 'undefined' && el != null;
    },
    getParents: function (el, selector, filter) {
        const result = [];
        const matchesSelector = el.matches || el.webkitMatchesSelector || el.mozMatchesSelector || el.msMatchesSelector;

        // match start from parent
        el = el.parentElement;
        while (el && !matchesSelector.call(el, selector)) {
            if (!filter) {
                if (selector) {
                    if (matchesSelector.call(el, selector)) {
                        return result.push(el);
                    }
                } else {
                    result.push(el);
                }
            } else {
                if (matchesSelector.call(el, filter)) {
                    result.push(el);
                }
            }
            el = el.parentElement;
            if (e.isVariableDefined(el)) {
                if (matchesSelector.call(el, selector)) {
                    return el;
                }
            }

        }
        return result;
    },
    getNextSiblings: function (el, selector, filter) {
        let sibs = [];
        let nextElem = el.parentNode.firstChild;
        const matchesSelector = el.matches || el.webkitMatchesSelector || el.mozMatchesSelector || el.msMatchesSelector;
        do {
            if (nextElem.nodeType === 3) continue; // ignore text nodes
            if (nextElem === el) continue; // ignore elem of target
            if (nextElem === el.nextElementSibling) {
                if ((!filter || filter(el))) {
                    if (selector) {
                        if (matchesSelector.call(nextElem, selector)) {
                            return nextElem;
                        }
                    } else {
                        sibs.push(nextElem);
                    }
                    el = nextElem;

                }
            }
        } while (nextElem = nextElem.nextSibling)
        return sibs;
    },
    on: function (selectors, type, listener) {
        document.addEventListener("DOMContentLoaded", () => {
            if (!(selectors instanceof HTMLElement) && selectors !== null) {
                selectors = document.querySelector(selectors);
            }
            selectors.addEventListener(type, listener);
        });
    },
    onAll: function (selectors, type, listener) {
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll(selectors).forEach((element) => {
                if (type.indexOf(',') > -1) {
                    let types = type.split(',');
                    types.forEach((type) => {
                        element.addEventListener(type, listener);
                    });
                } else {
                    element.addEventListener(type, listener);
                }


            });
        });
    },
    removeClass: function (selectors, className) {
        if (!(selectors instanceof HTMLElement) && selectors !== null) {
            selectors = document.querySelector(selectors);
        }
        if (e.isVariableDefined(selectors)) {
            selectors.removeClass(className);
        }
    },
    removeAllClass: function (selectors, className) {
        if (e.isVariableDefined(selectors) && (selectors instanceof HTMLElement)) {
            document.querySelectorAll(selectors).forEach((element) => {
                element.removeClass(className);
            });
        }

    },
    toggleClass: function (selectors, className) {
        if (!(selectors instanceof HTMLElement) && selectors !== null) {
            selectors = document.querySelector(selectors);
        }
        if (e.isVariableDefined(selectors)) {
            selectors.toggleClass(className);
        }
    },
    toggleAllClass: function (selectors, className) {
        if (e.isVariableDefined(selectors) && (selectors instanceof HTMLElement)) {
            document.querySelectorAll(selectors).forEach((element) => {
                element.toggleClass(className);
            });
        }
    },
    addClass: function (selectors, className) {
        if (!(selectors instanceof HTMLElement) && selectors !== null) {
            selectors = document.querySelector(selectors);
        }
        if (e.isVariableDefined(selectors)) {
            selectors.addClass(className);
        }
    },
    select: function (selectors) {
        return document.querySelector(selectors);
    },
    selectAll: function (selectors) {
        return document.querySelectorAll(selectors);
    },


    // START: 01 Preloader
    preLoader: function () {
        window.onload = function () {
            var preloader = e.select('.preloader');
            if (e.isVariableDefined(preloader)) {
                preloader.className += ' animate__animated animate__fadeOut';
                setTimeout(function () {
                    preloader.style.display = 'none';
                }, 200);
            }
        };
    },
    // END: Preloader

    // START: 02 Menu Dropdown Hover
    /*
    dropdownHover: function () {
      if (window.matchMedia('(min-width: 992px)').matches) {
        (function($bs) {
          document.querySelectorAll('.dropdown-hover .dropdown').forEach(function(dd) {
              dd.addEventListener('mouseenter', function(e) {
                  let toggle = e.target.querySelector(':scope>[data-bs-toggle="dropdown"]');
                  if (!toggle.classList.contains('show')) {
                      $bs.Dropdown.getOrCreateInstance(toggle).toggle();
                  }
              });
              dd.addEventListener('mouseleave', function(e) {
                  let toggle = e.target.querySelector(':scope>[data-bs-toggle="dropdown"]');
                  if (toggle.classList.contains('show')) {
                      $bs.Dropdown.getOrCreateInstance(toggle).toggle();
                  }
              });
          });
        })(bootstrap);
      }
    },
    */
    // END: Menu Dropdown Hover

    // START: 03 Sticky Header
    stickyHeader: function () {
        if (window.matchMedia('(min-width: 992px)').matches) {
            var stickyNav = e.select('.header-sticky');
            if (e.isVariableDefined(stickyNav)) {
                document.addEventListener('scroll', function (event) {
                    var scTop = window.pageYOffset || document.documentElement.scrollTop;
                    if (scTop >= 400) {
                        stickyNav.addClass('header-sticky-on');
                    } else {
                        stickyNav.removeClass("header-sticky-on");
                    }
                });
            }
        }
    },
    // END: Sticky Header

    // START: 05 Tooltip
    // Enable tooltips everywhere via data-toggle attribute
    toolTipFunc: function () {
        var tooltipTriggerList = [].slice.call(e.selectAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new Tooltip(tooltipTriggerEl)
        })
    },
    // END: Tooltip

    // START: 06 Popover
    // Enable popover everywhere via data-toggle attribute
    popOverFunc: function () {
        var popoverTriggerList = [].slice.call(e.selectAll('[data-bs-toggle="popover"]'))
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new Popover(popoverTriggerEl)
        })
    },
    // END: Popover

    // START: 07 Back to Top
    backTotop: function () {
        var scrollpos = window.scrollY;
        var backBtn = e.select('.back-top');
        if (e.isVariableDefined(backBtn)) {
            var add_class_on_scroll = () => backBtn.addClass("back-top-show");
            var remove_class_on_scroll = () => backBtn.removeClass("back-top-show");

            window.addEventListener('scroll', function () {
                scrollpos = window.scrollY;
                if (scrollpos >= 800) {
                    add_class_on_scroll()
                } else {
                    remove_class_on_scroll()
                }
            });

            backBtn.addEventListener('click', () => window.scrollTo({
                top: 0,
                behavior: 'smooth',
            }));
        }
    },
    // END: Back to Top
};
e.init();

// js test

import $ from 'jquery';
window.jQuery = $;

$(function() {
    // Enable tooltips everywhere via data-toggle attribute
    function tooltip() {
        var tooltipTriggerList = [].slice.call(e.selectAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new Tooltip(tooltipTriggerEl)
        })
    }
    window.tooltip = tooltip;
    // END: Tooltip

    // Initializes bootstrap components
    //$('[data-bs-toggle="popover"], .has-popover').popover();
    /*$('[data-bs-toggle="tooltip"], .has-tooltip').tooltip({
        trigger: 'hover'
    });
    */

    // Notyf notification ( success, warning, info, error )
    function showStackBarTop() {
        // Create an instance of Notyf
        const notyf = new Notyf({
            duration: 8000,
            position: {
                x: 'right',
                y: 'top',
            },
            types: [
                {
                    type: 'info',
                    background: '#00bfff',
                    icon: false
                },
                {
                    type: 'warning',
                    background: '#ffd700',
                    icon: false
                },
            ]
        });

        let messages = document.querySelectorAll('notyf');

        messages.forEach(message => {
            if (message.className === 'success') {
                notyf.success(message.innerHTML);
            }

            if (message.className === 'error') {
                notyf.error(message.innerHTML);
            }

            if (message.className === 'info') {
                notyf.open({
                    type: 'info',
                    message: '<b>Info</b> - ' + message.innerHTML,
                });
            }

            if (message.className === 'warning') {
                notyf.open({
                    type: 'warning',
                    message: '<b>Warning</b> - ' + message.innerHTML
                });
            }
        });
    }
    window.showStackBarTop = showStackBarTop;

    // Product favorites ajax new and remove
    $(document).on("click", ".product-favorites-new, .product-favorites-remove", function () {
        var $thisButton = $(this);
        if ($thisButton.attr("data-action-done") == "1") {
            $thisButton.unbind("click");
            return false;
        }
        $.ajax({
            type: "GET",
            url: $thisButton.data('target'),
            beforeSend: function () {
                $thisButton.attr("data-action-done", "1");
                $thisButton.html("<i class='fas fa-spinner fa-spin'></i>");
            },
            success: function (response) {
                if (response.hasOwnProperty('success')) {
                    if ($thisButton.hasClass('product-favorites-new')) {
                        $thisButton.html('<i class="fas fa-heart"></i>');
                    } else {
                        $thisButton.html('<i class="far fa-heart"></i>');
                    }
                    $thisButton.attr("title", response.success).tooltip("_fixTitle");
                    showStackBarTop('success', '', response.success);
                } else if (response.hasOwnProperty('error')) {
                    $thisButton.html('<i class="far fa-heart"></i>');
                    $thisButton.attr("title", response.error).tooltip("_fixTitle");
                    showStackBarTop('error', '', response.error);
                } else {
                    $thisButton.html('<i class="far fa-heart"></i>');
                    //$thisButton.attr("title", Translator.trans('An error has occured', {}, 'javascript')).tooltip("_fixTitle");
                    //showStackBarTop('error', '', Translator.trans('An error has occured', {}, 'javascript'));
                    $thisButton.attr("title", 'An error has occured').tooltip("title");
					showStackBarTop('error', '', 'An error has occured');
                }
            }
        });
    });

    // Initializes Font Awesome picker
    /*
    if ($('.icon-picker').length) {
        $('.icon-picker').iconpicker({
            animation: false,
            inputSearch: true
        });
    }

    // Initializes wysiwyg editor
    if ($('.wysiwyg').length) {
        $('.wysiwyg').summernote({
            height: 500,
        });
    }

    // Jquery Cookie Bar
    if (typeof $("body").data('cookie-bar-page-link') !== 'undefined') {
        $.cookieBar('addTranslation', 'fr', {
            message: 'Nous utilisons des cookies pour fournir nos services. En utilisant ce site Web, vous acceptez cela.',
            acceptText: 'D\'accord',
            infoText: 'Plus d\'information'
        });
        $.cookieBar('addTranslation', 'es', {
            message: 'Usamos cookies para brindar nuestros servicios. Al utilizar este sitio web, acepta esto.',
            acceptText: 'Bueno',
            infoText: 'Más información'
        });
        $.cookieBar('addTranslation', 'ar', {
            message: 'نحن نستخدم ملفات تعريف الارتباط لتقديم خدماتنا. باستخدام هذا الموقع ، فإنك توافق على ذلك.',
            acceptText: 'حسنا',
            infoText: 'المزيد من المعلومات'
        });
        $.cookieBar('addTranslation', 'de', {
            message: 'Wir verwenden Cookies, um unsere Dienste bereitzustellen. Durch die Nutzung dieser Website stimmen Sie dem zu.',
            acceptText: 'OK',
            infoText: 'Mehr Informationen'
        });
        $.cookieBar('addTranslation', 'pt', {
            message: 'Usamos cookies para fornecer nossos serviços. Ao usar este site, você concorda com isso.',
            acceptText: 'OK',
            infoText: 'Mais Informações'
        });
        $.cookieBar('addTranslation', 'br', {
            message: 'Usamos cookies para fornecer nossos serviços. Ao usar este site, você concorda com isso.',
            acceptText: 'OK',
            infoText: 'Mais Informações'
        });
        $.cookieBar('addTranslation', 'it', {
            message: 'Utilizziamo i cookie per fornire i nostri servizi. Utilizzando questo sito Web, accetti questo.',
            acceptText: 'Va bene',
            infoText: 'Maggiori informazioni'
        });
        $.cookieBar({
            style: 'bottom',
            infoLink: $("body").data('cookie-bar-page-link'),
            language: $("html").attr("lang")
        });
    }

    // Color picker
    $(".color-picker").colorpicker();

    // Get background color of an element
    function hex(x) {
        return ("0" + parseInt(x).toString(16)).slice(-2);
    }
    function rgb2hex(rgb) {
        rgb = rgb.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(,\s*\d+\.*\d+)?\)$/);
        return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
    }

    function getProperty(el, prop) {
        return $(el).css(prop) || '';
    }

    $.fn.bgColor = function () {
        return rgb2hex(getProperty(this.get(0), 'background-color'));
    };

    $.fn.fgColor = function () {
        return rgb2hex(getProperty(this.get(0), 'color'));
    };

    $.fn.hasAttr = function (name) {
        return this.attr(name) !== undefined;
    };

    $.fn.rotationInfo = function () {
        var el = $(this),
                tr = el.css("-webkit-transform") || el.css("-moz-transform") || el.css("-ms-transform") || el.css("-o-transform") || '',
                info = {rad: 0, deg: 0};
        if (tr = tr.match('matrix\\((.*)\\)')) {
            tr = tr[1].split(',');
            if (typeof tr[0] != 'undefined' && typeof tr[1] != 'undefined') {
                info.rad = Math.atan2(tr[1], tr[0]);
                info.deg = parseFloat((info.rad * 180 / Math.PI).toFixed(1));
            }
        }
        return info;
    };
    */
});
