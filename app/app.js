var app = angular.module('myApp', ['ngRoute', 'ui.bootstrap', 'ngAnimate', 'ang-drag-drop']);

app.config(['$routeProvider',
  function($routeProvider) {
    $routeProvider.
    when('/', {
      title: 'Queue',
      templateUrl: 'partials/queue.html',
      controller: 'queueCtrl'
    })
    .otherwise({
      redirectTo: '/'
    });;
}]);
    