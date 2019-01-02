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
/******/ 	return __webpack_require__(__webpack_require__.s = 274);
/******/ })
/************************************************************************/
/******/ ({

/***/ 274:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(275);


/***/ }),

/***/ 275:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


function PopulateportalSingleShortcode() {

	jQuery('.portal-loading').show();

	data = { action: 'portal_get_projects' };

	jQuery.post(ajaxurl, data, function (response) {

		response = response.slice(0, -1);

		jQuery('#portal-single-project-list').html(response);
		jQuery('.portal-loading').hide();
	});
}

function InsertportalProject() {

	portalId = jQuery('#portal-single-project-id').val();
	portalStyle = jQuery('input[name="portal-display-style"]:checked').val();

	if (portalStyle == 'full') {

		portalOverview = jQuery('#portal-single-overview').val();
		if (portalOverview.length) {
			portalOverviewAtt = 'overview="' + portalOverview + '"';
		}

		portalMilestones = jQuery('#portal-single-milestones').val();
		if (portalMilestones.length) {
			portalMilestonesAtt = 'milestones="' + portalMilestones + '"';
		}

		portalPhases = jQuery('#portal-single-phases').val();
		if (portalPhases.length) {
			portalPhasesAtt = 'phases="' + portalPhases + '"';
		}

		portalTasks = jQuery('#portal-single-tasks').val();
		if (portalTasks.length) {
			portalTasksAtt = 'tasks="' + portalTasks + '"';
		}

		portalProgress = jQuery('#portal-single-progress').val();
		if (portalProgress.length) {
			portalProgressAtt = 'progress="' + portalProgress + '"';
		}

		shortcode = '[project_status id="' + portalId + '" ' + portalProgressAtt + ' ' + portalOverviewAtt + ' ' + portalMilestonesAtt + ' ' + portalPhasesAtt + ' ' + portalTasksAtt + ']';
	} else {

		portalPart = jQuery('#portal-part-display').val();

		if (portalPart == 'overview') {

			shortcode = '[project_status_part id="' + portalId + '" display="overview"]';
		} else if (portalPart == 'documents') {

			shortcode = '[project_status_part id="' + portalId + '" display="documents"]';
		} else if (portalPart == 'progress') {

			portalPartStyle = jQuery('#portal-part-overview-progress-select').val();
			shortcode = '[project_status_part id="' + portalId + '" display="progress" style="' + portalPartStyle + '"]';
		} else if (portalPart == 'phases') {

			portalPartStyle = jQuery('#portal-part-phases-select').val();
			shortcode = '[project_status_part id="' + portalId + '" display="phases" style="' + portalPartStyle + '"]';
		} else if (portalPart == 'tasks') {

			portalPartStyle = jQuery('#portal-part-tasks-select').val();
			shortcode = '[project_status_part id="' + portalId + '" display="tasks" style="' + portalPartStyle + '"]';
		}
	}

	tinymce.activeEditor.execCommand('mceInsertContent', false, shortcode);

	tb_remove();return false;
}

function InsertportalProjectList() {

	portalListTax = jQuery('#portal-project-taxonomy').val();
	portalListStatus = jQuery('#portal-project-status').val();
	portalUserAccess = jQuery('#portal-user-access').val();
	portalCount = jQuery('#portal-project-count').val();
	portalSort = jQuery('#portal-project-sort').val();

	if (portalUserAccess == 'on') {
		portalAccess = 'user';
	} else {
		portalAccess = 'all';
	}

	shortcode = '[project_list type="' + portalListTax + '" status="' + portalListStatus + '" access="' + portalAccess + '" count="' + portalCount + '" sort="' + portalSort + '" ]';

	tinymce.activeEditor.execCommand('mceInsertContent', false, shortcode);

	tb_remove();return false;
}

/***/ })

/******/ });