//LoginService
angular.module('edemoApp')
  .factory('LoginService', ['$http', '$rootScope', '$q', '$window',
    function($http, $rootScope,$q, $window, $location){
      return {
        login : function(login,password,callback){
          var actionName = 'login';
          $http.post('../php/User/doLogin.json','login='+login+'&password='+password)
            .success(function(data){
              console.log(data);
              if (validator.isNull(data.response.code)) {
                $rootScope.current_user = data.response;
                $rootScope.logged_in = true;
                $window.sessionStorage["userInfo"] = JSON.stringify($rootScope.current_user);
                return callback(true,actionName);
              }else{
                return callback(false,actionName,data.response.code);
              }
            })
            .error(function(data){
              return callback(false,actionName);
            });
        },
        logout : function(callback){
          var actionName = 'logout';
          console.log($rootScope.current_user.sid);
          $http.post('../php/User/doLogout.json','sid='+$rootScope.current_user.sid)
            .success(function(data){
              if(data.response.code == "111"){
                $rootScope.current_user = null;
                $rootScope.logged_in = false;
                $window.sessionStorage["userInfo"] = null;
                return callback(true,actionName,data.response.code);
              }else{
                return callback(false,actionName,data.response.code);
              }
            })
            .error(function(data){
              return callback(false,actionName);
            });
        }
      }
  }]);

//ErrorService
angular.module('edemoApp')
  .factory('ErrorService', ['$http',
    function($http){
      return {
        getErrorMessage : function(action,code){
          code = validator.isNull(code) ? 404 : code;
          $http.get('statics/error.json').success(function(data){
            //@TODO
            console.log(data[action][code]);
          });
        }
      }
  }]);