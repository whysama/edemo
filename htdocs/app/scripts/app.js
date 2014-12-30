var app = angular
  .module('edemoApp',[
    'ngResource',
    'ngRoute',
    'LocalStorageModule'
  ])
 .config(function ($routeProvider) {
    $routeProvider
      .when('/',{
        templateUrl : 'views/main.html',
        controller: 'MainCtrl'
      });
  });

