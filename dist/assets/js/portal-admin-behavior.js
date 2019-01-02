/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 263);
/******/ })
/************************************************************************/
/******/ ({

/***/ 1:
/***/ (function(module, exports) {

module.exports = jQuery;

/***/ }),

/***/ 263:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(264);


/***/ }),

/***/ 264:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


__webpack_require__(265);

jQuery(function ($) {

    /*
     Admin Settings Panel
     */

    // Repeaters
    $(function () {

        var $repeaters = $('[data-portal-repeater]');

        if (!$repeaters.length) {
            return;
        }

        $repeaters.each(function () {

            var $repeater = $(this),
                $dummy = $repeater.find('[data-repeater-item-dummy]');

            // Repeater
            $repeater.repeater({
                hide: function hide() {

                    $(this).stop().slideUp(300, function () {
                        $(this).remove();
                    });

                    $repeater.trigger('portal-repeater-remove', [$(this)]);
                },
                show: function show() {

                    var index = $(this).index();

                    $(this).find('[data-repeater-item-handle]').text(index + 1);

                    // Hide current title for new item and show default title
                    $(this).find('[data-repeater-collapsable-handle-title]').hide();
                    $(this).find('[data-repeater-collapsable-handle-default]').show();

                    $(this).addClass('opened').removeClass('closed').stop().slideDown();

                    init_colorpickers();
                    init_datepickers();

                    $repeater.trigger('portal-repeater-add', [$(this)]);
                },
                ready: function ready(setIndexes) {
                    $repeater.find('tbody').on('sortupdate', setIndexes);
                }
            });

            // Dummy item
            if ($dummy.length) {
                $dummy.remove();
            }

            // Sortable
            if (typeof $repeater.attr('data-repeater-sortable') !== 'undefined') {
                $repeater.find('.portal-repeater-list').sortable({
                    axis: 'y',
                    handle: '[data-repeater-item-handle]',
                    forcePlaceholderSize: true,
                    update: function update(e, ui) {

                        // Update the number in each row
                        $repeater.find('.portal-repeater-item').each(function () {
                            var index = $(this).index();
                            $(this).find('[data-repeater-item-handle]').text(index + 1);
                        });

                        init_colorpickers();
                        init_datepickers();
                    }
                });
            }

            // Collapsable
            if (typeof $repeater.attr('data-repeater-collapsable') !== 'undefined') {
                $repeater.find('.portal-repeater-item-content').hide();
            }

            $(document).on('click touchend', '.portal-repeater[data-repeater-collapsable] [data-repeater-collapsable-handle]', function () {

                var $repeater_field = $(this).closest('.portal-repeater-item'),
                    $content = $repeater_field.find('.portal-repeater-item-content'),
                    status = $repeater_field.hasClass('opened') ? 'closing' : 'opening';

                if (status == 'opening') {

                    $content.stop().slideDown();
                    $repeater_field.addClass('opened');
                    $repeater_field.removeClass('closed');
                } else {

                    $content.stop().slideUp();
                    $repeater_field.addClass('closed');
                    $repeater_field.removeClass('opened');
                }
            });
        });
    });

    // Notification feeds
    $(function () {

        var $repeaters = $('.portal-notification-feeds [data-portal-repeater]');

        if ($repeaters.length) {
            $repeaters.on('portal-repeater-remove', delete_notification_feed);
        }
    });

    /**
     * Fires when removing a repeater item (feed).
     *
     * @since {{VERSION}}
     */
    function delete_notification_feed(e, $item) {

        var post_ID = $item.find('.portal-repeater-item-field-post_id input[type="hidden"]').val(),
            $delete_feeds = $('input[type="hidden"][name="portal_notification_deleted_feeds"]'),
            deleted = $delete_feeds.val();

        if (!post_ID) {
            return;
        }

        if (!deleted) {
            var deleted = post_ID;
        } else {
            deleted = deleted + ',' + post_ID;
        }

        $delete_feeds.val(deleted);
    }

    function portal_use_custom_template_toggle(checkbox) {

        var checked = $(checkbox).prop('checked');

        if (false == checked) {

            $(checkbox).parents('tr').next().hide();
        } else {

            $(checkbox).parents('tr').next().show();
        }
    }

    if ($('#portal_settings\\[portal_use_custom_template\\]').length) {

        var checkbox = $('#portal_settings\\[portal_use_custom_template\\]');

        portal_use_custom_template_toggle(checkbox);

        $(checkbox).change(function () {

            portal_use_custom_template_toggle(checkbox);
        });
    }

    // Initialize special fields if they exist
    function init_datepickers() {
        if (jQuery('.datepicker').length) {
            jQuery(".datepicker").datepicker();
        }
    }

    function init_colorpickers() {
        if (jQuery('.color-field').length) {
            jQuery('.color-field').wpColorPicker();
        }
    }

    $(function () {
        init_datepickers();
        init_colorpickers();
    });

    // Deal with the task rows

    jQuery(".task-row :checked").each(function () {
        jQuery(this).parent().parent().addClass('completed');
        jQuery(this).addClass('completed');
    });

    jQuery(".task-row :checkbox").change(function () {
        if (jQuery(this).hasClass('completed')) {
            jQuery(this).removeClass('completed');
            jQuery(this).parent().parent().removeClass('completed');
        } else {
            jQuery(this).parent().parent().addClass('completed');
            jQuery(this).addClass('completed');
        }
    });

    var portal_uploader;

    jQuery('#portal_upload_image_button').click(function (e) {

        e.preventDefault();

        //If the uploader object has already been created, reopen the dialog
        if (portal_uploader) {
            portal_uploader.open();
            return;
        }

        //Extend the wp.media object
        var portal_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });

        //When a file is selected, grab the URL and set it as the text field's value
        portal_uploader.on('select', function () {
            var attachment = portal_uploader.state().get('selection').first().toJSON();
            jQuery('#portal_logo').val(attachment.url);
        });

        //Open the uploader dialog
        portal_uploader.open();
    });

    jQuery('#portal_upload_favicon_button').click(function (e) {

        e.preventDefault();

        //If the uploader object has already been created, reopen the dialog
        if (portal_uploader) {
            portal_uploader.open();
            return;
        }

        //Extend the wp.media object
        var portal_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });

        //When a file is selected, grab the URL and set it as the text field's value
        portal_uploader.on('select', function () {
            var attachment = portal_uploader.state().get('selection').first().toJSON();
            jQuery('#portal_favicon').val(attachment.url);
        });

        //Open the uploader dialog
        portal_uploader.open();
    });

    // Reset the colors to default
    jQuery('#portal-reset-colors').click(function (event) {
        event.preventDefault();

        jQuery('input.color-field').each(function () {

            var default_color = jQuery(this).attr('rel');
            jQuery(this).wpColorPicker('color', default_color);
        });

        return false;
    });

    if ($('#portal_settings\\[notification\\]').length) {

        $('#portal_settings\\[notification\\]').each(function () {

            var val = $(this).val();
            $(this).parents('.portal-repeater-item-content').addClass(val);
        });

        $('#portal_settings\\[notification\\]').change(function () {

            var val = $(this).val();
            $(this).parents('.portal-repeater-item-content').removeClass().addClass('portal-repeater-item-content').addClass(val);
        });
    }

    if (jQuery('#portal-notify-users').length) {

        if (jQuery('#portal-notify-users').prop('checked')) {
            jQuery('.portal-notification-edit').show();
        }

        jQuery('#portal-notify-users').change(function () {

            if (jQuery(this).prop('checked')) {
                tb_show('Notify Users', '#TB_inline?width=480&inlineId=portal-notification-modal&width=640&height=640');
            }
        });
    }

    jQuery('.portal-notification-help').click(function () {

        tb_show('Notificiation Help', '#TB_inline?width=480&inlineId=portal-notification-modal&width=640&height=640');
    });

    jQuery('.portal-notification-edit').click(function () {

        tb_show('Notify Users', '#TB_inline?width=480&inlineId=portal-notification-modal&width=640&height=640');
    });

    jQuery('.portal-notify-ok').click(function () {

        portalSetNotifications();
        tb_remove();
        return false;
    });

    jQuery('.all-checkbox').change(function () {

        if (jQuery(this).prop('checked')) {
            jQuery('.portal-notify-list input').prop('checked', true);
        } else {
            jQuery('.portal-notify-list input').prop('checked', false);
        }
    });

    jQuery('#acf-allowed_users .acf-button').click(function () {

        portalShowNotifyWarning();
    });

    jQuery('#acf-allowed_users select.user').change(function () {

        portalShowNotifyWarning();
    });

    if (jQuery('#acf_acf_portal_milestones').length) {

        portalDisableUsedMilestones();

        portalMakeMilestoneSelects();

        jQuery('#acf_acf_portal_milestones a.acf-button').click(function () {

            setTimeout(function () {
                portalMakeMilestoneSelects();
            }, 1000);
        });
    }

    /* Phase progress status visibility */
});

function portalMakeMilestoneSelects() {

    jQuery('#acf_acf_portal_milestones select').on('change', function () {

        portalDisableUsedMilestones();
    });
}

function portalDisableUsedMilestones() {

    var used_milestones = new Array();

    jQuery('.field_key-field_563d2b7cc8f6e option').prop('disabled', false);

    jQuery('.field_key-field_563d2b7cc8f6e select').each(function () {

        used_milestones.push(jQuery(this).val());
    });

    for (var i = 0; i < used_milestones.length; i++) {

        jQuery('.field_key-field_563d2b7cc8f6e option[value="' + used_milestones[i] + '"]').prop('disabled', true);
    }

    jQuery('.field_key-field_563d2b7cc8f6e option:selected').prop('disabled', false);

    portalMakeMilestoneSelects();
}

function portalShowNotifyWarning() {
    jQuery('.portal-notify-warning').show();
}

function portalSetNotifications() {

    var portal_notification_list = jQuery('.portal-notify-user-box:checkbox:checked');

    if (portal_notification_list.length) {

        jQuery('.portal-notification-edit').show();
    } else {

        jQuery('.portal-notification-edit').hide();
        jQuery('#portal-notify-users').prop('checked', false);
    }
}

/***/ }),

/***/ 265:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _jquery = __webpack_require__(1);

var _jquery2 = _interopRequireDefault(_jquery);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Create a Phase ID in JavaScript exactly as you would in PHP (No need to maintain two different functions in two different languages this way)
 * 
 * @since {{VERSION}}
 * @returns {string} Phase ID
 */
window.portal_generate_phase_id = function () {

	var ajaxUrl = ajaxurl ? ajaxurl : projectyoop.ajaxUrl;

	return _jquery2.default.ajax({
		url: ajaxUrl,
		data: {
			action: 'portal_generate_phase_id'
		},
		async: false
	}).responseJSON.data;
};

/**
 * Create a Task ID in JavaScript exactly as you would in PHP (No need to maintain two different functions in two different languages this way)
 * 
 * @since {{VERSION}}
 * @returns {string} Task ID
 */
// Makes some PHP methods to JS available using a Synchronous call

window.portal_generate_task_id = function () {

	var ajaxUrl = ajaxurl ? ajaxurl : projectyoop.ajaxUrl;

	return _jquery2.default.ajax({
		url: ajaxUrl,
		data: {
			action: 'portal_generate_task_id'
		},
		async: false
	}).responseJSON.data;
};

/***/ })

/******/ });