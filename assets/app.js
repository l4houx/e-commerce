import './bootstrap.js';

import '@fortawesome/fontawesome-free/css/all.css';
import './styles/app.css';

import { Tooltip, Toast, Modal, Popover } from 'bootstrap';

Array.from(document.querySelectorAll('.modal-onload'))
    .map((e) => new Modal(e, { backdrop: false }))
    .forEach((modal) => modal.show());

Array.from(document.querySelectorAll('[data-bs-toggle=tooltip]'))
    .map((e) => new Tooltip(e));

Array.from(document.querySelectorAll('.input-group-password'))
    .forEach((e) => {
        const button = e.querySelector('button');
        const input = e.querySelector('input');
        const state = {
            password: 'text',
            text: 'password',
        };
        button.addEventListener('click', () => {
            input.setAttribute('type', state[input.getAttribute('type')]);
        });
    });

Array.from(document.querySelectorAll('.slider'))
    .map((slider) => {
        const min = parseInt(slider.dataset.min);
        const max = parseInt(slider.dataset.max);
        const minTarget = document.querySelector(slider.dataset.minTarget);
        const maxTarget = document.querySelector(slider.dataset.maxTarget);
        noUiSlider.create(slider, {
            start: [parseInt(minTarget.value), parseInt(maxTarget.value)],
            tooltips: true,
            connect: true,
            step: 5,
            range: {
                'min': min,
                'max': max,
            },
            format: {
                to: (value) => parseInt(value),
                from: (value) => parseInt(value),
            },
        }).on('update', function (values, handle) {
            const value = values[handle];
            if (handle) {
                maxTarget.value = value;
            } else {
                minTarget.value = value;
            }
        });
    });

const sidebarTogglers = Array.from(
    document.querySelectorAll('.sidebar-toggler'),
);

sidebarTogglers.map((toggler) => toggler.addEventListener('click', () => {
    document.querySelector('body').classList.toggle('sidebar-open');
    sidebarTogglers.map((e) => e
        .setAttribute('aria-expanded', document.querySelector('body')
            .classList
            .contains('sidebar-open')));
}));

$(document).ready(function () {
    function Toastr() {
        Array.from(document.querySelectorAll('.toast'))
        .map((toast) => (new Toast(toast)).show());
    }

    // Product favorites ajax add and remove
    $(document).on("click", ".product-favorites-new, .product-favorites-remove", function () {
        const thisButton = $(this);
        if (thisButton.attr("data-action-done") == "1") {
            thisButton.unbind("click");
            return false;
        }
        function ajaxRequest(thisButton) {
            $.ajax({
                type: "GET",
                url: thisButton.data('target'),
                beforeSend: function () {
                    thisButton.attr("data-action-done", "1");
                    thisButton.html("<i class='fas fa-spinner fa-spin'></i>");
                },
                success: function (response) {
                    if (response.hasOwnProperty('success')) {
                        if (thisButton.hasClass('product-favorites-new')) {
                            thisButton.html('<i class="fas fa-heart"></i>');
                        } else {
                            thisButton.html('<i class="far fa-heart"></i>');
                        }
                        thisButton.attr("title", response.success).tooltip("_fixTitle");
                        Toastr('success', '', response.success);
                    } else if (response.hasOwnProperty('error')) {
                        thisButton.html('<i class="far fa-heart"></i>');
                        thisButton.attr("title", response.error).tooltip("_fixTitle");
                        Toastr('error', '', response.error);
                    } else {
                        thisButton.html('<i class="far fa-heart"></i>');
                        thisButton.attr("title", 'An error has occured').tooltip("_fixTitle");
                        Toastr('error', '', 'An error has occured');
                    }
                },
                error: function (xhr, status, error) {

                }
            });
        }

        ajaxRequest(thisButton);
    });
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
