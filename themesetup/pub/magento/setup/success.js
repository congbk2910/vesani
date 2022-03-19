/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

'use strict';
angular.module('success', ['ngStorage'])
    .controller('successController', ['$scope', '$localStorage', function ($scope, $localStorage) {
        $scope.url = {
            front: '',
            admin: ''
        };
        $scope.db     = $localStorage.db;
        $scope.admin  = $localStorage.admin;
        $scope.config = $localStorage.config;
        $scope = {'config':{'address':{'actual_base_url':''}}};

        $scope.messages = ['test'];
        $localStorage.$reset();
        $localStorage.$reset();
        var $divs = document.getElementsByClassName('content-success');
        var $html = '<h1 class="jumbo-title"><span class="jumbo-icon icon-success-round"></span>Success</h1>';
        $html += '<div class="message message-notice"><span class="message-text">Be sure to delete or rename <strong>themesetup</strong> folder.</span></div>';
        $divs[0].innerHTML = $html;
    }]);
